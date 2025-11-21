<?php
// available sessions handler - manages resource availability time slots for the acrl system
require_once 'dbConnector.php';
header('Content-Type: application/json');

// establish database connection
$db = connectDB();
if (!$db) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// get the action from request
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch($action) {
    case 'getResources':
        getResourcesWithAvailability($db);
        break;
    case 'getAvailabilitySlots':
        $resourceId = $_GET['resource_id'] ?? null;
        if ($resourceId) {
            getAvailabilitySlots($db, $resourceId);
        } else {
            echo json_encode(['success' => false, 'message' => 'Resource ID required']);
        }
        break;
    case 'addSlot':
        addAvailabilitySlot($db);
        break;
    case 'deleteSlot':
        $slotId = $_POST['slot_id'] ?? null;
        if ($slotId) {
            deleteAvailabilitySlot($db, $slotId);
        } else {
            echo json_encode(['success' => false, 'message' => 'Slot ID required']);
        }
        break;
    case 'updateSlots':
        updateResourceSlots($db);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

// get all resources with their availability information
function getResourcesWithAvailability($db) {
    try {
        $query = "SELECT r.resource_id, r.name, r.capacity, r.latitude, r.longitude, 
                         r.is_bookable, rt.type_name,
                         COUNT(ra.availability_id) as slot_count
                  FROM resources r
                  LEFT JOIN resource_types rt ON r.type_id = rt.type_id
                  LEFT JOIN resource_availability ra ON r.resource_id = ra.resource_id
                  GROUP BY r.resource_id
                  ORDER BY r.name";
        $result = $db->query($query);
        $resources = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $resources[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $resources]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

// get availability slots for a specific resource
function getAvailabilitySlots($db, $resourceId) {
    try {
        $stmt = $db->prepare("SELECT * FROM resource_availability 
                             WHERE resource_id = :resource_id 
                             ORDER BY 
                                CASE day_of_week
                                    WHEN 'monday' THEN 1
                                    WHEN 'tuesday' THEN 2
                                    WHEN 'wednesday' THEN 3
                                    WHEN 'thursday' THEN 4
                                    WHEN 'friday' THEN 5
                                    WHEN 'saturday' THEN 6
                                    WHEN 'sunday' THEN 7
                                END,
                                start_time");
        $stmt->bindValue(':resource_id', $resourceId, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $slots = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $slots[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $slots]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

// add a new availability slot
function addAvailabilitySlot($db) {
    try {
        $resourceId = $_POST['resource_id'] ?? null;
        $dayOfWeek = strtolower($_POST['day_of_week'] ?? '');
        $startTime = $_POST['start_time'] ?? null;
        $endTime = $_POST['end_time'] ?? null;
        if (!$resourceId || !$dayOfWeek || !$startTime || !$endTime) {
            echo json_encode(['success' => false, 'message' => 'All fields required']);
            return;
        }
        // validate time format and logic
        if ($startTime >= $endTime) {
            echo json_encode(['success' => false, 'message' => 'End time must be after start time']);
            return;
        }
        // check for overlapping slots
        $checkStmt = $db->prepare("SELECT COUNT(*) as count FROM resource_availability 
                                   WHERE resource_id = :resource_id 
                                   AND day_of_week = :day_of_week
                                   AND (
                                       (:start_time >= start_time AND :start_time < end_time)
                                       OR (:end_time > start_time AND :end_time <= end_time)
                                       OR (:start_time <= start_time AND :end_time >= end_time)
                                   )");
        $checkStmt->bindValue(':resource_id', $resourceId, SQLITE3_INTEGER);
        $checkStmt->bindValue(':day_of_week', $dayOfWeek, SQLITE3_TEXT);
        $checkStmt->bindValue(':start_time', $startTime, SQLITE3_TEXT);
        $checkStmt->bindValue(':end_time', $endTime, SQLITE3_TEXT);
        $checkResult = $checkStmt->execute();
        $count = $checkResult->fetchArray(SQLITE3_ASSOC)['count'];
        if ($count > 0) {
            echo json_encode(['success' => false, 'message' => 'Time slot overlaps with existing slot']);
            return;
        }
        // insert the new slot
        $stmt = $db->prepare("INSERT INTO resource_availability 
                             (resource_id, day_of_week, start_time, end_time) 
                             VALUES (:resource_id, :day_of_week, :start_time, :end_time)");
        $stmt->bindValue(':resource_id', $resourceId, SQLITE3_INTEGER);
        $stmt->bindValue(':day_of_week', $dayOfWeek, SQLITE3_TEXT);
        $stmt->bindValue(':start_time', $startTime, SQLITE3_TEXT);
        $stmt->bindValue(':end_time', $endTime, SQLITE3_TEXT);
        $stmt->execute();
        echo json_encode([
            'success' => true, 
            'message' => 'Slot added successfully',
            'slot_id' => $db->lastInsertRowID()
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

// delete an availability slot
function deleteAvailabilitySlot($db, $slotId) {
    try {
        $stmt = $db->prepare("DELETE FROM resource_availability WHERE availability_id = :slot_id");
        $stmt->bindValue(':slot_id', $slotId, SQLITE3_INTEGER);
        $stmt->execute();
        echo json_encode(['success' => true, 'message' => 'Slot deleted successfully']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

// update all slots for a resource (bulk update)
function updateResourceSlots($db) {
    try {
        $resourceId = $_POST['resource_id'] ?? null;
        $slots = json_decode($_POST['slots'] ?? '[]', true);
        if (!$resourceId) {
            echo json_encode(['success' => false, 'message' => 'Resource ID required']);
            return;
        }
        // start transaction
        $db->exec('BEGIN TRANSACTION');
        // delete existing slots for this resource
        $deleteStmt = $db->prepare("DELETE FROM resource_availability WHERE resource_id = :resource_id");
        $deleteStmt->bindValue(':resource_id', $resourceId, SQLITE3_INTEGER);
        $deleteStmt->execute();
        // insert new slots
        $insertStmt = $db->prepare("INSERT INTO resource_availability 
                                    (resource_id, day_of_week, start_time, end_time) 
                                    VALUES (:resource_id, :day_of_week, :start_time, :end_time)");
        foreach ($slots as $slot) {
            $insertStmt->bindValue(':resource_id', $resourceId, SQLITE3_INTEGER);
            $insertStmt->bindValue(':day_of_week', strtolower($slot['day_of_week']), SQLITE3_TEXT);
            $insertStmt->bindValue(':start_time', $slot['start_time'], SQLITE3_TEXT);
            $insertStmt->bindValue(':end_time', $slot['end_time'], SQLITE3_TEXT);
            $insertStmt->execute();
            $insertStmt->reset();
        }
        // commit transaction
        $db->exec('COMMIT');
        echo json_encode(['success' => true, 'message' => 'Slots updated successfully']);
    } catch (Exception $e) {
        $db->exec('ROLLBACK');
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

$db->close();
?>