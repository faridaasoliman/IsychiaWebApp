<?php
session_start();
require 'db.php';
require 'functions.php';

if (!isset($_SESSION['user_id'])) {
  http_response_code(401);
  echo json_encode(['error' => 'Not authenticated']);
  exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$sender_id = $_SESSION['user_id'];
$receiver_username = $data['receiver'];
$message = $data['message'];

// Get receiver ID
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $receiver_username);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
  echo json_encode(['error' => 'Receiver not found']);
  exit;
}
$receiver = $result->fetch_assoc();
$receiver_id = $receiver['id'];

// Insert message
$stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message_text) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $sender_id, $receiver_id, $message);
$stmt->execute();

echo json_encode(['success' => true]);
?>
