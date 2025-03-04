<?php
require 'includes/config.php';
session_start();
$message = '';

if (isset($_POST['login'])) {
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];


        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();


            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                header("Location: site/main.php");
                exit();
            } else {
                $message = 'Invalid username or password.';
            }
        } else {
            $message = 'User not found.';
        }
        $stmt->close();
    } else {
        $message = 'Please fill in all fields.';
    }
}
if (isset($_POST['register'])) {
    if (!empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['email'])) {
        $username = $_POST['username'];
        $passwordHash = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $email = $_POST['email'];

        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $passwordHash);

        if ($stmt->execute()) {
            $message = 'Registration successful! You can now log in.';
            header("Location: login.php");
            exit();
        } else {
            $message = 'Registration failed. Please try again.';
        }
        $stmt->close();
    } else {
        $message = 'Please fill in all fields.';
    }
}
echo $message;
?>
