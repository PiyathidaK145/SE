<?php
date_default_timezone_set('Asia/Bangkok');
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "sys_durable_articles";
    $base_url = "http://localhost/SE-main/";

    // Create connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    if (!defined('WP')) {
        define('WP', 'se2025');
    }
    

?>
