<?php
include '../includes/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $content = $_POST['text'];
    $user_id = $_POST['user_id'];

    if($_FILES['postImage']['error'] != 4){
        $imagename = $_FILES['postImage']['name'];
        $imagetmpname = $_FILES['postImage']['tmp_name'];
    
        $imagename = explode(".", $imagename);
        $imageext = strtolower(end($imagename));
        $imagename = uniqid().".".$imageext;
    
        $folder = "../uploads/". $imagename;
        move_uploaded_file($imagetmpname, $folder);
    }

    if(isset($imagename)){
        $sql = "INSERT INTO `posts` (`uid`, `content`, `media`, `dop`) VALUES (?, ?, '$imagename', current_timestamp());";
    }else{
        $sql = "INSERT INTO `posts` (`uid`, `content`, `dop`) VALUES (?, ?, current_timestamp());";
    }

    $sql = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($sql,"is", $user_id, $content);
    $sql->execute();
    header("Location: ../site/home.php");
}