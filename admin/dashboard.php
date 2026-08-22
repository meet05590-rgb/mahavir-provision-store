<?php
// Admin Dashboard
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit;
}

// Sample data
$stats = [
    'total_products' => 45,
    'total_orders' => 234,
    'total_customers' => 89,
    'monthly_revenue' => '₹1,25,000'
];

$recent_orders = [
    ['id' => 'ORD001', 'customer' => 'John Doe', 'amount' => '₹2,500', 'status' => 'Delivered', 'date' => '2026-08-20'],
    ['id' => 'ORD002', 'customer' => 'Jane Smith', 'amount' => '₹1,800', 'status' => 'Processing', 'date' => '2026-08-21'],
    ['id' => 'ORD003', 'customer' => 'Raj Kumar', 'amount' => '₹3,200', 'status' => 'Pending', 'date' => '2026-08-22'],
    ['id' => 'ORD004', 'customer' => 'Priya Singh', 'amount' => '₹950', 'status' => 'Delivered', 'date' => '2026-08-22'],
    ['id' => 'ORD005', 'customer' => 'Amit Patel', 'amount' => '₹1,500', 'status' => 'Shipped', 'date' => '2026-08-22'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Mahavir Provision Store</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: #333;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            height: 100vh;
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            overflow-y: auto;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            z-index: 1000;
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-header h2 {
            font-size: 1.3rem;
            margin-bottom: 0.3rem;
        }

        .sidebar-header p {
            font-size: 0.8rem;
            opacity: 0.8;
        }

        .sidebar-menu {
            list-style: none;
            padding: 1rem 0;
        }

        .sidebar-menu li {
            margin: 0;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.5rem;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .sidebar-menu a:hover {
            background-color: rgba(255,255,255,0.1);
            border-left-color: #f39c12;
            padding-left: 1.2rem;
        }

        .sidebar-menu a.active {
            background-color: rgba(243, 156, 18, 0.2);
            border-left-color: #f39c12;
        }

        /* Main Content */
        .main-content {
            margin-left: 250px;
            padding: 2rem;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
        }

        .header h1 {
            font-size: 1.8rem;
            color: #2c3e50;
        }

        .logout-btn {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 0.7rem 1.5rem;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            border-left: 4px solid #f39c12;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .stat-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #7f8c8d;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: #2c3e50;
        }

        /* Tables */
        .table-section {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .table-section h2 {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            color: #2c3e50;
            border-bottom: 2px solid #f39c12;
            padding-bottom: 0.5rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table thead {
            background-color: #ecf0f1;
        }

        table th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #2c3e50;
            border-bottom: 2px solid #bdc3c7;
        }

        table td {
            padding: 1rem;
            border-bottom: 1px solid #ecf0f1;
        }

        table tbody tr:hover {
            background-color: #f9f9f9;
        }

        .status-badge {
            display: inline-block;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .status-delivered {
            background-color: #d5f4e6;
            color: #27ae60;
        }

        .status-processing {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-pending {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-shipped {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        /* Action Buttons */
        .action-btn {
            padding: 0.5rem 1rem;
            margin-right: 0.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-view {
            background-color: #3498db;
            color: white;
        }

        .btn-view:hover {
            background-color: #2980b9;
        }

        .btn-edit {
            background-color: #f39c12;
            color: white;
        }

        .btn-edit:hover {
            background-color: #e67e22;
        }

        .btn-delete {
            background-color: #e74c3c;
            color: white;
        }

        .btn-delete:hover {
            background-color: #c0392b;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 0;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            table {
                font-size: 0.9rem;
            }

            table th, table td {
                padding: 0.7rem;
            }

            .header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>🛒 Admin Panel</h2>
            <p>Mahavir Provisions</p>
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php" class="active">📊 Dashboard</a></li>
            <li><a href="products.php">📦 Products</a></li>
            <li><a href="orders.php">🛍️ Orders</a></li>
            <li><a href="customers.php">👥 Customers</a></li>
            <li><a href="categories.php">📂 Categories</a></li>
            <li><a href="reports.php">📈 Reports</a></li>
            <li><a href="settings.php">⚙️ Settings</a></li>
            <li><a href="logout.php" style="margin-top: 2rem; color: #e74c3c;">🚪 Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1>Dashboard</h1>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>

        <!-- Welcome Message -->
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 1.5rem; border-radius: 10px; margin-bottom: 2rem;">
            <h2>Welcome, <?php echo ucfirst($_SESSION['admin_username']); ?>! 👋</h2>
            <p>Here's what's happening with your store today.</p>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">📦</div>
                <div class="stat-label">Total Products</div>
                <div class="stat-value"><?php echo $stats['total_products']; ?></div>
            </div>

            <div class="stat-card" style="border-left-color: #3498db;">
                <div class="stat-icon">🛍️</div>
                <div class="stat-label">Total Orders</div>
                <div class="stat-value"><?php echo $stats['total_orders']; ?></div>
            </div>

            <div class="stat-card" style="border-left-color: #27ae60;">
                <div class="stat-icon">👥</div>
                <div class="stat-label">Total Customers</div>
                <div class="stat-value"><?php echo $stats['total_customers']; ?></div>
            </div>

            <div class="stat-card" style="border-left-color: #e74c3c;">
                <div class="stat-icon">💰</div>
                <div class="stat-label">Monthly Revenue</div>
                <div class="stat-value"><?php echo $stats['monthly_revenue']; ?></div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="table-section">
            <h2>Recent Orders</h2>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer Name</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($recent_orders as $order): ?>
                    <tr>
                        <td><strong><?php echo $order['id']; ?></strong></td>
                        <td><?php echo $order['customer']; ?></td>
                        <td><?php echo $order['amount']; ?></td>
                        <td>
                            <span class="status-badge status-<?php echo strtolower(str_replace(' ', '_', $order['status'])); ?>">
                                <?php echo $order['status']; ?>
                            </span>
                        </td>
                        <td><?php echo $order['date']; ?></td>
                        <td>
                            <a href="orders.php" class="action-btn btn-view">View</a>
                            <a href="orders.php" class="action-btn btn-edit">Edit</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
