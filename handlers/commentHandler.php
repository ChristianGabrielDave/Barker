<?php
session_start();
include '../includes/config.php';

header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in."]);
    exit();
}

$current_user = $_SESSION['user_id'];

$data = json_decode(file_get_contents("php://input"), true);
if (isset($data['post_id'], $data['text']) && !empty($data['text'])) {
    $user_id = $_SESSION['user_id'];
    $post_id = intval($data['post_id']);
    $comment_text = mysqli_real_escape_string($conn, $data['text']);

    $query = "INSERT INTO comments (uid, pid, comment, doc) VALUES ('$user_id', '$post_id', '$comment_text', NOW())";
    if (mysqli_query($conn, $query)) {
        echo json_encode(["success" => true]);
        $postQuery = $conn->prepare("SELECT uid FROM posts WHERE id = ?");
$postQuery->bind_param("i", $post_id);
$postQuery->execute();
$postResult = $postQuery->get_result();
$postData = $postResult->fetch_assoc();
$postOwner = $postData['uid'];
if ($postOwner != $current_user) {
    // Insert notification for the post owner
    $notif_stmt = $conn->prepare("
    INSERT INTO notifications (user_id, type, from_user_id, post_id, message) 
    VALUES (?, 'comment', ?, ?, ?)
");
$message = "commented on your post.";
$notif_stmt->bind_param("iiis", $postOwner, $current_user, $post_id, $message);
$notif_stmt->execute();

}
    } else {
        echo json_encode(["success" => false, "message" => "Failed to add comment."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid data."]);
}
?>
