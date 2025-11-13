<?php
require_once 'dbConnector.php';
echo "Database path: " . realpath("../setup/mockDatabase.db") . "<br>";


$db = connectDB();

if ($db === null) {
    http_response_code(500); // Internal Server Error
    die("Error: Could not connect to the database.");
}

// Set content type to plain text for simple AJAX response messages
header('Content-Type: text/plain');

// 1. Check if the request method is POST and the required data is set
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['type_name'])) {
    http_response_code(400); 
    die("Error: Invalid request method or missing data.");
}

// Sanitize and validate the input
$typeName = trim($_POST['type_name']);

if (empty($typeName)) {
    http_response_code(400);
    die("Error: Resource Type Name cannot be empty.");
}

// Limit the type name length to prevent database errors (matching VARCHAR(100))
if (strlen($typeName) > 100) {
    http_response_code(400);
    die("Error: Resource Type Name is too long.");
}

//Connect to the database


// 3. Prepare and Execute the SQL Insertion
// Using a prepared statement prevents SQL injection attacks.
$sql = "INSERT INTO resource_types (type_name) VALUES (:type_name)";

try {
    // SQLite3 doesn't have a separate prepare/execute method like PDO, 
    // so we use SQLite3Stmt for prepared statements.
    $stmt = $db->prepare($sql);
    
    // Bind the parameter securely
    // We use SQLITE3_TEXT for string data
    $stmt->bindValue(':type_name', $typeName, SQLITE3_TEXT);
    
    // Execute the statement
    $result = $stmt->execute();
    
    if ($result) {
        // Successful insertion
        echo "Resource Type '$typeName' added successfully.";
        
    } else {
        // Handle database execution errors (e.g., UNIQUE constraint violation)
        // If the type already exists, SQLite3::lastErrorMsg() will contain the message.
        $errorCode = $db->lastErrorCode();
        $errorMsg = $db->lastErrorMsg();

        if ($errorCode === 19) { // SQLite code 19 is for UNIQUE constraint violation
            http_response_code(409); // Conflict
            die("Error: The resource type '$typeName' already exists.");
        }
        
        http_response_code(500);
        die("Database Error: Failed to insert data. Message: $errorMsg");
    }

    // Clean up the statement and close the connection
    $stmt->close();
    $db->close();

} catch (Exception $e) {
    http_response_code(500);
    error_log("Resource Type Insertion Failed: " . $e->getMessage());
    die("An unexpected error occurred during insertion.");
}

?>