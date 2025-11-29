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

// Validate booking ID
$bookingId = filter_var($_POST['booking_id'] ?? null, FILTER_VALIDATE_INT);

if (!$bookingId) {
    $_SESSION['booking_error'] = 'Invalid booking ID.';
    header('Location: ../frontend/bookings.php');
    exit;
}

try {
    // First, verify the booking belongs to this user and is cancellable
    $checkStmt = $db->prepare("
        SELECT booking_id, status, start_time
        FROM Bookings
        WHERE booking_id = :booking_id
        AND user_id = :user_id
    ");
    
    $checkStmt->bindValue(':booking_id', $bookingId, SQLITE3_INTEGER);
    $checkStmt->bindValue(':user_id', $userId, SQLITE3_INTEGER);
    
    $result = $checkStmt->execute();
    $booking = $result->fetchArray(SQLITE3_ASSOC);
    
    if (!$booking) {
        $_SESSION['booking_error'] = 'Booking not found or you do not have permission to cancel it.';
        header('Location: ../frontend/bookings.php');
        exit;
    }
    
    // Check if booking is already cancelled or completed
    if ($booking['status'] === 'Cancelled') {
        $_SESSION['booking_error'] = 'This booking is already cancelled.';
        header('Location: ../frontend/bookings.php');
        exit;
    }
    
    if ($booking['status'] === 'Completed') {
        $_SESSION['booking_error'] = 'Cannot cancel a completed booking.';
        header('Location: ../frontend/bookings.php');
        exit;
    }
    
    // Check if booking start time has already passed
    if (strtotime($booking['start_time']) < time()) {
        $_SESSION['booking_error'] = 'Cannot cancel a booking that has already started.';
        header('Location: ../frontend/bookings.php');
        exit;
    }
    
    // Update booking status to Cancelled
    $updateStmt = $db->prepare("
        UPDATE Bookings
        SET status = 'Cancelled'
        WHERE booking_id = :booking_id
    ");
    
    $updateStmt->bindValue(':booking_id', $bookingId, SQLITE3_INTEGER);
    $updateResult = $updateStmt->execute();
    
    if ($updateResult) {
        $_SESSION['booking_success'] = 'Booking cancelled successfully.';
    } else {
        throw new Exception('Failed to cancel booking.');
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
