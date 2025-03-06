<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$post_id = intval($_POST['post_id']);

// Check if the post exists
$postCheckSql = "SELECT * FROM posts WHERE id = $post_id";
$postCheckResult = mysqli_query($conn, $postCheckSql);

if (mysqli_num_rows($postCheckResult) == 0) {
    echo json_encode(['success' => false, 'message' => 'Post not found']);
    exit();
}

// Insert repost
$repostSql = "INSERT INTO posts (uid, content, media, dop, repost) 
              VALUES ($user_id, '', '', NOW(), $post_id)";
if (mysqli_query($conn, $repostSql)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Repost failed']);
}
?>
