<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../auth/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

requireRole('cashier');

$conn = getDBConnection();

// Get all customers
$customersQuery = "SELECT * FROM customers ORDER BY created_at DESC";
$customers = $conn->query($customersQuery);

renderHeader('Customers');
?>

<div class="dashboard-layout">
    <?php renderSidebar('customers'); ?>

    <main class="main-content">
        <div class="content-wrapper">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Customers</h1>
                    <p class="page-subtitle">View customer database</p>
                </div>
            </div>

            <!-- Search -->
            <div class="card mb-6">
                <div class="input-group">
                    <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.35-4.35" />
                    </svg>
                    <input type="search" id="searchInput" class="input" placeholder="Search customers...">
                </div>
            </div>

            <!-- Customers Table -->
            <div class="card">
                <div class="table-container">
                    <table class="table" id="customersTable">
                        <thead>
                            <tr>
                                <th>Customer Name</th>
                                <th>Phone</th>
                                <th>Address</th>
                                <th>Status</th>
                                <th>Total Orders</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($customers->num_rows > 0): ?>
                                <?php while ($customer = $customers->fetch_assoc()): ?>
                                    <tr>
                                        <td class="font-medium"><?php echo htmlspecialchars($customer['name']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['phone']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['address']); ?></td>
                                        <td>
                                            <span class="badge <?php echo getStatusBadgeClass($customer['status']); ?>">
                                                <?php echo ucfirst($customer['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo number_format($customer['total_orders']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5">
                                        <div class="empty-state">
                                            <div class="empty-state-title">No customers found</div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<?php
closeDBConnection($conn);
renderFooter();
?>