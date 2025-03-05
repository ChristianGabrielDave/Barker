<?php
session_start();
include '../includes/config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$post_id = $data['post_id'];
$user_id = $_SESSION['user_id'];

$check_like = $conn->query("SELECT * FROM likes WHERE pid = $post_id AND uid = $user_id");
if ($check_like->num_rows > 0) {
    $conn->query("DELETE FROM likes WHERE pid = $post_id AND uid = $user_id");
} else {
    $conn->query("INSERT INTO likes (pid, uid) VALUES ($post_id, $user_id)");
}

$like_result = $conn->query("SELECT COUNT(*) AS like_count FROM likes WHERE pid = $post_id");
$like_count = $like_result->fetch_assoc()['like_count'];

echo json_encode(["success" => true, "likes" => $like_count]);
?>
