<?php
require_once 'configs/auth.php';
checkAuth();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archive</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
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
    background-color: rgb(249, 244, 239);
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
        
        .table-container {
    max-height: 680px; /* Adjust height as needed */
    overflow-y: auto;
    display: block;
    border: 2px solid rgb(8, 7, 106);/* Optional: Add border for visibility */
    border-radius: 8px; /* Optional: Rounded edges */
    background: white; /* Ensure the table background remains white */
    box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.1); /* Optional: Add shadow */
}

table {
    width: 100%; /* Ensure it takes full width */
    border-collapse: collapse;
}

th, td {
    padding: 12px;
    text-align: left;
    border-bottom: 2px solid rgb(8, 7, 106); 
}

th {
    background-color:white;
    color:rgb(8, 7, 106);
    font-weight: bold;
}

tbody tr:nth-child(odd) {
    background-color:rgb(202, 219, 255);
}
tbody tr:hover .action-icons i {
    color: white;
}
tbody tr:hover {
    background-color:rgb(8, 7, 106);
    color:white;
}


td i {
    color:rgb(8, 7, 106);
    cursor: pointer;
    margin-right: 10px;
    font-size: 18px;
    transition: color 0.3s ease;
}

td i:hover {
    color: rgb(8, 7, 106);  
}

/* Action buttons for restore */
.action-icons i {
    color: rgb(8, 7, 106);
    cursor: pointer;
    margin-right: 10px;
    font-size: 18px;
    transition: color 0.3s ease;
}

.action-icons i:hover {
    color: white;
}

/* Restore Popup Styling */
.popup {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: linear-gradient(135deg, #ffffff, #f4f4f4);
    padding: 30px 25px;
    border-radius: 12px;
    text-align: center;
    min-width: 320px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    border: 1px solid #e0e0e0;
    animation: popupFade 0.4s ease-in-out;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
.popup h2 {
    font-size: 24px;
    color: rgb(8, 7, 106);
    font-weight: 700;
    margin-bottom: 20px;
    border-bottom: 2px solid rgb(8, 7, 106);
    padding-bottom: 10px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    text-align: left;
}
/* Restore Icon */
.popup i,
.popup img.restore-icon {
    font-size: 48px;
    color: #2e7d32;
    margin-bottom: 15px;
    width: 48px;
    height: 48px;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
    animation: fadeIcon 1.5s ease-in-out;
}

/* Restore Text */
.popup p {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 20px;
    color: #333;
    line-height: 1.4;
    text-align: center;
}

/* Button Container */
.popup-buttons {
    display: flex;
    justify-content: center;
    gap: 15px;
}

/* Yes Button */
.popup .yes-btn {
    background: #2e7d32;
    color: white;
    border: none;
    padding: 10px 24px;
    font-size: 15px;
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
    box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.2);
    transition: background-color 0.3s ease;
}

.popup .yes-btn:hover {
    background-color: #1b5e20;
}

/* No Button */
.popup .no-btn {
    background: #e0e0e0;
    color: #555;
    border: none;
    padding: 10px 24px;
    font-size: 15px;
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
    box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.15);
    transition: background-color 0.3s ease;
}

.popup .no-btn:hover {
    background-color: #c2c2c2;
}

.case-info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-top: 20px;
    text-align: left;
}

.case-info-grid div {
    background: #ffffff;
    padding: 12px 16px;
    border-radius: 8px;
    box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.05);
    font-size: 14px;
    font-weight: 500;
    color: #2e2e2e;
    border-left: 4px solid rgb(8, 7, 106);
    text-align: left;
}

.case-info-grid div strong {
    color:rgb(8, 7, 106);
    font-weight: 600;
    margin-right: 5px;
}

@keyframes popupFade {
    from {
        opacity: 0;
        transform: translate(-50%, -55%) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }
}

