<?php
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
            name TEXT NOT NULL,                     
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
