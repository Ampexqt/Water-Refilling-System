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
                $order_id = intval($_POST['order_id']);
                $customer_id = intval($_POST['customer_id']);
                $delivery_address = sanitizeInput($_POST['delivery_address']);
                $items = sanitizeInput($_POST['items']);
                $notes = sanitizeInput($_POST['notes']);

                // Get delivery date and time from the order
                $orderQuery = $conn->prepare("SELECT delivery_date, delivery_time FROM orders WHERE id = ?");
                $orderQuery->bind_param("i", $order_id);
                $orderQuery->execute();
                $orderResult = $orderQuery->get_result();
                $orderData = $orderResult->fetch_assoc();
                $scheduled_date = $orderData['delivery_date'];
                $scheduled_time = $orderData['delivery_time'];
                $orderQuery->close();

                $stmt = $conn->prepare("INSERT INTO deliveries (order_id, customer_id, delivery_address, items, scheduled_date, scheduled_time, status, notes, created_by) VALUES (?, ?, ?, ?, ?, ?, 'pending', ?, ?)");
                $userId = getUserId();
                $stmt->bind_param("iisssssi", $order_id, $customer_id, $delivery_address, $items, $scheduled_date, $scheduled_time, $notes, $userId);

                if ($stmt->execute()) {
                    // Update order status to processing when delivery is created
                    $updateStmt = $conn->prepare("UPDATE orders SET status = 'processing' WHERE id = ?");
                    $updateStmt->bind_param("i", $order_id);
                    $updateStmt->execute();
                    $updateStmt->close();

                    $message = 'Delivery created successfully!';
                    $messageType = 'success';
                } else {
                    $message = 'Error creating delivery: ' . $conn->error;
                    $messageType = 'error';
                }
                $stmt->close();
                break;

            case 'update':
                $id = intval($_POST['id']);
                $delivery_address = sanitizeInput($_POST['delivery_address']);
                $scheduled_date = sanitizeInput($_POST['scheduled_date']);
                $scheduled_time = sanitizeInput($_POST['scheduled_time']);
                $status = sanitizeInput($_POST['status']);
                $notes = sanitizeInput($_POST['notes']);

                $stmt = $conn->prepare("UPDATE deliveries SET delivery_address = ?, scheduled_date = ?, scheduled_time = ?, status = ?, notes = ? WHERE id = ?");
                $stmt->bind_param("sssssi", $delivery_address, $scheduled_date, $scheduled_time, $status, $notes, $id);

                if ($stmt->execute()) {
                    $message = 'Delivery updated successfully!';
                    $messageType = 'success';
                } else {
                    $message = 'Error updating delivery: ' . $conn->error;
                    $messageType = 'error';
                }
                $stmt->close();
                break;

            case 'delete':
                $id = intval($_POST['id']);

                $stmt = $conn->prepare("DELETE FROM deliveries WHERE id = ?");
                $stmt->bind_param("i", $id);

                if ($stmt->execute()) {
                    $message = 'Delivery deleted successfully!';
                    $messageType = 'success';
                } else {
                    $message = 'Error deleting delivery: ' . $conn->error;
                    $messageType = 'error';
                }
                $stmt->close();
                break;
        }
    }
}

// Get all deliveries with customer information
$deliveriesQuery = "SELECT d.*, c.name as customer_name, c.phone, c.address as customer_address
                    FROM deliveries d
                    LEFT JOIN customers c ON d.customer_id = c.id
                    ORDER BY d.scheduled_date DESC, d.scheduled_time DESC";
$deliveries = $conn->query($deliveriesQuery);

// Get pending orders for the delivery form
$pendingOrdersQuery = "SELECT o.id, o.customer_id, o.container_size, o.quantity, o.delivery_date, o.delivery_time, c.name as customer_name, c.phone, c.address
                       FROM orders o
                       JOIN customers c ON o.customer_id = c.id
                       WHERE o.status = 'pending'
                       ORDER BY o.created_at DESC";