@keyframes fadeIcon {
    from {
        opacity: 0;
        transform: scale(0.8);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

/* Close Button Styling */
.close-button {
    position: absolute;
    top: 12px;
    right: 16px;
    font-size: 22px;
    font-weight: bold;
    color: #888;
    cursor: pointer;
    transition: color 0.3s ease, transform 0.3s ease;
    z-index: 1001;
}

.close-button:hover {
    color: rgb(8, 7, 106);
    transform: scale(1.2) rotate(90deg);
}

/* Enhanced Search Container Styles */
.search-container {
    width: 100%;
    display: flex;
    flex-direction: column;
    padding-bottom: 20px;
    border-bottom: 3px solid white;
    margin-bottom: 11px;
}

.search-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
}

.search-bar {
    display: flex;
    align-items: center;
    border:2px solid rgb(8, 7, 106);
    border-radius: 10px;
    padding: 8px 15px;
    background: white;
    width: 390px;
}

.search-bar i {
    color: rgb(8, 7, 106);
}

.search-bar input {
    border: none;
    outline: none;
    padding: 5px;
    margin-left: 8px;
    font-size: 14px;
    width: 200px;
}

.filter-controls {
    display: flex;
    gap: 10px;
}

.filter-btn {
    background:white;
    color: rgb(8, 7, 106);
    border: 2px solid rgb(8, 7, 106);
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 5px;
    transition: background 0.3s ease;
}

.filter-btn:hover {
    background: rgb(8, 7, 106);
    color: white;
}

/* Advanced Filters Panel */
.advanced-filters-panel {
    position: absolute;
    margin-top: 60px;
    right: 20px; /* or left: 20px if you want it on the left */
    z-index: 999; /* high value to stay on top */
    background: rgb(235, 235, 241);
    border: 2px solid rgb(5, 5, 92);
    border-radius: 8px;
    padding: 15px;
    width: 50%; /* or set to auto if preferred */
    box-shadow: 0 4px 8px rgb(198, 204, 218);
}

.filter-row {
    display: flex;
    gap: 15px;
    margin-bottom: 12px;
}

.filter-group {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.filter-group label {
    margin-bottom: 5px;
    font-weight: bold;
    color:rgb(23, 0, 104);
    font-size: 14px;
}

.filter-group input, .filter-group select {
    padding: 8px 12px;
    border: 1px solid rgb(30, 4, 133);
    border-radius: 5px; 
    background: white;
    font-size: 14px;
}

.filter-group select {
    margin-top: 6px;
}

.filter-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 10px;
}

.apply-btn {
    background:rgb(2, 80, 5);
    color: white;
    border-color:rgb(2, 77, 6);
    font-size:13px;
    padding:8px;
}

.apply-btn:hover{
    background:rgb(5, 142, 10);
}

.reset-btn {
    background: #f5f5f5;
    color: #555;
    border-color: #ccc;
    font-size:13px;
    padding:8px;
}


.reset-btn:hover {
    background:rgb(197, 195, 195);
    color: #333;
}


/* Columns Toggle Panel */
.columns-toggle-panel {
    position: absolute;
    margin-top: 60px;
    right: 20px; /* or left: 20px if you want it on the left */
    z-index: 999; /* high value to stay on top */
    background: rgb(235, 235, 241);
    border: 2px solid rgb(5, 5, 92);
    border-radius: 8px;
    padding: 15px;
    width: 200px;
    box-shadow: 0 4px 8px rgb(198, 204, 218);
}

.column-toggle-header {
    font-weight: bold;
    color:rgb(0, 5, 104);
    margin-bottom: 10px;
    padding-bottom: 8px;
    font-size:15px;
    border-bottom: 1px solid rgb(147, 127, 246);
}

.column-toggle-options {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.column-toggle-options label {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
}

.column-toggle-options input[type="checkbox"] {
    width: 16px;
    height: 16px;
}

.column-toggle-options label:hover {
    color:rgb(52, 47, 211);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .filter-row {
        flex-direction: column;
        gap: 10px;
    }
    
    .search-controls {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .search-bar {
        width: 100%;
    }
    
}

#search-btn {
    background-color: rgb(23, 6, 120);
    color: white;
    padding: 10px 10px;
    border: none;
    cursor: pointer;
    font-weight: bold;
    border-radius: 6px;
    font-size: 13px;
    margin-left: auto; /* Push the button to the far right */
}

    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="dashboard-header">
            <span>Archive</span>
            <div class="header-right">
            <div id="current-time" class="current-time"></div>
                <button onclick="redirectToAuthorization(event)"class="lupon-btn">
                <?php echo htmlspecialchars($_SESSION['username']); ?> <i class="fas fa-sign-out-alt"></i>
                </button>
            </div>
        </div>
        
        <!-- Add this search container right after the dashboard header in archive.php -->
        <div class="search-container">
            <div class="search-controls">
                <div class="search-bar">
                    <i class="fas fa-search"></i>
                    <input type="text" id="search-input" placeholder="Search by ID, Name, Title...">
                    <button id="search-btn">Search</button>
                </div>
            </div>
            
            <!-- Advanced Filters Panel -->
            <div id="advanced-filters" class="advanced-filters-panel" style="display: none;">
                <div class="filter-row">
                    <div class="filter-group">
                        <label for="filter-case-type">Case Type:</label>
                        <select id="filter-case-type" class="filter-select">
                            <option value="">All Types</option>
                            <option value="Criminal">Criminal</option>
                            <option value="Civil">Civil</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="filter-complainant">Complainant:</label>
                        <input type="text" id="filter-complainant" placeholder="Complainant name">
                    </div>
                    <div class="filter-group">
                        <label for="filter-respondent">Respondent:</label>
                        <input type="text" id="filter-respondent" placeholder="Respondent name">
                    </div>
                </div>
                <div class="filter-row">
                    <div class="filter-group">
                        <label for="filter-date-from">Filed From:</label>
                        <input type="date" id="filter-date-from">
                    </div>
                    <div class="filter-group">
                        <label for="filter-date-to">Filed To:</label>
                        <input type="date" id="filter-date-to">
                    </div>
                </div>
                <div class="filter-actions">
                    <button id="apply-filters" class="filter-btn apply-btn"><i class="fas fa-check"></i> Apply Filters</button>
                    <button id="reset-filters" class="filter-btn reset-btn"><i class="fas fa-undo"></i> Reset</button>
                </div>
            </div>
            
            <!-- Columns Toggle Panel -->
            <div id="columns-toggle-panel" class="columns-toggle-panel" style="display: none;">
                <div class="column-toggle-header">Show/Hide Columns</div>
                <div class="column-toggle-options">
                    <label><input type="checkbox" class="column-toggle" data-column="0" checked> Case ID</label>
                    <label><input type="checkbox" class="column-toggle" data-column="1" checked> Complainant</label>
                    <label><input type="checkbox" class="column-toggle" data-column="2" checked> Respondent</label>
                    <label><input type="checkbox" class="column-toggle" data-column="3" checked> Title</label>
                    <label><input type="checkbox" class="column-toggle" data-column="4" checked> Nature</label>
                    <label><input type="checkbox" class="column-toggle" data-column="5" checked> Date Filed</label>
                    <label><input type="checkbox" class="column-toggle" data-column="6" checked> Action</label>
                </div>
            </div>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Case ID</th>
                        <th>Complainant</th>
                        <th>Respondent</th>
                        <th>Title</th>
                        <th>Nature</th>
                        <th>Date Filed</th>
                        <th>Archive Reason</th>
                        <th>Archived Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
include 'configs/config.php';

// Assuming the account type is stored in the session
$accountType = isset($_SESSION['accountType']) ? $_SESSION['accountType'] : '';

// SQL query to fetch archived cases
$sql = "SELECT 
            ac.case_no, 
            GROUP_CONCAT(DISTINCT CONCAT(p1.first_name, ' ', COALESCE(p1.middle_name, ''), ' ', p1.last_name, ' ', COALESCE(p1.suffix, '')) SEPARATOR ' & ') AS complainants,
            GROUP_CONCAT(DISTINCT CONCAT(p2.first_name, ' ', COALESCE(p2.middle_name, ''), ' ', p2.last_name, ' ', COALESCE(p2.suffix, '')) SEPARATOR ' & ') AS respondents,
            ac.title, 
            ac.nature, 
            ac.file_date, 
            ac.confrontation_date, 
            ac.action_taken, 
            ac.settlement_date, 
            ac.exec_settlement_date, 
            ac.main_agreement, 
            ac.compliance_status, 
            ac.remarks,
            ac.archive_reason,
            ac.archived_date,
            ac.archived_by
        FROM archived_cases ac
        LEFT JOIN case_persons cp1 ON ac.case_no = cp1.case_no AND cp1.role = 'Complainant'
        LEFT JOIN persons p1 ON cp1.person_id = p1.person_id
        LEFT JOIN case_persons cp2 ON ac.case_no = cp2.case_no AND cp2.role = 'Respondent'
        LEFT JOIN persons p2 ON cp2.person_id = p2.person_id
        GROUP BY ac.case_no, ac.title, ac.nature, ac.file_date, ac.confrontation_date, ac.action_taken, 
                 ac.settlement_date, ac.exec_settlement_date, ac.main_agreement, ac.compliance_status, ac.remarks
        ORDER BY ac.case_no ASC";

$result = $conn->query($sql);

if (!$result) {
    die("<tr><td colspan='7'>SQL Error: " . $conn->error . "</td></tr>");
}

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr id='row-{$row['case_no']}'>
                <td>{$row['case_no']}</td>
                <td>" . htmlspecialchars($row['complainants']) . "</td>
                <td>" . htmlspecialchars($row['respondents']) . "</td>
                <td>" . htmlspecialchars($row['title']) . "</td>
                <td>" . htmlspecialchars($row['nature']) . "</td>
                <td>" . (!empty($row['file_date']) ? date("F j, Y", strtotime($row['file_date'])) : 'N/A') . "</td>
                <td>" . htmlspecialchars($row['archive_reason'] ?? 'Not specified') . "</td>
                <td>" . (!empty($row['archived_date']) ? date("F j, Y, g:i A", strtotime($row['archived_date'])) : 'N/A') . "</td>
                <td class='action-icons'>
                    <i class='fas fa-ellipsis-h case-details-btn' 
                        data-case-no='" . htmlspecialchars($row['case_no']) . "'
                        data-complainants='" . htmlspecialchars($row['complainants']) . "'
                        data-respondents='" . htmlspecialchars($row['respondents']) . "'
                        data-title='" . htmlspecialchars($row['title']) . "'
                        data-nature='" . htmlspecialchars($row['nature']) . "'
                        data-file-date='" . htmlspecialchars($row['file_date']) . "'
                        data-confrontation-date='" . htmlspecialchars($row['confrontation_date']) . "'
                        data-action='" . htmlspecialchars($row['action_taken']) . "'
                        data-settlement-date='" . htmlspecialchars($row['settlement_date']) . "'
                        data-exec-settlement-date='" . htmlspecialchars($row['exec_settlement_date']) . "'
                        data-main-agreement='" . htmlspecialchars($row['main_agreement']) . "'
                        data-compliance='" . htmlspecialchars($row['compliance_status']) . "'
                        data-remarks='" . htmlspecialchars($row['remarks']) . "'
                        data-archive-reason='" . htmlspecialchars($row['archive_reason'] ?? '') . "'
                        data-archived-by='" . htmlspecialchars($row['archived_by'] ?? '') . "'
                        data-archived-date='" . htmlspecialchars($row['archived_date'] ?? '') . "'>
                    </i>";

        if ($accountType === 'lupon') {
            echo "<i class='fas fa-redo-alt restore-btn' data-case-no='{$row['case_no']}'></i>";
        }

        echo "</td>
            </tr>";
    }
} else {
    echo "<tr><td colspan='7'>No archived cases found</td></tr>";
}

