<?php
/**
 * API endpoint to check resource availability for a specific date.
 * Returns available time slots and already booked times.
 */

session_start();
header('Content-Type: application/json');

// Suppress error display for API
ini_set('display_errors', 0);
error_reporting(0);

require_once 'dbConnector.php';

// Check authentication
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

// Get parameters
$resourceId = isset($_GET['resource_id']) ? intval($_GET['resource_id']) : 0;
$date = isset($_GET['date']) ? $_GET['date'] : '';

// Validate inputs
if ($resourceId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid resource ID']);
    exit;
}

if (empty($date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    echo json_encode(['success' => false, 'message' => 'Invalid date format']);
    exit;
}

// Connect to database
$db = connectDB();
if (!$db) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Get availability information
$result = getAvailableTimeSlotsForDate($db, $resourceId, $date);

$db->close();

echo json_encode([
    'success' => true,
    'data' => $result
]);
?>

