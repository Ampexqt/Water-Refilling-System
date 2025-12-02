<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../auth/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

requireRole('admin');

$conn = getDBConnection();

// Get date range filter (default to last 30 days)
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Get daily sales data for the selected period
$salesQuery = "SELECT 
    DATE(created_at) as sale_date,
    SUM(total) as daily_total,
    COUNT(*) as transaction_count
    FROM pos_transactions 
    WHERE DATE(created_at) BETWEEN ? AND ?
    GROUP BY DATE(created_at)
    ORDER BY sale_date ASC";

$stmt = $conn->prepare($salesQuery);
if ($stmt) {
    $stmt->bind_param("ss", $startDate, $endDate);
    if ($stmt->execute()) {
        $salesResult = $stmt->get_result();
    } else {
        $salesResult = false;
    }
} else {
    $salesResult = false;
}

// Prepare data for chart
$chartLabels = [];
$chartData = [];
$totalSales = 0;
$totalTransactions = 0;

if ($salesResult && $salesResult->num_rows > 0) {
    while ($row = $salesResult->fetch_assoc()) {
        $chartLabels[] = date('M d', strtotime($row['sale_date']));
        $chartData[] = floatval($row['daily_total']);
        $totalSales += floatval($row['daily_total']);
        $totalTransactions += intval($row['transaction_count']);
    }
}
if ($stmt) {
    $stmt->close();
}

// Get summary statistics
$summaryQuery = "SELECT 
    COALESCE(SUM(total), 0) as total_sales,
    COALESCE(COUNT(*), 0) as total_transactions,
    COALESCE(AVG(total), 0) as avg_transaction,
    COALESCE(SUM(CASE WHEN payment_method = 'cash' THEN total ELSE 0 END), 0) as cash_sales,
    COALESCE(SUM(CASE WHEN payment_method = 'card' THEN total ELSE 0 END), 0) as card_sales,
    COALESCE(SUM(CASE WHEN payment_method = 'gcash' THEN total ELSE 0 END), 0) as gcash_sales
    FROM pos_transactions 
    WHERE DATE(created_at) BETWEEN ? AND ?";

$stmt2 = $conn->prepare($summaryQuery);
if ($stmt2) {
    $stmt2->bind_param("ss", $startDate, $endDate);
    if ($stmt2->execute()) {
        $summaryResult = $stmt2->get_result();
        $summary = $summaryResult->fetch_assoc();
    } else {
        $summary = null;
    }
} else {
    $summary = null;
}

if (!$summary) {
    $summary = [
        'total_sales' => 0,
        'total_transactions' => 0,
        'avg_transaction' => 0,
        'cash_sales' => 0,
        'card_sales' => 0,
        'gcash_sales' => 0
    ];
}
if ($stmt2) {
    $stmt2->close();
}

renderHeader('Reports');
?>

