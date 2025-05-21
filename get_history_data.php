<?php
session_start();
require_once 'config.php'; // Include your database connection file

// Kontrola, či je používateľ prihlásený a je administrátor
if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] !== true || 
    !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

try {
    $sql = "SELECT
                u.username AS user,
                h.operation,
                h.time,
                h.city,
                h.state,
                h.platform
            FROM
                history h
            JOIN
                users u ON h.user_id = u.id
            ORDER BY
                h.time DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    
    $historyData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Set Content-Type to application/json
    header('Content-Type: application/json');
    
    // Output the data as JSON
    echo json_encode($historyData);
} catch (PDOException $e) {
    // Log error and return empty JSON array instead of exposing error details
    error_log("Database error in get_history_data.php: " . $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode([]);
}
?>