document.addEventListener('DOMContentLoaded', function() {
    // Initialize modal
    const modal = document.getElementById('editClientModal');
    const closeBtn = document.querySelector('.close');
    
    // Close modal when clicking X
    closeBtn.addEventListener('click', function() {
        modal.style.display = 'none';
    });
    
    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });

    // Delete Client Handler
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-delete')) {
            const button = e.target.closest('.btn-delete');
            const clientId = button.getAttribute('data-id');
            
            if (confirm('Are you sure you want to delete this client?')) {
                deleteClient(clientId);
            }
        }
        
        // Edit Client Handler
        if (e.target.closest('.btn-edit')) {
            const button = e.target.closest('.btn-edit');
            openEditModal(
                button.getAttribute('data-id'),
                button.getAttribute('data-name'),
                button.getAttribute('data-id-number'),
                button.getAttribute('data-phone'),
                button.getAttribute('data-address'),
                button.getAttribute('data-id-front'),
                button.getAttribute('data-id-back'),
                button.getAttribute('data-profile')
            );
        }
    });

    // Edit Client Form Submission
    document.getElementById('editClientForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        updateClient(formData);
    });
});

// Delete Client Function
function deleteClient(clientId) {
    fetch('api/delete_client.php', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `clientId=${clientId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the row from the table
            document.querySelector(`tr[data-id="${clientId}"]`).remove();
            showAlert('Client deleted successfully', 'success');
        } else {
            showAlert('Error: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while deleting the client', 'error');
    });
}

// Open Edit Modal
function openEditModal(id, name, idNumber, phone, address, idFront, idBack, profile) {
    const modal = document.getElementById('editClientModal');
    
    // Populate form fields
    document.getElementById('editClientId').value = id;
    document.getElementById('editClientName').value = name;
    document.getElementById('editClientIdNumber').value = idNumber;
    document.getElementById('editClientPhone').value = phone;
    document.getElementById('editClientAddress').value = address;
    
    // Display current images
    document.getElementById('currentIdFront').innerHTML = idFront ? 
        `<img src="images/clients/${idFront}" alt="ID Front" class="preview-img">` : 
        '<p>No ID Front Image</p>';
    
    document.getElementById('currentIdBack').innerHTML = idBack ? 
        `<img src="images/clients/${idBack}" alt="ID Back" class="preview-img">` : 
        '<p>No ID Back Image</p>';
    
    document.getElementById('currentProfile').innerHTML = profile ? 
        `<img src="images/clients/${profile}" alt="Profile" class="preview-img">` : 
        '<p>No Profile Image</p>';
    
    // Show modal
    modal.style.display = 'block';
}

// Update Client
function updateClient(formData) {
    fetch('api/update_client.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Client updated successfully', 'success');
            setTimeout(() => location.reload(), 1500); // Refresh after 1.5 seconds
        } else {
            showAlert('Error: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while updating the client', 'error');
    });
}

// Show alert message
function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    document.querySelector('.client-management').prepend(alertDiv);
    
    setTimeout(() => alertDiv.remove(), 5000);
}

// Close modal
function closeModal() {
    document.getElementById('editClientModal').style.display = 'none';
}