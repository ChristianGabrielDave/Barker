<?php 
include '../includes/config.php';

$sql = "SELECT * FROM post ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../design/homeStyle.css">
    </head>
    <body>
        <form action="post_handler.php" method="POST" enctype="multipart/form-data">
            <div class="postBox">
                <textarea name="content" placeholder="What's new?"></textarea>
                <input type="file" name="media">
                <button type="submit">Bark</button>
            </div>
        </form>
        <div id="posts">
        <?php while ($row = $result->fetch_assoc()) : ?>
            <div class="post">
                <p><?= htmlspecialchars($row['content']) ?></p>
                
                <?php if ($row['type'] == 'image') : ?>
                    <img src="uploads/<?= $row['media'] ?>" width="200">
                <?php elseif ($row['type'] == 'video') : ?>
                    <video width="200" controls>
                        <source src="uploads/<?= htmlspecialchars($row['media']) ?>" type="video/mp4">
                    </video>
                <?php endif; ?>

                <p>Likes: <span id="likes-<?= $row['id'] ?>"><?= $row['likes'] ?></span></p>
                <button onclick="likePost(<?= $row['id'] ?>)">Like</button>

                <!-- Comment Form -->
                <form action="comment_handler.php" method="POST">
                    <input type="hidden" name="post_id" value="<?= $row['id'] ?>">
                    <input type="text" name="comment" placeholder="Add a comment">
                    <button type="submit">Comment</button>
                </form>

                <!-- Display Comments -->
                <div class="comments">
                    <h4>Comments</h4>
                    <?php
                    $post_id = $row['id'];
                    $comment_sql = "SELECT * FROM comments WHERE post_id = $post_id ORDER BY created_at ASC";
                    $comment_result = $conn->query($comment_sql);

                    while ($comment = $comment_result->fetch_assoc()) :
                    ?>
                        <p><strong><?= htmlspecialchars($comment['comment']) ?></strong> <small>(<?= $comment['created_at'] ?>)</small></p>
                    <?php endwhile; ?>
                </div>

                <a href="edit_handler.php?id=<?= $row['id'] ?>">Edit</a> | 
                <a href="delete_handler.php?id=<?= $row['id'] ?>">Delete</a>
            </div>
        <?php endwhile; ?>
        </div>
        <script>
            function likePost(postId, button) {
            fetch("like_handler.php", {
                method: "POST",
                body: new URLSearchParams({ post_id: postId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let likeSpan = document.getElementById("likes-" + postId);
                    likeSpan.textContent = parseInt(likeSpan.textContent) + 1;
                    button.disabled = true; // Disable button after liking
                    button.textContent = "Liked";
                } else {
                    alert(data.message); // Show alert if already liked
                }
            });
        }
        </script>
    </body>
</html>

