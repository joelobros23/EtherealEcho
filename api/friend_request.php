<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id1 = $_SESSION['user_id'];
    $user_id2 = $_POST['user_id2'];

    if ($user_id1 == $user_id2) {
        http_response_code(400);
        echo json_encode(['message' => 'Cannot send friend request to yourself.']);
        exit;
    }

    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Check if a friendship already exists
        $stmt = $pdo->prepare("SELECT * FROM friendships WHERE (user_id1 = :user_id1 AND user_id2 = :user_id2) OR (user_id1 = :user_id2 AND user_id2 = :user_id1)");
        $stmt->execute(['user_id1' => $user_id1, 'user_id2' => $user_id2]);
        $friendship = $stmt->fetch();

        if ($friendship) {
            http_response_code(400);
            echo json_encode(['message' => 'Friend request already sent or you are already friends.']);
            exit;
        }

        // Create a new friend request
        $stmt = $pdo->prepare("INSERT INTO friendships (user_id1, user_id2) VALUES (:user_id1, :user_id2)");
        $stmt->execute(['user_id1' => $user_id1, 'user_id2' => $user_id2]);

        echo json_encode(['message' => 'Friend request sent successfully.']);

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
}
?>