$pendingOrders = $conn->query($pendingOrdersQuery);

renderHeader('Deliveries');
?>

<div class="dashboard-layout">
    <?php renderSidebar('deliveries'); ?>

    <main class="main-content">
        <div class="content-wrapper">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Deliveries</h1>
                    <p class="page-subtitle">Track and manage delivery schedules</p>
                </div>
                <button class="btn btn-primary" onclick="openModal('addDeliveryModal')">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    Add Delivery
                </button>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <!-- Deliveries Table -->
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
                                <th>Actions</th>
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
                                        <td>
                                            <div class="table-actions">
                                                <button class="icon-btn" onclick='editDelivery(<?php echo htmlspecialchars(json_encode($delivery)); ?>)' title="Edit">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                    </svg>
                                                </button>
                                                <button class="icon-btn icon-btn-delete" onclick="deleteDelivery(<?php echo $delivery['id']; ?>, '<?php echo htmlspecialchars($delivery['customer_name']); ?>')" title="Delete">
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
                                    <td colspan="7">
                                        <div class="empty-state">
                                            <div class="empty-state-title">No deliveries found</div>
                                            <div class="empty-state-description">Deliveries will appear here</div>
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

<!-- Add Delivery Modal -->
<div id="addDeliveryModal" class="modal-overlay">
    <div class="modal" style="overflow: hidden; max-height: 70vh; display: flex; flex-direction: column;">
        <div class="modal-header">
            <h2 class="modal-title">Create New Delivery</h2>
            <button class="modal-close" data-close-modal="addDeliveryModal">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
            </button>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="action" value="create">
            <input type="hidden" id="add_customer_id" name="customer_id">
            <div class="modal-body" style="flex: 1 1 auto; overflow-y: auto; overflow-x: hidden; min-height: 0; max-height: calc(70vh - 180px);">
                <div class="form-group">
                    <label class="form-label" for="add_order_id">Select Pending Order</label>
                    <select id="add_order_id" name="order_id" class="input" required onchange="populateDeliveryForm(this)">
                        <option value="">Select Order</option>
                        <?php while ($order = $pendingOrders->fetch_assoc()): ?>
                            <option value="<?php echo $order['id']; ?>"
                                data-customer-id="<?php echo $order['customer_id']; ?>"
                                data-customer-name="<?php echo htmlspecialchars($order['customer_name']); ?>"
                                data-customer-phone="<?php echo htmlspecialchars($order['phone']); ?>"
                                data-address="<?php echo htmlspecialchars($order['address']); ?>"
                                data-items="<?php echo $order['quantity']; ?>x <?php echo formatContainerSize($order['container_size']); ?>">
                                Order #<?php echo str_pad($order['id'], 4, '0', STR_PAD_LEFT); ?> - <?php echo htmlspecialchars($order['customer_name']); ?> (<?php echo $order['quantity']; ?>x <?php echo formatContainerSize($order['container_size']); ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Customer Information</label>
                    <div id="customer_info_box" style="padding: 1rem; background: var(--neutral-50); border-radius: 8px; border: 1px solid var(--neutral-200);">
                        <div id="customer_info" style="color: var(--neutral-600); margin-bottom: 0.75rem;">
                            Select an order to view customer details
                        </div>
                        <div id="delivery_address_section" style="display: none;">
                            <label style="font-size: 0.875rem; font-weight: 500; color: var(--neutral-700); display: block; margin-bottom: 0.5rem;">Delivery Address</label>
                            <textarea id="add_delivery_address" name="delivery_address" class="input" rows="2" required style="margin-bottom: 0.75rem;"></textarea>
                        </div>
                        <div id="items_section" style="display: none;">
                            <label style="font-size: 0.875rem; font-weight: 500; color: var(--neutral-700); display: block; margin-bottom: 0.5rem;">Items</label>
                            <input type="text" id="add_items" name="items" class="input" required readonly>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label" for="add_notes">Notes (Optional)</label>
                    <textarea id="add_notes" name="notes" class="input" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer" style="flex-shrink: 0; background: white; padding: 1.5rem; border-top: 1px solid #B2DFDB; display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" class="btn btn-secondary" data-close-modal="addDeliveryModal">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Delivery</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Delivery Modal -->
