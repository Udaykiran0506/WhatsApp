<?php
include 'db.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access");
}

$blocker_id = $_SESSION['user_id'];
$blocked_id = $_POST['blocked_id'];

$sql = "DELETE FROM blocked_users WHERE blocker_id = ? AND blocked_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $blocker_id, $blocked_id);

if ($stmt->execute()) {
    echo "User unblocked successfully";
} else {
    echo "Error unblocking user";
}
?>
