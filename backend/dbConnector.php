<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//usiing sqlite to connect to a db file locally
$dbPath = "../setup/mockDatabase.db";

/**
 * Connects to the SQLite database file and ensures all necessary tables exist.
 * This function creates tables for users, courses, enrollments, sessions,
 * attendance, issues, and the new resource (building) locator system.
 *
 * @return SQLite3|null The database connection object or null if connection fails.
 */
function connectDB(){
    global $dbPath;
    $fullPath = $dbPath;

    try{
        if(file_exists($dbPath)){
            $fullPath = realpath($dbPath);
        }
        $db = new SQLite3($dbPath);
        
        // 1. Users Table (Schema updated to reflect user types: student, faculty, visitor, admin)
        $db->exec("CREATE TABLE IF NOT EXISTS Users (
            user_id INTEGER PRIMARY KEY AUTOINCREMENT,
            ashesi_email TEXT UNIQUE NOT NULL,       
            first_name TEXT NOT NULL,                     
            last_name Text NOT NULL,
            role TEXT NOT NULL CHECK(role IN ('Student', 'Faculty', 'Staff', 'Visitor')),
            password_hash TEXT NOT NULL,            
            is_active INTEGER NOT NULL DEFAULT 1,   
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )");
        
        //2. Resource Types Table
        $db->exec("CREATE TABLE IF NOT EXISTS Resource_Types (
            type_id INTEGER PRIMARY KEY AUTOINCREMENT,
            type_name VARCHAR(100) UNIQUE NOT NULL)");

        //3. Resources Table
        $db->exec("CREATE TABLE IF NOT EXISTS Resources(
            resource_id INTEGER PRIMARY KEY AUTOINCREMENT,
            type_id INTEGER NOT NULL,
            name VARCHAR(255) NOT NULL,
            capacity INTEGER,
            description TEXT,
            latitude DECIMAL(9,6),
            longitude DECIMAL(9,6),
            is_bookable BOOLEAN DEFAULT 0,
            FOREIGN KEY (type_id) REFERENCES Resource_Types(type_id))");

        //4. Bookings Table
        $db->exec("CREATE TABLE IF NOT EXISTS Bookings (
            booking_id INTEGER PRIMARY KEY AUTOINCREMENT,
            resource_id INTEGER NOT NULL,
            user_id INTEGER NOT NULL,
            start_time TIMESTAMP NOT NULL,
            end_time TIMESTAMP NOT NULL,
            purpose TEXT,
            status TEXT CHECK(status IN ('Confirmed', 'Cancelled', 'Completed')) DEFAULT 'Confirmed',
            FOREIGN KEY (resource_id) REFERENCES Resources(resource_id),
            FOREIGN KEY (user_id) REFERENCES Users(user_id)
        )");

        //5. Resource Availability Slots Table (for Available Sessions page)
        $db->exec("CREATE TABLE IF NOT EXISTS resource_availability (
            availability_id INTEGER PRIMARY KEY AUTOINCREMENT,
            resource_id INTEGER NOT NULL,
            day_of_week TEXT NOT NULL,
            start_time TEXT NOT NULL,
            end_time TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (resource_id) REFERENCES resources(resource_id) ON DELETE CASCADE)");

        
        
        return $db; 
    } catch(Exception $e){
        error_log("There was a database connection error: ". $e->getMessage() . " Path: " . $fullPath);
        return null;
    }
}

/**
 * Executes a resource query with optional filtering.
 *
 * @param SQLite3 $db The database connection object.
 * @param string $searchTerm Optional term to filter by name, description, or type.
 * @return array List of resources.
 */
