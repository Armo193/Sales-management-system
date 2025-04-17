<?php
header('Content-Type: application/json');
require_once '../db.php';

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}

// Validate required fields
if (empty($_POST['clientId'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Client ID is required']);
    exit;
}

try {
    // Get existing client data
    $stmt = $pdo->prepare("SELECT * FROM clients WHERE client_id = ?");
    $stmt->execute([$_POST['clientId']]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$client) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Client not found']);
        exit;
    }

    // Handle file uploads
    $uploadsDir = '../images/clients/';
    $idFrontImage = $client['id_front_image'];
    $idBackImage = $client['id_back_image'];
    $profileImage = $client['profile_image'];

    // Update ID front image if new one was uploaded
    if (isset($_FILES['clientIdFront']) && $_FILES['clientIdFront']['error'] === UPLOAD_ERR_OK) {
        // Delete old image if exists
        if (!empty($idFrontImage) && file_exists($uploadsDir . $idFrontImage)) {
            unlink($uploadsDir . $idFrontImage);
        }
        
        $ext = pathinfo($_FILES['clientIdFront']['name'], PATHINFO_EXTENSION);
        $idFrontImage = 'id_front_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['clientIdFront']['tmp_name'], $uploadsDir . $idFrontImage);
    }

    // Update ID back image if new one was uploaded
    if (isset($_FILES['clientIdBack']) && $_FILES['clientIdBack']['error'] === UPLOAD_ERR_OK) {
        // Delete old image if exists
        if (!empty($idBackImage) && file_exists($uploadsDir . $idBackImage)) {
            unlink($uploadsDir . $idBackImage);
        }
        
        $ext = pathinfo($_FILES['clientIdBack']['name'], PATHINFO_EXTENSION);
        $idBackImage = 'id_back_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['clientIdBack']['tmp_name'], $uploadsDir . $idBackImage);
    }

    // Update profile image if new one was uploaded
    if (isset($_FILES['clientProfile']) && $_FILES['clientProfile']['error'] === UPLOAD_ERR_OK) {
        // Delete old image if exists
        if (!empty($profileImage) && file_exists($uploadsDir . $profileImage)) {
            unlink($uploadsDir . $profileImage);
        }
        
        $ext = pathinfo($_FILES['clientProfile']['name'], PATHINFO_EXTENSION);
        $profileImage = 'profile_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['clientProfile']['tmp_name'], $uploadsDir . $profileImage);
    }

    // Update client in database
    $stmt = $pdo->prepare("UPDATE clients SET 
        name = ?, 
        id_number = ?, 
        phone = ?, 
        address = ?, 
        id_front_image = ?, 
        id_back_image = ?, 
        profile_image = ? 
        WHERE client_id = ?");
    
    $stmt->execute([
        $_POST['clientName'] ?? $client['name'],
        $_POST['clientIdNumber'] ?? $client['id_number'],
        $_POST['clientPhone'] ?? $client['phone'],
        $_POST['clientAddress'] ?? $client['address'],
        $idFrontImage,
        $idBackImage,
        $profileImage,
        $_POST['clientId']
    ]);

    echo json_encode(['success' => true, 'message' => 'Client updated successfully']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>