$conn->close();
?>

                    <p id="no-case-message" style="display: none; text-align: center; color: red;">No case available</p>
                </tbody>
                
            </table>
        </div>
    </div>

    <div id="caseDetailsPopup" class="popup">
        <div class="popup-content">
            <span class="close-button" id="closeCasePopup">&times;</span>
            <h2>Case Details</h2>
            <div class="case-info-grid">
                <div><strong>Case No.:</strong> <span id="caseNo"></span></div>
                <div><strong>Complainant:</strong> <span id="complainant"></span></div>
                <div><strong>Respondent:</strong> <span id="respondent"></span></div>
                <div><strong>Title:</strong> <span id="title"></span></div>
                <div><strong>Nature:</strong> <span id="nature"></span></div>
                <div><strong>Date Filed:</strong> <span id="dateFiled"></span></div>
                <div><strong>Initial Confrontation:</strong> <span id="initialConfrontation"></span></div>
                <div><strong>Action Taken:</strong> <span id="action"></span></div>
                <div><strong>Settlement Date:</strong> <span id="settlement"></span></div>
                <div><strong>Execution Date:</strong> <span id="execution"></span></div>
                <div><strong>Agreement:</strong> <span id="agreement"></span></div>
                <div><strong>Compliance:</strong> <span id="compliance"></span></div>
                <div><strong>Remarks:</strong> <span id="remarks"></span></div>
                <div><strong>Archive Reason:</strong> <span id="archiveReason"></span></div>
                <div><strong>Archived By:</strong> <span id="archivedBy"></span></div>
                <div><strong>Archived Date:</strong> <span id="archivedDate"></span></div>
            </div>
        </div>
    </div>


    <div class="popup" id="restorePopup" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);">
        <div class="popup-content">
            <img src="LOGOS/restore.png" alt="Restore Icon" class="restore-icon">
            <p>Are you sure you want to restore this case?</p>
            <div class="popup-buttons">
                <button class="yes-btn">YES</button>
                <button class="no-btn">NO</button>
            </div>
        </div>
    </div>
