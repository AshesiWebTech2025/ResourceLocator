<?php 
require_once 'dbConnector.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    die("Invalid request method.");
}

$name = htmlspecialchars(trim($_POST['name'] ?? ''));
$type = trim($_POST['type'] ?? '');
$capacity = intval($_POST['capacity'] ?? 0);
$description = htmlspecialchars(trim($_POST['description'] ?? ''));
$latitude = isset($_POST['latitude']) ? round(floatval($_POST['latitude']), 5) : null;
$longitude = isset($_POST['longitude']) ? round(floatval($_POST['longitude']), 5) : null;

if (!$name ===""|| !$type==="" || !$description==="" || !$latitude ===null|| !$longitude === null) {
    http_response_code(400);
    die("All fields including map location are required.");
}

$db = connectDB();
if (!$db) {
    http_response_code(500);
    die("Database connection failed.");
}


try {
    // Get type_id from resource_types
    $typeStmt = $db->prepare("SELECT type_id FROM resource_types WHERE type_name = :type");
    $typeStmt->bindValue(':type', $type, SQLITE3_TEXT);
    $typeResult = $typeStmt->execute();
    $typeRow = $typeResult->fetchArray(SQLITE3_ASSOC);

    if (!$typeRow) {
        http_response_code(400);
        die("Invalid resource type.");
    }

    $type_id = $typeRow['type_id'];


    // Insert into database
    $sql = "INSERT INTO resources (type_id, name, capacity, description, latitude, longitude)
        VALUES (:type_id, :name, :capacity, :description, :lat, :lng)";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':type_id', $type_id, SQLITE3_TEXT);
    $stmt->bindValue(':name', $name, SQLITE3_TEXT);
    $stmt->bindValue(':capacity', $capacity, SQLITE3_INTEGER);
    $stmt->bindValue(':description', $description, SQLITE3_TEXT);
    $stmt->bindValue(':lat', $latitude, SQLITE3_FLOAT);
    $stmt->bindValue(':lng', $longitude, SQLITE3_FLOAT);

    $result = $stmt->execute();
    if ($result) {
        echo "Resource '$name' added successfully!";
    } else {
        http_response_code(500);
        die("Database error: " . $db->lastErrorMsg());
    }

    $stmt->close();
    $db->close();

} catch (Exception $e) {
    http_response_code(500);
    die("Exception: " . $e->getMessage());
}
?>

