<?php
session_start();
include '../includes/config.php';

$data = json_decode(file_get_contents("php://input"), true);
$post_id = $data['post_id'];

$postSql = "SELECT * FROM posts WHERE id = ? AND uid = ?";
$stmt = $conn->prepare($postSql);
$stmt->bind_param("ii", $post_id, $_SESSION['user_id']);
$stmt->execute();
$postResult = $stmt->get_result();

if ($postResult->num_rows > 0) {
    $deleteSql = "DELETE FROM posts WHERE id = ?";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("i", $post_id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete post.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Post not found or you are not authorized.']);
}
?>