</body>


<script>
function formatDateWords(dateString) {
    if (!dateString || dateString === "0000-00-00") return "N/A";
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return "N/A";
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}
  

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const searchBtn = document.getElementById('search-btn');
    const tableRows = document.querySelectorAll('table tbody tr');
    const noCaseMessage = document.getElementById('no-case-message'); // "No case available" message

    searchBtn.addEventListener('click', function() {
        const searchTerm = searchInput.value.toLowerCase();
        let caseFound = false; // Flag to check if any case matches the search term

        tableRows.forEach(function(row) {
            const rowText = row.textContent.toLowerCase();
            
            // If the row contains the search term, show it
            if (rowText.includes(searchTerm)) {
                row.style.display = ''; // Show row
                caseFound = true;
            } else {
                row.style.display = 'none'; // Hide row
            }
        });

        // Show or hide the "No case available" message based on search results
        if (caseFound) {
            noCaseMessage.style.display = 'none'; // Hide the "No case available" message
        } else {
            noCaseMessage.style.display = 'block'; // Show the "No case available" message
        }
    });
});


function updateTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    document.getElementById('current-time').textContent = timeString;
  }

  // Update time every second
  setInterval(updateTime, 1000);
  updateTime(); // Run once on page load
  
document.addEventListener("DOMContentLoaded", function () {
    const caseDetailsButtons = document.querySelectorAll(".case-details-btn");
    const popup = document.getElementById("caseDetailsPopup");
    const closeButton = document.getElementById("closeCasePopup");
    const restorePopup = document.getElementById("restorePopup");
    const restoreIcons = document.querySelectorAll(".fa-redo-alt");
    const restoreNoButton = restorePopup.querySelector(".no-btn");
    const restoreYesButton = restorePopup.querySelector(".yes-btn");

    let selectedCaseNo = null; // Store selected case_no


    caseDetailsButtons.forEach(button => {
        button.addEventListener("click", function () {
            // Fetch data attributes from the clicked button
            document.getElementById("caseNo").innerText = button.dataset.caseNo;
            document.getElementById("complainant").innerText = button.dataset.complainants;
            document.getElementById("respondent").innerText = button.dataset.respondents;
            document.getElementById("title").innerText = button.dataset.title;
            document.getElementById("nature").innerText = button.dataset.nature;
            document.getElementById("dateFiled").innerText = formatDateWords(button.dataset.fileDate);
            document.getElementById("initialConfrontation").innerText = formatDateWords(button.dataset.confrontationDate);
            document.getElementById("action").innerText = button.dataset.action;
            document.getElementById("settlement").innerText = button.dataset.settlementDate;
            document.getElementById("execution").innerText = button.dataset.execSettlementDate;
            document.getElementById("agreement").innerText = button.dataset.mainAgreement;
            document.getElementById("compliance").innerText = button.dataset.compliance;
            document.getElementById("remarks").innerText = button.dataset.remarks;

            // Show popup
            popup.style.display = "block";
        });
    });

    // Close popup when clicking the close button
    closeButton.addEventListener("click", function () {
        popup.style.display = "none";
    });

    // Close popup when clicking outside the popup
    window.addEventListener("click", function (event) {
        if (event.target === popup) {
            popup.style.display = "none";
        }
    });
    // Show restore popup when clicking the restore icon
    restoreIcons.forEach(icon => {
        icon.addEventListener("click", function () {
            selectedCaseNo = this.getAttribute("data-case-no"); // Get case_no from button
            restorePopup.style.display = "block";
        });
    });

    // Hide restore popup when clicking the No button
    restoreNoButton.addEventListener("click", function () {
        restorePopup.style.display = "none";
    });

    // Confirm Restore - AJAX Request to restore_case.php
    restoreYesButton.addEventListener("click", function () {
        if (!selectedCaseNo) {
            alert("Error: No case selected.");
            return;
        }

        fetch("configs/restore_case.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "case_no=" + encodeURIComponent(selectedCaseNo),
        })
        .then(response => response.text())
        .then(data => {
            if (data.trim() === "success") {
                document.getElementById("row-" + selectedCaseNo).remove(); // Remove row from table
                restorePopup.style.display = "none"; // Close popup
            } else {
                alert("Error restoring case.");
            }
        })
        .catch(error => console.error("Error:", error));
    });
});

