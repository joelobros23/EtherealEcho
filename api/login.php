<?php
// Enable CORS for cross-origin requests
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit; // Exit preflight requests
}

// Include database configuration
require_once 'config.php';

// Start session (if not already started)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get username and password from the request
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    // Validate input (basic check)
    if (empty($username) || empty($password)) {
        http_response_code(400); // Bad Request
        echo json_encode(['message' => 'Username and password are required.']);
        exit;
    }

    try {
        // Prepare SQL query to fetch user data by username
        $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if user exists and verify password
        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            // Return success response
            http_response_code(200); // OK
            echo json_encode(['message' => 'Login successful.', 'user' => ['id' => $user['id'], 'username' => $user['username']]]);
            exit;
        } else {
            // Return error response for invalid credentials
            http_response_code(401); // Unauthorized
            echo json_encode(['message' => 'Invalid username or password.']);
            exit;
        }
    } catch (PDOException $e) {
        // Handle database errors
        http_response_code(500); // Internal Server Error
        echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
        exit;
    }
} else {
    // Return error response for invalid request method
    http_response_code(405); // Method Not Allowed
    echo json_encode(['message' => 'Method not allowed. Use POST.']);
    exit;
}
?>