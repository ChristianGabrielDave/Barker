document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".more-btn").forEach(button => {
        button.addEventListener("click", function (event) {
            event.stopPropagation()
            const dropdown = this.nextElementSibling;

            document.querySelectorAll(".dropdown-content").forEach(dropdownMenu => {
                if (dropdownMenu !== dropdown) {
                    dropdownMenu.style.display = "none";
                }
            });

            dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
        });
    });

    document.addEventListener("click", () => {
        document.querySelectorAll(".dropdown-content").forEach(dropdown => {
            dropdown.style.display = "none";
        });
    });

    window.likePost = function (postId) {
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
        })
        .catch(error => console.error("Error liking post:", error));
    };

    let commentModal = document.getElementById("commentModal");
    let closeBtn = document.querySelector(".close");
    let commentText = document.getElementById("commentText");
    let submitComment = document.getElementById("submitComment");
    let modalCommentsDiv = document.getElementById("modal-comments");
    let currentPostId = null;

    commentModal.classList.remove("show");

    document.querySelectorAll(".comment-btn").forEach(button => {
        button.addEventListener("click", function () {
            currentPostId = this.getAttribute("data-post-id");
            commentText.value = "";
            modalCommentsDiv.innerHTML = "<p>Loading comments...</p>";
            commentModal.classList.add("show");
            loadAllComments(currentPostId);
        });
    });

    closeBtn.addEventListener("click", closeModal);
    window.addEventListener("click", event => {
        if (event.target === commentModal) closeModal();
    });

    function closeModal() {
        commentModal.classList.remove("show");
    }

    submitComment.addEventListener("click", () => {
        if (commentText.value.trim() === "") {
            alert("Comment cannot be empty.");
            return;
        }

        fetch("../handlers/commentHandler.php", {
            method: "POST",
            body: JSON.stringify({ post_id: currentPostId, text: commentText.value }),
            headers: { "Content-Type": "application/json" }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                commentText.value = "";
                loadAllComments(currentPostId);
            }
        })
        .catch(error => console.error("Error posting comment:", error));
    });

    function loadAllComments(postId) {
        fetch("../handlers/getCommentsHandler.php", {
            method: "POST",
            body: JSON.stringify({ post_id: postId }),
            headers: { "Content-Type": "application/json" }
        })
        .then(response => response.json())
        .then(data => {
            modalCommentsDiv.innerHTML = "";

            if (!data.comments || data.comments.length === 0) {
                modalCommentsDiv.innerHTML = "<p>No comments yet.</p>";
                return;
            }

            data.comments.forEach(comment => {
                let commentElement = document.createElement("div");
                commentElement.classList.add("comment-item");
                commentElement.innerHTML = `
                    <div class="comment-header">
                        <a href="account.php?username=${comment.user}">
                            <img src="${comment.profile_pic}" alt="profile" class="comment-profile-pic">
                        </a>
                        <a href="account.php?username=${comment.user}" class="comment-username">${comment.user}</a>
                    </div>
                    <p class="comment-text">${comment.comment}</p>
                    <small class="comment-date">${comment.created_at}</small>
                `;
                modalCommentsDiv.appendChild(commentElement);
            });
        })
        .catch(error => console.error("Error loading comments:", error));
    }
});

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
                alert("Post deleted successfully.");
                
                const postElement = document.getElementById("post-" + postId);
                if (postElement) {
                    postElement.remove();
                }
                
                location.reload();
            } else {
                alert("Error deleting post.");
            }
        })
        .catch(error => console.error("Error deleting post:", error));
    }
};


function repostPost(postId) {
    fetch('../handlers/repostHandler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'post_id=' + postId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}

function openEditProfileModal() {
    document.getElementById("editProfileModal").style.display = "flex";
}

function closeEditProfileModal() {
    document.getElementById("editProfileModal").style.display = "none";
}

window.onclick = function(event) {
    let modal = document.getElementById("editProfileModal");
    if (event.target === modal) {
        modal.style.display = "none";
    }
};

function editPost(postId) {
    fetch(`../handlers/getPost.php?post_id=${postId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('editText').value = data.content;
                document.getElementById('editPostId').value = postId;

                document.getElementById('editModal').style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

document.querySelector('.close-edit-modal').onclick = function() {
    document.getElementById('editModal').style.display = 'none';
};

document.getElementById('editForm').onsubmit = function(event) {
    event.preventDefault();

    const postId = document.getElementById('editPostId').value;
    const updatedContent = document.getElementById('editText').value;

    fetch('../handlers/editPostHandler.php', {
        method: 'POST',
        body: new URLSearchParams({
            post_id: postId,
            content: updatedContent
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('post-' + postId).querySelector('.postDisplayBoxMessage').innerText = updatedContent;
            document.getElementById('editModal').style.display = 'none';
        } else {
            alert('Error saving post!');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
};


document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".edit-btn").forEach(button => {
        button.addEventListener("click", function () {
            const postId = this.getAttribute("data-post-id");
            const postContent = this.getAttribute("data-post-content");
            const postMedia = this.getAttribute("data-post-media");

            openEditModal(postId, postContent, postMedia);
        });
    });

    function openEditModal(postId, content, media) {
        document.getElementById("editPostId").value = postId;
        document.getElementById("editPostText").value = content;
    
        let imageContainer = document.getElementById("editPostImage");
        if (media) {
            imageContainer.innerHTML = `<img src="../uploads/${media}" style="width: 100%; border-radius: 5px;">`;
            imageContainer.style.display = "block";
        } else {
            imageContainer.style.display = "none";
        }
    
        document.getElementById("editModal").classList.add("show");
    }
    
    document.querySelectorAll('.close').forEach(item => {
        item.addEventListener('click', () => {
            document.getElementById('editModal').style.display = 'none';
        });
    });

    window.addEventListener('click', function (event) {
        let modal = document.getElementById("editModal");
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });
});

document.getElementById("followBtn").addEventListener("click", function() {
    let followedId = this.getAttribute("data-followed-id");
    let action = "follow";

    fetch("followHandler.php", {
        method: "POST",
        body: new URLSearchParams({ followed_id: followedId, action: action }),
        headers: { "Content-Type": "application/x-www-form-urlencoded" }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log("Action successful:", action);
        } else {
            alert("Error: " + data.error);
        }
    })
    .catch(error => console.error("Error:", error));
});