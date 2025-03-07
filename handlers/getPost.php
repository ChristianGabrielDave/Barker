<?php
include '../includes/config.php';
session_start();

if (!isset($_GET['post_id'])) {
    echo json_encode(['success' => false, 'message' => 'Post ID is missing']);
    exit();
}

$post_id = $_GET['post_id'];
$postQuery = "SELECT * FROM posts WHERE id = $post_id";
$postResult = mysqli_query($conn, $postQuery);

if ($postRow = mysqli_fetch_assoc($postResult)) {
    echo json_encode(['success' => true, 'content' => $postRow['content']]);
} else {
    echo json_encode(['success' => false, 'message' => 'Post not found']);
}
?>
