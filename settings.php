<?php
require_once 'configs/auth.php';
require_once 'configs/logger.php';
checkAuth();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;700&display=swap');

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Montserrat', sans-serif;
}
body {
    display: flex;
    background-color: rgb(241, 239, 249);
}

.current-time {
            font-size: 16px;
            margin-right: 20px;
            color:rgb(16, 69, 205);
            background-color: white;
            padding: 12px 24px;
            border-radius: 12px;
            border: 2px solid rgb(15, 1, 97);
            font-weight: bold;
            width: 150px;                     /* Fixed width */
    text-align: center;               /* Centers the time text */
    white-space: nowrap; 
        }

    .settings-container {
    margin-top: 20px;
}
.section {
    margin-bottom: 30px;
}
.settings-section h2 {
    color:rgb(251, 251, 251);
    margin-bottom: 10px;
}
.settings-option {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    gap: 20px;
    flex-wrap: wrap;
}
.settings-card {
    background:rgb(232, 237, 253);
    border: 2px solid rgb(18, 3, 95);
    padding: 20px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    gap: 15px;
    cursor: pointer;
    width: 300px;
    transition: all 0.3s ease;
}
.settings-card:hover {
    background:rgb(236, 241, 255);
}
.settings-card i {
    font-size: 50px;
    color:rgb(3, 7, 98);
}
.settings-card .text {
    color:rgb(3, 26, 116);
    font-size: 30px;
    font-weight: bold;
}
.settings-card p {
    font-size: 14px;
    color:rgb(5, 108, 219);
}

/* ===== Modal Styles ===== */
.modal {
    display: none;  /* Ensure it starts hidden */
    position: fixed;
    top: 0; 
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(3px);
 
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

/* Modal Content */
.popup-content {
    background: white;
    padding: 25px;
    border-radius: 15px;
    width: 60%;
    max-width: 500px;
    position: relative;
    text-align: center;
    box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
}
.popup-content1 {
    background: white;
    padding: 25px;
    border-radius: 15px;
    width: 60%;
    max-width: 500px;
    position: relative;
    text-align: center;
    box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
    margin: 0 auto; /* This centers the modal horizontally */
    top: 50%; /* This centers the modal vertically */
    transform: translateY(-50%); 
}
.popup-content2 {
    background: white;
    padding: 25px;
    border-radius: 15px;
    width: 60%;
    max-width: 500px;
    position: relative;
    text-align: center;
    box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
    margin: 0 auto; /* This centers the modal horizontally */
    top: 50%; /* This centers the modal vertically */
    transform: translateY(-50%); 
}


/* Close Button */
.close-button {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 22px;
    cursor: pointer;
    color:rgb(23, 1, 111);
    transition: 0.2s ease-in-out;
}

.close-button:hover {
    color:rgb(35, 2, 168);
    transform: scale(1.2);
}

/* Table Styles */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
}

th, td {
    border: 1px solid rgb(7, 2, 98);
    padding: 12px;
    text-align: center;
}

th {
    background:rgb(20, 2, 110);
    font-weight: bold;
    color: white;
    text-transform: uppercase;
}

td {
    background:rgb(248, 248, 248);
}

/* Edit Button */
.edit-button, .deactivate-button {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 18px;
    color:rgb(8, 2, 91);
    transition: 0.3s ease-in-out;
}

.edit-button:hover, .deactivate-button:hover {
    color:rgb(33, 22, 183);
}

form {
    justify-content: left;
    text-align: left;
    margin: 20px;
}

label {
    color:rgba(22, 4, 95, 0.47);
    font-weight: bold;
    font-size: 15px;
    margin-bottom: 30px;
}

select {
    margin-bottom: 15px;
}
input {
    margin-bottom: 15px;
}

input[type="password"], input[type="text"], input[type="email"],
select {
    width: 100%;
    padding: 10px;
    border: 2px solid rgb(20, 3, 90);
    font-size: 20px;
    color:rgb(13, 0, 150);
    border-radius: 8px;
    font-size: 16px;
    outline: none;
    transition: all 0.3s ease-in-out;
}

input[type="password"]:focus, input[type="text"]:focus,  input[type="email"]:focus,
select:focus {
    border-color:rgb(20, 3, 90);
    box-shadow: 0px 0px 8px rgba(77, 34, 230, 0.5);
    font-size: 20px;
    color:rgb(13, 0, 150);
}

/* Submit Button */
button[type="submit"] {
    background:rgb(20, 3, 90);
    color: white;
    padding: 12px 15px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
    width: 100%;
    margin-top: 10px;
}

