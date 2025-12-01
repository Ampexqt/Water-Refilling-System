<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../auth/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

requireRole('admin');

$conn = getDBConnection();

// Get statistics
$totalCustomersQuery = "SELECT COUNT(*) as count FROM customers WHERE status = 'active'";
$totalCustomers = $conn->query($totalCustomersQuery)->fetch_assoc()['count'];

$dailySalesQuery = "SELECT SUM(total) as total FROM pos_transactions WHERE DATE(created_at) = CURDATE()";
$dailySalesResult = $conn->query($dailySalesQuery)->fetch_assoc();
$dailySales = $dailySalesResult['total'] ?? 0;

$refillsTodayQuery = "SELECT COUNT(*) as count FROM orders WHERE DATE(created_at) = CURDATE()";
$refillsToday = $conn->query($refillsTodayQuery)->fetch_assoc()['count'];

$deliveriesQuery = "SELECT COUNT(*) as count FROM deliveries WHERE status IN ('pending', 'in-transit')";
$pendingDeliveries = $conn->query($deliveriesQuery)->fetch_assoc()['count'];

// Get recent orders
$recentOrdersQuery = "SELECT o.*, c.name as customer_name 
                      FROM orders o 
                      JOIN customers c ON o.customer_id = c.id 
                      ORDER BY o.created_at DESC 
                      LIMIT 4";
$recentOrders = $conn->query($recentOrdersQuery);

// Get pending deliveries
$pendingDeliveriesQuery = "SELECT d.*, c.name as customer_name 
                           FROM deliveries d 
                           JOIN customers c ON d.customer_id = c.id 
                           WHERE d.status IN ('pending', 'in-transit')
                           ORDER BY d.scheduled_date ASC, d.scheduled_time ASC 
                           LIMIT 3";
$pendingDeliveriesList = $conn->query($pendingDeliveriesQuery);

renderHeader('Dashboard');
?>

<div class="dashboard-layout">
    <?php renderSidebar('index'); ?>

    <main class="main-content">
        <div class="content-wrapper">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Dashboard</h1>
                    <p class="page-subtitle">Welcome back, <?php echo htmlspecialchars(getUserName()); ?>!</p>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-4 mb-6">
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-card-title">Total Customers</div>
                        <div class="stat-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            </svg>
                        </div>
                    </div>
                    <div class="stat-card-value"><?php echo number_format($totalCustomers); ?></div>
                    <div class="stat-card-change">Active customers</div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-card-title">Daily Sales</div>
                        <div class="stat-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="1" x2="12" y2="23" />
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                            </svg>
                        </div>
                    </div>
                    <div class="stat-card-value"><?php echo formatCurrency($dailySales); ?></div>
                    <div class="stat-card-change">Today's revenue</div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-card-title">Refills Today</div>
                        <div class="stat-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="stat-card-value"><?php echo number_format($refillsToday); ?></div>
                    <div class="stat-card-change">Orders placed today</div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-card-title">Deliveries</div>
                        <div class="stat-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="1" y="3" width="15" height="13" />
                                <polygon points="16 8 20 8 23 11 23 16 16 16 16 8" />
                                <circle cx="5.5" cy="18.5" r="2.5" />
                                <circle cx="18.5" cy="18.5" r="2.5" />
                            </svg>
                        </div>
                    </div>
                    <div class="stat-card-value"><?php echo number_format($pendingDeliveries); ?></div>
                    <div class="stat-card-change">Pending deliveries</div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="grid grid-cols-2">
                <!-- Recent Orders -->
                <div class="card">
                    <h2 class="text-lg font-semibold mb-4">Recent Orders</h2>
                    <?php if ($recentOrders->num_rows > 0): ?>
                        <div style="display: flex; flex-direction: column; gap: var(--space-4);">
                            <?php while ($order = $recentOrders->fetch_assoc()): ?>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: var(--space-4); border-bottom: 1px solid var(--water-100);">
                                    <div>
                                        <div class="font-medium"><?php echo htmlspecialchars($order['customer_name']); ?></div>
                                        <div class="text-sm" style="color: var(--neutral-600);">
                                            <?php echo $order['quantity']; ?>x <?php echo formatContainerSize($order['container_size']); ?>
                                        </div>
                                    </div>
                                    <span class="badge <?php echo getStatusBadgeClass($order['status']); ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-state-title">No orders yet</div>
                            <div class="empty-state-description">Orders will appear here</div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Pending Deliveries -->
                <div class="card">
                    <h2 class="text-lg font-semibold mb-4">Pending Deliveries</h2>
                    <?php if ($pendingDeliveriesList->num_rows > 0): ?>
                        <div style="display: flex; flex-direction: column; gap: var(--space-4);">
                            <?php while ($delivery = $pendingDeliveriesList->fetch_assoc()): ?>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: var(--space-4); border-bottom: 1px solid var(--water-100);">
                                    <div>
                                        <div class="font-medium"><?php echo htmlspecialchars($delivery['customer_name']); ?></div>
                                        <div class="text-sm" style="color: var(--neutral-600);">
                                            <?php echo formatDate($delivery['scheduled_date']); ?> at <?php echo formatTime($delivery['scheduled_time']); ?>
                                        </div>
                                    </div>
                                    <span class="badge <?php echo getStatusBadgeClass($delivery['status']); ?>">
                                        <?php echo ucfirst($delivery['status']); ?>
                                    </span>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-state-title">No pending deliveries</div>
                            <div class="empty-state-description">Deliveries will appear here</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</div>

<?php
closeDBConnection($conn);
renderFooter();
?>