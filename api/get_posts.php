<?php
header('Content-Type: application/json');

require_once 'config.php';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->prepare("SELECT posts.id, posts.content, posts.created_at, users.username, users.profile_picture, users.id AS user_id
                                FROM posts
                                INNER JOIN users ON posts.user_id = users.id
                                ORDER BY posts.created_at DESC");
        $stmt->execute();
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($posts);

    } catch (PDOException $e) {
        echo json_encode(['error' => 'Failed to fetch posts: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>