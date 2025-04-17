<?php
include 'db.php';

// Fetch sales data joined with product details
$stmt = $pdo->query("
    SELECT s.id, s.product_id, s.quantity_sold, s.sale_date,
           p.name AS product_name, p.price
    FROM sales s
    JOIN products p ON s.product_id = p.id
    ORDER BY s.sale_date DESC
");

$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total revenue
$totalRevenue = 0;
foreach ($sales as $sale) {
    $totalRevenue += $sale['price'] * $sale['quantity_sold'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sales Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .total { font-weight: bold; margin-top: 20px; font-size: 1.2em; }
        a { text-decoration: none; color: #2196F3; }
    </style>
    <link rel="stylesheet" href="clients.css">
</head>
<body>
    <h1>Sales Report</h1>
    <p><a href="products.php">Back to Product List</a></p>

    <?php if (count($sales) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Sale ID</th>
                    <th>Product</th>
                    <th>Quantity Sold</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sales as $sale): ?>
                <tr>
                    <td><?= $sale['id'] ?></td>
                    <td><?= htmlspecialchars($sale['product_name']) ?></td>
                    <td><?= $sale['quantity_sold'] ?></td>
                    <td>Sh.<?= number_format($sale['price'], 2) ?></td>
                    <td>Sh.<?= number_format($sale['price'] * $sale['quantity_sold'], 2) ?></td>
                    <td><?= $sale['sale_date'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total">
            Total Revenue: Ksh <?= number_format($totalRevenue, 2) ?>
        </div>
    <?php else: ?>
        <p>No sales recorded yet.</p>
    <?php endif; ?>

    <?php include 'footer.php'; ?>
</body>
</html>
