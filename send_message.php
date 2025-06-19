<?php
session_start();
include('db.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "Please log in first.";
    exit();
}

// Get logged-in user's ID and the receiver's ID from the request
$senderId = $_SESSION['user_id'];
$receiverId = $_POST['receiver_id'];
$message = $_POST['message'];
$timestamp = date('Y-m-d H:i:s'); // Store current date-time

// Sanitize the message input to avoid security issues (e.g., XSS attacks)
$message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

// Validate input fields
if (empty($senderId) || empty($receiverId) || empty($message)) {
    echo "Error: Missing required fields.";
    exit();
}

// **Check if the sender is blocked by the receiver**
$checkBlockedQuery = "SELECT * FROM blocked_users WHERE blocker_id = ? AND blocked_id = ?";
$stmt = $conn->prepare($checkBlockedQuery);
$stmt->bind_param("ii", $receiverId, $senderId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "You are blocked by this user.";
    exit();
}

// **Check if the sender has blocked the receiver**
$checkSenderBlockedQuery = "SELECT * FROM blocked_users WHERE blocker_id = ? AND blocked_id = ?";
$stmt = $conn->prepare($checkSenderBlockedQuery);
$stmt->bind_param("ii", $senderId, $receiverId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "You have blocked this user.";
    exit();
}

// Insert the message into the database
$query = "INSERT INTO messagestable (sender_id, receiver_id, message, timestamp) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("iiss", $senderId, $receiverId, $message, $timestamp);

if ($stmt->execute()) {
    echo "Message sent successfully.";
} else {
    echo "Error sending message: " . $conn->error;
}
?>
