<?php 
// Include database connection first
require_once 'db.php';

// Check if client_id is provided for focused operations
$client_id = isset($_GET['client_id']) ? intval($_GET['client_id']) : null;

// Handle delete actions
if (isset($_GET['delete_purchase'])) {
    $purchase_id = intval($_GET['delete_purchase']);
    try {
        // First delete associated payments
        $stmt = $pdo->prepare("DELETE FROM payments WHERE purchase_id = ?");
        $stmt->execute([$purchase_id]);
        
        // Then delete the purchase
        $stmt = $pdo->prepare("DELETE FROM purchases WHERE purchase_id = ?");
        $stmt->execute([$purchase_id]);
        
        header("Location: purchases.php?success=Purchase+deleted+successfully");
        exit();
    } catch (PDOException $e) {
        header("Location: purchases.php?error=" . urlencode($e->getMessage()));
        exit();
    }
}

if (isset($_GET['delete_payment'])) {
    $payment_id = intval($_GET['delete_payment']);
    try {
        $stmt = $pdo->prepare("DELETE FROM payments WHERE payment_id = ?");
        $stmt->execute([$payment_id]);
        
        header("Location: purchases.php?success=Payment+deleted+successfully");
        exit();
    } catch (PDOException $e) {
        header("Location: purchases.php?error=" . urlencode($e->getMessage()));
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client's Purchase Management</title>
    <link rel="stylesheet" href="clients.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .action-btns {
            display: flex;
            gap: 5px;
        }
        .btn-delete {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
        }
        .btn-delete:hover {
            background-color: #d32f2f;
        }
        .payment-details {
            margin-top: 5px;
            font-size: 0.9em;
            color: #555;
        }
    </style>
</head>
<body>

<div class="client-management">
    <h1>Client Purchase Management page.</h1>
    
    <!-- Success/Error Messages -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
    <?php elseif (isset($_GET['error'])): ?>
        <div class="alert alert-error">Error: <?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>
    
    <!-- Main Purchase Sections -->
    <div class="section">
            <h2>Record New Purchase</h2>
            <form action="api/add_purchase.php" method="POST">
                <div class="form-group">
                    <label for="clientSelect">Select Client:</label>
                    <select id="clientSelect" name="client_id" required>
                        <option value="">-- Select Client --</option>
                        <?php
                        $stmt = $pdo->query("SELECT client_id, name, id_number FROM clients ORDER BY name");
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $selected = $client_id == $row['client_id'] ? 'selected' : '';
                            echo "<option value='{$row['client_id']}' $selected>{$row['name']} ({$row['id_number']})</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="productName">Product Name:</label>
                    <input type="text" id="productName" name="product_name" required>
                </div>
                
                <div class="form-group">
                    <label for="productPrice">Unit Price:</label>
                    <input type="number" id="productPrice" name="product_price" min="0" step="0.01" required>
                </div>
                
                <div class="form-group">
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" min="1" value="1" required>
                </div>
                
                <div class="form-group">
                    <label for="totalAmount">Total Amount:</label>
                    <input type="number" id="totalAmount" name="total_amount" min="0" step="0.01" readonly>
                </div>
                
                <button type="submit" class="btn">Record Purchase</button>
            </form>
        </div>
        
        
        <!-- Record Payment Form -->
        <div class="section">
            <h2>Record Payment</h2>
            <form action="api/record_payment.php" method="POST">
                <!-- ... (existing form content remains the same) ... -->
            </form>
        </div>
    </div>
    
    <!-- Purchase Reports -->
    <div class="report-section">
        <h2>Purchase Reports</h2>
        
        <!-- Filter Form -->
        <form method="GET" action="purchases.php" class="filter-form">
            <!-- ... (existing filter form remains the same) ... -->
        </form>
        
        <!-- Report Table -->
        <table class="report-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Client Name</th>
                    <th>ID Number</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total Amount</th>
                    <th>Amount Paid</th>
                    <th>Balance</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT 
                            p.purchase_id,
                            DATE_FORMAT(p.purchase_date, '%Y-%m-%d') as purchase_date,
                            c.name as client_name,
                            c.id_number,
                            p.product_name,
                            p.quantity,
                            p.unit_price,
                            p.total_amount,
                            COALESCE(SUM(py.amount), 0) as paid_amount,
                            (p.total_amount - COALESCE(SUM(py.amount), 0)) as balance,
                            CASE 
                                WHEN (p.total_amount - COALESCE(SUM(py.amount), 0)) <= 0 THEN 'Paid'
                                ELSE 'Pending'
                            END as status
                        FROM purchases p
                        JOIN clients c ON p.client_id = c.client_id
                        LEFT JOIN payments py ON p.purchase_id = py.purchase_id";
                
                if ($client_id) {
                    $sql .= " WHERE p.client_id = ?";
                    $params = [$client_id];
                }
                
                $sql .= " GROUP BY p.purchase_id
                          ORDER BY p.purchase_date DESC";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params ?? []);
                
                if ($stmt->rowCount() > 0) {
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>
                                <td>{$row['purchase_date']}</td>
                                <td>{$row['client_name']}</td>
                                <td>{$row['id_number']}</td>
                                <td>{$row['product_name']}</td>
                                <td>{$row['quantity']}</td>
                                <td>" . number_format($row['unit_price'], 2) . "</td>
                                <td>" . number_format($row['total_amount'], 2) . "</td>
                                <td>" . number_format($row['paid_amount'], 2) . "</td>
                                <td>" . number_format($row['balance'], 2) . "</td>
                                <td class='status-{$row['status']}'>{$row['status']}</td>
                                <td class='action-btns'>
                                    <button class='btn-delete' onclick=\"confirmDelete('purchase', {$row['purchase_id']})\">
                                        <i class='fas fa-trash'></i> Delete
                                    </button>
                                </td>
                              </tr>";
                        
                        // Show payment details for this purchase
                        $payment_stmt = $pdo->prepare("SELECT * FROM payments WHERE purchase_id = ? ORDER BY payment_date DESC");
                        $payment_stmt->execute([$row['purchase_id']]);
                        $payments = $payment_stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        if (!empty($payments)) {
                            echo "<tr class='payment-row'>
                                    <td colspan='11'>
                                        <div class='payment-details'>
                                            <strong>Payment History:</strong><br>";
                            foreach ($payments as $payment) {
                                echo "<span>
                                        " . date('Y-m-d', strtotime($payment['payment_date'])) . " - 
                                        " . number_format($payment['amount'], 2) . " ({$payment['payment_method']})
                                        <button class='btn-delete-sm' onclick=\"confirmDelete('payment', {$payment['payment_id']})\">
                                            <i class='fas fa-trash'></i>
                                        </button>
                                      </span><br>";
                            }
                            echo "</div>
                                    </td>
                                  </tr>";
                        }
                    }
                } else {
                    echo "<tr><td colspan='11' class='no-data'>No purchases found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Calculate total amount when price or quantity changes
$(document).ready(function() {
    $('#productPrice, #quantity').on('input', function() {
        let price = parseFloat($('#productPrice').val()) || 0;
        let quantity = parseInt($('#quantity').val()) || 0;
        $('#totalAmount').val((price * quantity).toFixed(2));
    });
    
    // Auto-fill amount paid with the remaining balance
    $('#purchaseSelect').change(function() {
        if (this.value) {
            let optionText = $(this).find('option:selected').text();
            let balanceMatch = optionText.match(/Balance: ([0-9.]+)/);
            if (balanceMatch && balanceMatch[1]) {
                $('#amountPaid').val(balanceMatch[1]);
            }
        }
    });
});

function confirmDelete(type, id) {
    if (confirm(`Are you sure you want to delete this ${type}?`)) {
        if (type === 'purchase') {
            window.location.href = `purchases.php?delete_purchase=${id}`;
        } else if (type === 'payment') {
            window.location.href = `purchases.php?delete_payment=${id}`;
        }
    }
}
</script>

<?php include 'footer.php'; ?>
</body>
</html>