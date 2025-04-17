<?php
header('Content-Type: application/json');
require_once '../db.php';

// Check if request method is DELETE
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}

// Get the raw input data
$input = file_get_contents('php://input');
parse_str($input, $params);

// Validate required fields
if (empty($params['clientId'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Client ID is required']);
    exit;
}

try {
    // Get client data first to delete associated images
    $stmt = $pdo->prepare("SELECT * FROM clients WHERE client_id = ?");
    $stmt->execute([$params['clientId']]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$client) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Client not found']);
        exit;
    }

    // Delete associated images
    $uploadsDir = '../images/clients/';
    $images = [
        $client['id_front_image'],
        $client['id_back_image'],
        $client['profile_image']
    ];

    foreach ($images as $image) {
        if (!empty($image) && file_exists($uploadsDir . $image)) {
            unlink($uploadsDir . $image);
        }
    }

    // Delete client from database
    $stmt = $pdo->prepare("DELETE FROM clients WHERE client_id = ?");
    $stmt->execute([$params['clientId']]);

    echo json_encode(['success' => true, 'message' => 'Client deleted successfully']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>