<?php
    session_start();
    include '../includes/config.php';

    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login.php");
        exit();
    }

    $conn = new mysqli("localhost", "root", "", "barker");

    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    if (isset($_GET['username']) && !empty($_GET['username'])) { 
        $username = $_GET['username'];
        $sql = "SELECT `id`, `username`, `dp`, `bp`, `bio` FROM `users` WHERE `username` = ?";
    } else {
        $user_id = $_SESSION['user_id'];
        $sql = "SELECT `id`, `username`, `dp`, `bp`, `bio` FROM `users` WHERE `id` = ?";
    }

    $stmt = $conn->prepare($sql);
    if (isset($username)) {
        $stmt->bind_param("s", $username);
    } else {
        $stmt->bind_param("i", $user_id);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $user_id = $row['id'];
        $username = $row['username'];
        $dp = $row['dp'];
        $bp = $row['bp'];
        $bio = $row['bio'];
    } else {
        echo "User not found!";
        exit();
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../design/homeStyle.css">
        <script src="https://kit.fontawesome.com/2960bf0645.js" crossorigin="anonymous"></script>
    </head>
    <style>
    .Profilemodal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background-color: #fff;
        padding: 20px;
        border-radius: 10px;
        width: 400px;
        position: relative;
    }

    .close {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 20px;
        cursor: pointer;
    }

    .save-btn {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 10px;
        width: 100%;
        cursor: pointer;
        margin-top: 10px;
        border-radius: 5px;
    }

    .save-btn:hover {
        background-color: #0056b3;
    }
</style>
    <body>
        <div class="account">
            <div class="account-body">
            <div class="account-banner" style="background-image: url('<?php echo $bp ? "../uploads/background_pictures/" . $bp : "../assets/background.png"; ?>');">
                    <div class="account-img">
                        <ul>
                            <li>
                                <img src="<?php echo ($dp ? '../uploads/profile_pictures/' . $dp : 'https://api.dicebear.com/6.x/initials/png?seed=' . urlencode($username) . '&size=128'); ?>" 
                                alt="profile" class="account-profpic">
                            </li>
                            <li>
                                <b><?php echo htmlspecialchars($username); ?></b>
                            </li>
                        </ul>
                        <?php if ($user_id == $_SESSION['user_id']) : ?>
                            <button class="edit-btn" onclick="openEditProfileModal()">
                                <i class="fa-solid fa-pen"></i> Edit
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
// Check if the logged-in user follows the current profile
$followStatus = false;
if ($_SESSION['user_id'] != $user_id) {
    $checkFollowSql = "SELECT * FROM followers WHERE follower_id = ? AND followed_id = ?";
    $stmt = $conn->prepare($checkFollowSql);
    $stmt->bind_param("ii", $_SESSION['user_id'], $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $followStatus = true;
    }
}
?>
<!-- Follow Button -->
<?php if ($_SESSION['user_id'] != $user_id): ?>
    <form action="../handlers/followHandler.php" method="POST">
    <input type="hidden" name="followed_id" value="<?php echo $user_id; ?>">
    <input type="hidden" name="username" value="<?php echo htmlspecialchars($username); ?>">
    <?php if ($followStatus): ?>
        <button type="submit" name="action" value="unfollow">Unfollow</button>
    <?php else: ?>
        <button type="submit" name="action" value="follow">Follow</button>
    <?php endif; ?>
