<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bio = $_POST['bio'] ?? null;

    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $file_name = $_FILES['profile_picture']['name'];
        $file_tmp = $_FILES['profile_picture']['tmp_name'];
        $file_size = $_FILES['profile_picture']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $extensions = ['jpeg', 'jpg', 'png'];

        if (in_array($file_ext, $extensions)) {
            if ($file_size <= 2097152) { // 2MB
                $new_file_name = uniqid('', true) . '.' . $file_ext;
                $file_destination = '../assets/img/' . $new_file_name;

                if (move_uploaded_file($file_tmp, $file_destination)) {
                    // Update profile picture in database
                    $sql = "UPDATE users SET profile_picture = ? WHERE id = ?";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$new_file_name, $user_id]);

                    // Optionally delete the old profile picture if it's not the default one
                    $get_old_picture_sql = "SELECT profile_picture FROM users WHERE id = ?";
                    $get_old_picture_stmt = $pdo->prepare($get_old_picture_sql);
                    $get_old_picture_stmt->execute([$user_id]);
                    $old_picture = $get_old_picture_stmt->fetchColumn();

                     if ($old_picture && $old_picture !== 'default.png' && file_exists('../assets/img/' . $old_picture)) {
                            unlink('../assets/img/' . $old_picture);
                     }

                } else {
                    http_response_code(500);
                    echo json_encode(['message' => 'Failed to upload profile picture.']);
                    exit;
                }
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'File size too large. Max size is 2MB.']);
                exit;
            }
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid file type. Allowed types: jpeg, jpg, png.']);
            exit;
        }
    }
    

    // Update bio
    if ($bio !== null) {
        $sql = "UPDATE users SET bio = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$bio, $user_id]);
    }

    echo json_encode(['message' => 'Profile updated successfully.']);
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed.']);
}
?>