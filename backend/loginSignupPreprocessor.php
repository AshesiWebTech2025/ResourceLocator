<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
// The path is correct because this file is in 'backend/' and dbConnector is also in 'backend/'
require_once 'dbConnector.php';

// Check if the server request is POST; if not, redirect to the main login page
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../frontend/login_signup.php");
    exit();
}

$action = $_POST["action"] ?? "";
$passedValidation = true;
$message = "";

if ($action === "login") {
    // --- LOGIN LOGIC ---
    $ashesi_email = htmlspecialchars(trim($_POST["login_email"] ?? ""));
    $password = $_POST["login_password"] ?? '';

    if (empty($ashesi_email) || empty($password)) {
        $message = "Ensure all fields are filled for login.";
        $passedValidation = false;
    } elseif (!filter_var($ashesi_email, FILTER_VALIDATE_EMAIL) || !str_ends_with($ashesi_email, "@ashesi.edu.gh")) {
        $message = "Enter a valid Ashesi email address (ending with @ashesi.edu.gh).";
        $passedValidation = false;
    }
    
    if ($passedValidation) {
        $db = connectDB();
        if ($db) {
            try {
                // IMPORTANT: Updated query to select 'name' and 'role' (previously user_type)
                $stmt = $db->prepare('SELECT user_id, password_hash, name, role FROM users WHERE ashesi_email = :email AND is_active = 1');
                $stmt->bindValue(':email', $ashesi_email, SQLITE3_TEXT);
                $result = $stmt->execute();
                $user = $result->fetchArray(SQLITE3_ASSOC);
                $db->close();

                if ($user) {
                    $passwordValid = password_verify($password, $user['password_hash']);
                    if ($passwordValid) {
                        // Login successful
                        $_SESSION['user_id'] = $user['user_id'];
                        $_SESSION['role'] = $user['role']; // Using 'role' from the new schema
                        $_SESSION['name'] = $user['name'];
                        $_SESSION['is_logged_in'] = true;
                        $_SESSION['message'] = "Welcome back, " . $user['name'] . "!";
                        $_SESSION['message_type'] = "success";
                        
                        // Routing based on user role (you may need to adjust these file paths)
                        switch ($user["role"]) {
                            case "Student":
                                header("Location: ../studentDashboard.php");
                                exit();
                            case "Faculty":
                            case "Staff":
                                header("Location: ../facultyStaffDashboard.php"); // Example
                                exit();
                            case "Admin":
                                header("Location: ../adminDashboard.php"); // Example
                                exit();
                            default:
                                header('Location: ../dashboard.php');
                                exit();
                        }
                    } else {
                        $message = "Invalid email or password.";
                    }
                } else {
                    $message = "Invalid email or password, or account is inactive.";
                }
            } catch (Exception $e) {
                $message = "A system error occurred during login. Please try again.";
                error_log("Login error: " . $e->getMessage());
            }
        } else {
            $message = "Server maintenance in progress. Please try again later.";
        }
    }

    // Redirect back to the login form if login fails
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = 'error';
    header('Location: ../frontend/login_signup.php');
    exit();

} elseif ($action === "signup") {
    // --- SIGNUP LOGIC ---
    $name = htmlspecialchars(trim($_POST["name"] ?? ""));
    $ashesi_email = htmlspecialchars(trim($_POST["email"] ?? ""));
    $password = $_POST["password"] ?? '';
    $cpassword = $_POST["confirm_password"] ?? '';
    $role = $_POST["role"] ?? '';

    // Validation
    if (empty($name) || empty($ashesi_email) || empty($password) || empty($cpassword) || empty($role)) {
        $message = "All fields are required.";
        $passedValidation = false;
    } elseif ($password !== $cpassword) {
        $message = "Passwords do not match.";
        $passedValidation = false;
    } elseif (!filter_var($ashesi_email, FILTER_VALIDATE_EMAIL) || !str_ends_with($ashesi_email, "@ashesi.edu.gh")) {
        $message = "Enter a valid Ashesi email address (ending with @ashesi.edu.gh).";
        $passedValidation = false;
    }

    // Password strength check (simple server-side check)
    if ($passedValidation && strlen($password) < 8) {
        $message = "Password must be at least 8 characters long.";
        $passedValidation = false;
    }

    // Role validation against expected values
    $valid_roles = ['Student', 'Faculty', 'Staff', 'Visitor'];
    if ($passedValidation && !in_array($role, $valid_roles)) {
        $message = "Invalid role selected.";
        $passedValidation = false;
    }


    if ($passedValidation) {
        $db = connectDB();
        if ($db) {
            try {
                // Check if user already exists
                $checkStmt = $db->prepare('SELECT user_id FROM users WHERE ashesi_email = :email');
                $checkStmt->bindValue(':email', $ashesi_email, SQLITE3_TEXT);
                $existingUser = $checkStmt->execute()->fetchArray(SQLITE3_ASSOC);

                if ($existingUser) {
                    $message = "An account with this email already exists.";
                } else {
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                    
                    // Insert new user
                    $insertStmt = $db->prepare('INSERT INTO users (name, ashesi_email, password_hash, role) 
                                                VALUES (:name, :email, :pass, :role)');
                    
                    $insertStmt->bindValue(':name', $name, SQLITE3_TEXT);
                    $insertStmt->bindValue(':email', $ashesi_email, SQLITE3_TEXT);
                    $insertStmt->bindValue(':pass', $password_hash, SQLITE3_TEXT);
                    $insertStmt->bindValue(':role', $role, SQLITE3_TEXT); // Storing the selected role
                    
                    $success = $insertStmt->execute();

                    if ($success) {
                        $message = "Account created successfully! Please log in.";
                        $_SESSION['message_type'] = "success";
                        $db->close();
                        header('Location: ../login_signup.php'); // Redirect to login page
                        exit();
                    } else {
                        $message = "Registration failed due to a database error.";
                        error_log("DB Insert Failed for new user: " . $db->lastErrorMsg());
                    }
                }
            } catch (Exception $e) {
                $message = "A system error occurred during registration. Please try again.";
                error_log("Registration error: " . $e->getMessage());
            }
            $db->close();
        } else {
            $message = "Server maintenance in progress. Please try again later.";
        }
    }

    // Redirect back to the signup form if registration fails
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = 'error';
    header('Location: ../frontend/login_signup.php?view=signup'); // Append ?view=signup to show the signup form
    exit();

} else {
    // Unknown action
    $_SESSION['message'] = "Invalid request action.";
    $_SESSION['message_type'] = 'error';
    header('Location: ../frontend/login_signup.php');
    exit();
}
?>