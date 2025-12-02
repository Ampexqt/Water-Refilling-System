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
    if (!id || id <= 0) {
        console.error('Invalid order ID:', id);
        alert('Error: Invalid order ID. Please try again.');
        return;
    }
    
    deleteOrderId = id;
    deleteOrderName = customerName;
    
    const deleteIdField = document.getElementById('delete_id');
    const deleteNameField = document.getElementById('deleteOrderName');
    
    if (deleteIdField && deleteNameField) {
        deleteIdField.value = id;
        deleteNameField.textContent = 'Order for: ' + customerName;
        openModal('deleteOrderModal');
    } else {
        console.error('Delete modal elements not found');
        alert('Error: Delete modal not properly loaded. Please refresh the page.');
    }
}
