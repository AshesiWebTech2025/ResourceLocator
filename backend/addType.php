<?php
require_once 'dbConnector.php';
echo "Database path: " . realpath("../setup/mockDatabase.db") . "<br>";


$db = connectDB();

if ($db === null) {
    http_response_code(500); 
    die("Error: Could not connect to the database.");
}

// Set content type to plain text for simple AJAX response messages
header('Content-Type: text/plain');

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



$sql = "INSERT INTO resource_types (type_name) VALUES (:type_name)";

try {
    $stmt = $db->prepare($sql);
    
    $stmt->bindValue(':type_name', $typeName, SQLITE3_TEXT);
    
    $result = $stmt->execute();
    
    if ($result) {
        echo "Resource Type '$typeName' added successfully.";
        
    } else {
        $errorCode = $db->lastErrorCode();
        $errorMsg = $db->lastErrorMsg();

        if ($errorCode === 19) { 
            http_response_code(409); 
            die("Error: The resource type '$typeName' already exists.");
        }
        
        http_response_code(500);
        die("Database Error: Failed to insert data. Message: $errorMsg");
    }

    $stmt->close();
    $db->close();

} catch (Exception $e) {
    http_response_code(500);
    error_log("Resource Type Insertion Failed: " . $e->getMessage());
    die("An unexpected error occurred during insertion.");
}

?>