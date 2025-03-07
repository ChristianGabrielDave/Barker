<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if (isset($_POST['followed_id']) && isset($_POST['action'])) {
    $follower_id = $_SESSION['user_id'];
    $followed_id = $_POST['followed_id'];
    $action = $_POST['action'];

    if ($action == 'follow') {
        // Add the follow relationship
        $stmt = $conn->prepare("INSERT INTO followers (follower_id, followed_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $follower_id, $followed_id);
        $stmt->execute();

        $notif_stmt = $conn->prepare("
    INSERT INTO notifications (user_id, type, from_user_id, message) 
    VALUES (?, 'follow', ?, ?)
");
$message = "started following you.";
$notif_stmt->bind_param("iis", $followed_id, $follower_id, $message);
$notif_stmt->execute();

    } elseif ($action == 'unfollow') {
        // Remove the follow relationship
        $stmt = $conn->prepare("DELETE FROM followers WHERE follower_id = ? AND followed_id = ?");
        $stmt->bind_param("ii", $follower_id, $followed_id);
        $stmt->execute();
    }

    // Use the username from the POST data for the redirect
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    header("Location: ../site/account.php?username=" . $username);
    exit();
}
?>
