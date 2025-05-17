<?php

    $servername = "localhost";
    $username = "xplesko";
    $password = "";
    $dbname = "pdfApp";

    $conn = null;

    try {
        $conn = new PDO("mysql:host=:$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
     } catch (PDOException $e) {
        die("Database connection failedL: ". $e->getMessage());
     }

