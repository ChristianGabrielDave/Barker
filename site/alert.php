<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    exit();
}

$current_user = $_SESSION['user_id'];

// Fetch notifications along with the username of the user who triggered them
$notifQuery = $conn->prepare("
    SELECT n.*, u.username AS from_username 
    FROM notifications n 
    JOIN users u ON n.from_user_id = u.id 
    WHERE n.user_id = ? 
    ORDER BY n.created_at DESC
");
$notifQuery->bind_param("i", $current_user);
$notifQuery->execute();
$notifResult = $notifQuery->get_result();
?>

<div class="notifications">
    <h2>Notifications</h2>
    <?php
    if ($notifResult->num_rows > 0) {
        while ($notification = $notifResult->fetch_assoc()) {
            $fromUser = htmlspecialchars($notification['from_username']);
            $message = htmlspecialchars($notification['message']);
            $timestamp = $notification['created_at'];

            echo "<div class='notification'>";
            echo "<p><strong>{$fromUser}</strong> {$message}</p>";
            echo "<small>{$timestamp}</small>";
            echo "</div>";
        }
    } else {
        echo "<p>No new notifications</p>";
    }
    ?>
</div>
