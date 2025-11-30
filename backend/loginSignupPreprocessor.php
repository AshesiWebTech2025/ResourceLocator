<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'dbConnector.php';
//
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../frontend/login_signup.php");
    exit();
}
$action = $_POST["action"] ?? "";
$passedValidation = true;
$message = "";
if ($action === "login") {
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
                $stmt = $db->prepare('SELECT user_id, password_hash, first_name, last_name, role FROM users WHERE ashesi_email = :email AND is_active = 1');
                $stmt->bindValue(':email', $ashesi_email, SQLITE3_TEXT);
                $result = $stmt->execute();
                $user = $result->fetchArray(SQLITE3_ASSOC);
                $db->close();
                if ($user) {
                    $passwordValid = password_verify($password, $user['password_hash']);
                    if ($passwordValid) {
                        //successful login
                        $_SESSION['user_id'] = $user['user_id'];
                        $_SESSION['role'] = $user['role']; // Using 'role' from the new schema
                        $_SESSION['first_name'] = $user['first_name'];
                        $_SESSION['last_name']= $user['last_name'];
                        $_SESSION['is_logged_in'] = true;
                        $_SESSION['message'] = "Welcome back, " . $user['first_name'] . "!";
                        $_SESSION['message_type'] = "success";
                        
                        //routing users based on roles. for now all rols go to resouceLocator.html 
                        switch ($user["role"]) {
                            case "Student":
                                header("Location: ../frontend/resourceLocator.php");
                                exit();
                            case "Faculty":
                            case "Staff":
                                header("Location: ../frontend/resourceLocator.php");
                                exit();
                            case "Admin":
                                header("Location: ../frontend/resourceAllocator.html");
                                exit();
                            default:
                                header('Location: ../frontend/resourceLocator.php');
                                exit();
                        }
                    } else {
                        $message = "Invalid email or password.";
                    }
                } else {
                    $message = "Invalid email or password, or account is inactive.";
                }
            } catch (Exception $e) {
                //capturing specific error
                $message = "A system error occurred during login: " . $e->getMessage();
                error_log("Login error: " . $e->getMessage());
            }
        } else {
            $message = "Server maintenance in progress. Please try again later.";
        }
    }
    //redirecting user to lgin page if login fails
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = 'error';
    header('Location: ../frontend/login_signup.php');
    exit();
} elseif ($action === "signup") {
    $first_name = htmlspecialchars(trim($_POST["first_name"] ?? ""));
    $last_name = htmlspecialchars(trim($_POST["last_name"] ?? ""));
    $ashesi_email = htmlspecialchars(trim($_POST["email"] ?? ""));
    $password = $_POST["password"] ?? '';
    $cpassword = $_POST["confirm_password"] ?? '';
    $role = $_POST["role"] ?? '';
    //validation
    if (empty($first_name) || empty($last_name) || empty($ashesi_email) || empty($password) || empty($cpassword) || empty($role)) {
        $message = "All fields are required.";
        $passedValidation = false;
    } elseif ($password !== $cpassword) {
        $message = "Passwords do not match.";
        $passedValidation = false;
    } elseif (!filter_var($ashesi_email, FILTER_VALIDATE_EMAIL) || !str_ends_with($ashesi_email, "@ashesi.edu.gh")) {
        $message = "Enter a valid Ashesi email address (ending with @ashesi.edu.gh).";
        $passedValidation = false;
    }
    //additional server side check for password
    if ($passedValidation && strlen($password) < 8) {
        $message = "Password must be at least 8 characters long.";
        $passedValidation = false;
    }
    //role validation
    $valid_roles = ['Student', 'Faculty', 'Staff', 'Visitor'];
    if ($passedValidation && !in_array($role, $valid_roles)) {
        $message = "Invalid role selected.";
        $passedValidation = false;
    }
    if ($passedValidation) {
        $db = connectDB();
        if ($db) {
            try {
                //checking if user already exists in database
                $checkStmt = $db->prepare('SELECT user_id FROM users WHERE ashesi_email = :email');
                $checkStmt->bindValue(':email', $ashesi_email, SQLITE3_TEXT);
                $existingUser = $checkStmt->execute()->fetchArray(SQLITE3_ASSOC);

                if ($existingUser) {
                    $message = "An account with this email already exists.";
                } else {
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                    //insert new user
                    $insertStmt = $db->prepare('INSERT INTO users (first_name, last_name, ashesi_email, password_hash, role) 
                                                VALUES (:first_name, :last_name, :email, :pass, :role)');
                    $insertStmt->bindValue(':first_name', $first_name, SQLITE3_TEXT);
                    $insertStmt->bindValue(':last_name', $last_name, SQLITE3_TEXT);
                    $insertStmt->bindValue(':email', $ashesi_email, SQLITE3_TEXT);
                    $insertStmt->bindValue(':pass', $password_hash, SQLITE3_TEXT);
                    $insertStmt->bindValue(':role', $role, SQLITE3_TEXT);
                    $success = $insertStmt->execute();
                    if ($success) {
                        $message = "Account created successfully! Please log in.";
                        $_SESSION['message_type'] = "success";
                        $db->close();
                        header('Location: ../frontend/login_signup.php'); // Redirect to login page
                        exit();
                    } else {
//capturing specific error message with database
                        $message = "Registration failed: DB Error - " . $db->lastErrorMsg();
                        error_log("DB Insert Failed for new user: " . $db->lastErrorMsg());
                    }
                }
            } catch (Exception $e) {
                //capturing exception errors
                $message = "A system error occurred during registration: " . $e->getMessage();
                error_log("Registration error: " . $e->getMessage());
            }
            //close db only if was successfull open
            if ($db) {
                $db->close();
            }
        } else {
            $message = "Server maintenance in progress. Please try again later.";
        }
    }
    //redirect user. to signup if login failed
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = 'error';
    header('Location: ../frontend/login_signup.php?view=signup'); //append ?view=signup to show the signup form
    exit();

} else {
    //unknown action
    $_SESSION['message'] = "Invalid request action.";
    $_SESSION['message_type'] = 'error';
    header('Location: ../frontend/login_signup.php');
    exit();
}
?>