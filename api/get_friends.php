<?php
header('Content-Type: application/json');
require_once 'config.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$userId = $_SESSION['user_id'];

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

$stmt = $pdo->prepare("SELECT u.id, u.username, u.profile_picture FROM users u INNER JOIN friendships f ON (u.id = f.user_id2 AND f.user_id1 = :user_id AND f.status = 'accepted') OR (u.id = f.user_id1 AND f.user_id2 = :user_id AND f.status = 'accepted')");
$stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
$stmt->execute();

$friends = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['status' => 'success', 'friends' => $friends]);
?>