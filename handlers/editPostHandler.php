<?php
include '../includes/config.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_POST['post_id']) || !isset($_POST['content'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$post_id = $_POST['post_id'];
$new_content = mysqli_real_escape_string($conn, $_POST['content']);
$user_id = $_SESSION['user_id'];

$postCheckQuery = "SELECT * FROM posts WHERE id = $post_id AND uid = $user_id";
$postCheckResult = mysqli_query($conn, $postCheckQuery);

if (mysqli_num_rows($postCheckResult) > 0) {
    $updateQuery = "UPDATE posts SET content = '$new_content' WHERE id = $post_id";
    if (mysqli_query($conn, $updateQuery)) {
        echo json_encode(['success' => true, 'message' => 'Post updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update post']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Post not found or not authorized']);
}
?>