function getFilteredResources(SQLite3 $db, $searchTerm = ''){
    $resources = [];
    //escape the search term to prevent SQL injection.
    $escapedTerm = $db->escapeString($searchTerm);
    //wrapping the escaped term in SQL LIKE wildcards (%) and single quotes (')
    // for direct insertion into the query string.
    $searchPattern = "'%" . $escapedTerm . "%'";
    $query = "SELECT r.*, rt.type_name 
              FROM Resources r 
              JOIN Resource_Types rt ON r.type_id = rt.type_id";
    
    if (!empty($searchTerm)) {
        //concatenate the query using the correctly formatted search pattern.
        $query .= " WHERE r.name LIKE " . $searchPattern . 
                    " OR r.description LIKE " . $searchPattern . 
                    " OR rt.type_name LIKE " . $searchPattern;
    }
    
    // Log the query for debugging purposes
    error_log("Executing Resource Query: " . $query);

    $results = $db->query($query);

    if ($results) {
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $resources[] = $row;
        }
    }
    return $resources;
}

/**
 * Fetches all resources (no filter).
 *
 * @param SQLite3 $db The database connection object.
 * @return array List of all resources.
 */
function getAllResources(SQLite3 $db){
    return getFilteredResources($db);
}

//booking related code below
/**
 * Retrieves all bookings for a specific user with resource details.
 * @param SQLite3 $db The database connection object.
 * @param int $userId The ID of the user whose bookings to retrieve.
 * @param string $status Optional status filter ('Confirmed', 'Cancelled', 'Completed').
 * @return array List of bookings with resource information.
 */
function getAllBookings(SQLite3 $db, $userId, $status = null){
    $bookings = []; 
    //build query with JOIN to get resource details
    $query = "SELECT 
                b.booking_id, 
                b.resource_id, 
                b.user_id, 
                b.start_time, 
                b.end_time, 
                b.purpose, 
                b.status,
                r.name as resource_name,
                r.capacity,
                r.description,
                rt.type_name as resource_type
              FROM Bookings b
              JOIN Resources r ON b.resource_id = r.resource_id
              JOIN Resource_Types rt ON r.type_id = rt.type_id
              WHERE b.user_id = :userId";
    //add status filter if provided
    if ($status !== null && in_array($status, ['Confirmed', 'Cancelled', 'Completed'])) {
        $query .= " AND b.status = :status";
    }
    //order by start time (upcoming bookings first)
    $query .= " ORDER BY b.start_time ASC";
    //log query for debugging
    error_log("Executing Bookings Query: " . $query);
    //prepare statement to prevent SQL injection
    $stmt = $db->prepare($query);
    if ($stmt) {
        //bind parameters
        $stmt->bindValue(':userId', $userId, SQLITE3_INTEGER);
        
        if ($status !== null && in_array($status, ['Confirmed', 'Cancelled', 'Completed'])) {
            $stmt->bindValue(':status', $status, SQLITE3_TEXT);
        }
        //execute query
        $results = $stmt->execute();
        if ($results) {
            while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
                $bookings[] = $row;
            }
        }
        $stmt->close();
    } else {
        error_log("Failed to prepare bookings query: " . $db->lastErrorMsg());
    }
    return $bookings;
}
/**
 * Retrieves upcoming bookings for a specific user (Confirmed status only, future dates).
 *
 * @param SQLite3 $db The database connection object.
 * @param int $userId The ID of the user whose bookings to retrieve.
 * @return array List of upcoming bookings.
 */
function getUpcomingBookings(SQLite3 $db, $userId){
    $bookings = [];
    $query = "SELECT 
                b.booking_id, 
                b.resource_id, 
                b.user_id, 
                b.start_time, 
                b.end_time, 
                b.purpose, 
                b.status,
                r.name as resource_name,
                r.capacity,
                r.description,
                rt.type_name as resource_type
              FROM Bookings b
              JOIN Resources r ON b.resource_id = r.resource_id
              JOIN Resource_Types rt ON r.type_id = rt.type_id
              WHERE b.user_id = :userId 
              AND b.status = 'Confirmed'
              AND b.start_time >= datetime('now')
              ORDER BY b.start_time ASC";
    
    $stmt = $db->prepare($query);
    if ($stmt) {
        $stmt->bindValue(':userId', $userId, SQLITE3_INTEGER);
        $results = $stmt->execute();
        
        if ($results) {
            while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
                $bookings[] = $row;
            }
        }
        $stmt->close();
    }
    return $bookings;
}
/**
 * Retrieves a single booking by booking ID.
 *
 * @param SQLite3 $db The database connection object.
 * @param int $bookingId The ID of the booking to retrieve.
 * @return array|null Booking details or null if not found.
 */
