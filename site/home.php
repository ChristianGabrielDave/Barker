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
                $followedUsersSql = "SELECT followed_id FROM followers WHERE follower_id = ?";
                $stmt = $conn->prepare($followedUsersSql);
                $stmt->bind_param("i", $_SESSION['user_id']);
                $stmt->execute();
                $followedUsersResult = $stmt->get_result();

                $followedUsers = [$_SESSION['user_id']];
                while ($row = $followedUsersResult->fetch_assoc()) {
                    $followedUsers[] = $row['followed_id'];
                }


                // Check if the user follows anyone
                if (count($followedUsers) > 0) {
                // Display posts from followed users only
                    $followedUserIds = implode(",", $followedUsers);
                    $postsql = "SELECT id, content, media, uid, dop, repost FROM posts WHERE uid IN ($followedUserIds) ORDER BY dop DESC";
                    $postresult = mysqli_query($conn, $postsql);

                if (mysqli_num_rows($postresult) > 0) {
                    while ($postrow = mysqli_fetch_assoc($postresult)) {
                        $post_id = $postrow['id'];
                        $user_id = $postrow['uid'];

                        $usersql = "SELECT `username`, `dp` FROM `users` WHERE `id` = $user_id;";
                        $userresult = mysqli_query($conn, $usersql);
                        $userrow = mysqli_fetch_assoc($userresult);

                        $likeResult = $conn->query("SELECT COUNT(*) AS like_count FROM likes WHERE pid = $post_id;");
                        $likeCount = $likeResult->fetch_assoc()['like_count'];

                        $commentResult = $conn->query("SELECT * FROM comments WHERE pid = $post_id ORDER BY doc DESC LIMIT 2;");
                        $commentCResult = $conn->query("SELECT COUNT(*) AS comment_count FROM comments WHERE pid = $post_id;");
                        $comments = [];
                        while ($commentRow = $commentResult->fetch_assoc()) {
                            $comments[] = $commentRow;
                        }
                        $commentCount = $commentCResult->fetch_assoc()['comment_count'];

                        if (isset($postrow['repost']) && !empty($postrow['repost'])) { 
                            $originalPostSql = "SELECT `id`, `content`, `media`, `uid`, `dop` FROM `posts` WHERE `id` = " . intval($postrow['repost']);
                            $originalPostResult = mysqli_query($conn, $originalPostSql);
                        
                            if ($originalPost = mysqli_fetch_assoc($originalPostResult)) {
                                $originalUserSql = "SELECT `username`, `dp` FROM `users` WHERE `id` = " . intval($originalPost['uid']);
                                $originalUserResult = mysqli_query($conn, $originalUserSql);
                                $originalUser = mysqli_fetch_assoc($originalUserResult);
                        
                                echo '<div class="repost-container">
                                        <ul>
                                            <li>  
                                                <a href="account.php?username=' . $userrow['username'] . '" style="text-decoration: none;">
                                                <img src="' . ($userrow['dp'] ? '../uploads/profile_pictures/' . $userrow['dp'] : 'https://api.dicebear.com/6.x/initials/png?seed=' . $userrow['username'] . '&size=128') . '" 
                                                alt="profile" class="account-profpic">
                                                </a>
                                            </li>
                                            <li style="padding-left: 10px; padding-right: 10px;">
                                                <a href="account.php?username=' . $userrow['username'] . '" style="text-decoration: none;">' . $userrow['username'] . '</a>
                                                <p> reposted <a href="account.php?username=' . $originalUser['username'] .'" style="text-decoration: none;">' . $originalUser['username'] . '</a>\'s post.</p>
                                            </li>
                                        </ul>';
                                echo '<div class="postDisplayBoxHead">
                                        <ul>
                                            <li>
                                                <a href="account.php?username=' . $originalUser['username'] . '" style="text-decoration: none;">
                                                    <img src="' . (!empty($originalUser['dp']) ? '../uploads/profile_pictures/' . $originalUser['dp'] : 'https://api.dicebear.com/6.x/initials/png?seed=' . $originalUser['username'] . '&size=128') . '" 
                                                    alt="profile" class="account-profpic">
                                                </a>
                                            </li>
                                            <li style="padding-left: 10px; padding-right: 10px;">
                                                <a href="account.php?username=' . $originalUser['username'] . '" style="text-decoration: none;">' . $originalUser['username'] . '</a>
                                            </li>
                                            <li style="vertical-align:baseline;">
                                                <small>' . $originalPost['dop'] . '</small>
                                            </li>
                                        </ul>
                                      </div>';
                                
                                echo '<div class="repost-box">' . nl2br(htmlspecialchars($originalPost['content'])) . '</div>';
                        
                                if (!empty($originalPost['media'])) {
                                    echo '<div class="postDisplayBoxImage">
                                            <a href="../uploads/' . $originalPost['media'] . '" target="_blank">
                                                <img src="../uploads/' . $originalPost['media'] . '" alt="' . $originalPost['media'] . '" style="width: 100%; object-fit: contain; margin-bottom: 20px; border-radius: 5px;">
                                            </a>
                                        </div>';
                                }
                                echo '<div class="feed-post-actions">
                                        <button class="like-btn" data-post-id="' . $post_id . '" onclick="likePost(' . $post_id . ')"><i class="fa-solid fa-heart"></i> (<span id="like-count-' . $post_id . '">' . $likeCount . '</span>)</button>
                                        <button class="comment-btn" data-post-id="' . $post_id . '" onclick="showCommentBox(' . $post_id . ')"><i class="fa-solid fa-comment"></i> (<span id="comment-count-' . $post_id . '">' . $commentCount . '</span>)</button>';
                                        
                                if ($user_id == $_SESSION['user_id']) {        
                                        echo '<button class="delete-btn" onclick="deletePost('. $post_id . ')"><i class="fa-solid fa-trash"></i></button>
                                        </div>';
                                        echo '</div>';
                                }
                            }
                        } else {
                            echo '<div  id="post-'. $post_id . '" class="postDisplayBoxHead">
                                    <ul>
                                        <li>
                                            <a href="account.php?username=' . $userrow['username'] . '" style="text-decoration: none;">
                                            <img src="' . ($userrow['dp'] ? '../uploads/profile_pictures/' . $userrow['dp'] : 'https://api.dicebear.com/6.x/initials/png?seed=' . $userrow['username'] . '&size=128') . '" 
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
                                                <button class="edit-btn" onclick="openEditModal(' . $post_id . ', \'' . htmlspecialchars($postrow['content'], ENT_QUOTES, 'UTF-8') . '\', \'' . $postrow['media'] . '\')" data-post-id="' . $post_id . '" data-post-content="' . htmlspecialchars($postrow['content'], ENT_QUOTES, 'UTF-8') . '" data-post-media="' . $postrow['media'] . '"><i class="fa-solid fa-pen"></i> Edit</button>
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
                                <button class="like-btn" data-post-id="' . $post_id . '" onclick="likePost(' . $post_id . ')"><i class="fa-solid fa-heart"></i> <span id="like-count-' . $post_id . '">' . $likeCount . '</span></button>
                                <button class="comment-btn" data-post-id="' . $post_id . '" onclick="showCommentBox(' . $post_id . ')"><i class="fa-solid fa-comment"></i> <span id="comment-count-' . $post_id . '">' . $commentCount . '</span></button>
                                <button class="repost-btn" data-post-id="' . $post_id . '" onclick="repostPost(' . $post_id . ')"><i class="fa-solid fa-share"></i></button>
                            </div>';
                            echo "</div>";
                        }
                    }
                }  else {
                    echo '<p>No posts from followed users</p>';
                }
                } else {
                    echo '<p>You are not following anyone yet</p>';
                }
            ?>
        </div>
            <div id="commentModal" class="modal">
                <div class="modal-content">
                <span class="close">&times;</span>
                <textarea id="commentText" placeholder="Write a comment..."></textarea>
                <button id="submitComment">Post Comment</button>
                <h2>Comments</h2>
                <div id="modal-comments"></div> <!-- Scrollable Comment Section mewthhed lolis-->
            </div>
        </div>
        <div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <form id="editPostForm">
            <textarea id="editPostText" name="content" placeholder="Edit your post..."></textarea>
            <input type="hidden" id="editPostId" name="post_id">
            <div id="editPostImage"></div>
            <button type="submit">Save Changes</button>
        </form>
    </div>
</div>

        <script src="../handlers/handlerScript.js"></script>
    </body>
</html>
