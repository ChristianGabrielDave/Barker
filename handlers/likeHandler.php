<?php
session_start();
include '../includes/config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$post_id = $data['post_id'];
$user_id = $_SESSION['user_id'];
$current_user = $_SESSION['user_id'];

$check_like = $conn->query("SELECT * FROM likes WHERE pid = $post_id AND uid = $user_id");
if ($check_like->num_rows > 0) {
    $conn->query("DELETE FROM likes WHERE pid = $post_id AND uid = $user_id");
} else {
    $conn->query("INSERT INTO likes (pid, uid) VALUES ($post_id, $user_id)");
}

$like_result = $conn->query("SELECT COUNT(*) AS like_count FROM likes WHERE pid = $post_id");
$like_count = $like_result->fetch_assoc()['like_count'];

echo json_encode(["success" => true, "likes" => $like_count]);

$postQuery = $conn->prepare("SELECT uid FROM posts WHERE id = ?");
$postQuery->bind_param("i", $post_id);
$postQuery->execute();
$postResult = $postQuery->get_result();
$postData = $postResult->fetch_assoc();
$postOwner = $postData['uid'];

if ($postOwner != $current_user) {
    $notif_stmt = $conn->prepare("
    INSERT INTO notifications (user_id, type, from_user_id, post_id, message) 
    VALUES (?, 'like', ?, ?, ?)
");
$message = "liked your post.";
$notif_stmt->bind_param("iiis", $postOwner, $current_user, $post_id, $message);
$notif_stmt->execute();

}
?>
