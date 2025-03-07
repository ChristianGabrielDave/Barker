<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$post_id = intval($_POST['post_id']);
$current_user = $_SESSION['user_id'];

// Check if the post exists
$postCheckSql = "SELECT uid FROM posts WHERE id = ?";
$postCheckStmt = $conn->prepare($postCheckSql);
$postCheckStmt->bind_param("i", $post_id);
$postCheckStmt->execute();
$postCheckResult = $postCheckStmt->get_result();

if ($postCheckResult->num_rows == 0) {
    echo json_encode(['success' => false, 'message' => 'Post not found']);
    exit();
}

$postData = $postCheckResult->fetch_assoc();
$originalPostOwner = $postData['uid'];

// Insert repost
$repostSql = "INSERT INTO posts (uid, content, media, dop, repost) VALUES (?, '', '', NOW(), ?)";
$repostStmt = $conn->prepare($repostSql);
$repostStmt->bind_param("ii", $user_id, $post_id);

if ($repostStmt->execute()) {
    echo json_encode(['success' => true]);

    // Prevent self-notification
    if ($originalPostOwner != $current_user) {
        // Insert notification for the original post owner
        $notif_stmt = $conn->prepare("
            INSERT INTO notifications (user_id, type, from_user_id, post_id, message) 
            VALUES (?, 'repost', ?, ?, ?)
        ");
        $message = "reposted your post.";
        $notif_stmt->bind_param("iiis", $originalPostOwner, $current_user, $post_id, $message);
        $notif_stmt->execute();
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Repost failed']);
}
?>
