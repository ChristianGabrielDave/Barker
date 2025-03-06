<?php
    session_start();
    include '../includes/config.php';

    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login.php");
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
    <div class="searchBox">
        <form action="discover.php" method="GET">
            <input type="text" id="content" name="search" class="searchBar" placeholder="What are you looking for?">
            <button type="submit" class="search-btn"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>
    </div>

    <div class="userResult">
        <?php 
            if (isset($_GET['search']) && !empty($_GET['search'])) {
                $search = mysqli_real_escape_string($conn, $_GET['search']);

                $stmt = $conn->prepare("SELECT `username`, `id`, `dp`, `bio` FROM `users` WHERE `username` LIKE ?");
                $searchTerm = "%$search%";
                $stmt->bind_param("s", $searchTerm);
                $stmt->execute();
                $userresult = $stmt->get_result();

                if ($userresult->num_rows > 0) {
                    echo '<h1>Users</h1>';
                    while ($userrow = $userresult->fetch_assoc()) {
                        echo '<div class="postDisplayBoxHead">
                            <ul>
                                <li>
                                    <a href="account.php?username=' . htmlspecialchars($userrow['username']) . '" style="text-decoration: none;">
                                    <img src="' . ($userrow['dp'] ? '../uploads/profile_pictures/' . $userrow['dp'] : 'https://api.dicebear.com/6.x/initials/png?seed=' . htmlspecialchars($userrow['username']) . '&size=128') . '" 
                                    alt="profile" class="account-profpic">
                                    </a>
                                </li>
                                <li style="padding-left: 10px; padding-right: 10px;">
                                    <a href="account.php?username=' . htmlspecialchars($userrow['username']) . '" style="text-decoration: none;">' . htmlspecialchars($userrow['username']) . '</a>
                                </li>
                            </ul>
                            <div class="user-bio">' . htmlspecialchars($userrow['bio']) . '</div>
                        </div>';
                    }
                } else {
                    echo '<p>No users found</p>';
                }
            } 
        ?>                    
    </div>

    <div class="postResult">
        <?php
            if (isset($_GET['search']) && !empty($_GET['search'])) {
                $search = mysqli_real_escape_string($conn, $_GET['search']);

                $stmt = $conn->prepare("SELECT `id`, `content`, `media`, `uid`, `dop` FROM `posts` WHERE `content` LIKE ? ORDER BY `dop` DESC");
                $stmt->bind_param("s", $searchTerm);
                $stmt->execute();
                $postresult = $stmt->get_result();

                if ($postresult->num_rows > 0) {
                    echo '<h1>Posts</h1>';
                    while ($postrow = $postresult->fetch_assoc()) {
                        $post_id = $postrow['id'];
                        $user_id = $postrow['uid'];

                        // Get user data
                        $stmtUser = $conn->prepare("SELECT `username`, `dp` FROM `users` WHERE `id` = ?");
                        $stmtUser->bind_param("i", $user_id);
                        $stmtUser->execute();
                        $userresult = $stmtUser->get_result();
                        $userrow = $userresult->fetch_assoc();

                        // Get like count
                        $likeResult = $conn->query("SELECT COUNT(*) AS like_count FROM likes WHERE pid = $post_id");
                        $likeCount = $likeResult->fetch_assoc()['like_count'];

                        // Get comment count
                        $stmtComment = $conn->prepare("SELECT COUNT(*) as comments FROM `comments` WHERE `pid` = ?");
                        $stmtComment->bind_param("i", $post_id);
                        $stmtComment->execute();
                        $commentresult = $stmtComment->get_result();
                        $commentrow = $commentresult->fetch_assoc();
                        $commentcount = $commentrow["comments"];

                        echo '<div class="postDisplayBoxHead">
                                <ul>
                                    <li>
                                        <a href="account.php?username=' . htmlspecialchars($userrow['username']) . '" style="text-decoration: none;">
                                        <img src="' . ($userrow['dp'] ? '../uploads/profile_pictures/' . $userrow['dp'] : 'https://api.dicebear.com/6.x/initials/png?seed=' . htmlspecialchars($userrow['username']) . '&size=128') . '" 
                                        alt="profile" class="account-profpic">
                                        </a>
                                    </li>
                                    <li style="padding-left: 10px; padding-right: 10px;">
                                        <a href="account.php?username=' . htmlspecialchars($userrow['username']) . '" style="text-decoration: none;">' . htmlspecialchars($userrow['username']) . '</a>
                                    </li>
                                    <li style="vertical-align:baseline;">
                                    <small>' . htmlspecialchars($postrow['dop']) . '</small>
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
                                <a href="../uploads/' . htmlspecialchars($postrow['media']) . '" target="_blank">
                                    <img src="../uploads/' . htmlspecialchars($postrow['media']) . '" alt="Post Media" style="width: 100%; object-fit: contain; margin-bottom: 20px; border-radius: 5px;">
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
            }
        ?>
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
</body>
</html>
