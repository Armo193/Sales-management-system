<?php 
// Include database connection first
require_once 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Management System</title>
    <link rel="stylesheet" href="clients.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>

<div class="client-management">
    <h1>Client Management</h1>
    
    <!-- Success/Error Messages -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Client operation completed successfully!</div>
    <?php elseif (isset($_GET['error'])): ?>
        <div class="alert alert-error">Error: <?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>
    
    <!-- Add Client Form -->
    <div class="add-client-form">
        <h2>Add New Client</h2>
        <form id="clientForm" enctype="multipart/form-data" action="api/add_client.php" method="POST">
            <div class="form-group">
                <label for="clientName">Full Name:</label>
                <input type="text" id="clientName" name="clientName" required>
            </div>
            
            <div class="form-group">
                <label for="clientIdNumber">ID Number:</label>
                <input type="text" id="clientIdNumber" name="clientIdNumber" required>
            </div>
            
            <div class="form-group">
                <label for="clientIdFront">ID Front Image:</label>
                <input type="file" id="clientIdFront" name="clientIdFront" accept="image/*" required>
                <small>Max size: 2MB (JPEG, PNG)</small>
            </div>
            
            <div class="form-group">
                <label for="clientIdBack">ID Back Image:</label>
                <input type="file" id="clientIdBack" name="clientIdBack" accept="image/*" required>
                <small>Max size: 2MB (JPEG, PNG)</small>
            </div>
            
            <div class="form-group">
                <label for="clientPhone">Phone Number:</label>
                <input type="tel" id="clientPhone" name="clientPhone" required pattern="[0-9]{10,15}">
            </div>
            
            <div class="form-group">
                <label for="clientAddress">Location Address:</label>
                <textarea id="clientAddress" name="clientAddress" required rows="3"></textarea>
            </div>
            
            <div class="form-group">
                <label for="clientProfile">Profile Photo:</label>
                <input type="file" id="clientProfile" name="clientProfile" accept="image/*">
                <small>Optional (Max size: 2MB)</small>
            </div>
            
            <button type="submit" class="btn">Save Client</button>
        </form>
    </div>
    
    <!-- Client List -->
    <div class="client-list">
        <h2>Client Directory</h2>
        <div class="search-box">
            <input type="text" id="clientSearch" placeholder="Search clients..." onkeyup="searchClients()">
            <button class="btn-search" onclick="searchClients()"><i class="fas fa-search"></i></button>
        </div>
        <table id="clientsTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Photo</th>
                    <th>Name</th>
                    <th>ID Number</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                try {
                    $stmt = $pdo->query("SELECT * FROM clients ORDER BY name ASC");
                    if ($stmt->rowCount() > 0) {
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            // Set default image if not available
                            $profileImage = !empty($row['profile_image']) ? 
                                'images/clients/' . htmlspecialchars($row['profile_image']) : 
                                'images/default-profile.png';
                            
                            echo "<tr data-id='".htmlspecialchars($row['client_id'])."'>";
                            echo "<td>".htmlspecialchars($row['client_id'])."</td>";
                            echo "<td><img src='".$profileImage."' alt='".htmlspecialchars($row['name'])."' class='client-thumb'></td>";
                            echo "<td>".htmlspecialchars($row['name'])."</td>";
                            echo "<td>".htmlspecialchars($row['id_number'])."</td>";
                            echo "<td>".htmlspecialchars($row['phone'])."</td>";
                            echo "<td>".htmlspecialchars($row['address'])."</td>";
                            echo "<td>
                                    <button class='btn-view' 
                                            data-id='".htmlspecialchars($row['client_id'])."'
                                            data-name='".htmlspecialchars($row['name'])."'
                                            data-id-number='".htmlspecialchars($row['id_number'])."'
                                            data-phone='".htmlspecialchars($row['phone'])."'
                                            data-address='".htmlspecialchars($row['address'])."'
                                            data-id-front='".htmlspecialchars($row['id_front_image'] ?? '')."'
                                            data-id-back='".htmlspecialchars($row['id_back_image'] ?? '')."'
                                            data-profile='".htmlspecialchars($row['profile_image'] ?? '')."'>
                                        <i class='fas fa-eye'></i> View
                                    </button>
                                    <button class='btn-edit' 
                                            data-id='".htmlspecialchars($row['client_id'])."'
                                            data-name='".htmlspecialchars($row['name'])."'
                                            data-id-number='".htmlspecialchars($row['id_number'])."'
                                            data-phone='".htmlspecialchars($row['phone'])."'
                                            data-address='".htmlspecialchars($row['address'])."'
                                            data-id-front='".htmlspecialchars($row['id_front_image'] ?? '')."'
                                            data-id-back='".htmlspecialchars($row['id_back_image'] ?? '')."'
                                            data-profile='".htmlspecialchars($row['profile_image'] ?? '')."'>
                                        <i class='fas fa-edit'></i> Edit
                                    </button>
                                    <button class='btn-delete' data-id='".htmlspecialchars($row['client_id'])."'>
                                        <i class='fas fa-trash'></i> Delete
                                    </button>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7' class='no-data'>No clients found</td></tr>";
                    }
                } catch (PDOException $e) {
                    echo "<tr><td colspan='7' class='error'>Error loading clients: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Edit Client Modal -->
<div id="editClientModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Edit Client</h2>
        <form id="editClientForm" enctype="multipart/form-data">
            <input type="hidden" id="editClientId" name="clientId">
            
            <div class="form-group">
                <label for="editClientName">Full Name:</label>
                <input type="text" id="editClientName" name="clientName" required>
            </div>
            
            <div class="form-group">
                <label for="editClientIdNumber">ID Number:</label>
                <input type="text" id="editClientIdNumber" name="clientIdNumber" required>
            </div>
            
            <div class="form-group">
                <label>Current ID Front Image:</label>
                <div id="currentIdFront" class="image-preview"></div>
                <label for="editClientIdFront">Update ID Front Image:</label>
                <input type="file" id="editClientIdFront" name="clientIdFront" accept="image/*">
            </div>
            
            <div class="form-group">
                <label>Current ID Back Image:</label>
                <div id="currentIdBack" class="image-preview"></div>
                <label for="editClientIdBack">Update ID Back Image:</label>
                <input type="file" id="editClientIdBack" name="clientIdBack" accept="image/*">
            </div>
            
            <div class="form-group">
                <label for="editClientPhone">Phone Number:</label>
                <input type="tel" id="editClientPhone" name="clientPhone" required>
            </div>
            
            <div class="form-group">
                <label for="editClientAddress">Location Address:</label>
                <textarea id="editClientAddress" name="clientAddress" required rows="3"></textarea>
            </div>
            
            <div class="form-group">
                <label>Current Profile Photo:</label>
                <div id="currentProfile" class="image-preview"></div>
                <label for="editClientProfile">Update Profile Photo:</label>
                <input type="file" id="editClientProfile" name="clientProfile" accept="image/*">
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update Client</button>
                <button type="button" class="btn btn-cancel" onclick="closeModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- View Client Modal -->
<div id="viewClientModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Client Details</h2>
        
        <div class="client-details">
            <div class="detail-row">
                <span class="detail-label">Client ID:</span>
                <span id="viewClientId" class="detail-value"></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Full Name:</span>
                <span id="viewClientName" class="detail-value"></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">ID Number:</span>
                <span id="viewClientIdNumber" class="detail-value"></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Phone Number:</span>
                <span id="viewClientPhone" class="detail-value"></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Address:</span>
                <span id="viewClientAddress" class="detail-value"></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">ID Front Image:</span>
                <div id="viewIdFront" class="image-preview"></div>
            </div>
            <div class="detail-row">
                <span class="detail-label">ID Back Image:</span>
                <div id="viewIdBack" class="image-preview"></div>
            </div>
            <div class="detail-row">
                <span class="detail-label">Profile Photo:</span>
                <div id="viewProfile" class="image-preview"></div>
            </div>
        </div>
        
        <div class="modal-actions">
            <button type="button" class="btn btn-close" onclick="closeViewModal()">Close</button>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="clients.js"></script>
<script>
// View Client Details functionality
document.addEventListener('DOMContentLoaded', function() {
    // Handle view button clicks
    document.querySelectorAll('.btn-view').forEach(button => {
        button.addEventListener('click', function() {
            const clientId = this.getAttribute('data-id');
            const clientName = this.getAttribute('data-name');
            const clientIdNumber = this.getAttribute('data-id-number');
            const clientPhone = this.getAttribute('data-phone');
            const clientAddress = this.getAttribute('data-address');
            const idFrontImage = this.getAttribute('data-id-front');
            const idBackImage = this.getAttribute('data-id-back');
            const profileImage = this.getAttribute('data-profile');
            
            // Populate modal
            document.getElementById('viewClientId').textContent = clientId;
            document.getElementById('viewClientName').textContent = clientName;
            document.getElementById('viewClientIdNumber').textContent = clientIdNumber;
            document.getElementById('viewClientPhone').textContent = clientPhone;
            document.getElementById('viewClientAddress').textContent = clientAddress;
            
            // Set images (if available)
            const viewIdFront = document.getElementById('viewIdFront');
            const viewIdBack = document.getElementById('viewIdBack');
            const viewProfile = document.getElementById('viewProfile');
            
            viewIdFront.innerHTML = idFrontImage ? 
                `<img src="images/clients/${idFrontImage}" alt="ID Front" style="max-width: 100%;">` : 
                '<p>No image available</p>';
                
            viewIdBack.innerHTML = idBackImage ? 
                `<img src="images/clients/${idBackImage}" alt="ID Back" style="max-width: 100%;">` : 
                '<p>No image available</p>';
                
            viewProfile.innerHTML = profileImage ? 
                `<img src="images/clients/${profileImage}" alt="Profile" style="max-width: 100%; border-radius: 50%;">` : 
                '<p>No profile image</p>';
            
            // Show modal
            document.getElementById('viewClientModal').style.display = 'block';
        });
    });

    // Close View Modal
    window.closeViewModal = function() {
        document.getElementById('viewClientModal').style.display = 'none';
    }

    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        const viewModal = document.getElementById('viewClientModal');
        if (event.target == viewModal) {
            viewModal.style.display = 'none';
        }
    });

    // Close button for view modal
    document.querySelector('#viewClientModal .close').addEventListener('click', closeViewModal);
});
</script>

<?php include 'footer.php'; ?>
</body>
</html>