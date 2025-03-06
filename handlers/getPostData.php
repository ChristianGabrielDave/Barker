<?php
session_start();
include '../includes/config.php';

$data = json_decode(file_get_contents('php://input'), true);
$post_id = $data['post_id'];

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$sql = "SELECT `content` FROM `posts` WHERE `id` = ? AND `uid` = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $post_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $post = $result->fetch_assoc();
    echo json_encode(['success' => true, 'post_content' => $post['content']]);
} else {
    echo json_encode(['success' => false, 'message' => 'Post not found']);
}

$stmt->close();
?>