function getBookingById(SQLite3 $db, $bookingId){
    $query = "SELECT 
                b.booking_id, 
                b.resource_id, 
                b.user_id, 
                b.start_time, 
                b.end_time, 
                b.purpose, 
                b.status,
                r.name as resource_name,
                r.capacity,
                r.description,
                rt.type_name as resource_type
              FROM Bookings b
              JOIN Resources r ON b.resource_id = r.resource_id
              JOIN Resource_Types rt ON r.type_id = rt.type_id
              WHERE b.booking_id = :bookingId";
    $stmt = $db->prepare($query);
    if ($stmt) {
        $stmt->bindValue(':bookingId', $bookingId, SQLITE3_INTEGER);
        $results = $stmt->execute();
        
        if ($results) {
            $booking = $results->fetchArray(SQLITE3_ASSOC);
            $stmt->close();
            return $booking ? $booking : null;
        }
        $stmt->close();
    }
    return null;
}
/**
 * Cancels a booking by updating its status to 'Cancelled'.
 * @param SQLite3 $db The database connection object.
 * @param int $bookingId The ID of the booking to cancel.
 * @param int $userId The ID of the user (for verification).
 * @return bool True if cancelled successfully, false otherwise.
 */
function cancelBooking(SQLite3 $db, $bookingId, $userId){
    //verify the booking belongs to the user before cancelling
    $query = "UPDATE Bookings 
              SET status = 'Cancelled' 
              WHERE booking_id = :bookingId 
              AND user_id = :userId 
              AND status = 'Confirmed'";
    
    $stmt = $db->prepare($query);
    if ($stmt) {
        $stmt->bindValue(':bookingId', $bookingId, SQLITE3_INTEGER);
        $stmt->bindValue(':userId', $userId, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $stmt->close();
        //check if any rows were affected
        return $db->changes() > 0;
    }
    return false;
}

//code for setting resource availabilty below
/**
 * Fetches all resources marked as bookable (is_bookable = 1).
 *
 * @param SQLite3 $db The database connection object.
 * @return array List of bookable resources.
 */
function getBookableResources(SQLite3 $db) {
    $resources = [];
    $query = "SELECT r.*, rt.type_name 
              FROM Resources r 
              JOIN Resource_Types rt ON r.type_id = rt.type_id
              WHERE r.is_bookable = 1
              ORDER BY r.name";
    $results = $db->query($query);
    if ($results) {
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            //convert to a manner easy for js consumption later
            $resources[] = [
                'resource_id' => $row['resource_id'],
                'name' => $row['name'],
                'capacity' => $row['capacity'],
                'description' => $row['description'],
                'type_name' => $row['type_name'],
                'latitude' => $row['latitude'],
                'longitude' => $row['longitude']
            ];
        }
    }
    return $resources;
}
/**
 * Retrieves the resource_availability slots for a specific resource.
 *
 * @param SQLite3 $db The database connection object.
 * @param int $resourceId The ID of the resource.
 * @return array List of availability slots.
 */