</form>
<?php endif; ?>
                <div class="account-bio">
                    <?php echo nl2br(htmlspecialchars($bio)); ?>
                </div>
                <div id="editProfileModal" class="Profilemodal">
                <div class="modal-content">
                    <span class="close" onclick="closeEditProfileModal()">&times;</span>
                    <h2>Edit Profile</h2>
                    <form action="../handlers/editProfileHandler.php" method="POST" enctype="multipart/form-data">
                        <label for="bio">Bio:</label>
                        <textarea name="bio" id="bio" placeholder="Update your bio..."><?php echo htmlspecialchars($bio); ?></textarea>
                        
                        <label for="profile_picture">Profile Picture:</label>
                        <input type="file" name="profile_picture" id="profile_picture" accept=".jpg, .jpeg, .png">
                        
                        <label for="background_picture">Background Picture:</label>
                        <input type="file" name="background_picture" id="background_picture" accept=".jpg, .jpeg, .png">
                        
                        <button type="submit" class="save-btn">Save Changes</button>
                        </form>
                    </div>
                </div>
                <div class="acc-feed">
                    <?php if ($user_id == $_SESSION['user_id']) : ?>
                        <div class="postBox">
                            <form action="../handlers/profilepostHandler.php" method="POST" enctype="multipart/form-data">
                                <textarea name="text" id="text" wrap="hard" placeholder="What's new?" class="postText"></textarea>
                                <input type="hidden" name="user_id" id="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                                <input type="file" name="postImage" accept=".jpg, .png, .jpeg" class="postImage">
                                <button type="submit" class="post-btn">Bark!</button>
                            </form>
                        </div>
                    <?php endif; ?>
                    <?php
                        $postsql = "SELECT `id`, `content`, `media`, `uid`, `dop`, `repost` FROM `posts`WHERE `uid` = " . $user_id . " ORDER BY `dop` DESC;";
                        $postresult = mysqli_query($conn, $postsql);

                        if (mysqli_num_rows($postresult) > 0) {
                            while ($postrow = mysqli_fetch_assoc($postresult)) {
                                $post_id = $postrow['id'];
                                
        
                                $usersql = "SELECT `username`, `dp` FROM `users` WHERE `id` = $user_id;";
                                $userresult = mysqli_query($conn, $usersql);
                                $userrow = mysqli_fetch_assoc($userresult);
        
                                $likeResult = $conn->query("SELECT COUNT(*) AS like_count FROM likes WHERE pid = $post_id");
                                $likeCount = $likeResult->fetch_assoc()['like_count'];
        
                                $commentsql = "SELECT COUNT(*) as comments FROM `comments` WHERE `pid` = $post_id;";
                                $commentresult = mysqli_query($conn, $commentsql);
                                $commentrow = mysqli_fetch_assoc($commentresult);
                                $commentCount = $commentrow["comments"];

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
                                } echo '<div class="postDisplayBoxHead">
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
                                        <button class="like-btn" data-post-id="' . $post_id . '" onclick="likePost(' . $post_id . ')"><i class="fa-solid fa-heart"></i> (<span id="like-count-' . $post_id . '">' . $likeCount . '</span>)</button>
                                        <button class="comment-btn" data-post-id="' . $post_id . '" onclick="showCommentBox(' . $post_id . ')"><i class="fa-solid fa-comment"></i> (<span id="comment-count-' . $post_id . '">' . $commentCount . '</span>)</button>
                                        <button class="repost-btn" data-post-id="' . $post_id . '" onclick="repostPost(' . $post_id . ')"><i class="fa-solid fa-share"></i></button>
                            </div>';
                            }
                        } else {
                                echo '<p>No posts found</p>';
                        }
                    ?>
                </div>
            </div>
        </div>
        </div>
            <div id="commentModal" class="modal">
                <div class="modal-content">
                <span class="close">&times;</span>
                <textarea id="commentText" placeholder="Write a comment..."></textarea>
                <button id="submitComment">Post Comment</button>
                <h2>Comments</h2>
                <div id="modal-comments"></div> <!-- Scrollable Comment Section -->
            </div>
        </div>
        <script src="../handlers/handlerScript.js"></script>
        <script>
            document.getElementById("followBtn").addEventListener("click", function() {
    let followedId = this.getAttribute("data-followed-id");
    // Determine the action based on current state (this example assumes "follow")
    let action = "follow"; // Change to "unfollow" if already following

    fetch("../handlers/followHandler.php", {
        method: "POST",
        body: new URLSearchParams({ followed_id: followedId, action: action }),
        headers: { "Content-Type": "application/x-www-form-urlencoded" }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log("Follow action successful:", action);
            // Optionally update button text or state here
        } else {
            alert("Error: " + data.error);
        }
    })
    .catch(error => console.error("Error:", error));
});
        </script>
    </body>
</html>
