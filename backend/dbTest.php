<?php
// ... (your existing connectDB function) ...

$db = connectDB();

if ($db) {
    echo "Database connection successful! You are now connected to " . $dbPath . "\n";
    
    // Example test query to show the 'resources' table exists
    $result = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='resources'");
    if ($result->fetchArray()) {
        echo "The 'resources' table exists and is ready.\n";
    } else {
        echo "Error: The 'resources' table was not found or created.\n";
    }
    
    $db->close();
} else {
    echo "Database connection failed. Check error logs.\n";
}
?>
