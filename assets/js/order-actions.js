// Order management functions for forms page
let deleteOrderId = null;
let deleteOrderName = '';

function editOrder(order) {
    document.getElementById('edit_id').value = order.id;
    document.getElementById('edit_customer_id').value = order.customer_id;
    document.getElementById('edit_container_size').value = order.container_size;
    document.getElementById('edit_quantity').value = order.quantity;
    document.getElementById('edit_delivery_date').value = order.delivery_date;
    document.getElementById('edit_delivery_time').value = order.delivery_time;
    document.getElementById('edit_notes').value = order.notes || '';
    document.getElementById('edit_status').value = order.status;
    openModal('editOrderModal');
}

function deleteOrder(id, customerName) {
    deleteOrderId = id;
    deleteOrderName = customerName;
    document.getElementById('deleteOrderName').textContent = 'Order for: ' + customerName;
    openModal('deleteOrderModal');
}

function confirmDeleteOrder() {
    if (deleteOrderId) {
        document.getElementById('delete_id').value = deleteOrderId;
        document.getElementById('deleteForm').submit();
    }
}
