<?php
header('Content-Type: application/json');

// Start session to get user ID
session_start();
$userId = $_SESSION['user_id'] ?? ($_POST['user_id'] ?? 1); 

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST)) {
    echo json_encode(['success' => false, 'message' => 'Invalid request method or missing data.']);
    exit;
}

// Load the database connector
require_once 'dbConnector.php';
$db = connectDB();

if (!$db) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

// Validate and sanitize input
$resourceId = filter_var($_POST['resource_id'] ?? null, FILTER_VALIDATE_INT);
$resourceName = filter_var($_POST['resource_name'] ?? null, FILTER_SANITIZE_STRING);
$startTime = filter_var($_POST['start_time'] ?? null, FILTER_SANITIZE_STRING);
$endTime = filter_var($_POST['end_time'] ?? null, FILTER_SANITIZE_STRING);

if (!$resourceId || !$resourceName || !$startTime || !$endTime) {
    echo json_encode(['success' => false, 'message' => 'Missing or invalid booking details.']);
    $db->close();
    exit;
}

try {
    // Basic collision check - updated table name to Bookings
    $collisionCheck = $db->prepare("
        SELECT COUNT(*) as count FROM Bookings 
        WHERE resource_id = :resource_id 
        AND status = 'Confirmed'
        AND (
            (:start_time < end_time AND :end_time > start_time)
        )
    ");
    $collisionCheck->bindValue(':resource_id', $resourceId, SQLITE3_INTEGER);
    $collisionCheck->bindValue(':start_time', $startTime, SQLITE3_TEXT);
    $collisionCheck->bindValue(':end_time', $endTime, SQLITE3_TEXT);
    
    $result = $collisionCheck->execute();
    $collisionRow = $result->fetchArray(SQLITE3_ASSOC);
    
    if ($collisionRow['count'] > 0) {
        echo json_encode(['success' => false, 'message' => 'Booking failed: Resource is already reserved during this time.']);
        $db->close();
        exit;
    }

    // Insert the new booking - updated table name to Bookings
    $stmt = $db->prepare("
        INSERT INTO Bookings (user_id, resource_id, start_time, end_time, purpose, status) 
        VALUES (:user_id, :resource_id, :start_time, :end_time, :purpose, 'Confirmed')
    ");

    $stmt->bindValue(':user_id', $userId, SQLITE3_INTEGER);
    $stmt->bindValue(':resource_id', $resourceId, SQLITE3_INTEGER);
    $stmt->bindValue(':start_time', $startTime, SQLITE3_TEXT);
    $stmt->bindValue(':end_time', $endTime, SQLITE3_TEXT);
    $stmt->bindValue(':purpose', $resourceName, SQLITE3_TEXT);

    $result = $stmt->execute();

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Resource booked successfully!']);
    } else {
        throw new Exception("Failed to insert booking.");
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
} finally {
    $db->close();
}
?>