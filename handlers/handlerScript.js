document.querySelectorAll(".more-btn").forEach((button) => {
    button.addEventListener("click", function () {
    let dropdown = this.nextElementSibling;
    dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
    });
});

function editPost(postId) {
    let newText = prompt("Edit post text:");
    if (newText) {
        fetch("../handlers/editHandler.php", {
            method: "POST",
            body: JSON.stringify({ post_id: postId, content: newText }),
            headers: { "Content-Type": "application/json" }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Post updated successfully!");
                location.reload();
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(error => console.error("Error:", error));
    }
}


function deletePost(postId) {
    if (confirm("Are you sure you want to delete this post?")) {
        fetch("../handlers/deleteHandler.php", {
            method: "POST",
            body: JSON.stringify({ post_id: postId }),
            headers: { "Content-Type": "application/json" }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Post deleted successfully!");
                location.reload();
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(error => console.error("Error:", error));
    }
}