function redirectToAuthorization(event) {
            event.preventDefault(); 
            window.location.href = "configs/logout.php"; 
        }

// Add filtering functionality
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const searchInput = document.getElementById('search-input');
    const showAdvancedFilters = document.getElementById('show-advanced-filters');
    const advancedFilters = document.getElementById('advanced-filters');
    const applyFilters = document.getElementById('apply-filters');
    const resetFilters = document.getElementById('reset-filters');
    const toggleColumnsBtn = document.getElementById('toggle-columns-btn');
    const columnsTogglePanel = document.getElementById('columns-toggle-panel');
    const columnToggles = document.querySelectorAll('.column-toggle');
    const caseTypeFilter = document.getElementById('filter-case-type');
    const complainantFilter = document.getElementById('filter-complainant');
    const respondentFilter = document.getElementById('filter-respondent');
    const dateFromFilter = document.getElementById('filter-date-from');
    const dateToFilter = document.getElementById('filter-date-to');
    const tableRows = document.querySelectorAll('tbody tr');
    
    // Toggle advanced filters panel
    showAdvancedFilters.addEventListener('click', function() {
        advancedFilters.style.display = advancedFilters.style.display === 'none' ? 'block' : 'none';
        columnsTogglePanel.style.display = 'none'; // Hide columns panel when showing filters
    });
    
    // Toggle columns panel
    toggleColumnsBtn.addEventListener('click', function() {
        columnsTogglePanel.style.display = columnsTogglePanel.style.display === 'none' ? 'block' : 'none';
        advancedFilters.style.display = 'none'; // Hide filters panel when showing columns
    });
    
    // Column visibility toggle
    columnToggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const columnIndex = this.getAttribute('data-column');
            const isVisible = this.checked;
            
            // Update table cell visibility
            const headerCells = document.querySelectorAll('thead th');
            if (columnIndex < headerCells.length) {
                headerCells[columnIndex].style.display = isVisible ? '' : 'none';
            }
            
            document.querySelectorAll('tbody tr').forEach(row => {
                const cells = row.querySelectorAll('td');
                if (columnIndex < cells.length) {
                    cells[columnIndex].style.display = isVisible ? '' : 'none';
                }
            });
        });
    });
    
    // Search input functionality
    searchInput.addEventListener( applyAllFilters);
    
    // Apply all filters
    applyFilters.addEventListener('click', applyAllFilters);
    
    // Reset all filters
    resetFilters.addEventListener('click', function() {
        searchInput.value = '';
        caseTypeFilter.value = '';
        complainantFilter.value = '';
        respondentFilter.value = '';
        dateFromFilter.value = '';
        dateToFilter.value = '';
        
        applyAllFilters();
    });
    
    // Function to apply all filters
    function applyAllFilters() {
        const searchTerm = searchInput.value.toLowerCase();
        const caseType = caseTypeFilter.value.toLowerCase();
        const complainant = complainantFilter.value.toLowerCase();
        const respondent = respondentFilter.value.toLowerCase();
        const dateFrom = dateFromFilter.value ? new Date(dateFromFilter.value) : null;
        const dateTo = dateToFilter.value ? new Date(dateToFilter.value) : null;
        
        tableRows.forEach(row => {
            const caseId = row.cells[0].textContent.toLowerCase();
            const complainantText = row.cells[1].textContent.toLowerCase();
            const respondentText = row.cells[2].textContent.toLowerCase();
            const title = row.cells[3].textContent.toLowerCase();
            const nature = row.cells[4].textContent.toLowerCase();
            const dateFiledText = row.cells[5].textContent;
            const dateFiled = new Date(dateFiledText);
            
            // Search filter
            const matchesSearch = searchTerm === '' || 
                caseId.includes(searchTerm) || 
                complainantText.includes(searchTerm) || 
                respondentText.includes(searchTerm) || 
                title.includes(searchTerm) ||
                nature.includes(searchTerm);
            
            // Advanced filters
            const matchesCaseType = caseType === '' || nature.includes(caseType);
            const matchesComplainant = complainant === '' || complainantText.includes(complainant);
            const matchesRespondent = respondent === '' || respondentText.includes(respondent);
            const matchesDateFrom = !dateFrom || !isNaN(dateFiled.getTime()) && dateFiled >= dateFrom;
            const matchesDateTo = !dateTo || !isNaN(dateFiled.getTime()) && dateFiled <= dateTo;
            
            // Combined result
            const isVisible = matchesSearch && matchesCaseType && matchesComplainant && 
                matchesRespondent && matchesDateFrom && matchesDateTo;
            
            row.style.display = isVisible ? '' : 'none';
        });
    }
    
    // Close panels when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('#advanced-filters') && 
            !event.target.closest('#show-advanced-filters')) {
            advancedFilters.style.display = 'none';
        }
        
        if (!event.target.closest('#columns-toggle-panel') && 
            !event.target.closest('#toggle-columns-btn')) {
            columnsTogglePanel.style.display = 'none';
        }
    });
});

</script>

</html>