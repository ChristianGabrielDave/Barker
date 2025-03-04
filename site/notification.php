<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Barker</title>
        <link rel="shortcut icon" type="x-icon" href="../assets/logo.png">
        <link rel="stylesheet" href="../design/mainStyle.css">
        <script src="https://kit.fontawesome.com/2960bf0645.js" crossorigin="anonymous"></script>
        <style>
            body {
                background: url('../assets/background.png') no-repeat center center fixed;
                background-size: cover;
            }
            a {
                text-decoration: none;
                color: inherit;
                cursor: pointer;
            }
            a:hover {
                color: inherit;
                background: none;
            }
            .container {
                max-width: 60%;
                margin: auto;
                height: 90vh;
                height: auto;
                height: 550px;
            
            }
            iframe {
                height: 1000px;
                width: 100%;
                position: relative;
                border: none;
            }
        </style>
    </head>
    <body>
        <div class="side">
            <a href="main.php"><img src="../assets/sideLogo.png" class="logo"></a>
        </div>
        <div class="container">
        <div class="tab">
                <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i></a>
                <a href="profile.php"><i class="fa-solid fa-user"></i></a>
                <a href="main.php"><i class="fa-solid fa-house"></i></a>
                <a href="notification.php"><i class="fa-solid fa-bell"></i></a>
                <a href="search.php"><i class="fa-solid fa-magnifying-glass"></i></a>
            </div>
            <iframe iframe src="#"></iframe>
        </div>
    </body>
</html>