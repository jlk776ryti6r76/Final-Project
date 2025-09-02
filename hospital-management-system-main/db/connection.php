<?php
$host = "localhost";
$port = "3307";
$db_name = "city_hospital";
$db_user = "root";
$db_pass = "";

try {
    $conn = new PDO("mysql:host=$host;port=$port;dbname=$db_name", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected!";
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>