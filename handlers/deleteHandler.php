<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

file_put_contents("debug.log", print_r($data, true), FILE_APPEND);

session_start();
require '../includes/config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in."]);
    exit();
}

if (!isset($data['post_id'])) {
    echo json_encode(["success" => false, "message" => "Invalid post ID."]);
    exit();
}

$post_id = (int) $data['post_id'];
$user_id = $_SESSION['user_id'];

// Ensure the user owns the post
$query = "DELETE FROM posts WHERE id = ? AND uid = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("ii", $post_id, $user_id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    echo json_encode(["success" => true, "message" => "Post deleted."]);
} else {
    echo json_encode(["success" => false, "message" => "Post not found or delete failed."]);
}
?>
