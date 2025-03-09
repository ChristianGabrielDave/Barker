<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$bio = isset($_POST['bio']) ? $_POST['bio'] : '';

if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
    $profilePicName = $_FILES['profile_picture']['name'];
    $profilePicTemp = $_FILES['profile_picture']['tmp_name'];
    $profilePicExt = pathinfo($profilePicName, PATHINFO_EXTENSION);
    $profilePicNewName = uniqid('dp_') . '.' . $profilePicExt;
    $profilePicPath = "../uploads/profile_pictures/" . $profilePicNewName;

    if (move_uploaded_file($profilePicTemp, $profilePicPath)) {
        $profilePic = $profilePicNewName;
    } else {
        $profilePic = null;
    }
} else {
    $profilePic = null;
}

if (isset($_FILES['background_picture']) && $_FILES['background_picture']['error'] == 0) {
    $backgroundPicName = $_FILES['background_picture']['name'];
    $backgroundPicTemp = $_FILES['background_picture']['tmp_name'];
    $backgroundPicExt = pathinfo($backgroundPicName, PATHINFO_EXTENSION);
    $backgroundPicNewName = uniqid('bp_') . '.' . $backgroundPicExt;
    $backgroundPicPath = "../uploads/background_pictures/" . $backgroundPicNewName;

    if (move_uploaded_file($backgroundPicTemp, $backgroundPicPath)) {
        $backgroundPic = $backgroundPicNewName;
    } else {
        $backgroundPic = null;
    }
} else {
    $backgroundPic = null;
}
$sql = "UPDATE `users` SET `bio` = ?, `dp` = ?, `bp` = ? WHERE `id` = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssi", $bio, $profilePic, $backgroundPic, $user_id);

if ($stmt->execute()) {
    header("Location: ../site/account.php");
    exit();
} else {
    echo "Error updating profile.";
}
?>