button[type="submit"]:hover {
    background:rgb(37, 4, 169);
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
}

/* Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive */
@media (max-width: 768px) {
    .popup-content {
        width: 80%;
    }
}

@media (max-width: 500px) {
    .popup-content {
        width: 95%;
    }
}



.add-account-btn, .view-accounts-btn {
    background-color:rgb(1, 13, 101);
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 4px;
    cursor: pointer;
    font-weight: bold;
}
.add-account-btn:hover {
    background-color:rgb(38, 4, 153);
}
#accountsTableSection {
    position: fixed; /* stays in place and supports z-index */
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%); /* perfectly center */
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    z-index: 9999; /* bring to front */
    width: auto; /* adjust based on content */
    max-width: 90vw;
    height: 300px; 
}
#closeAccountsTable {
    position: absolute;
    top: 10px;
    right: 10px;
    background: transparent;
    border: none;
    font-size: 24px;
    cursor: pointer;
    z-index: 10000;
}
.ac{
    font-size: 25px;
    margin-bottom: 20px; /* Adjust as needed */
}
#closeAccountsSection{
    position: absolute;
    top: 10px;
    right: 10px;
    background: transparent;
    border: none;
    font-size: 24px;
    cursor: pointer;
    z-index: 10000;

}

</style>
<body>
    <?php include 'sidebar.php'; ?>
    
    <div class="main-content">
        <div class="dashboard-header">
            <span>Settings</span>
            <div class="header-right">
            <div id="current-time" class="current-time"></div>
                <button onclick="redirectToAuthorization(event)"class="lupon-btn">
                <?php echo htmlspecialchars($_SESSION['username']); ?> <i class="fas fa-sign-out-alt"></i>
                </button>
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php 
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <div class="settings-container">
            <div class="settings-section">
                <h2>Data</h2>
                <div class="settings-option">
                <div class="settings-card" onclick="backupDatabase()">
                    <i class="fas fa-download"></i>
                    <div>
                        <div class="text">Backup Data</div>
                        <strong><p>Click to download the backup</p></strong>
                    </div>
                </div>


                <form action="configs/backup_restore.php" method="post" enctype="multipart/form-data">
    <div class="settings-card" onclick="document.getElementById('restoreInput').click();">
        <i class="fas fa-sync-alt"></i>
        <div>
            <div class="text">Restore Data</div>
            <strong><p>Upload backup and Restore</p></strong>
        </div>
        <input id="restoreInput" type="file" name="backup_file" accept=".sql" style="display: none;" onchange="this.form.submit()">
    </div>
</form>

                  </div>
             </div>


            <div class="settings-section">
                <h2>Account</h2>
                <div class="settings-option">
                    <div class="settings-card" onclick="openManageAccountModal()">
                        <i class="fas fa-users"></i>
                        <div>
                            <div class="text">Manage Account</div>
                            <strong><p>Lupon / Official</p></strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="settings-section">
                <h2>System Logs</h2>
                <div class="settings-option">
                    <div class="settings-card" onclick="openLogSettingsModal()">
                        <i class="fas fa-clipboard-list"></i>
                        <div>
                            <div class="text">Logging Settings</div>
                            <strong><p>File / Database Storage</p></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Manage Account Modal -->
    <div id="manageAccountModal" class="modal">
        <div class="popup-content">
        <div style="text-align: left; margin-bottom: 10px;">
    <button class="add-account-btn" onclick="openAddAccountModal()">+</button>
    <button class="view-accounts-btn" onclick="toggleAccountsTable()" title="View Accounts">
        <i class="fas fa-eye"></i>
    </button>
