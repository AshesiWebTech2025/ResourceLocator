<?php
header('Content-Type: application/json');

// Start session to get user ID
session_start();
$userId = $_SESSION['user_id'] ?? ($_GET['user_id'] ?? 1); 

if (empty($userId)) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated.']);
    exit;
}

// Load the database connector
require_once 'dbConnector.php';
$db = connectDB();

if (!$db) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

$bookings = [];

try {
    // Query the correct table with proper column names and join with Resources
    $stmt = $db->prepare("
        SELECT 
            b.booking_id as id,
            b.start_time, 
            b.end_time, 
            b.status,
            b.purpose,
            r.name AS resource_name
        FROM Bookings b
        JOIN Resources r ON b.resource_id = r.resource_id
        WHERE b.user_id = :user_id 
        ORDER BY b.start_time DESC
    ");
    $stmt->bindValue(':user_id', $userId, SQLITE3_INTEGER);
    
    $result = $stmt->execute();

    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $bookings[] = $row;
    }

    echo json_encode(['success' => true, 'bookings' => $bookings]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
} finally {
    $db->close();
}
?>