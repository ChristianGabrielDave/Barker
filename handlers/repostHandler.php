<?php
session_start();
include '../includes/config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$post_id = $data['post_id'];
$user_id = $_SESSION['user_id'];

$conn->query("INSERT INTO posts (content, uid, reposted_from) SELECT content, $user_id, id FROM posts WHERE id = $post_id");

echo json_encode(["success" => true]);
?>
