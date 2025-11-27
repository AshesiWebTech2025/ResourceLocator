<?php
// --- START OF PHP ERROR DEBUGGING ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// --- END OF PHP ERROR DEBUGGING ---

session_start();

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['booking_error'] = 'Invalid request method.';
    header('Location: ../frontend/bookings.php');
    exit;
}

// Include database connector
require_once 'dbConnector.php';
$db = connectDB();

if (!$db) {
    $_SESSION['booking_error'] = 'Database connection failed.';
    header('Location: ../frontend/bookings.php');
    exit;
}

// Get user ID (currently hardcoded, will come from session in production)
$userId = $_SESSION['user_id'] ?? 1;

// Validate and sanitize input
$resourceId = filter_var($_POST['resource_id'] ?? null, FILTER_VALIDATE_INT);
$bookingDate = filter_var($_POST['booking_date'] ?? null, FILTER_SANITIZE_STRING);
$startTime = filter_var($_POST['start_time'] ?? null, FILTER_SANITIZE_STRING);
$endTime = filter_var($_POST['end_time'] ?? null, FILTER_SANITIZE_STRING);
$purpose = filter_var($_POST['purpose'] ?? null, FILTER_SANITIZE_STRING);

// Check for missing fields
if (!$resourceId || !$bookingDate || !$startTime || !$endTime || !$purpose) {
    $_SESSION['booking_error'] = 'All fields are required.';
    header('Location: ../frontend/bookings.php');
    exit;
}

// Combine date and time into timestamp format
$startDateTime = $bookingDate . ' ' . $startTime . ':00';
$endDateTime = $bookingDate . ' ' . $endTime . ':00';

// Validate that end time is after start time
if (strtotime($endDateTime) <= strtotime($startDateTime)) {
    $_SESSION['booking_error'] = 'End time must be after start time.';
    header('Location: ../frontend/bookings.php');
    exit;
}

// Validate that booking is not in the past
if (strtotime($startDateTime) < time()) {
    $_SESSION['booking_error'] = 'Cannot book resources in the past.';
    header('Location: ../frontend/bookings.php');
    exit;
}

try {
    // Check for booking conflicts
    $conflictStmt = $db->prepare("
        SELECT COUNT(*) as conflict_count
        FROM Bookings
        WHERE resource_id = :resource_id
        AND status = 'Confirmed'
        AND (
            (start_time < :end_time AND end_time > :start_time)
        )
    ");
    
    $conflictStmt->bindValue(':resource_id', $resourceId, SQLITE3_INTEGER);
    $conflictStmt->bindValue(':start_time', $startDateTime, SQLITE3_TEXT);
    $conflictStmt->bindValue(':end_time', $endDateTime, SQLITE3_TEXT);
    
    $conflictResult = $conflictStmt->execute();
    $conflictRow = $conflictResult->fetchArray(SQLITE3_ASSOC);
    
    if ($conflictRow['conflict_count'] > 0) {
        $_SESSION['booking_error'] = 'This resource is already booked during the selected time slot.';
        header('Location: ../frontend/bookings.php');
        exit;
    }
    
    // Insert the new booking
    $insertStmt = $db->prepare("
        INSERT INTO Bookings (resource_id, user_id, start_time, end_time, purpose, status)
        VALUES (:resource_id, :user_id, :start_time, :end_time, :purpose, 'Confirmed')
    ");
    
    $insertStmt->bindValue(':resource_id', $resourceId, SQLITE3_INTEGER);
    $insertStmt->bindValue(':user_id', $userId, SQLITE3_INTEGER);
    $insertStmt->bindValue(':start_time', $startDateTime, SQLITE3_TEXT);
    $insertStmt->bindValue(':end_time', $endDateTime, SQLITE3_TEXT);
    $insertStmt->bindValue(':purpose', $purpose, SQLITE3_TEXT);
    
    $result = $insertStmt->execute();
    
    if ($result) {
        $_SESSION['booking_success'] = 'Resource booked successfully!';
    } else {
        throw new Exception('Failed to create booking.');
    }
    
} catch (Exception $e) {
    $_SESSION['booking_error'] = 'Error: ' . $e->getMessage();
}

// Close database connection
$db->close();

// Redirect back to bookings page
header('Location: ../frontend/bookings.php');
exit;
?>
