<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['post_id'])) {
    $post_id = $_POST['post_id'];
    $text = mysqli_real_escape_string($conn, $_POST['text']);

    // Handle file upload (optional, if there's a new image)
    if (isset($_FILES['editPostImage']) && $_FILES['editPostImage']['error'] == 0) {
        $targetDir = "../uploads/";
        $fileName = basename($_FILES["editPostImage"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        // Check file type and size here (optional)
        if (move_uploaded_file($_FILES["editPostImage"]["tmp_name"], $targetFilePath)) {
            // Update post with new image
            $updateQuery = "UPDATE `posts` SET `content` = '$text', `media` = '$fileName' WHERE `id` = $post_id";
        }
    } else {
        // Update post without image
        $updateQuery = "UPDATE `posts` SET `content` = '$text' WHERE `id` = $post_id";
    }

    // Execute the update query
    if (mysqli_query($conn, $updateQuery)) {
        header("Location: home.php"); // Redirect to the home page after successful edit
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
