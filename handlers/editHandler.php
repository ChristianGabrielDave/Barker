<?php
session_start();
include '../includes/config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$post_id = $data['post_id'];
$user_id = $_SESSION['user_id'];
$content = $conn->real_escape_string($data['content']);

$conn->query("UPDATE posts SET content = '$content' WHERE id = $post_id AND uid = $user_id");

echo json_encode(["success" => true]);
?>
