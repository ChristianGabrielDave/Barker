<?php
session_start();
include '../includes/config.php';

header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in."]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
if (isset($data['post_id'], $data['text']) && !empty($data['text'])) {
    $user_id = $_SESSION['user_id'];
    $post_id = intval($data['post_id']);
    $comment_text = mysqli_real_escape_string($conn, $data['text']);

    $query = "INSERT INTO comments (uid, pid, comment, doc) VALUES ('$user_id', '$post_id', '$comment_text', NOW())";
    if (mysqli_query($conn, $query)) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to add comment."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid data."]);
}
?>
