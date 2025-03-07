document.addEventListener("DOMContentLoaded", () => {
    // Handle dropdown menu toggling
    document.querySelectorAll(".more-btn").forEach(button => {
        button.addEventListener("click", function (event) {
            event.stopPropagation(); // Prevent closing the dropdown when clicking inside
            const dropdown = this.nextElementSibling; // Get the corresponding dropdown menu

            // Close all other dropdowns
            document.querySelectorAll(".dropdown-content").forEach(dropdownMenu => {
                if (dropdownMenu !== dropdown) {
                    dropdownMenu.style.display = "none";
                }
            });

            // Toggle the clicked dropdown menu
            dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
        });
    });

    // Close dropdowns when clicking outside
    document.addEventListener("click", () => {
        document.querySelectorAll(".dropdown-content").forEach(dropdown => {
            dropdown.style.display = "none";
        });
    });

    // Like a post
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

    // Comment Modal Handling
    let commentModal = document.getElementById("commentModal");
    let closeBtn = document.querySelector(".close");
    let commentText = document.getElementById("commentText");
    let submitComment = document.getElementById("submitComment");
    let modalCommentsDiv = document.getElementById("modal-comments");
    let currentPostId = null;

    // Ensure the modal is hidden properly on page load
    commentModal.classList.remove("show");

    // Open comment modal
    document.querySelectorAll(".comment-btn").forEach(button => {
        button.addEventListener("click", function () {
            currentPostId = this.getAttribute("data-post-id");
            commentText.value = "";
            modalCommentsDiv.innerHTML = "<p>Loading comments...</p>"; // Show loading text
            commentModal.classList.add("show"); // Show modal smoothly
            loadAllComments(currentPostId);
        });
    });

    // Close modal when clicking the close button or outside the modal
    closeBtn.addEventListener("click", closeModal);
    window.addEventListener("click", event => {
        if (event.target === commentModal) closeModal();
    });

    function closeModal() {
        commentModal.classList.remove("show");
    }

    // Submit a comment
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
                commentText.value = ""; // Clear input field after submission
                loadAllComments(currentPostId); // Refresh comments
            }
        })
        .catch(error => console.error("Error posting comment:", error));
    });

    // Load all comments into modal
    function loadAllComments(postId) {
        fetch("../handlers/getCommentsHandler.php", {
            method: "POST",
            body: JSON.stringify({ post_id: postId }),
            headers: { "Content-Type": "application/json" }
        })
        .then(response => response.json())
        .then(data => {
            modalCommentsDiv.innerHTML = ""; // Clear old comments

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
                
                // Remove the post from the page instantly
                const postElement = document.getElementById("post-" + postId);
                if (postElement) {
                    postElement.remove(); // Remove the post from the DOM
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

// Close modal when clicking outside of it
window.onclick = function(event) {
    let modal = document.getElementById("editProfileModal");
    if (event.target === modal) {
        modal.style.display = "none";
    }
};

// JavaScript to handle the edit functionality
function editPost(postId) {
    // Fetch the current post content using the postId (AJAX or fetch request)
    fetch(`../handlers/getPost.php?post_id=${postId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Populate the edit modal with the current post's content
                document.getElementById('editText').value = data.content;
                document.getElementById('editPostId').value = postId;

                // Show the modal
                document.getElementById('editModal').style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

// Close edit modal
document.querySelector('.close-edit-modal').onclick = function() {
    document.getElementById('editModal').style.display = 'none';
};

// Save edited post
document.getElementById('editForm').onsubmit = function(event) {
    event.preventDefault();

    const postId = document.getElementById('editPostId').value;
    const updatedContent = document.getElementById('editText').value;

    // Send updated post content to the server
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
            // Update the content in the post display area (if you don't want to reload the page)
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
    // Handle the edit button click
    document.querySelectorAll(".edit-btn").forEach(button => {
        button.addEventListener("click", function () {
            const postId = this.getAttribute("data-post-id");
            const postContent = this.getAttribute("data-post-content");
            const postMedia = this.getAttribute("data-post-media");

            openEditModal(postId, postContent, postMedia); // Open modal with post data
        });
    });

    // Modal opening function
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
    

    // Close modal when clicking the close button
    document.querySelectorAll('.close').forEach(item => {
        item.addEventListener('click', () => {
            document.getElementById('editModal').style.display = 'none';
        });
    });

    // Close modal when clicking outside of it
    window.addEventListener('click', function (event) {
        let modal = document.getElementById("editModal");
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });
});

document.getElementById("followBtn").addEventListener("click", function() {
    let followedId = this.getAttribute("data-followed-id");
    // Toggle follow/unfollow action based on current state; here we assume "follow"
    let action = "follow"; // or "unfollow" if already following

    fetch("followHandler.php", {
        method: "POST",
        body: new URLSearchParams({ followed_id: followedId, action: action }),
        headers: { "Content-Type": "application/x-www-form-urlencoded" }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update UI accordingly
            console.log("Action successful:", action);
        } else {
            alert("Error: " + data.error);
        }
    })
    .catch(error => console.error("Error:", error));
});