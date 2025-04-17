<?php
include 'db.php';

if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit;
}

$productId = $_GET['id'];

// Fetch the product
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$productId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Product not found");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $quantityToSell = $_POST['quantity'];
    
    if ($quantityToSell <= 0) {
        die("Quantity must be greater than 0");
    }
    
    if ($quantityToSell > $product['quantity']) {
        die("Not enough stock available");
    }
    
    // Start transaction
    $pdo->beginTransaction();
    
    try {
        // Record the sale
        $stmt = $pdo->prepare("INSERT INTO sales (product_id, quantity_sold) VALUES (?, ?)");
        $stmt->execute([$productId, $quantityToSell]);
        
        // Update product quantity
        $newQuantity = $product['quantity'] - $quantityToSell;
        $stmt = $pdo->prepare("UPDATE products SET quantity = ? WHERE id = ?");
        $stmt->execute([$newQuantity, $productId]);
        
        // If quantity reaches zero, delete the product
        if ($newQuantity <= 0) {
            $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$productId]);
        }
        
        $pdo->commit();
        header("Location: products.php");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error processing sale: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sell Product</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { max-width: 500px; }
        .product-info { margin-bottom: 20px; }
        label { display: block; margin-top: 10px; }
        input { padding: 8px; margin-top: 5px; }
        button { margin-top: 15px; padding: 10px 15px; background: #2196F3; color: white; border: none; cursor: pointer; }
        button:hover { background: #0b7dda; }
    </style>
    <link rel="stylesheet" href="clients.css">
</head>
<body>
    <h1>Sell Product</h1>
    
    <div class="product-info">
        <h2><?= htmlspecialchars($product['name']) ?></h2>
        <p>Description: <?= htmlspecialchars($product['description']) ?></p>
        <p>Price: Sh<?= number_format($product['price'], 2) ?></p>
        <p>Available Quantity: <?= htmlspecialchars($product['quantity']) ?></p>
    </div>
    
    <form method="post">
        <label for="quantity">Quantity to Sell:</label>
        <input type="number" id="quantity" name="quantity" min="1" max="<?= $product['quantity'] ?>" required>
        
        <button type="submit">Complete Sale</button>
    </form>
    
    <p><a href="products.php">Back to Product List</a></p>
    <?php include 'footer.php'; ?>
</body>
</html>
