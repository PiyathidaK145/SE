<?php
date_default_timezone_set('Asia/Bangkok');
    $servername = "localhost";
    $username = "root";
    $password = "123456";
    $dbname = "sys_durable_articles";

    // Create connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    define('WP', 'se2025');

    // ป้องกันการกำหนดค่าซ้ำ
    //if (!defined('WP')) {
        //define('WP', 'se2025');
    //}
?>
