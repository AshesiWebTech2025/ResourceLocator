<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once dirname(__DIR__) . '/backend/dbConnector.php';

try{
    $db = connectDB();
    $name = sanitizeInput($_POST['name']);
    $capacity = sanitizeInput($_POST['capacity']);
    $description = sanitizeInput($_POST['description']);
    

}