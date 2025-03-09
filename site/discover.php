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
    <script src="https://kit.fontawesome.com/2960bf0645.js" crossorigin="anonymous"></script>
    <style>
        modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
}

.modal-content {
    background-color: white;
    padding: 20px;
    border-radius: 10px;
    width: 50%;
    max-width: 500px;
    max-height: 80vh;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    position: relative;
}

.close {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 20px;
    cursor: pointer;
}

#commentModal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    visibility: hidden;
    opacity: 0;
    transition: opacity 0.2s ease-in-out, visibility 0.2s ease-in-out;
}

#commentModal.show {
    visibility: visible;
    opacity: 1;
}

#modal-comments {
    max-height: 50vh;
    overflow-y: auto;
    padding: 10px;
    border-top: 1px solid #ddd;
    background-color: #f9f9f9;
}
h1 {
        color: rgba(168, 147, 120);
        font-family: 'Franklin Gothic Medium';
        position: relative;
        font-size: 50px;
        font-style: bold;
        left: 50px;
       }

       .postDisplayHead {
        margin-top: 10px;
        background-color: rgba(168, 147, 120, 1);
        border: solid rgba(128, 128, 128, 0.356) 1px;
        border-radius: 5px;
        padding-left: 20px;
        padding-right: 20px;
        position: relative;
        left: 30px;
        width: 90%;
        color: rgba(255, 249, 235, 1);
       }

       .users {
        margin-top: 10px;
    background-color: rgba(168, 147, 120, 1);
    border: solid rgba(128, 128, 128, 0.356) 1px;
    border-radius: 5px;
    padding-left: 20px;
    padding-right: 20px;
    position: relative;
    left: 30px;
    width: 90%;
    color: rgba(255, 249, 235, 1);
       }

       a {
    color: rgba(255, 249, 235, 1);
    font-size: 25px;
}

.postDisplayBoxHead li{
    display: inline-block;
    vertical-align: middle;
    position: relative;
    left: -30px;
}
.postDisplayBoxHead img{
    height: 40px;
    border-radius: 360px;
}
.user-bio {
    position: relative;
    left: 10px;
}

.postDisplayBoxHead li{
    display: inline-block;
    vertical-align: middle;
    position: relative;
    left: -30px;
}
.postDisplayBoxHead img{
    height: 40px;
    border-radius: 360px;
}

.delete-btn {
    position: relative;
    top: 10px;
    left: 400px;
    border-radius: 10px;
    border: none;
    height: 45px;
    width: 75px;
    background-color: rgba(255, 249, 235, 1);
    color: rgba(168, 147, 120, 1);
    font-size: 16px;
    transition: 0.3s;
}

.delete-btn:hover {
    color: rgba(68, 49, 43, 1);
    cursor: pointer;
}

.feed-post-actions
{
    position: relative;
    bottom: 10px;
    left: 10px;
}

.like-btn {
    position: relative;
    border-radius: 10px;
    border: none;
    height: 45px;
    width: 75px;
    background-color: rgba(255, 249, 235, 1);
    color: rgba(168, 147, 120, 1);
    font-size: 16px;
    transition: 0.3s;
}

.like-btn:hover {
    color: rgba(68, 49, 43, 1);
    cursor: pointer;
}
.comment-btn {
    position: relative;
    border-radius: 10px;
    border: none;
    height: 45px;
    width: 75px;
    background-color: rgba(255, 249, 235, 1);
    color: rgba(168, 147, 120, 1);
    font-size: 16px;
    transition: 0.3s;
}

.comment-btn:hover {
    color: rgba(68, 49, 43, 1);
    cursor: pointer;
}
.repost-btn {
    position: relative;
    border-radius: 10px;
    border: none;
    height: 45px;
    width: 75px;
    background-color: rgba(255, 249, 235, 1);
    color: rgba(168, 147, 120, 1);
    font-size: 16px;
    transition: 0.3s;
}

.repost-btn:hover {
    color: rgba(68, 49, 43, 1);
    cursor: pointer;
}

.postDisplayBoxMessage{
    display: block;
    margin-top: 20px;
    margin-bottom: 20px;
    padding-right: 20px;
    padding-left: 20px;
    font-size: 17px;
    border-left: solid rgba(68, 49, 43, 1) 3px;
    position: relative;
    left: 10px;
}


.postDisplayBoxImage{
    padding-right: 60%;
    max-height: inherit;
    position: relative;
    left: 10px;
}

.postDisplayBox {
    margin-top: 10px;
    background-color: rgba(168, 147, 120, 1);
    border: solid rgba(128, 128, 128, 0.356) 1px;
    border-radius: 5px;
    padding-left: 20px;
    padding-right: 20px;
    position: relative;
    left: 30px;
    width: 90%;
    color: rgba(255, 249, 235, 1);
}

.searchBox {
    margin-top: 10px;
    background-color: rgba(168, 147, 120, 1);
    border: solid rgba(128, 128, 128, 0.356) 1px;
    border-radius: 5px;
    padding-left: 20px;
    padding-right: 20px;
    position: relative;
    left: 30px;
    width: 90%;
    height: auto;
    color: rgba(255, 249, 235, 1);
}

.searchBar {
    width: 90%;
    max-width: 800px;
    height: 20px;
    background: rgba(168, 147, 120, 1);
    border-radius: 15px;
    color: rgba(255, 249, 235, 1);
    border: none;
    font-size: 15px;
    padding: 10px;
    resize: none;
}

.search-btn {
    position: relative;
    border-radius: 10px;
    border: none;
    height: auto;
    width: auto;
    background: none;
    color: rgba(255, 249, 235, 1);
    font-size: 20px;
    transition: 0.3s;
    right: -20px;
}

.search-btn:hover {
    color: rgba(68, 49, 43, 1);
    cursor: pointer;
}
    </style>
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
                        echo ' <div class="users">
                        <div class="postDisplayBoxHead">
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
                        </div>
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

                        $stmtUser = $conn->prepare("SELECT `username`, `dp` FROM `users` WHERE `id` = ?");
                        $stmtUser->bind_param("i", $user_id);
                        $stmtUser->execute();
                        $userresult = $stmtUser->get_result();
                        $userrow = $userresult->fetch_assoc();

                        $likeResult = $conn->query("SELECT COUNT(*) AS like_count FROM likes WHERE pid = $post_id");
                        $likeCount = $likeResult->fetch_assoc()['like_count'];

                        $stmtComment = $conn->prepare("SELECT COUNT(*) as comments FROM `comments` WHERE `pid` = ?");
                        $stmtComment->bind_param("i", $post_id);
                        $stmtComment->execute();
                        $commentresult = $stmtComment->get_result();
                        $commentrow = $commentresult->fetch_assoc();
                        $commentCount = $commentrow["comments"];

                        echo '<div class="postDisplayBox">
                                <div  id="post-'. $post_id . '" class="postDisplayBoxHead">
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
                                    ';
                            
                            if ($user_id == $_SESSION['user_id']) {
                                echo '<li>
                                        <button class="delete-btn" onclick="deletePost('. $post_id . ')"><i class="fa-solid fa-trash"></i></button>
                                    </li>
                                    </ul>
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
                            echo "</div>
                            </div>";
                        }
                    }
                }
            ?>
        </div>
        <div id="commentModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <textarea id="commentText" placeholder="Write a comment..."></textarea>
                <button id="submitComment">Post Comment</button>
                <h2>Comments</h2>
            <div id="modal-comments"></div>
        </div>
    </div>
    <script src="../handlers/handlerScript.js"></script>
</body>
</html>
