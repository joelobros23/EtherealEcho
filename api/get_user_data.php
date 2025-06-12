<?php
require_once 'config.php';

// Check if user ID is provided
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Prepare and execute the SQL query
    $sql = "SELECT id, username, email, profile_picture, bio, created_at FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user_data = $result->fetch_assoc();

            // Return user data as JSON
            header('Content-Type: application/json');
            echo json_encode($user_data);
        } else {
            // User not found
            header('Content-Type: application/json');
            echo json_encode(array("message" => "User not found"));
            http_response_code(404);
        }

        $stmt->close();
    } else {
        // Error in preparing the statement
        header('Content-Type: application/json');
        echo json_encode(array("message" => "Error in preparing the statement"));
        http_response_code(500);
    }
} else {
    // User ID not provided
    header('Content-Type: application/json');
    echo json_encode(array("message" => "User ID not provided"));
    http_response_code(400);
}

// Close the database connection
$conn->close();
?>