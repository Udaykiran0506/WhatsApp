<?php
session_start();
include "db.php"; // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['user_id'])) {
        echo "Error: User not logged in.";
        exit;
    }

    $sender_id = $_POST['sender_id'];
    $receiver_id = $_POST['receiver_id'];

    if (empty($sender_id) || empty($receiver_id)) {
        echo "Error: Invalid parameters.";
        exit;
    }

    // Update messages to mark them as deleted for sender
    $query = "UPDATE messagestable SET is_deleted_sender = 1 WHERE sender_id = ? AND receiver_id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $sender_id, $receiver_id);

    if ($stmt->execute()) {
        echo "Success: Chat cleared.";
    } else {
        echo "Error: Could not clear chat.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Error: Invalid request.";
}
?>