<div class="dashboard-layout">
    <?php renderSidebar('reports'); ?>

    <main class="main-content">
        <div class="content-wrapper">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Sales Reports</h1>
                    <p class="page-subtitle">View sales analytics and statistics</p>
                </div>
                <div style="display: flex; gap: var(--space-2);">
                    <button onclick="window.print()" class="btn btn-secondary" style="display: flex; align-items: center; gap: var(--space-2);">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 6 2 18 2 18 9"></polyline>
                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                            <rect x="6" y="14" width="12" height="8"></rect>
                        </svg>
                        <span>Print</span>
                    </button>
                    <button onclick="exportToCSV()" class="btn btn-secondary" style="display: flex; align-items: center; gap: var(--space-2);">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        <span>Export</span>
                    </button>
                </div>
            </div>

            <!-- Date Filter -->
            <div class="card mb-6">
                <form method="GET" action="" style="display: flex; gap: var(--space-4); align-items: end;">
                    <div class="form-group" style="flex: 1;">
                        <label class="form-label" for="start_date">Start Date</label>
                        <input type="date" id="start_date" name="start_date" class="input" value="<?php echo htmlspecialchars($startDate); ?>" required>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label class="form-label" for="end_date">End Date</label>
                        <input type="date" id="end_date" name="end_date" class="input" value="<?php echo htmlspecialchars($endDate); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-4 mb-6">
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-card-title">Total Sales</div>
                        <div class="stat-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="1" x2="12" y2="23"></line>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="stat-card-value"><?php echo formatCurrency($summary['total_sales']); ?></div>
                    <div class="stat-card-change"><?php echo date('M d', strtotime($startDate)); ?> - <?php echo date('M d', strtotime($endDate)); ?></div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-card-title">Transactions</div>
                        <div class="stat-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                                <line x1="1" y1="10" x2="23" y2="10"></line>
                            </svg>
                        </div>
                    </div>
                    <div class="stat-card-value"><?php echo number_format($summary['total_transactions']); ?></div>
                    <div class="stat-card-change">Total count</div>
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
                    <div class="stat-card-value"><?php echo formatCurrency($summary['avg_transaction']); ?></div>
                    <div class="stat-card-change">Per transaction</div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-card-title">Daily Average</div>
                        <div class="stat-card-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                    </div>
                    <div class="stat-card-value">
                        <?php 
                        $days = max(1, (strtotime($endDate) - strtotime($startDate)) / 86400 + 1);
                        echo formatCurrency($summary['total_sales'] / $days); 
                        ?>
                    </div>
                    <div class="stat-card-change">Per day</div>
                </div>
            </div>

            <!-- Chart -->
            <div class="card mb-6" id="chartContainer">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-4);">
                    <h2 class="text-lg font-semibold">Daily Sales (₱)</h2>
                </div>
                <canvas id="salesChart" style="max-height: 400px;"></canvas>
            </div>

            <!-- Payment Method Breakdown -->
            <div class="card">
                <h2 class="text-lg font-semibold mb-4">Payment Method Breakdown</h2>
                <div class="grid grid-cols-3" style="gap: var(--space-4);">
                    <div style="padding: var(--space-4); background: var(--water-50); border-radius: var(--radius-lg);">
                        <div style="font-size: 0.875rem; color: var(--neutral-600); margin-bottom: var(--space-2);">Cash</div>
                        <div style="font-size: 1.5rem; font-weight: 600; color: var(--water-400);">
                            <?php echo formatCurrency($summary['cash_sales']); ?>
                        </div>
                    </div>
                    <div style="padding: var(--space-4); background: var(--water-50); border-radius: var(--radius-lg);">
                        <div style="font-size: 0.875rem; color: var(--neutral-600); margin-bottom: var(--space-2);">GCash</div>
                        <div style="font-size: 1.5rem; font-weight: 600; color: var(--water-400);">
                            <?php echo formatCurrency($summary['gcash_sales']); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Chart data
    const chartLabels = <?php echo json_encode($chartLabels ?: []); ?>;
    const chartData = <?php echo json_encode($chartData ?: []); ?>;

    // Initialize chart
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Sales (₱)',
                data: chartData,
                backgroundColor: 'rgba(34, 197, 94, 0.6)',
                borderColor: 'rgba(34, 197, 94, 1)',
                borderWidth: 2,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return '₱' + context.parsed.y.toLocaleString('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toLocaleString('en-US');
                        }
                    }
                }
            }
        }
    });

    // Export to CSV
    function exportToCSV() {
        const data = [];
        data.push(['Date', 'Sales (₱)']);
        
        chartLabels.forEach((label, index) => {
            data.push([label, chartData[index]]);
        });

        const csvContent = data.map(row => row.join(',')).join('\n');
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        
        link.setAttribute('href', url);
        link.setAttribute('download', 'sales_report_<?php echo $startDate; ?>_to_<?php echo $endDate; ?>.csv');
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // Print styles
    const style = document.createElement('style');
    style.textContent = `
        @media print {
            .sidebar, .page-header button, .card form {
                display: none !important;
            }
            .main-content {
                margin-left: 0 !important;
            }
            .card {
                page-break-inside: avoid;
            }
        }
    `;
    document.head.appendChild(style);
</script>

<?php
closeDBConnection($conn);
renderFooter();
?>

