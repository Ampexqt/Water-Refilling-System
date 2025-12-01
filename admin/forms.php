<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../auth/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

requireRole('admin');

$conn = getDBConnection();
$message = '';
$messageType = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $customer_id = intval($_POST['customer_id']);
                $container_size = sanitizeInput($_POST['container_size']);
                $quantity = intval($_POST['quantity']);
                $delivery_date = sanitizeInput($_POST['delivery_date']);
                $delivery_time = sanitizeInput($_POST['delivery_time']);
                $notes = sanitizeInput($_POST['notes']);
                $status = sanitizeInput($_POST['status']);

                $stmt = $conn->prepare("INSERT INTO orders (customer_id, container_size, quantity, delivery_date, delivery_time, notes, status, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $userId = getUserId();
                $stmt->bind_param("ississsi", $customer_id, $container_size, $quantity, $delivery_date, $delivery_time, $notes, $status, $userId);

                if ($stmt->execute()) {
                    $updateStmt = $conn->prepare("UPDATE customers SET total_orders = total_orders + 1 WHERE id = ?");
                    $updateStmt->bind_param("i", $customer_id);
                    $updateStmt->execute();
                    $updateStmt->close();

                    $message = 'Order created successfully!';
                    $messageType = 'success';
                } else {
                    $message = 'Error creating order: ' . $conn->error;
                    $messageType = 'error';
                }
                $stmt->close();
                break;

            case 'update':
                $id = intval($_POST['id']);
                $customer_id = intval($_POST['customer_id']);
                $container_size = sanitizeInput($_POST['container_size']);
                $quantity = intval($_POST['quantity']);
                $delivery_date = sanitizeInput($_POST['delivery_date']);
                $delivery_time = sanitizeInput($_POST['delivery_time']);
                $notes = sanitizeInput($_POST['notes']);
                $status = sanitizeInput($_POST['status']);

                $stmt = $conn->prepare("UPDATE orders SET customer_id = ?, container_size = ?, quantity = ?, delivery_date = ?, delivery_time = ?, notes = ?, status = ? WHERE id = ?");
                $stmt->bind_param("isissssi", $customer_id, $container_size, $quantity, $delivery_date, $delivery_time, $notes, $status, $id);

                if ($stmt->execute()) {
                    $message = 'Order updated successfully!';
                    $messageType = 'success';
                } else {
                    $message = 'Error updating order: ' . $conn->error;
                    $messageType = 'error';
                }
                $stmt->close();
                break;

            case 'delete':
                $id = intval($_POST['id']);
                $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
                $stmt->bind_param("i", $id);

                if ($stmt->execute()) {
                    $message = 'Order deleted successfully!';
                    $messageType = 'success';
                } else {
                    $message = 'Error deleting order: ' . $conn->error;
                    $messageType = 'error';
                }
                $stmt->close();
                break;
        }
    }
}

// Get all orders with customer info
$ordersQuery = "SELECT o.*, c.name as customer_name, c.phone, c.address 
                FROM orders o 
                JOIN customers c ON o.customer_id = c.id 
                ORDER BY o.created_at DESC";
$orders = $conn->query($ordersQuery);

// Get all active customers for dropdown
$customersQuery = "SELECT id, name, phone FROM customers WHERE status = 'active' ORDER BY name ASC";
$customers = $conn->query($customersQuery);

renderHeader('Forms');
?>

