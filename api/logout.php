<?php
session_start();

// Destroy the session
session_destroy();

// Respond with success
$response = array(
    'status' => 'success',
    'message' => 'Logged out successfully.'
);

header('Content-Type: application/json');
echo json_encode($response);
exit();
?>