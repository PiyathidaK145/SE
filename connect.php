<?php

    $base_url = "http://localhost/SE-main/";

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "se";

    // Create connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    //echo "Connected successfully";
    define('WP', 'se2025');

?>