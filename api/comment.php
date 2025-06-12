<?php
header('Content-Type: application/json');
require_once 'config.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the post ID, user ID, and comment content from the request body
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';

    // Validate the input data
    if ($post_id <= 0 || $user_id <= 0 || empty($content)) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid input data.']);
        exit;
    }

    // Sanitize the comment content
    $content = htmlspecialchars($content);

    // Create a database connection
    $conn = new mysqli($db_host, $db_user, $db_password, $db_name);

    // Check the connection
    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(['message' => 'Database connection failed: ' . $conn->connect_error]);
        exit;
    }

    // Prepare the SQL query to insert the comment into the comments table
    $sql = "INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)";

    // Prepare the statement
    $stmt = $conn->prepare($sql);

    // Bind the parameters
    $stmt->bind_param("iis", $post_id, $user_id, $content);

    // Execute the statement
    if ($stmt->execute()) {
        // Comment created successfully
        http_response_code(201); // Created
        echo json_encode(['message' => 'Comment created successfully.']);
    } else {
        // Error creating comment
        http_response_code(500);
        echo json_encode(['message' => 'Error creating comment: ' . $stmt->error]);
    }

    // Close the statement and the connection
    $stmt->close();
    $conn->close();
} else {
    // Invalid request method
    http_response_code(405); // Method Not Allowed
    echo json_encode(['message' => 'Method not allowed. Use POST.']);
}
?>