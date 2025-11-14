<?php
header('Content-Type: application/json');

//start session to get user ID
session_start();
$userId = $_SESSION['user_id'] ?? ($_POST['user_id'] ?? 'test_user_123'); 

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST)) {
    echo json_encode(['success' => false, 'message' => 'Invalid request method or missing data.']);
    exit;
}
//load the database connector
$connector = require_once 'dbConnector.php';
$db = $connector->getDb();
//validate and sanitize input
$resourceId = filter_var($_POST['resource_id'] ?? null, FILTER_VALIDATE_INT);
$resourceName = filter_var($_POST['resource_name'] ?? null, FILTER_SANITIZE_STRING);
$startTime = filter_var($_POST['start_time'] ?? null, FILTER_SANITIZE_STRING);
$endTime = filter_var($_POST['end_time'] ?? null, FILTER_SANITIZE_STRING);

if (!$resourceId || !$resourceName || !$startTime || !$endTime) {
    echo json_encode(['success' => false, 'message' => 'Missing or invalid booking details.']);
    $connector->close();
    exit;
}

try {
    //basic collision check
    $collisionCheck = $db->prepare("
        SELECT COUNT(*) FROM bookings 
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
    $collisionCount = $result->fetchArray(SQLITE3_NUM)[0];
    
    if ($collisionCount > 0) {
        echo json_encode(['success' => false, 'message' => 'Booking failed: Resource is already reserved during this time.']);
        $connector->close();
        exit;
    }

    //insert the new booking
    $stmt = $db->prepare("
        INSERT INTO bookings (user_id, resource_id, resource_name, start_time, end_time, status) 
        VALUES (:user_id, :resource_id, :resource_name, :start_time, :end_time, 'Confirmed')
    ");

    $stmt->bindValue(':user_id', $userId, SQLITE3_TEXT);
    $stmt->bindValue(':resource_id', $resourceId, SQLITE3_INTEGER);
    $stmt->bindValue(':resource_name', $resourceName, SQLITE3_TEXT);
    $stmt->bindValue(':start_time', $startTime, SQLITE3_TEXT);
    $stmt->bindValue(':end_time', $endTime, SQLITE3_TEXT);

    $result = $stmt->execute();

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Resource booked successfully!']);
    } else {
        throw new Exception("Failed to insert booking.");
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
} finally {
    $connector->close();
}
?>