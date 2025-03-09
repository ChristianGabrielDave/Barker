<?php
include '../includes/config.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['post_id'])) {
    echo json_encode(["success" => false, "message" => "Post ID is missing"]);
    exit();
}

$post_id = intval($data['post_id']);

$commentQuery = "SELECT c.comment, c.doc, u.username, u.dp 
                 FROM comments c
                 JOIN users u ON c.uid = u.id
                 WHERE c.pid = ? 
                 ORDER BY c.doc DESC";

$stmt = $conn->prepare($commentQuery);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

$comments = [];
while ($row = $result->fetch_assoc()) {
    $comments[] = [
        "user" => $row['username'],
        "comment" => $row['comment'],
        "created_at" => $row['doc'],
        "profile_pic" => $row['dp'] ? "../uploads/profile_pictures/" . $row['dp'] : "https://api.dicebear.com/6.x/initials/png?seed=" . $row['username'] . "&size=128"
    ];
}

echo json_encode(["success" => true, "comments" => $comments]);
?>
