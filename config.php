<?php

    $servername = "localhost";
    $username = "xplesko";
    $password = "xplesko";
    //$username = "xlapos";
    //$password = "mittudomen123";
    $dbname = "pdfApp";

    $conn = null;

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
     } catch (PDOException $e) {
        // Log the actual error for debugging
        error_log("Database connection failed: " . $e->getMessage());
        
        // Die with a user-friendly message
        die("Database connection failed: The application could not connect to the database. Please try again later or contact support.");
     }