<div class="dashboard-layout">
    <?php renderSidebar('forms'); ?>

    <main class="main-content">
        <div class="content-wrapper">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Order Forms</h1>
                    <p class="page-subtitle">Manage customer orders and refill requests</p>
                </div>
                <button class="btn btn-primary" onclick="openModal('addOrderModal')">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    New Order
                </button>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Container Size</th>
                                <th>Quantity</th>
                                <th>Delivery Date</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($orders->num_rows > 0): ?>
                                <?php while ($order = $orders->fetch_assoc()): ?>
                                    <tr>
                                        <td class="font-medium">#<?php echo str_pad($order['id'], 4, '0', STR_PAD_LEFT); ?></td>
                                        <td>
                                            <div class="font-medium"><?php echo htmlspecialchars($order['customer_name']); ?></div>
                                            <div class="text-sm" style="color: var(--neutral-600);"><?php echo htmlspecialchars($order['phone']); ?></div>
                                        </td>
                                        <td><?php echo formatContainerSize($order['container_size']); ?></td>
                                        <td><?php echo number_format($order['quantity']); ?></td>
                                        <td>
                                            <?php if ($order['delivery_date']): ?>
                                                <?php echo formatDate($order['delivery_date']); ?><br>
                                                <span class="text-sm" style="color: var(--neutral-600);"><?php echo formatTime($order['delivery_time']); ?></span>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo getStatusBadgeClass($order['status']); ?>">
                                                <?php echo ucfirst($order['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo formatDate($order['created_at']); ?></td>
                                        <td>
                                            <div class="table-actions">
                                                <button class="icon-btn" onclick='editOrder(<?php echo htmlspecialchars(json_encode($order)); ?>)' title="Edit">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                    </svg>
                                                </button>
                                                <button class="icon-btn icon-btn-delete" onclick="deleteOrder(<?php echo $order['id']; ?>, '<?php echo htmlspecialchars($order['customer_name']); ?>')" title="Delete">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <polyline points="3 6 5 6 21 6" />
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8">
                                        <div class="empty-state">
                                            <div class="empty-state-title">No orders found</div>
                                            <div class="empty-state-description">Create your first order to get started</div>
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

<div id="addOrderModal" class="modal-overlay">
    <div class="modal" style="overflow: hidden; max-height: 70vh; display: flex; flex-direction: column;">
        <div class="modal-header">
            <h2 class="modal-title">Create New Order</h2>
            <button class="modal-close" data-close-modal="addOrderModal">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
            </button>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="action" value="create">
            <div class="modal-body" style="flex: 1 1 auto; overflow-y: auto; overflow-x: hidden; min-height: 0; max-height: calc(70vh - 180px);">
                <div class="form-group">
                    <label class="form-label" for="add_customer_id">Customer</label>
                    <select id="add_customer_id" name="customer_id" class="input" required>
                        <option value="">Select Customer</option>
                        <?php
                        $customers->data_seek(0);
                        while ($customer = $customers->fetch_assoc()):
                        ?>
                            <option value="<?php echo $customer['id']; ?>">
                                <?php echo htmlspecialchars($customer['name']); ?> - <?php echo htmlspecialchars($customer['phone']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="add_container_size">Container Size</label>
                    <select id="add_container_size" name="container_size" class="input" required>
                        <option value="">Select Size</option>
                        <option value="5-gallon">5 Gallon - ₱25.00</option>
                        <option value="3-gallon">3 Gallon - ₱15.00</option>
                        <option value="1-gallon">1 Gallon - ₱8.00</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="add_quantity">Quantity</label>
                    <input type="number" id="add_quantity" name="quantity" class="input" min="1" value="1" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="add_delivery_date">Delivery Date</label>
                    <input type="date" id="add_delivery_date" name="delivery_date" class="input" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="add_delivery_time">Delivery Time</label>
                    <input type="time" id="add_delivery_time" name="delivery_time" class="input" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="add_notes">Notes (Optional)</label>
                    <textarea id="add_notes" name="notes" class="input" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label" for="add_status">Status</label>
                    <select id="add_status" name="status" class="input" required>
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer" style="flex-shrink: 0; background: white; padding: 1.5rem; border-top: 1px solid #B2DFDB; display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" class="btn btn-secondary" data-close-modal="addOrderModal">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Order</button>
            </div>
        </form>
    </div>
</div>

<div id="editOrderModal" class="modal-overlay">
    <div class="modal" style="overflow: hidden; max-height: 70vh; display: flex; flex-direction: column;">
        <div class="modal-header">
            <h2 class="modal-title">Edit Order</h2>
            <button class="modal-close" data-close-modal="editOrderModal">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
            </button>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="action" value="update">
            <input type="hidden" id="edit_id" name="id">
            <div class="modal-body" style="flex: 1 1 auto; overflow-y: auto; overflow-x: hidden; min-height: 0; max-height: calc(70vh - 180px);">
                <div class="form-group">
                    <label class="form-label" for="edit_customer_id">Customer</label>
                    <select id="edit_customer_id" name="customer_id" class="input" required>
                        <option value="">Select Customer</option>
                        <?php
                        $customers->data_seek(0);
                        while ($customer = $customers->fetch_assoc()):
                        ?>
                            <option value="<?php echo $customer['id']; ?>">
                                <?php echo htmlspecialchars($customer['name']); ?> - <?php echo htmlspecialchars($customer['phone']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_container_size">Container Size</label>
                    <select id="edit_container_size" name="container_size" class="input" required>
                        <option value="">Select Size</option>
                        <option value="5-gallon">5 Gallon - ₱25.00</option>
                        <option value="3-gallon">3 Gallon - ₱15.00</option>
                        <option value="1-gallon">1 Gallon - ₱8.00</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_quantity">Quantity</label>
                    <input type="number" id="edit_quantity" name="quantity" class="input" min="1" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_delivery_date">Delivery Date</label>
                    <input type="date" id="edit_delivery_date" name="delivery_date" class="input" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_delivery_time">Delivery Time</label>
                    <input type="time" id="edit_delivery_time" name="delivery_time" class="input" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_notes">Notes (Optional)</label>
                    <textarea id="edit_notes" name="notes" class="input" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_status">Status</label>
                    <select id="edit_status" name="status" class="input" required>
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer" style="flex-shrink: 0; background: white; padding: 1.5rem; border-top: 1px solid #B2DFDB; display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" class="btn btn-secondary" data-close-modal="editOrderModal">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Order</button>
            </div>
        </form>
    </div>
</div>

<form id="deleteForm" method="POST" action="" style="display: none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" id="delete_id" name="id">
</form>

<?php
closeDBConnection($conn);
renderFooter();
?>