<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'dbConnector.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    die("Invalid request method.");
}

$name = trim($_POST['name'] ?? '');
$type = trim($_POST['type'] ?? '');
$capacity = intval($_POST['capacity'] ?? 0);
$description = trim($_POST['description'] ?? '');
$latitude = trim($_POST['latitude'] ?? '');
$longitude = trim($_POST['longitude'] ?? '');

if (!$name || !$type || !$description || !$latitude || !$longitude) {
    http_response_code(400);
    die("All fields including map location are required.");
}

$db = connectDB();
if (!$db) {
    http_response_code(500);
    die("Database connection failed.");
}

// Insert into database
$sql = "INSERT INTO resources (name, type, capacity, description, latitude, longitude)
        VALUES (:name, :type, :capacity, :description, :lat, :lng)";

try {
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':name', $name, SQLITE3_TEXT);
    $stmt->bindValue(':type', $type, SQLITE3_TEXT);
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