<div id="editDeliveryModal" class="modal-overlay">
    <div class="modal" style="overflow: hidden; max-height: 70vh; display: flex; flex-direction: column;">
        <div class="modal-header">
            <h2 class="modal-title">Edit Delivery</h2>
            <button class="modal-close" data-close-modal="editDeliveryModal">
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
                    <label class="form-label" for="edit_delivery_address">Delivery Address</label>
                    <textarea id="edit_delivery_address" name="delivery_address" class="input" rows="2" required></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_scheduled_date">Scheduled Date</label>
                    <input type="date" id="edit_scheduled_date" name="scheduled_date" class="input" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_scheduled_time">Scheduled Time</label>
                    <input type="time" id="edit_scheduled_time" name="scheduled_time" class="input" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_status">Status</label>
                    <select id="edit_status" name="status" class="input" required>
                        <option value="pending">Pending</option>
                        <option value="in-transit">In Transit</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_notes">Notes (Optional)</label>
                    <textarea id="edit_notes" name="notes" class="input" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer" style="flex-shrink: 0; background: white; padding: 1.5rem; border-top: 1px solid #B2DFDB; display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" class="btn btn-secondary" data-close-modal="editDeliveryModal">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Delivery</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Form -->
<form id="deleteForm" method="POST" action="" style="display: none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" id="delete_id" name="id">
</form>

<script>
    function populateDeliveryForm(selectElement) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];

        if (selectedOption.value) {
            const customerId = selectedOption.getAttribute('data-customer-id');
            const customerName = selectedOption.getAttribute('data-customer-name');
            const customerPhone = selectedOption.getAttribute('data-customer-phone');
            const address = selectedOption.getAttribute('data-address');
            const items = selectedOption.getAttribute('data-items');

            // Populate hidden customer_id field
            document.getElementById('add_customer_id').value = customerId;

            // Display customer info
            document.getElementById('customer_info').innerHTML = `
            <div style="font-weight: 500; color: var(--neutral-900);">${customerName}</div>
            <div style="font-size: 0.875rem; margin-top: 0.25rem;">${customerPhone}</div>
        `;

            // Show and populate address and items sections
            document.getElementById('delivery_address_section').style.display = 'block';
            document.getElementById('items_section').style.display = 'block';
            document.getElementById('add_delivery_address').value = address;
            document.getElementById('add_items').value = items;
        } else {
            document.getElementById('add_customer_id').value = '';
            document.getElementById('customer_info').innerHTML = 'Select an order to view customer details';
            document.getElementById('delivery_address_section').style.display = 'none';
            document.getElementById('items_section').style.display = 'none';
            document.getElementById('add_delivery_address').value = '';
            document.getElementById('add_items').value = '';
        }
    }

    function editDelivery(delivery) {
        document.getElementById('edit_id').value = delivery.id;
        document.getElementById('edit_delivery_address').value = delivery.delivery_address;
        document.getElementById('edit_scheduled_date').value = delivery.scheduled_date;
        document.getElementById('edit_scheduled_time').value = delivery.scheduled_time;
        document.getElementById('edit_status').value = delivery.status;
        document.getElementById('edit_notes').value = delivery.notes || '';
        openModal('editDeliveryModal');
    }

    function deleteDelivery(id, customerName) {
        if (confirmDelete(`delivery for ${customerName}`)) {
            document.getElementById('delete_id').value = id;
            document.getElementById('deleteForm').submit();
        }
    }
</script>

<?php
closeDBConnection($conn);
renderFooter();
?>