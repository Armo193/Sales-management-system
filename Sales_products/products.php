<?php
include 'db.php';

// Handle product deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);
    header("Location: products.php");
    exit;
}

// Fetch all products
$stmt = $pdo->query("SELECT * FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Product List</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .actions a { margin-right: 10px; text-decoration: none; }
        .sell-btn { background: #2196F3; color: white; padding: 5px 10px; border-radius: 3px; }
        .delete-btn { background: #f44336; color: white; padding: 5px 10px; border-radius: 3px; }
    </style>
    <link rel="stylesheet" href="clients.css">
</head>
<body>
    <h1>Product List</h1>
    <p><a href="add_product.php">Add New Product</a></p>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
            <tr>
                <td><?= htmlspecialchars($product['id']) ?></td>
                <td><?= htmlspecialchars($product['name']) ?></td>
                <td><?= htmlspecialchars($product['description']) ?></td>
                <td>Sh <?= number_format($product['price'], 2) ?></td>
                <td><?= htmlspecialchars($product['quantity']) ?></td>
                <td class="actions">
                    <a href="sell_product.php?id=<?= $product['id'] ?>" class="sell-btn">Sell</a>
                    <a href="products.php?delete=<?= $product['id'] ?>" class="delete-btn" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php include 'footer.php'; ?>    
</body>
</html>