function getResourceAvailability(SQLite3 $db, $resourceId) {
    $slots = [];
    $query = "SELECT 
                availability_id, 
                day_of_week, 
                start_time, 
                end_time 
              FROM resource_availability 
              WHERE resource_id = :resourceId 
              ORDER BY day_of_week, start_time";
    $stmt = $db->prepare($query);
    if ($stmt) {
        $stmt->bindValue(':resourceId', $resourceId, SQLITE3_INTEGER);
        $results = $stmt->execute();
        
        if ($results) {
            while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
                $slots[] = $row;
            }
        }
        $stmt->close();
    } else {
        error_log("Failed to prepare getResourceAvailability query: " . $db->lastErrorMsg());
    }
    return $slots;
}
/**
 * Sets the resource_availability slots for a specific resource by deleting 
 * existing records and inserting the new batch.
 *
 * @param SQLite3 $db The database connection object.
 * @param int $resourceId The ID of the resource.
 * @param array $slots Array of slot objects (must contain 'day', 'start', 'end').
 * @return bool True on successful update, false otherwise.
 */
function setResourceAvailability(SQLite3 $db, $resourceId, $slots) {
    // Start transaction for atomic operation
    $db->exec('BEGIN TRANSACTION');
    try {
        // Delete all existing slots for resource
        $deleteQuery = "DELETE FROM resource_availability WHERE resource_id = :resourceId";
        $deleteStmt = $db->prepare($deleteQuery);
        $deleteStmt->bindValue(':resourceId', $resourceId, SQLITE3_INTEGER);
        $deleteStmt->execute();
        $deleteStmt->close();
        
        // Insert new slots
        if (!empty($slots)) {
            $insertQuery = "INSERT INTO resource_availability (resource_id, day_of_week, start_time, end_time) 
                            VALUES (:resourceId, :day, :start, :end)";
            $insertStmt = $db->prepare($insertQuery);

            foreach ($slots as $slot) {
                // Use the time strings exactly as received - they're TEXT fields
                $insertStmt->bindValue(':resourceId', $resourceId, SQLITE3_INTEGER);
                $insertStmt->bindValue(':day', $slot['day'], SQLITE3_TEXT);
                $insertStmt->bindValue(':start', $slot['start'], SQLITE3_TEXT);
                $insertStmt->bindValue(':end', $slot['end'], SQLITE3_TEXT);
                
                $result = $insertStmt->execute();
                if (!$result) {
                    error_log("Failed to insert slot: " . $db->lastErrorMsg());
                    throw new Exception("Database insert failed: " . $db->lastErrorMsg());
                }
                $insertStmt->reset();  // Reset statement for reuse with new bindings
            }
            $insertStmt->close();
        }
        
        // Commit transaction
        $db->exec('COMMIT');
        return true;
    } catch (Exception $e) {
        // Rollback on error
        $db->exec('ROLLBACK');
        error_log("Failed to set resource availability: " . $e->getMessage());
        return false;
    }
}
//cpde for setting resource availabiltty above

/**
 * Checks if a booking time falls within the allowed availability slots for a resource.
 * If NO availability slots are defined for a resource at all, it's freely bookable.
 * If slots ARE defined, bookings must fall within those slots.
 * 
 * @param SQLite3 $db The database connection object.
 * @param int $resourceId The ID of the resource being booked.
 * @param string $bookingDate The date of the booking (YYYY-MM-DD format).
 * @param string $startTime The start time of the booking (HH:MM or HH:MM:SS format).
 * @param string $endTime The end time of the booking (HH:MM or HH:MM:SS format).
 * @return array ['valid' => bool, 'message' => string, 'available_slots' => array, 'has_restrictions' => bool]
 */
