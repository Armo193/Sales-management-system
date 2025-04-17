<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management System</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }
        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
            padding: 20px;
        }
        header {
            background: #35424a;
            color: #ffffff;
            padding-top: 30px;
            min-height: 70px;
            border-bottom: #e8491d 3px solid;
        }
        header h1 {
            text-align: center;
            margin: 0;
            padding-bottom: 10px;
        }
        .main {
            padding: 20px 0;
        }
        .card {
            background: #ffffff;
            margin: 20px 0;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .card h2 {
            color: #35424a;
            margin-top: 0;
        }
        .btn {
            display: inline-block;
            background: #e8491d;
            color: #ffffff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
            transition: background 0.3s ease;
        }
        .btn:hover {
            background: #333;
        }
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        footer {
            background: #35424a;
            color: #ffffff;
            text-align: center;
            padding: 20px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>Planet victoria Sales Management System</h1>
        </div>
    </header>

    <div class="container main">
        <div class="card">
            <h2>Welcome to Planet victoria Sales Management System</h2>
            <p>This system allows us to manage your product inventory, track sales, and maintain your product catalog efficiently.</p>
        </div>

        <div class="features">
            <div class="card">
                <h2>Product Management</h2>
                <p>Add, view, edit, and delete products from your inventory. Keep track of all your available products in one place.</p>
                <a href="products.php" class="btn">Manage Products</a>
            </div>

            <div class="card">
                <h2>Add New Product</h2>
                <p>Add new products to your inventory with details like name, description, price, and quantity.</p>
                <a href="add_product.php" class="btn">Add Product</a>
            </div>

            <div class="card">
                <h2>Sales Report</h2>
                <p>View all sales transactions and track your revenue. Analyze your sales performance over time.</p>
                <a href="sales_report.php" class="btn">View Sales</a>
            </div>
            <div class="card">
                <h2>Clients</h2>
                <p>Add, view, update and delete clients.</p>
                <a href="clients.php" class="btn">Manage clients</a>
            </div>
            <div class="card">
                <h2>Client's Puchases</h2>
                <p>Manage Details of the clients who puchases and have the remaining balance to pay later.</p>
                <a href="purchases.php" class="btn">Clients purchases report</a>
            </div></footer>
        </div>
    </div>

    <footer>
        <div class="container">
            <p>Planet Victoria Sales Management System &copy; 2025</p>
        </div>
    </footer>
   
</body>
</html>