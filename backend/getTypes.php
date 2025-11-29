<?php
session_start();
require_once "dbConnector.php";

$db = connectDB();
if (!$db) {
    http_response_code(500);
    die(json_encode([]));
}

$result = $db->query("SELECT type_name FROM resource_types ORDER BY type_name ASC");
$types = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $types[] = $row;
}

header("Content-Type: application/json");
echo json_encode($types);
$db->close();
?>