function isBookingWithinAvailability(SQLite3 $db, $resourceId, $bookingDate, $startTime, $endTime) {
    // First, check if this resource has ANY availability slots defined at all
    $checkQuery = "SELECT COUNT(*) as slot_count FROM resource_availability WHERE resource_id = :resourceId";
    $checkStmt = $db->prepare($checkQuery);
    if (!$checkStmt) {
        return ['valid' => false, 'message' => 'Database error checking availability.', 'available_slots' => [], 'has_restrictions' => false];
    }
    $checkStmt->bindValue(':resourceId', $resourceId, SQLITE3_INTEGER);
    $checkResult = $checkStmt->execute();
    $checkRow = $checkResult->fetchArray(SQLITE3_ASSOC);
    $checkStmt->close();
    
    // If NO slots are defined for this resource at all, it's freely bookable
    if ($checkRow['slot_count'] == 0) {
        return [
            'valid' => true, 
            'message' => 'No time restrictions for this resource.',
            'available_slots' => [],
            'has_restrictions' => false
        ];
    }
    
    // Resource HAS availability slots defined - now check if booking is within them
    $dayOfWeek = strtolower(date('l', strtotime($bookingDate)));
    
    // Normalize time to HH:MM format for comparison
    $bookingStart = date('H:i', strtotime($startTime));
    $bookingEnd = date('H:i', strtotime($endTime));
    
    // Get availability slots for this resource on this specific day
    $query = "SELECT start_time, end_time 
              FROM resource_availability 
              WHERE resource_id = :resourceId 
              AND LOWER(day_of_week) = :dayOfWeek
              ORDER BY start_time";
    
    $stmt = $db->prepare($query);
    if (!$stmt) {
        return ['valid' => false, 'message' => 'Database error checking availability.', 'available_slots' => [], 'has_restrictions' => true];
    }
    
    $stmt->bindValue(':resourceId', $resourceId, SQLITE3_INTEGER);
    $stmt->bindValue(':dayOfWeek', $dayOfWeek, SQLITE3_TEXT);
    
    $results = $stmt->execute();
    $availableSlots = [];
    $isWithinSlot = false;
    
    while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
        // Normalize slot times to HH:MM
        $slotStart = date('H:i', strtotime($row['start_time']));
        $slotEnd = date('H:i', strtotime($row['end_time']));
        
        $availableSlots[] = [
            'start' => $slotStart,
            'end' => $slotEnd
        ];
        
        // Check if booking falls completely within this slot
        if ($bookingStart >= $slotStart && $bookingEnd <= $slotEnd) {
            $isWithinSlot = true;
        }
    }
    $stmt->close();
    
    // If no slots are defined for this specific day, resource is not available on this day
    if (empty($availableSlots)) {
        $dayName = ucfirst($dayOfWeek);
        
        // Get all days that DO have slots for helpful message
        $daysQuery = "SELECT DISTINCT day_of_week FROM resource_availability WHERE resource_id = :resourceId ORDER BY 
                      CASE LOWER(day_of_week) 
                        WHEN 'monday' THEN 1 WHEN 'tuesday' THEN 2 WHEN 'wednesday' THEN 3 
                        WHEN 'thursday' THEN 4 WHEN 'friday' THEN 5 WHEN 'saturday' THEN 6 WHEN 'sunday' THEN 7 
                      END";
        $daysStmt = $db->prepare($daysQuery);
        $daysStmt->bindValue(':resourceId', $resourceId, SQLITE3_INTEGER);
        $daysResult = $daysStmt->execute();
        $availableDays = [];
        while ($dayRow = $daysResult->fetchArray(SQLITE3_ASSOC)) {
            $availableDays[] = ucfirst($dayRow['day_of_week']);
        }
        $daysStmt->close();
        
        return [
            'valid' => false, 
            'message' => "This resource is not available on {$dayName}s. Available days: " . implode(', ', $availableDays),
            'available_slots' => [],
            'has_restrictions' => true
        ];
    }
    
    if (!$isWithinSlot) {
        // Build a friendly message showing available slots
        $slotStrings = array_map(function($slot) {
            return $slot['start'] . ' - ' . $slot['end'];
        }, $availableSlots);
        
        $dayName = ucfirst($dayOfWeek);
        return [
            'valid' => false,
            'message' => "Your booking time ({$bookingStart} - {$bookingEnd}) is outside the allowed time slots. Available times on {$dayName}s: " . implode(', ', $slotStrings),
            'available_slots' => $availableSlots,
            'has_restrictions' => true
        ];
    }
    
    return ['valid' => true, 'message' => 'Booking time is valid.', 'available_slots' => $availableSlots, 'has_restrictions' => true];
}

