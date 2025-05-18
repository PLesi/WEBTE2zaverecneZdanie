<?php
require_once 'config.php'; // Include your database connection file

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

$historyData = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $historyData[] = $row;
    }
}

// Close connection
$conn->close();

// Set Content-Type to application/json
header('Content-Type: application/json');

// Output the data as JSON
echo json_encode($historyData);
?>