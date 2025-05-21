<?php
// clear_history.php - Endpoint pre vymazanie histórie z databázy
session_start();

// Kontrola, či je používateľ prihlásený a je administrátor
if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] !== true || 
    !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Načítanie dát z JSON payloadu
$data = json_decode(file_get_contents('php://input'), true);

// Kontrola, či je požiadavka na vymazanie histórie
if (isset($data['action']) && $data['action'] === 'clear_history') {
    require_once 'config.php'; // Pripojenie k databáze
    
    try {
        // Vymazanie všetkých záznamov z tabuľky history
        $sql = "DELETE FROM history";
        $stmt = $conn->prepare($sql);
        $result = $stmt->execute();
        
        if ($result) {
            // Úspešné vymazanie
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'History cleared successfully']);
        } else {
            // Chyba pri vymazávaní
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to clear history']);
        }
    } catch (PDOException $e) {
        // Logovanie chyby
        error_log("Error clearing history: " . $e->getMessage());
        
        // Odpoveď s chybou
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    // Neplatná požiadavka
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
