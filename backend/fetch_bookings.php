<?php
header('Content-Type: application/json');

//start session to get user ID
session_start();
$userId = $_SESSION['user_id'] ?? ($_GET['user_id'] ?? 'test_user_123'); 

if (empty($userId)) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated.']);
    exit;
}

//load the database connector
$connector = require_once 'dbConnector.php';
$db = $connector->getDb();

$bookings = [];

try {
    $stmt = $db->prepare("
        SELECT id, resource_name, start_time, end_time, status 
        FROM bookings 
        WHERE user_id = :user_id 
        ORDER BY start_time DESC
    ");
    $stmt->bindValue(':user_id', $userId, SQLITE3_TEXT);
    
    $result = $stmt->execute();

    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $bookings[] = $row;
    }

    echo json_encode(['success' => true, 'bookings' => $bookings]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
} finally {
    $connector->close();
}
?>