/**
 * Checks if a booking conflicts with existing confirmed bookings for the same resource.
 * 
 * @param SQLite3 $db The database connection object.
 * @param int $resourceId The ID of the resource being booked.
 * @param string $startDateTime The start datetime (YYYY-MM-DD HH:MM:SS format).
 * @param string $endDateTime The end datetime (YYYY-MM-DD HH:MM:SS format).
 * @param int|null $excludeBookingId Optional booking ID to exclude (for updates).
 * @return array ['has_conflict' => bool, 'message' => string, 'conflicting_bookings' => array]
 */
function checkBookingConflict(SQLite3 $db, $resourceId, $startDateTime, $endDateTime, $excludeBookingId = null) {
    $query = "SELECT b.booking_id, b.start_time, b.end_time, u.first_name, u.last_name
              FROM Bookings b
              LEFT JOIN Users u ON b.user_id = u.user_id
              WHERE b.resource_id = :resourceId
              AND b.status = 'Confirmed'
              AND (b.start_time < :endTime AND b.end_time > :startTime)";
    
    if ($excludeBookingId !== null) {
        $query .= " AND b.booking_id != :excludeId";
    }
    
    $stmt = $db->prepare($query);
    if (!$stmt) {
        return ['has_conflict' => true, 'message' => 'Database error checking conflicts.', 'conflicting_bookings' => []];
    }
    
    $stmt->bindValue(':resourceId', $resourceId, SQLITE3_INTEGER);
    $stmt->bindValue(':startTime', $startDateTime, SQLITE3_TEXT);
    $stmt->bindValue(':endTime', $endDateTime, SQLITE3_TEXT);
    
    if ($excludeBookingId !== null) {
        $stmt->bindValue(':excludeId', $excludeBookingId, SQLITE3_INTEGER);
    }
    
    $results = $stmt->execute();
    $conflicts = [];
    
    while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
        $conflicts[] = [
            'booking_id' => $row['booking_id'],
            'start_time' => $row['start_time'],
            'end_time' => $row['end_time'],
            'booked_by' => $row['first_name'] ? ($row['first_name'] . ' ' . $row['last_name']) : 'Another user'
        ];
    }
    $stmt->close();
    
    if (!empty($conflicts)) {
        $conflictTimes = array_map(function($c) {
            $start = date('H:i', strtotime($c['start_time']));
            $end = date('H:i', strtotime($c['end_time']));
            return "{$start} - {$end}";
        }, $conflicts);
        
        return [
            'has_conflict' => true,
            'message' => 'This time slot is already booked. Existing booking(s): ' . implode(', ', $conflictTimes),
            'conflicting_bookings' => $conflicts
        ];
    }
    
    return ['has_conflict' => false, 'message' => 'No conflicts found.', 'conflicting_bookings' => []];
}

/**
 * Gets available time slots for a resource on a specific date, excluding already booked times.
 * If a resource has NO availability slots defined at all, it's freely bookable (no time restrictions).
 * 
 * @param SQLite3 $db The database connection object.
 * @param int $resourceId The ID of the resource.
 * @param string $date The date to check (YYYY-MM-DD format).
 * @return array List of available time ranges.
 */
