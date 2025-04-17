<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $purchase_id = $_POST['purchase_id'];
    $amount = $_POST['amount'];
    $payment_method = $_POST['payment_method'];
    $payment_date = $_POST['payment_date'];
    
    try {
        // First verify the purchase exists and get the balance
        $stmt = $pdo->prepare("SELECT total_amount - COALESCE(SUM(amount), 0) as balance 
                              FROM purchases 
                              LEFT JOIN payments ON purchases.purchase_id = payments.purchase_id
                              WHERE purchases.purchase_id = ?");
        $stmt->execute([$purchase_id]);
        $balance = $stmt->fetchColumn();
        
        if ($balance === false) {
            throw new Exception("Invalid purchase selected");
        }
        
        // Record the payment
        $stmt = $pdo->prepare("INSERT INTO payments 
                             (purchase_id, amount, payment_method, payment_date) 
                             VALUES (?, ?, ?, ?)");
        $stmt->execute([$purchase_id, $amount, $payment_method, $payment_date]);
        
        header("Location: ../purchases.php?success=Payment+recorded+successfully");
        exit();
    } catch (Exception $e) {
        header("Location: ../purchases.php?error=" . urlencode($e->getMessage()));
        exit();
    }
}