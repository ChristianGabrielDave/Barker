document.addEventListener("DOMContentLoaded", () => {
    // Like Post
    window.likePost = function(postId) {
        fetch("../handlers/likeHandler.php", {
            method: "POST",
            body: JSON.stringify({ post_id: postId }),
            headers: { "Content-Type": "application/json" }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById("like-count-" + postId).innerText = data.likes;
            }
        });
    };

    // Show Comment Box
    window.showCommentBox = function(postId) {
        let commentBox = document.getElementById("comment-box-" + postId);
        if (commentBox) {
            commentBox.style.display = "block";
        }
    };

    // Post Comment
    window.commentOnPost = function(postId) {
        let commentInput = document.getElementById("comment-input-" + postId).value;
        fetch("../handlers/commentHandler.php", {
            method: "POST",
            body: JSON.stringify({ post_id: postId, comment: commentInput }),
            headers: { "Content-Type": "application/json" }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById("comment-count-" + postId).innerText = data.comments;
                document.getElementById("comment-input-" + postId).value = "";
            }
        });
    };

    // Repost
    window.repostPost = function(postId) {
        fetch("../handlers/repostHandler.php", {
            method: "POST",
            body: JSON.stringify({ post_id: postId }),
            headers: { "Content-Type": "application/json" }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Post Reposted!");
            }
        });
    };

    // Edit Post
    window.editPost = function(postId) {
        let newText = prompt("Edit your post:");
        if (newText) {
            fetch("../handlers/editHandler.php", {
                method: "POST",
                body: JSON.stringify({ post_id: postId, content: newText }),
                headers: { "Content-Type": "application/json" }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }
    };

    // Delete Post
    window.deletePost = function(postId) {
        if (confirm("Are you sure you want to delete this post?")) {
            fetch("../handlers/deleteHandler.php", {
                method: "POST",
                body: JSON.stringify({ post_id: postId }),
                headers: { "Content-Type": "application/json" }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }
    };
});

document.querySelectorAll(".more-btn").forEach((button) => {
    button.addEventListener("click", function () {
    let dropdown = this.nextElementSibling;
    dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
    });
});