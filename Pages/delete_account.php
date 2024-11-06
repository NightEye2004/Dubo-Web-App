<?php
session_start();
require_once '../connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Delete user's data from related tables
$tables = ['user_classes', 'private_tuitions', 'specialist_bookings', 'payments'];

foreach ($tables as $table) {
    $sql = "DELETE FROM $table WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
}

// Delete the user
$sql = "DELETE FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
    session_destroy();
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete user: ' . $conn->error]);
}
