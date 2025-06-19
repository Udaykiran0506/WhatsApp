<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access");
}

$blocker_id = $_SESSION['user_id'];
$blocked_id = $_POST['blocked_id'] ?? null;

if (!$blocked_id) {
    die("Error: No user selected.");
}

// Check if the user exists in `userstable`
$checkUserQuery = "SELECT id FROM userstable WHERE id = ?";
$stmt = $conn->prepare($checkUserQuery);
$stmt->bind_param("i", $blocked_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Error: User does not exist.");
}

// Check if the user is already blocked
$checkQuery = "SELECT * FROM blocked_users WHERE blocker_id = ? AND blocked_id = ?";
$stmt = $conn->prepare($checkQuery);
$stmt->bind_param("ii", $blocker_id, $blocked_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    die("User is already blocked.");
}

// Insert into `blocked_users` table
$sql = "INSERT INTO blocked_users (blocker_id, blocked_id) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $blocker_id, $blocked_id);

if ($stmt->execute()) {
    echo "User blocked successfully.";
} else {
    echo "Error blocking user: " . $conn->error;
}
?>
