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
$requiredFields = ['clientName', 'clientIdNumber', 'clientPhone', 'clientAddress'];
foreach ($requiredFields as $field) {
    if (empty($_POST[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
        exit;
    }
}

try {
    // Handle file uploads
    $uploadsDir = '../images/clients/';
    $idFrontImage = '';
    $idBackImage = '';
    $profileImage = '';

    // Upload ID front image
    if (isset($_FILES['clientIdFront']) && $_FILES['clientIdFront']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['clientIdFront']['name'], PATHINFO_EXTENSION);
        $idFrontImage = 'id_front_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['clientIdFront']['tmp_name'], $uploadsDir . $idFrontImage);
    }

    // Upload ID back image
    if (isset($_FILES['clientIdBack']) && $_FILES['clientIdBack']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['clientIdBack']['name'], PATHINFO_EXTENSION);
        $idBackImage = 'id_back_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['clientIdBack']['tmp_name'], $uploadsDir . $idBackImage);
    }

    // Upload profile image
    if (isset($_FILES['clientProfile']) && $_FILES['clientProfile']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['clientProfile']['name'], PATHINFO_EXTENSION);
        $profileImage = 'profile_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['clientProfile']['tmp_name'], $uploadsDir . $profileImage);
    }

    // Insert client into database
    $stmt = $pdo->prepare("INSERT INTO clients 
        (name, id_number, phone, address, id_front_image, id_back_image, profile_image) 
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $_POST['clientName'],
        $_POST['clientIdNumber'],
        $_POST['clientPhone'],
        $_POST['clientAddress'],
        $idFrontImage,
        $idBackImage,
        $profileImage
    ]);

    echo json_encode(['success' => true, 'message' => 'Client added successfully']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>