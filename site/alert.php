<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    exit();
}

$current_user = $_SESSION['user_id'];

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
<!DOCTYPE html>
<html lang="en">
    <head>
    <style>
       h1 {
        color: rgba(168, 147, 120);
        font-family: 'Franklin Gothic Medium';
        position: relative;
        font-size: 50px;
        font-style: bold;
        left: 50px;
       }

       .notification {
        margin-top: 10px;
        background-color: rgba(168, 147, 120, 1);
        border: solid rgba(128, 128, 128, 0.356) 1px;
        border-radius: 5px;
        padding-left: 20px;
        padding-right: 20px;
        position: relative;
        left: 30px;
        width: 90%;
        color: rgba(255, 249, 235, 1);
       }

       small {
        position: relative;
        bottom: 10px;
       }

    </style>
    </head>
    <body>
        <div class="notifications">
            <h1>Notifications</h1>
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
    </body>
</html>
