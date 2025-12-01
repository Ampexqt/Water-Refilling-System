<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../auth/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

requireRole('cashier');

$conn = getDBConnection();
$message = '';
$messageType = '';

// Handle POS transaction
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_transaction') {
    $customer_name = sanitizeInput($_POST['customer_name']);
    $items = $_POST['items']; // JSON data
    $subtotal = floatval($_POST['subtotal']);
    $tax = floatval($_POST['tax']);
    $total = floatval($_POST['total']);
    $payment_method = sanitizeInput($_POST['payment_method']);

    $itemsJson = json_encode($items);
    $userId = getUserId();

    $stmt = $conn->prepare("INSERT INTO pos_transactions (customer_name, items, subtotal, tax, total, payment_method, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdddsi", $customer_name, $itemsJson, $subtotal, $tax, $total, $payment_method, $userId);

    if ($stmt->execute()) {
        $message = 'Transaction completed successfully!';
        $messageType = 'success';
    } else {
        $message = 'Error processing transaction: ' . $conn->error;
        $messageType = 'error';
    }
    $stmt->close();
}

// Get recent transactions
$transactionsQuery = "SELECT * FROM pos_transactions ORDER BY created_at DESC LIMIT 10";
$transactions = $conn->query($transactionsQuery);

renderHeader('Point of Sale');
?>

<div class="dashboard-layout">
    <?php renderSidebar('pos'); ?>

    <main class="main-content">
        <div class="content-wrapper">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Point of Sale</h1>
                    <p class="page-subtitle">Process walk-in sales and transactions</p>
                </div>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-2">
                <!-- POS Interface -->
                <div class="card">
                    <h2 class="text-lg font-semibold mb-4">New Transaction</h2>
                    <form id="posForm" method="POST" action="">
                        <input type="hidden" name="action" value="create_transaction">
                        <input type="hidden" name="items" id="items_json">
                        <input type="hidden" name="subtotal" id="subtotal_input">
                        <input type="hidden" name="tax" id="tax_input">
                        <input type="hidden" name="total" id="total_input">

                        <div class="form-group">
                            <label class="form-label" for="customer_name">Customer Name (Optional)</label>
                            <input type="text" id="customer_name" name="customer_name" class="input" placeholder="Walk-in Customer">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Add Items</label>
                            <div style="display: flex; gap: var(--space-2); margin-bottom: var(--space-3);">
                                <select id="item_size" class="input">
                                    <option value="5-gallon">5 Gallon - ₱25.00</option>
                                    <option value="3-gallon">3 Gallon - ₱15.00</option>
                                    <option value="1-gallon">1 Gallon - ₱8.00</option>
                                </select>
                                <input type="number" id="item_qty" class="input" value="1" min="1" style="max-width: 100px;">
                                <button type="button" class="btn btn-primary" onclick="addItem()">Add</button>
                            </div>
                            <div id="items_list" style="min-height: 100px; border: 1px solid var(--water-200); border-radius: var(--radius-lg); padding: var(--space-3);"></div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="payment_method">Payment Method</label>
                            <select id="payment_method" name="payment_method" class="input" required>
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="gcash">GCash</option>
                            </select>
                        </div>

                        <div style="background: var(--water-50); padding: var(--space-4); border-radius: var(--radius-lg); margin-bottom: var(--space-4);">
                            <div style="display: flex; justify-content: space-between; margin-bottom: var(--space-2);">
                                <span>Subtotal:</span>
                                <span id="subtotal_display">₱0.00</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: var(--space-2);">
                                <span>Tax (12%):</span>
                                <span id="tax_display">₱0.00</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: 600; padding-top: var(--space-2); border-top: 1px solid var(--water-200);">
                                <span>Total:</span>
                                <span id="total_display">₱0.00</span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-full">Complete Transaction</button>
                    </form>
                </div>

                <!-- Recent Transactions -->
                <div class="card">
                    <h2 class="text-lg font-semibold mb-4">Recent Transactions</h2>
                    <?php if ($transactions->num_rows > 0): ?>
                        <div style="display: flex; flex-direction: column; gap: var(--space-3);">
                            <?php while ($transaction = $transactions->fetch_assoc()): ?>
                                <div style="padding: var(--space-3); background: var(--water-50); border-radius: var(--radius-lg);">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: var(--space-2);">
                                        <span class="font-medium"><?php echo htmlspecialchars($transaction['customer_name'] ?: 'Walk-in'); ?></span>
                                        <span class="font-semibold"><?php echo formatCurrency($transaction['total']); ?></span>
                                    </div>
                                    <div class="text-sm" style="color: var(--neutral-600);">
                                        <?php echo formatDateTime($transaction['created_at']); ?> • <?php echo ucfirst($transaction['payment_method']); ?>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-state-title">No transactions yet</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    let items = [];
    const prices = {
        '5-gallon': 25.00,
        '3-gallon': 15.00,
        '1-gallon': 8.00
    };

    function addItem() {
        const size = document.getElementById('item_size').value;
        const qty = parseInt(document.getElementById('item_qty').value);

        items.push({
            size,
            qty,
            price: prices[size]
        });
        updateItemsList();
        calculateTotal();
    }

    function removeItem(index) {
        items.splice(index, 1);
        updateItemsList();
        calculateTotal();
    }

    function updateItemsList() {
        const list = document.getElementById('items_list');
        if (items.length === 0) {
            list.innerHTML = '<div style="color: var(--neutral-500); text-align: center;">No items added</div>';
            return;
        }

        list.innerHTML = items.map((item, index) => `
        <div style="display: flex; justify-content: space-between; align-items: center; padding: var(--space-2); border-bottom: 1px solid var(--water-200);">
            <span>${item.qty}x ${item.size.replace('-', ' ')}</span>
            <div style="display: flex; align-items: center; gap: var(--space-3);">
                <span class="font-medium">${formatCurrency(item.price * item.qty)}</span>
                <button type="button" class="icon-btn icon-btn-delete" onclick="removeItem(${index})">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
        </div>
    `).join('');
    }

    function calculateTotal() {
        const subtotal = items.reduce((sum, item) => sum + (item.price * item.qty), 0);
        const tax = subtotal * 0.12;
        const total = subtotal + tax;

        document.getElementById('subtotal_display').textContent = formatCurrency(subtotal);
        document.getElementById('tax_display').textContent = formatCurrency(tax);
        document.getElementById('total_display').textContent = formatCurrency(total);

        document.getElementById('items_json').value = JSON.stringify(items);
        document.getElementById('subtotal_input').value = subtotal;
        document.getElementById('tax_input').value = tax;
        document.getElementById('total_input').value = total;
    }

    document.getElementById('posForm').addEventListener('submit', function(e) {
        if (items.length === 0) {
            e.preventDefault();
            alert('Please add at least one item');
        }
    });

    updateItemsList();
</script>

<?php
closeDBConnection($conn);
renderFooter();
?>