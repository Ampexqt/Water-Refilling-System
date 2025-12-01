<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../auth/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

requireRole('cashier');

$conn = getDBConnection();

// Get all deliveries
$deliveriesQuery = "SELECT d.*, c.name as customer_name, c.phone 
                    FROM deliveries d 
                    JOIN customers c ON d.customer_id = c.id 
                    ORDER BY d.scheduled_date DESC, d.scheduled_time DESC";
$deliveries = $conn->query($deliveriesQuery);

renderHeader('Deliveries');
?>

<div class="dashboard-layout">
    <?php renderSidebar('deliveries'); ?>

    <main class="main-content">
        <div class="content-wrapper">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Deliveries</h1>
                    <p class="page-subtitle">View delivery schedules</p>
                </div>
            </div>

            <div class="card">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Delivery ID</th>
                                <th>Customer</th>
                                <th>Items</th>
                                <th>Address</th>
                                <th>Scheduled</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($deliveries->num_rows > 0): ?>
                                <?php while ($delivery = $deliveries->fetch_assoc()): ?>
                                    <tr>
                                        <td class="font-medium">#<?php echo str_pad($delivery['id'], 4, '0', STR_PAD_LEFT); ?></td>
                                        <td>
                                            <div class="font-medium"><?php echo htmlspecialchars($delivery['customer_name']); ?></div>
                                            <div class="text-sm" style="color: var(--neutral-600);"><?php echo htmlspecialchars($delivery['phone']); ?></div>
                                        </td>
                                        <td><?php echo htmlspecialchars($delivery['items']); ?></td>
                                        <td><?php echo htmlspecialchars($delivery['delivery_address']); ?></td>
                                        <td>
                                            <?php echo formatDate($delivery['scheduled_date']); ?><br>
                                            <span class="text-sm" style="color: var(--neutral-600);"><?php echo formatTime($delivery['scheduled_time']); ?></span>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo getStatusBadgeClass($delivery['status']); ?>">
                                                <?php echo ucfirst($delivery['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6">
                                        <div class="empty-state">
                                            <div class="empty-state-title">No deliveries found</div>
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