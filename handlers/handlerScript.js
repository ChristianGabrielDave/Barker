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