</div>


            <span class="close-button" onclick="closeManageAccountModal()">&times;</span>
            <h2 style = "color: rgb(13, 0, 150)">MANAGE ACCOUNT</h2>
            <table>
                <thead>
                    <tr>
                        <th>ACTION</th>
                        <th>ROLE</th>
                        <th>ACCESS</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <button class="edit-button" onclick="openPasswordModal('Lupon')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="deactivate-button" onclick="openDeactivateModal('Lupon')">
            <i class="fas fa-user-slash"></i>
        </button>
                        </td>
                        <td><strong>LUPON</strong></td>
                        <td><span class="access-role">Editor</span></td>
                    </tr>
                    <tr>
                        <td>
                            <button class="edit-button" onclick="openPasswordModal('Official')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="deactivate-button" onclick="openDeactivateModal('Official')">
            <i class="fas fa-user-slash"></i>
        </button>
                        </td>
                        <td><strong>OFFICIAL</strong></td>
                        <td><span class="access-role">Viewer</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div id="passwordModal" class="modal">
        <div class="popup-content">
            <span class="close-button" onclick="closePasswordModal()">&times;</span>
            <h2 style = "color: rgb(6, 13, 147)">Change Password</h2>
            <form id="changePasswordForm" onsubmit="updatePassword(event)">
                <label for="accountType">Account Type:</label>
                <select name="accountType" id="accountType" required>
                    <option value="Lupon">Lupon</option>
                    <option value="Official">Official</option>
                </select><br>

                <label for="username">Username:</label>
                <input type="text" name="username" id="username" required><br>
                
                <label for="current_password">Current Password:</label>
                <input type="password" name="current_password" id="current_password" required><br>
                
                <label for="new_password">New Password:</label>
                <input type="password" name="new_password" id="new_password" required><br>
                
                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" name="confirm_password" id="confirm_password" required><br>
                
                <button type="submit">Change Password</button>
            </form>
        </div>
    </div>

<!-- Add Account Modal -->
<div id="addAccountModal" class="modal">
    <div class="popup-content1">
        <span class="close-button" onclick="closeAddAccountModal()">&times;</span>
        <h2 style="color: rgb(6, 13, 147)">Add New Account</h2>
        <form action="php/add_account.php" method="POST">
            <label for="new_username">Username:</label>
            <input type="text" name="new_username" required><br>

            <label for="new_email">Email:</label>
            <input type="email" name="new_email" required><br>

            <label for="new_password">Password:</label>
            <input type="password" name="new_password" required><br>

            <label for="accountType">Account Type:</label>
            <select name="accountType" required>
                <option value="Lupon">Lupon</option>
                <option value="Official">Official</option>
            </select><br>

            <button type="submit">Add Account</button>
        </form>
    </div>
</div>


<!-- View All Accounts Modal -->
<div id="accountsTableSection" style="display: none;">
<button id="closeAccountsSection">&times;</button>

    <h3 class="ac" style="margin-top: 15px;">All Accounts</h3>
    <table id="accountsTable">
        <thead>
            <tr>
                <th>USERNAME</th>
                <th>EMAIL</th>
                <th>TYPE</th>
                <th>STATUS</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>


<!-- Deactivate Account Modal -->
<div id="deactivateModal" class="modal">
    <div class="popup-content2">
        <span class="close-button" onclick="closeDeactivateModal()">&times;</span>
        <h2 style="color: red;">Deactivate Account</h2>
        <form id="deactivateForm" onsubmit="deactivateAccount(event)">
            <label for="deactivate_username">Username:</label>
            <input type="text" name="deactivate_username" id="deactivate_username" required><br>

            <label for="deactivate_password">Password:</label>
            <input type="password" name="deactivate_password" id="deactivate_password" required><br>

            <button type="submit" style="background-color: red; color: white;">Deactivate</button>
        </form>
    </div>
</div>


    <script>

function updateTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    document.getElementById('current-time').textContent = timeString;
  }

  // Update time every second
  setInterval(updateTime, 1000);
  updateTime(); // Run once on page load
  
function openDeactivateModal(accountType) {
    document.getElementById("deactivateModal").style.display = "block";
    document.getElementById("deactivateForm").accountType.value = accountType; // Set the account type in form
}

function closeDeactivateModal() {
    document.getElementById('deactivateModal').style.display = 'none';  // Hide the modal
}


function deactivateAccount(event) {
    event.preventDefault();  // Prevent form from submitting normally

    // Get form data
    const username = document.getElementById('deactivate_username').value;
    const password = document.getElementById('deactivate_password').value;

    // Create a FormData object
    const formData = new FormData();
    formData.append('deactivate_username', username);
    formData.append('deactivate_password', password);

    // Send the data to the server using Fetch API
    fetch('php/deactivate_account.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);  // Show success message
            closeDeactivateModal();  // Close the modal
        } else {
            alert(data.message);  // Show error message
        }
    })
    .catch(error => {
        alert('An error occurred: ' + error);  // Show error message
    });
}



function openAddAccountModal() {
    document.getElementById("addAccountModal").style.display = "block";
}

