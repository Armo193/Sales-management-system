<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("INSERT INTO purchases 
                              (client_id, product_name, unit_price, quantity, total_amount) 
                              VALUES (?, ?, ?, ?, ?)");
                              
        $stmt->execute([
            $_POST['client_id'],
            $_POST['product_name'],
            $_POST['product_price'],
            $_POST['quantity'],
            $_POST['total_amount']
        ]);
        
        header("Location: ../purchases.php?success=Purchase+recorded+successfully");
        exit();
    } catch (PDOException $e) {
        header("Location: ../purchases.php?error=" . urlencode($e->getMessage()));
        exit();
    }
}