function getAvailableTimeSlotsForDate(SQLite3 $db, $resourceId, $date) {
    $dayOfWeek = strtolower(date('l', strtotime($date)));
    
    // First, check if this resource has ANY availability slots defined at all
    $checkQuery = "SELECT COUNT(*) as slot_count FROM resource_availability WHERE resource_id = :resourceId";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindValue(':resourceId', $resourceId, SQLITE3_INTEGER);
    $checkResult = $checkStmt->execute();
    $checkRow = $checkResult->fetchArray(SQLITE3_ASSOC);
    $checkStmt->close();
    
    $hasRestrictions = ($checkRow['slot_count'] > 0);
    
    // Get existing bookings for this date (needed regardless of restrictions)
    $bookingsQuery = "SELECT start_time, end_time 
                      FROM Bookings 
                      WHERE resource_id = :resourceId 
                      AND status = 'Confirmed'
                      AND DATE(start_time) = :date
                      ORDER BY start_time";
    
    $stmt = $db->prepare($bookingsQuery);
    $stmt->bindValue(':resourceId', $resourceId, SQLITE3_INTEGER);
    $stmt->bindValue(':date', $date, SQLITE3_TEXT);
    $results = $stmt->execute();
    
    $bookedSlots = [];
    while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
        $bookedSlots[] = [
            'start' => date('H:i', strtotime($row['start_time'])),
            'end' => date('H:i', strtotime($row['end_time']))
        ];
    }
    $stmt->close();
    
    // If NO slots are defined at all, resource is freely bookable
    if (!$hasRestrictions) {
        return [
            'day_available' => true,
            'has_restrictions' => false,
            'available_slots' => [],
            'booked_slots' => $bookedSlots,
            'day_of_week' => ucfirst($dayOfWeek),
            'message' => 'No time restrictions - book any time (check for conflicts only)'
        ];
    }
    
    // Resource HAS restrictions - get defined availability slots for this day
    $availQuery = "SELECT start_time, end_time 
                   FROM resource_availability 
                   WHERE resource_id = :resourceId 
                   AND LOWER(day_of_week) = :dayOfWeek
                   ORDER BY start_time";
    
    $stmt = $db->prepare($availQuery);
    $stmt->bindValue(':resourceId', $resourceId, SQLITE3_INTEGER);
    $stmt->bindValue(':dayOfWeek', $dayOfWeek, SQLITE3_TEXT);
    $results = $stmt->execute();
    
    $availableSlots = [];
    while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
        $availableSlots[] = [
            'start' => date('H:i', strtotime($row['start_time'])),
            'end' => date('H:i', strtotime($row['end_time']))
        ];
    }
    $stmt->close();
    
    // If no slots defined for this specific day
    if (empty($availableSlots)) {
        // Get days that DO have availability
        $daysQuery = "SELECT DISTINCT day_of_week FROM resource_availability WHERE resource_id = :resourceId";
        $daysStmt = $db->prepare($daysQuery);
        $daysStmt->bindValue(':resourceId', $resourceId, SQLITE3_INTEGER);
        $daysResult = $daysStmt->execute();
        $availableDays = [];
        while ($dayRow = $daysResult->fetchArray(SQLITE3_ASSOC)) {
            $availableDays[] = ucfirst($dayRow['day_of_week']);
        }
        $daysStmt->close();
        
        return [
            'day_available' => false, 
            'has_restrictions' => true,
            'available_slots' => [], 
            'booked_slots' => $bookedSlots,
            'day_of_week' => ucfirst($dayOfWeek),
            'available_days' => $availableDays,
            'message' => 'Resource not available on ' . ucfirst($dayOfWeek) . 's. Try: ' . implode(', ', $availableDays)
        ];
    }
    
    return [
        'day_available' => true,
        'has_restrictions' => true,
        'available_slots' => $availableSlots,
        'booked_slots' => $bookedSlots,
        'day_of_week' => ucfirst($dayOfWeek)
    ];
}

//booking related code above

//if you wish to connect to a hosted mysql instance uncomment this ad fill out accordingly
// $servername = "localhost"; //use school server IP if connecting to school server over a newtork connection, or set to  'localhost' for XAMPP
// $username = "root";       //XAMPP default username
// $password = "";           //XAMPP default password
// $dbname = "schooldatabase";
// //establish a connection
// $conn = new mysqli($servername, $username, $password, $dbname);
// //ceck if. connection is successful
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }
// return $conn;
?>
