<?php
// connect.php

function connectToDatabase() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "delivery_cube";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $database);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

// Call this function to get a database connection
// $conn = connectToDatabase();
?>