function closeAddAccountModal() {
    document.getElementById("addAccountModal").style.display = "none";
}

            function backupDatabase() {
            window.location.href = "configs/backup_restore.php?backup=true";
        }

        function redirectToAuthorization(event) {
            event.preventDefault(); 
            window.location.href = "configs/logout.php"; 
        }


    
    // Open & Close Manage Account Modal
    function openManageAccountModal() {
        document.getElementById("manageAccountModal").style.display = "flex";
    }
    function closeManageAccountModal() {
        document.getElementById("manageAccountModal").style.display = "none";
    }
    
    // Open & Close Password Change Modal
    function openPasswordModal(role) {
        document.getElementById("accountType").value = role;
        document.getElementById("passwordModal").style.display = "flex";
    }
    function closePasswordModal() {
        document.getElementById("passwordModal").style.display = "none";
        document.getElementById("changePasswordForm").reset();
        document.getElementById("accountType").selectedIndex = 0;
    }
    document.getElementById('closeAccountsSection').addEventListener('click', function () {
    document.getElementById('accountsTableSection').style.display = 'none';
  });
    
    // Handle Password Update Submission
    function updatePassword(event) {
        event.preventDefault();
        let formData = new FormData(document.getElementById("changePasswordForm"));
    
        fetch("php/change_password.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.success) {
                document.getElementById("changePasswordForm").reset();
            }
            else {
                document.getElementById("changePasswordForm").reset(); 
            }
        })
        .catch(error => console.error("Error:", error));
    }

    function toggleAccountsTable() {
    const section = document.getElementById('accountsTableSection');
    if (section.style.display === 'none') {
        section.style.display = 'block';
        fetchAllAccounts();
    } else {
        section.style.display = 'none';
    }
}

