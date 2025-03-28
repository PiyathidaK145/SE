<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "asset_db"; // <- ฐานข้อมูลที่คุณใช้

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

mysqli_set_charset($conn, "utf8");
?>
