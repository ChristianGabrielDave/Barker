<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
?>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../design/homeStyle.css">
        <script src="https://kit.fontawesome.com/2960bf0645.js" crossorigin="anonymous"></script>
    </head>
    <body>
        <div class="postBox">
            <form action="../handlers/postHandler.php" method="POST" enctype="multipart/form-data">
                <textarea name="text" id="text" wrap="hard" placeholder="What's new?" class="postText"></textarea>
                <input type="hidden" name="user_id" id="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                <input type="file" name="postImage" accept=".jpg, .png, .jpeg" class="postImage">
                <button type="submit" class="post-btn">Bark!</button>
            </form>
        </div>
        <div class="postContainer">
            <?php
                $postsql = "SELECT `id`, `content`, `media`, `uid`, `dop` FROM `posts` ORDER BY `dop` DESC;";
                $postresult = mysqli_query($conn, $postsql);

                if (mysqli_num_rows($postresult) > 0) {
                    while ($postrow = mysqli_fetch_assoc($postresult)) {
                        $post_id = $postrow['id'];
                        $user_id = $postrow['uid'];

                        $usersql = "SELECT `username`, `dp` FROM `users` WHERE `id` = $user_id;";
                        $userresult = mysqli_query($conn, $usersql);
                        $userrow = mysqli_fetch_assoc($userresult);

                        $likesql = "SELECT COUNT(*) as likes FROM `likes` WHERE `pid` = $post_id;";
                        $likeresult = mysqli_query($conn, $likesql);
                        $likerow = mysqli_fetch_assoc($likeresult);
                        $likecount = $likerow['likes'];

                        $commentsql = "SELECT COUNT(*) as comments FROM `comments` WHERE `pid` = $post_id;";
                        $commentresult = mysqli_query($conn, $commentsql);
                        $commentrow = mysqli_fetch_assoc($commentresult);
                        $commentcount = $commentrow["comments"];

                        echo '<div class="postDisplayBoxHead">
                                <ul>
                                    <li>
                                        <a href="account.php?username=' . $userrow['username'] . '" style="text-decoration: none;">
                                        <img src="' . ($userrow['dp'] ? '../uploads/profile_pictures' . $userrow['dp'] : 'https://api.dicebear.com/6.x/initials/png?seed=' . $userrow['username'] . '&size=128') . '" 
                                        alt="profile" class="account-profpic">
                                        </a>
                                    </li>
                                    <li style="padding-left: 10px; padding-right: 10px;">
                                        <a href="account.php?username=' . $userrow['username'] . '" style="text-decoration: none;">' . $userrow['username'] . '</a>
                                    </li>
                                    <li style="vertical-align:baseline;">
                                    <small>' . $postrow['dop'] . '</small>
                                    </li>
                                </ul>
                                </div>';
                        
                        if ($user_id == $_SESSION['user_id']) {
                            echo '<div class="more-options">
                                    <button class="more-btn">⋮</button>
                                        <div class="dropdown-content" style="display: none;">
                                            <button class="edit-btn" onclick="editPost(' . $post_id . ')"><i class="fa-solid fa-pen"></i> Edit</button>
                                            <button class="delete-btn" onclick="deletePost('. $post_id . ')"><i class="fa-solid fa-trash"></i> Delete</button>
                                        </div>
                                    </div>';
                        }

                        echo '<div class="postDisplayBoxMessage">
                            ' . nl2br(htmlspecialchars($postrow['content'])) . '
                                </div>';
                
                        if (!empty($postrow['media'])) {
                            echo '<div class="postDisplayBoxImage">
                                <a href="../uploads/' . $postrow['media'] . '" target="_blank">
                                    <img src="../uploads/' . $postrow['media'] . '" alt="' . $postrow['media'] . '" style="width: 100%; object-fit: contain; margin-bottom: 20px; border-radius: 5px;">
                                </a>
                                </div>';
                            }

                        echo '<div class="feed-post-actions">
                                <button class="like-btn" data-post-id="' . $post_id . '"><i class="fa-solid fa-heart"></i>(' . $likeCount . ')</button>
                                <button class="comment-btn" data-post-id="' . $post_id . '"><i class="fa-solid fa-comment"></i>(' . $commentCount . ')</button>
                                <button class="repost-btn" data-post-id="' . $post_id . '"><i class="fa-solid fa-share"></i>(' . $repostCount . ')</button>';

                        echo '</div></div>';
                    }
                } else {
                        echo '<p>No posts found</p>';
                    }
            ?>
        </div>
        <script src="../handlers/handlerScript.js"></script>
    </body>
</html>
