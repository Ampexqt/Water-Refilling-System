<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../auth/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

requireRole('cashier');

$conn = getDBConnection();

// Get today's statistics
$today = date('Y-m-d');
$userId = getUserId();

// Today's sales (all transactions for today, not just current user)
$todaySalesQuery = "SELECT 
    COALESCE(SUM(total), 0) as total_sales,
    COALESCE(COUNT(*), 0) as transaction_count,
    COALESCE(AVG(total), 0) as avg_transaction
    FROM pos_transactions 
    WHERE DATE(created_at) = CURDATE()";
$todayStats = $conn->query($todaySalesQuery)->fetch_assoc();
if (!$todayStats) {
    $todayStats = ['total_sales' => 0, 'transaction_count' => 0, 'avg_transaction' => 0];
}

// This week's sales (all transactions for this week)
$weekStart = date('Y-m-d', strtotime('monday this week'));
$weekSalesQuery = "SELECT 
    COALESCE(SUM(total), 0) as total_sales,
    COALESCE(COUNT(*), 0) as transaction_count
    FROM pos_transactions 
    WHERE DATE(created_at) >= ?";
$stmt = $conn->prepare($weekSalesQuery);
$stmt->bind_param("s", $weekStart);
$stmt->execute();
$weekStats = $stmt->get_result()->fetch_assoc();
if (!$weekStats) {
    $weekStats = ['total_sales' => 0, 'transaction_count' => 0];
}
$stmt->close();

// Recent transactions (all recent transactions)
$recentTransactionsQuery = "SELECT * FROM pos_transactions 
                           ORDER BY created_at DESC 
                           LIMIT 5";
$recentTransactions = $conn->query($recentTransactionsQuery);

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
                        <div class="stat-card-title">Today's Sales</div>
                        <div class="stat-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="1" x2="12" y2="23"></line>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="stat-card-value"><?php echo formatCurrency($todayStats['total_sales'] ?? 0); ?></div>
                    <div class="stat-card-change">Total revenue today</div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-card-title">Transactions Today</div>
                        <div class="stat-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                                <line x1="1" y1="10" x2="23" y2="10"></line>
                            </svg>
                        </div>
                    </div>
                    <div class="stat-card-value"><?php echo number_format($todayStats['transaction_count'] ?? 0); ?></div>
                    <div class="stat-card-change">Completed transactions</div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-card-title">Average Transaction</div>
                        <div class="stat-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="20" x2="12" y2="10"></line>
                                <line x1="18" y1="20" x2="18" y2="4"></line>
                                <line x1="6" y1="20" x2="6" y2="16"></line>
                            </svg>
                        </div>
                    </div>
                    <div class="stat-card-value"><?php echo formatCurrency($todayStats['avg_transaction'] ?? 0); ?></div>
                    <div class="stat-card-change">Per transaction</div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-card-title">This Week</div>
                        <div class="stat-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </div>
                    </div>
                    <div class="stat-card-value"><?php echo formatCurrency($weekStats['total_sales'] ?? 0); ?></div>
                    <div class="stat-card-change"><?php echo number_format($weekStats['transaction_count'] ?? 0); ?> transactions</div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-4);">
                    <h2 class="text-lg font-semibold">Recent Transactions</h2>
                    <a href="pos.php" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: var(--space-2);">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="9" cy="21" r="1"></circle>
                            <circle cx="20" cy="21" r="1"></circle>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                        </svg>
                        <span>New Sale</span>
                    </a>
                </div>
                <?php if ($recentTransactions->num_rows > 0): ?>
                    <div style="display: flex; flex-direction: column; gap: var(--space-4);">
                        <?php while ($transaction = $recentTransactions->fetch_assoc()): ?>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: var(--space-4); border-bottom: 1px solid var(--water-100);">
                                <div>
                                    <div class="font-medium"><?php echo htmlspecialchars($transaction['customer_name'] ?: 'Walk-in Customer'); ?></div>
                                    <div class="text-sm" style="color: var(--neutral-600);">
                                        <?php echo formatDateTime($transaction['created_at']); ?> • 
                                        <span style="text-transform: capitalize;"><?php echo $transaction['payment_method']; ?></span>
                                    </div>
                                </div>
                                <div style="text-align: right;">
                                    <div class="font-semibold" style="font-size: 1.125rem; color: var(--water-400);">
                                        <?php echo formatCurrency($transaction['total']); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-state-title">No transactions yet</div>
                        <div class="empty-state-description">Start processing sales to see them here</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<?php
closeDBConnection($conn);
renderFooter();
?>