function fetchAllAccounts() {
    fetch('php/fetch_accounts.php')
        .then(response => response.json())
        .then(data => {
            const tableBody = document.querySelector('#accountsTable tbody');
            tableBody.innerHTML = ''; // Clear previous

            data.forEach(account => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${account.username}</td>
                    <td>${account.email}</td>
                    <td>${account.accountType}</td>
                    <td>${account.status}</td>
                `;
                tableBody.appendChild(row);
            });
        })
        .catch(error => {
            console.error('Error fetching accounts:', error);
        });
}

// Logging settings modal
function openLogSettingsModal() {
    document.getElementById('logSettingsModal').style.display = 'flex';
}

function closeLogSettingsModal() {
    document.getElementById('logSettingsModal').style.display = 'none';
}

function saveLoggingSettings() {
    const loggingType = document.querySelector('input[name="logging_type"]:checked').value;
    
    // Send AJAX request to save the settings
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'configs/save_logging_config.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (this.status === 200) {
            if (this.responseText === 'success') {
                alert('Logging settings saved successfully.');
                closeLogSettingsModal();
                // Optional: Reload the page to reflect changes
                location.reload();
            } else {
                alert('Error: ' + this.responseText);
            }
        } else {
            alert('Error updating settings.');
        }
    };
    xhr.send('logging_type=' + loggingType);
}
    </script>

<!-- Log Settings Modal -->
<div id="logSettingsModal" class="modal">
    <div class="popup-content">
        <span class="close-button" onclick="closeLogSettingsModal()">&times;</span>
        <h2 style="color: rgb(13, 0, 150)">System Logging Settings</h2>
        
        <?php 
        $configFile = __DIR__ . '/logs/config.php';
        $useDatabase = false;
        $useFile = true;
        if (file_exists($configFile)) {
            include_once $configFile;
            $useDatabase = isset($LOGGING_USE_DATABASE) ? $LOGGING_USE_DATABASE : false;
            $useFile = isset($LOGGING_USE_FILE) ? $LOGGING_USE_FILE : true;
        }
        $currentMode = 'file';
        if ($useDatabase && $useFile) {
            $currentMode = 'both';
        } elseif ($useDatabase) {
            $currentMode = 'database';
        }
        ?>
        
        <!-- Current Settings Status -->
        <div style="background-color: #f0f4ff; border-left: 4px solid #3f51b5; padding: 12px; margin: 15px 0; border-radius: 4px; text-align: left;">
            <p style="margin: 0; font-weight: bold; color: #3f51b5;">Current Setting: 
                <span style="
                    display: inline-block;
                    padding: 4px 10px;
                    border-radius: 12px;
                    font-size: 14px;
                    margin-left: 5px;
                    <?php if ($currentMode === 'both'): ?>
                        background-color: #e8f5e9; 
                        color: #2e7d32;
                    <?php elseif ($currentMode === 'database'): ?>
                        background-color: #e3f2fd; 
                        color: #1976d2;
                    <?php else: ?>
                        background-color: #fff8e1; 
                        color: #ff8f00;
                    <?php endif; ?>
                ">
                    <?php 
                    if ($currentMode === 'both') echo 'Both (File + Database)';
                    elseif ($currentMode === 'database') echo 'Database Only';
                    else echo 'File Only';
                    ?>
                </span>
            </p>
        </div>
        
        <form id="loggingSettingsForm">
            <div style="margin: 10px 0 20px 0; text-align: left;">
                <p style="font-weight: bold; margin-bottom: 15px; color: rgb(3, 26, 116);">Choose where to store system logs:</p>
                
                <div class="logging-option" style="display: flex; align-items: center; padding: 12px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 8px; cursor: pointer; transition: all 0.2s ease;" onclick="document.getElementById('file_logging').click()">
                    <input type="radio" id="file_logging" name="logging_type" value="file" 
                           <?php echo ($currentMode === 'file') ? 'checked' : ''; ?> style="margin-right: 15px; transform: scale(1.3);">
                    <div>
                        <label for="file_logging" style="font-weight: bold; color: rgb(3, 26, 116); margin-bottom: 5px; display: block; cursor: pointer;">File Storage Only</label>
                        <p style="margin: 0; color: #666; font-size: 13px;">Stores logs in text files. Simple but with limited search capabilities.</p>
                    </div>
                </div>
                
                <div class="logging-option" style="display: flex; align-items: center; padding: 12px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 8px; cursor: pointer; transition: all 0.2s ease;" onclick="document.getElementById('db_logging').click()">
                    <input type="radio" id="db_logging" name="logging_type" value="database"
                           <?php echo ($currentMode === 'database') ? 'checked' : ''; ?> style="margin-right: 15px; transform: scale(1.3);">
                    <div>
                        <label for="db_logging" style="font-weight: bold; color: rgb(3, 26, 116); margin-bottom: 5px; display: block; cursor: pointer;">Database Storage Only</label>
                        <p style="margin: 0; color: #666; font-size: 13px;">Stores logs in the database. Better for searching and filtering logs.</p>
                    </div>
                </div>
                
                <div class="logging-option" style="display: flex; align-items: center; padding: 12px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 8px; cursor: pointer; transition: all 0.2s ease;" onclick="document.getElementById('both_logging').click()">
                    <input type="radio" id="both_logging" name="logging_type" value="both"
                           <?php echo ($currentMode === 'both') ? 'checked' : ''; ?> style="margin-right: 15px; transform: scale(1.3);">
                    <div>
                        <label for="both_logging" style="font-weight: bold; color: rgb(3, 26, 116); margin-bottom: 5px; display: block; cursor: pointer;">Both File & Database Storage <span style="color: #4caf50;">(Recommended)</span></label>
                        <p style="margin: 0; color: #666; font-size: 13px;">Maximum reliability with dual storage. Best for important systems.</p>
                    </div>
                </div>
                
                <div style="background-color: #fffde7; border-left: 4px solid #ffc107; padding: 12px; margin: 15px 0; border-radius: 4px;">
                    <p style="margin: 0; font-weight: bold; color: #ff8f00;">Note:</p>
                    <p style="margin: 8px 0 0 0; color: #666; font-size: 14px;">
                        Changes to logging settings will take effect immediately. You can view logs at any time by going to 
                        <a href="logs.php" style="color: #1976d2; text-decoration: none; font-weight: bold;">System Logs</a>.
                    </p>
                </div>
            </div>
            
            <button type="button" onclick="saveLoggingSettings()" style="background: rgb(20, 3, 90); color: white; padding: 12px; border: none; border-radius: 5px; cursor: pointer; width: 100%; font-weight: bold; font-size: 16px; transition: all 0.2s ease;">
                Save Logging Settings
            </button>
        </form>
    </div>
</div>

<style>
/* Additional styles for logging options */
.logging-option:hover {
    background-color: #f8f9ff;
    border-color: #3f51b5;
}

/* Style for the active/selected logging option */
.logging-option:has(input:checked) {
    background-color: #edf7ff;
    border-color: #1976d2;
    border-width: 2px;
}
</style>

<script>
// Add to existing script to enhance the logging settings UI
document.addEventListener('DOMContentLoaded', function() {
    var loggingOptions = document.querySelectorAll('.logging-option');
    
    for (var i = 0; i < loggingOptions.length; i++) {
        loggingOptions[i].addEventListener('click', function() {
            // Visually select the option (update will happen via the radio button click handler)
            for (var j = 0; j < loggingOptions.length; j++) {
                loggingOptions[j].style.backgroundColor = '#ffffff';
            }
            this.style.backgroundColor = '#edf7ff';
        });
    }
});
</script>
</body>
</html>