<?php
session_start();
include '../includes/config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$post_id = $data['post_id'];
$user_id = $_SESSION['user_id'];
$comment = $conn->real_escape_string($data['comment']);

$conn->query("INSERT INTO comments (pid, uid, content) VALUES ($post_id, $user_id, '$comment')");

$comment_result = $conn->query("SELECT COUNT(*) AS comment_count FROM comments WHERE pid = $post_id");
$comment_count = $comment_result->fetch_assoc()['comment_count'];

echo json_encode(["success" => true, "comments" => $comment_count]);
?>
