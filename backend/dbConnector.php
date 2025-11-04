
<?php
//usiing sqlite to connect to a db file locally
$dbPath = "../setup/mockDatabase.db";

//function to connect to the database stored in setup
function connectDB(){
    global $dbPath;
    $fullPath = $dbPath;

    try{
        if(file_exists($dbPath)){
            $fullPath = realpath($dbPath);
        }
        $db = new SQLite3($dbPath);
        //ensuring the table exists, if not create it
        $db->exec("CREATE TABLE IF NOT EXISTS users (
            user_id INTEGER PRIMARY KEY AUTOINCREMENT,
            fullname TEXT NOT NULL,
            ashesi_email TEXT UNIQUE NOT NULL,
            password_hash TEXT NOT NULL,
            user_type TEXT NOT NULL,
            ashesi_id INTEGER NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        //courses table
        //instructor id here references the fi
        $db->exec("CREATE TABLE IF NOT EXISTS courses (
            course_id INTEGER PRIMARY KEY AUTOINCREMENT,
            course_code TEXT NOT NULL UNIQUE,
            course_name TEXT NOT NULL,
            instructor_name TEXT NOT NULL,
            instructor_id INTEGER NOT NULL, 
            credits INTEGER NOT NULL DEFAULT 1,
            auditor_count INTEGER,
            observer_count INTEGER,
            FOREIGN KEY(instructor_id) REFRENCES users(user_id) ON DELETE CASCADE
        )");
        //course enrollment table
        $db->exec("CREATE TABLE IF NOT EXISTS course_enrollment (
            enrollment_id INTEGER PRIMARY KEY AUTOINCREMENT,
            course_id INTEGER NOT NULL,
            user_id INTEGER NOT NULL,
            status TEXT,
            FOREIGN KEY(course_id) REFERENCES courses(course_id) ON DELETE CASCADE,
            FOREIGN KEY(user_id) REFERENCES users(user_id) ON DELETE CASCADE
        )");
        //session table
        $db->exec("CREATE TABLE IF NOT EXISTS sessions (
            session_id INTEGER PRIMARY KEY AUTOINCREMENT,
            course_id INTEGER NOT NULL,
            session_date TEXT,
            session_type TEXT NOT NULL DEFAULT 'Lecture',
            session_notes TEXT,
            FOREIGN KEY(course_id) REFERENCES courses(course_id) ON DELETE CASCADE
        )");
        
        //attendance table
        $db->exec("CREATE TABLE IF NOT EXISTS attendance (
            attendance_id INTEGER PRIMARY KEY AUTOINCREMENT,
            session_id INTEGER NOT NULL,
            user_id INTEGER NOT NULL,
            status TEXT NOT NULL DEFAULT 'Absent',
            FOREIGN KEY(session_id) REFERENCES sessions(session_id) ON DELETE CASCADE,
            FOREIGN KEY(user_id) REFERENCES users(user_id) ON DELETE CASCADE
        )");
        //attendance issue table
        $db->exec("CREATE TABLE attendance_issues (
            issue_id	INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id	INTEGER NOT NULL,
            course_id	INTEGER NOT NULL,
            session_date	INTEGER NOT NULL,
            issue_description	TEXT NOT NULL,
            status	TEXT DEFAULT 'Pending',
            created_at	TEXT DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(course_id) REFERENCES courses(course_id) ON DELETE CASCADE,
            FOREIGN KEY(user_id) REFERENCES users(user_id) ON DELETE CASCADE
        );");
        return $db; 
    } catch(Exception $e){
        error_log("there was a database connection error: ". $e->getMessage() . $fullPath);
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
