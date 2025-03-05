<?php
    session_start();
    include '../includes/config.php';

    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login.php");
        exit();
    }

    $connection = new mysqli("localhost", "root", "", "barker");

    if ($connection->connect_error) {
        die("Database connection failed: " . $connection->connect_error);
    }

    if (isset($_GET['username']) && !empty($_GET['username'])) { 
        $username = $_GET['username'];
        $sql = "SELECT `id`, `username`, `dp`, `bp`, `bio` FROM `users` WHERE `username` = ?";
    } else {
        $user_id = $_SESSION['user_id'];
        $sql = "SELECT `id`, `username`, `dp`, `bp`, `bio` FROM `users` WHERE `id` = ?";
    }

    $stmt = $connection->prepare($sql);
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
                        <?php
                        if ($user_id == $_SESSION['user_id']) {
                                echo '<button class="edit-btn"><i class="fa-solid fa-pen"></i> Edit</button>';
                            }
                        ?>
                    </div>
                </div>
                <div class="account-bio">
                    <?php echo nl2br(htmlspecialchars($bio)); ?>
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
                        $postsql = "SELECT `content`, `media`, `id`, `dop` FROM `posts` WHERE `uid` = " . $user_id . " ORDER BY `dop` DESC;";
                        $postresult = mysqli_query($connection, $postsql);

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
                                        <button class="like-btn" data-post-id="' . $post_id . '" onclick="likePost(' . $post_id . ')"><i class="fa-solid fa-heart"></i> (<span id="like-count-' . $post_id . '">' . $likeCount . '</span>)</button>
                                        <button class="comment-btn" data-post-id="' . $post_id . '" onclick="showCommentBox(' . $post_id . ')"><i class="fa-solid fa-comment"></i> (<span id="comment-count-' . $post_id . '">' . $commentcount . '</span>)</button>
                                        <button class="repost-btn" data-post-id="' . $post_id . '" onclick="repostPost(' . $post_id . ')"><i class="fa-solid fa-share"></i> (0)</button>
                            </div>';
                            }
                        } else {
                                echo '<p>No posts found</p>';
                        }
                    ?>
                </div>
            </div>
        </div>
        <script src="../handlers/handlerScript.js"></script>
    </body>
</html>
