<?php
require_once 'configs/auth.php';
checkAuth();
?>
 <?php
    include 'configs/config.php';
    ?>

<?php
// Ensure this is placed at the top of the file or before any HTML output using $page and $totalPages

// Defaults
$limit = 10; // Number of cases per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Get total number of cases
$totalCasesQuery = "SELECT COUNT(*) FROM cases"; // Change this if you're using filters
$totalCasesResult = mysqli_query($conn, $totalCasesQuery);
$totalCasesRow = mysqli_fetch_row($totalCasesResult);
$totalCases = $totalCasesRow[0];

$totalPages = ceil($totalCases / $limit);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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
         /* Search & Filter Row */
         .search-container {
            width: 100%;
            display: flex;
            flex-direction: column;
        }

        .search-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .filter-controls {
            display: flex;
            gap: 10px;
        }

.search-bar {
    display: flex;
    align-items: center;
    border:2px solid rgb(8, 7, 106);
    border-radius: 10px;
    padding: 3px 15px;
    background: white;
    width: 390px;
}

        .search-bar i {
            color:rgb(31, 12, 142);
        }

        .search-bar input {
            border: none;
            outline: none;
            padding: 5px;
            margin-left: 8px;
            font-size: 14px;
            width: 200px;
            margin-bottom: 4px;
        }

        .search-btn {
            background-color:rgb(23, 6, 120);
            color: white;
            padding: 10px 10px;
            border: none;
            cursor: pointer;
            display: block;
            margin: auto;
            font-weight: bold;
            border-radius: 6px;
            font-size: 13px;
        }

        .search-btn:hover {
            background-color:rgb(15, 7, 161);
        }

        .filters {
            display: flex;
            gap: 12px;
        }

        .filter-btn, .add-btn {
            background:rgb(255, 255, 255);
            color:rgb(23, 6, 120);
            border: 2px solid rgb(23, 6, 120);
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: background 0.3s ease;
        }

        .filter-btn:hover, .add-btn:hover {
            background: rgb(15, 7, 161);
            color: white;
        }
      
        .table-container {
            margin-top: 50px;
    max-height: 400px; /* Adjust height as needed */
    overflow-y: auto;
    overflow-x: auto; /* Add horizontal scrolling */
    display: block;
    border: 2px solid rgb(127, 175, 246); /* Optional: Add border for visibility */
    border-radius: 8px; /* Optional: Rounded edges */
    background: white; /* Ensure the table background remains white */
    box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.1); /* Optional: Add shadow */
    position: sticky; /* Make the header sticky */
    top: 0; /* Stick to the top of the table container */
    z-index: 1; /* Ensure header stays above the table content */
}

table {
    width: 100%; /* Ensure it takes full width */
    border-collapse: collapse;
    table-layout: fixed;
}

th, td {
    padding: 13px;
    text-align: left;
    border-bottom: 2px solid rgb(210, 212, 233);
}

th {
    background-color:rgb(255, 255, 255);
    color: rgb(5, 10, 97);
    font-weight: bold;
    font-size:15px;
    border-bottom:3px solid rgb(3, 6, 65);
    position: sticky; /* Make the header sticky */
    top: 0; /* Stick to the top of the table container */
    white-space: normal;  /* Allow text to wrap */
    word-wrap: break-word;  /* Break words to fit into the cell */
    overflow: hidden;  /* Hide anything that overflows */
    text-align: left;  /* Center the text in the header */
    padding: 10px;  /* Adjust padding to control space inside th */
}


tbody tr:nth-child(odd) {
    background-color:rgb(192, 209, 235);
}

tbody tr:hover {
    background-color:rgb(3, 16, 81);
    color:white;
}

td i {
    color:rgb(36, 3, 108);
    cursor: pointer;
    margin-right:10px;
    font-size: 15px;
    transition: color 0.3s ease;
    
}
tbody tr:hover td i {
    color: white;
}

td {
    font-size: 13px;
}

tbody tr:hover td i:hover {
    color:rgb(181, 194, 235);
}
#addCaseModal h2 {
    margin-bottom: 20px;
    font-size: 40px;
    color:rgb(5, 28, 110);
    font-weight: 600;
    text-align: left;
    letter-spacing: 1px;
}

.modal {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    font-size: 15px;
    color: #333;
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 800px;
    max-height: 80vh;
    overflow-y: auto;
    padding: 35px;
    border-radius: 7px;
    background: linear-gradient(to bottom right, #ffffff, #f7f3f0);
    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.2);
    border: 2px solid rgba(95, 50, 200, 0.2);
    backdrop-filter: blur(12px);
    z-index: 1000;
    animation: fadeInModal 0.4s ease forwards;
}

.modal::-webkit-scrollbar {
  width: 10px;
}

.modal::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 5px;
}

.modal::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 5px;
}

.modal::-webkit-scrollbar-thumb:hover {
  background: #555;
}


@keyframes fadeInModal {
    from {
        opacity: 0;
        transform: translate(-50%, -60%) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }
}

/* Form Group & Inline Fields */
.form-group {
    margin-bottom: 15px;
}

.inline {
    display: inline-block;
    width: 23%;
    margin-right: 2%;
}

.inline:last-child {
    margin-right: 0;
}

/* Inputs & Textareas */
textarea, input {
    padding: 10px;
    margin-top: 6px;
    font-size: 14px;
}

.modal input,
.modal textarea {
    border: 2px solid rgb(3, 15, 74);
    border-radius: 8px;
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.08);
    background-color:rgb(245, 245, 255);
    transition: all 0.3s ease;
}

.modal input:focus,
.modal textarea:focus {
    border-color:rgb(24, 2, 90);
    box-shadow: 0 0 10px rgba(42, 3, 134, 0.35);
    outline: none;
}

/* Case Textarea */
.case {
    width: 100%;
    height: 120px;
    border: 2px solid rgb(24, 2, 90);
    border-radius: 6px;
    resize: vertical;
    background:rgb(251, 251, 255);
}

/* Radio Group */
.radio-group {
    display: flex;
    align-items: center;
    gap: 12px;
}

input[type="radio"]:checked {
    accent-color:rgb(18, 3, 116);
}

/* Section Spacing */
.section {
    margin-bottom: 20px;
}

/* Button Styling */
.modal button {
    margin-top: 20px;
    margin-bottom: 20px;
    background:rgb(6, 1, 92);
    color: white;
    padding: 8px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 12px;
    transition: background 0.3s ease, transform 0.2s ease;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.modal button:hover {
    background: rgb(23, 18, 167);
}


.remove-person {
    height: 20px;
    text-align: center;
    font-weight: bold;
    font-size: 20px;
    margin-left: 5px;
    display: inline-flex;
    justify-content: center;
    align-items: center;
}

.case-details-container {
    display: flex;
    justify-content: space-between;
    gap: 25px;
    margin-top: 20px;
}

.case-left, .case-center, .case-right {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 12px;
    align-items: center;
    background:rgb(243, 243, 255);
    padding: 15px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    transition: transform 0.3s ease;
}

textarea,
input[type="text"],
input[type="date"] {
    width: 100%;
    max-width: 260px;
    padding: 10px;
    border-radius: 3px;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

textarea:focus,
input[type="text"]:focus,
input[type="date"]:focus {
    outline: none;
}

.complainant-fields,
.respondent-fields {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
    width: 100%;
  
}

.complainant-fields input,
.respondent-fields input {
    padding: 8px 10px;
    border: 2px solid rgb(24, 2, 90);
    border-radius: 6px;
    background: #fffefb;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
    width: 150px;
}

.complainant-fields input:focus,
.respondent-fields input:focus {
    border: 2px solid rgb(24, 2, 90);
    box-shadow: 0 0 6px rgba(12, 4, 124, 0.3);
}

.add-complainant-container,
.add-respondent-container {
    display: flex;
    justify-content: center;
  
    width: 100%;
}

.add-respondent {
    margin-bottom: 40px;
}

.add-complainant,
.add-respondent {
    background:rgb(6, 1, 92);
    color: white;
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
    width: 200px;
    text-align: center;
    transition: background 0.3s ease, transform 0.2s ease;
}

.add-complainant:hover,
.add-respondent:hover {
    background: rgb(23, 18, 167);
}

input[type="radio"]:checked {
    accent-color:rgb(67, 46, 200);
}


.popup {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    backdrop-filter: blur(10px);
    background: rgba(255, 255, 255, 0.15);
    padding: 40px;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.35);
    z-index: 9999;
    width: 500px;
    max-width: 90%;
    max-height: 80vh;
    overflow-y: auto;
    animation: popupFadeIn 0.5s ease forwards;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.popup-content {
    padding: 30px;
    border-radius: 20px;
    background: rgba(255, 255, 255, 0.85);
    box-shadow: inset 0 0 15px rgba(0,0,0,0.05);
    position: relative;
    text-align: center;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #333;
}

.popup-content h2 {
    margin-top: 0;
    color:rgb(43, 49, 168);
    font-size: 2em;
    letter-spacing: 1px;
    text-shadow: 1px 1px 2px rgba(49, 43, 168, 0.3);
}

.popup-content p {
    font-size: 1.1em;
    line-height: 1.8;
    font-weight: 600;
    color: #444;
    margin-bottom: 20px;
}

.popup-content button.close {
    position: absolute;
    top: 15px;
    right: 15px;
    background: none;
    border: none;
    font-size: 1.5em;
    cursor: pointer;
    color:rgb(78, 43, 168);
    transition: all 0.3s ease;
    text-shadow: 0 0 5px rgba(68, 43, 168, 0.5);
}

.popup-content button.close:hover {
    color:rgb(91, 66, 255);
    transform: scale(1.2);
    text-shadow: 0 0 10px rgb(7, 4, 131);
}

@keyframes popupFadeIn {
    from {
        opacity: 0;
        transform: translate(-50%, -60%) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }
}


@keyframes fadeIn {
    from { opacity: 0; transform: translate(-50%, -60%); }
    to { opacity: 1; transform: translate(-50%, -50%); }
}

.case-info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-top: 20px;
    text-align: left;
}

.case-info-grid div {
    background: rgba(255, 255, 255, 0.7);
    border-radius: 12px;
    padding: 12px 16px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    transition: transform 0.2s ease, box-shadow 0.3s ease;
    font-size: 0.95em;
    line-height: 1.4;
}

.case-info-grid div:hover {
    box-shadow: 0 4px 12px rgba(168, 93, 43, 0.2);
    background:rgb(247, 245, 255);
}

.case-info-grid strong {
    display: block;
    font-size: 0.85em;
    color:rgb(6, 1, 98);
    margin-bottom: 4px;
    font-weight: 700;
    letter-spacing: 0.3px;
}

.case-info-grid span {
    font-weight: 500;
    color: #333;
}


.file-attachment-container {
    margin-top: 15px;
}
/* Fancy Close Button */
.close-button {
    position: absolute;
    top: 15px;
    right: 20px;
    font-size: 24px;
    font-weight: bold;
    color:rgb(6, 1, 98);
    cursor: pointer;
    background: transparent;
    border: none;
    transition: all 0.3s ease;
}

.close-button:hover {
    color:rgb(189, 3, 3);
    transform: rotate(90deg) scale(1.2);
    text-shadow: 0 0 10px rgba(255, 92, 92, 0.5);
}


.trash-icon {
    width: 50px;
    height: 50px;
    color: red;
}

.delete-text {
    font-size: 20px;
    font-weight: bold;
    margin: 10px 0;
}


.remove-person {
    height: 20px;
    text-align: center;
    font-weight: bold;
    font-size: 20px;
    margin-left: 5px;
    display: inline-flex;
    justify-content: center;
    align-items: center;
}

.button-group {
    display: flex;
    justify-content: center;
    gap: 15px;  /* Ensures horizontal spacing */
    margin-top: 15px;
}

.yes-btn {
    background: #a50000;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    flex: 1;
    max-width: 100px;
}

.no-btn {
    background: lightgray;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    flex: 1;
    max-width: 100px;
}


.search-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
}
.relative-container {
    position: relative;
}
.filter-controls {
    display: flex;
    gap: 10px;
}

/* Advanced Filters Panel */
.advanced-filters-panel {
    position: absolute;
    top: 10px; /* adjust as needed */
    right: 0px; /* or left: 20px if you want it on the left */
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
    top: 10px; /* adjust as needed */
    right: 0px; /* or left: 20px if you want it on the left */
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
.popup-content1 p {
    margin-bottom: 25px;
    font-size: 18px;
    color:rgb(58, 47, 211);
}

.popup-content1 {
    background: #ffffff;
    padding: 30px 40px;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    max-width: 400px;
    width: 100%;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    text-align: center; /* Center all text and inline elements */
    animation: slideUp 0.3s ease-in-out;
}

/* Label styling with spacing */
.popup-content1 label {
    font-size: 16px;
    color: rgb(7, 12, 98);
    display: block;
    margin: 5px auto;
    text-align: left;
    width: fit-content;
}

/* Button stays centered */
.next-btn {
    padding: 10px 20px;
    background: #4CAF50;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 15px;
    transition: background 0.3s;
}
.pagination {
    text-align: center; /* Keep this for the number alignment */
    margin-top: 20px;
    display: flex; /* Enable Flexbox for the container */
    justify-content: center; /* Center the items horizontally */
    align-items: center;
  
}

.page-link {
    display: inline-block;
    padding: 6px 12px;
    margin: 0 3px; /* Adjust margin for spacing between links */
   
    color: white;
    text-decoration: none;
    border-radius: 4px;
   
    margin-bottom: 20px;
}

.page-link.active {
    background-color: white;
    color: #000;
    font-weight: bold;
    border: 2px solid rgb(66, 46, 199);;
}
.pagination > a.page-link:first-child, .pagination > a.page-link:last-child {
    background-color: white;
    color: rgb(0, 5, 104);
    font-weight: bold;
}
.page-link:hover {
    background-color: white;
    color: rgb(0, 5, 104);
    border: 2px solid rgb(89, 46, 199);;
    font-weight: bold;
}


    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="dashboard-header">
            <span>Cases</span>
            <div class="header-right">
            <div id="current-time" class="current-time"></div>
                <button onclick="redirectToAuthorization(event)"class="lupon-btn">
                <?php echo htmlspecialchars($_SESSION['username']); ?> <i class="fas fa-sign-out-alt"></i>
                </button>
            </div>
        </div>
        <div class="search-container">
            <div class="search-controls">
            <form method="GET" action="">
    <div class="search-bar">
        <i class="fas fa-search"></i>
        <input type="text" id="search-input" name="search" placeholder="Search by ID, Name, Title..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        <button type="submit" class="search-btn">Search</button>
    </div>
  
 
</form>

                <div class="filter-controls">
                    <button id="show-advanced-filters" class="filter-btn"><i class="fas fa-filter"></i></button>
                    <button id="toggle-columns-btn" class="filter-btn"><i class="fas fa-columns"></i></button>
                    <?php if (isset($_SESSION['accountType']) && $_SESSION['accountType'] === 'lupon'): ?>
                    <button class="add-btn"><i class="fas fa-plus"></i></button>
                    <?php endif; ?>
                </div>
            </div>
            <div class="relative-container">
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
                    <div class="filter-group">
                        <label for="filter-compliance">Compliance:</label>
                        <select id="filter-compliance" class="filter-select">
                            <option value="">All</option>
                            <option value="Complete">Complete</option>
                            <option value="Ongoing">Ongoing</option>
                        </select>
                    </div>
                </div>
                <div class="filter-actions">
                    <button id="apply-filters" class="filter-btn apply-btn"><i class="fas fa-check"></i> Apply</button>
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
                    <label><input type="checkbox" class="column-toggle" data-column="6" checked> Action Taken</label>
                    <label><input type="checkbox" class="column-toggle" data-column="7" checked> Initial Confrontation</label>
                    <label><input type="checkbox" class="column-toggle" data-column="8" checked> Settlement Date</label>
                    <label><input type="checkbox" class="column-toggle" data-column="9" checked> Execution Date</label>
                    <label><input type="checkbox" class="column-toggle" data-column="10" checked> Agreement</label>
                    <label><input type="checkbox" class="column-toggle" data-column="11" checked> Compliance</label>
                    <label><input type="checkbox" class="column-toggle" data-column="12" checked> Remarks</label>
                    <label><input type="checkbox" class="column-toggle" data-column="13" checked> File</label>
                    <label><input type="checkbox" class="column-toggle" data-column="14" checked> Action</label>
                </div>
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
                        <th>Action Taken</th>
                        <th>Initial Confrontation</th>
                        <th>Settlement Date</th>
                        <th>Execution Date</th>
                        <th>Agreement</th>
                        <th>Compliance</th>
                        <th>Remarks</th>
                        <th>File</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
    <?php
    $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
    // Count total filtered records based on the search term
$countSql = "SELECT COUNT(DISTINCT c.case_no)
FROM cases c
LEFT JOIN case_persons cp1 ON c.case_no = cp1.case_no AND cp1.role = 'Complainant'
LEFT JOIN persons p1 ON cp1.person_id = p1.person_id
LEFT JOIN case_persons cp2 ON c.case_no = cp2.case_no AND cp2.role = 'Respondent'
LEFT JOIN persons p2 ON cp2.person_id = p2.person_id
WHERE c.is_archived = 0";

// Apply the search filter to the count query
if ($searchTerm) {
$countSql .= " AND (c.case_no LIKE '%$searchTerm%' 
      OR c.title LIKE '%$searchTerm%' 
      OR CONCAT(p1.first_name, ' ', p1.last_name) LIKE '%$searchTerm%' 
      OR CONCAT(p2.first_name, ' ', p2.last_name) LIKE '%$searchTerm%')";
}

// Execute the count query
$countResult = $conn->query($countSql);
$totalRecords = $countResult->fetch_row()[0]; // Get the count of matching records

// Calculate total pages based on the number of records and your page size
$totalPages = ceil($totalRecords / $limit);


    $sql = "SELECT 
        c.case_no, 
        GROUP_CONCAT(DISTINCT CONCAT(p1.first_name, ' ', COALESCE(p1.middle_name, ''), ' ', p1.last_name, ' ', COALESCE(p1.suffix, '')) SEPARATOR ' & ') AS complainants,
        GROUP_CONCAT(DISTINCT CONCAT(p2.first_name, ' ', COALESCE(p2.middle_name, ''), ' ', p2.last_name, ' ', COALESCE(p2.suffix, '')) SEPARATOR ' & ') AS respondents,
        c.title, 
        c.nature, 
        c.file_date, 
        c.confrontation_date, 
        c.action_taken, 
        c.settlement_date, 
        c.exec_settlement_date, 
        c.main_agreement, 
        c.compliance_status, 
        c.remarks,
        c.attached_file
    FROM cases c
    LEFT JOIN case_persons cp1 ON c.case_no = cp1.case_no AND cp1.role = 'Complainant'
    LEFT JOIN persons p1 ON cp1.person_id = p1.person_id
    LEFT JOIN case_persons cp2 ON c.case_no = cp2.case_no AND cp2.role = 'Respondent'
    LEFT JOIN persons p2 ON cp2.person_id = p2.person_id
    WHERE c.is_archived = 0";
    
    // Apply the search filter to the query
    if ($searchTerm) {
        $sql .= " AND (c.case_no LIKE '%$searchTerm%' 
                  OR c.title LIKE '%$searchTerm%' 
                  OR CONCAT(p1.first_name, ' ', p1.last_name) LIKE '%$searchTerm%' 
                  OR CONCAT(p2.first_name, ' ', p2.last_name) LIKE '%$searchTerm%')";
    }
    
    $sql .= " GROUP BY c.case_no, c.title, c.nature, c.file_date, c.confrontation_date, c.action_taken, 
             c.settlement_date, c.exec_settlement_date, c.main_agreement, c.compliance_status, c.remarks, c.attached_file
    ORDER BY c.case_no ASC
    LIMIT $limit OFFSET $offset";


    $result = $conn->query($sql);

    if (!$result) {
        echo "<tr><td colspan='15'>SQL Error: " . $conn->error . "</td></tr>";
    } else if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Prepare attached file display
            $fileDisplay = $row['attached_file'] ? 
                "<a href='./uploads/{$row['attached_file']}' target='_blank'><i class='fas fa-file-alt'></i></a>" : 
                "<i class='fas fa-times text-muted'></i>";
                
            echo "<tr id='row-{$row['case_no']}'>
                    <td>{$row['case_no']}</td>
                    <td>" . htmlspecialchars($row['complainants']) . "</td>
                    <td>" . htmlspecialchars($row['respondents']) . "</td>
                    <td>" . htmlspecialchars($row['title']) . "</td>
                    <td>" . htmlspecialchars($row['nature']) . "</td>
                    <td>" . (!empty($row['file_date']) ? date("F j, Y", strtotime($row['file_date'])) : 'N/A') . "</td>
                    <td>" . htmlspecialchars($row['action_taken']) . "</td>
                    <td>" . htmlspecialchars($row['confrontation_date']) . "</td>
                    <td>" . htmlspecialchars($row['settlement_date']) . "</td>
                    <td>" . htmlspecialchars($row['exec_settlement_date']) . "</td>
                    <td>" . htmlspecialchars($row['main_agreement']) . "</td>
                    <td>" . htmlspecialchars($row['compliance_status']) . "</td>
                    <td>" . htmlspecialchars($row['remarks']) . "</td>
                    <td>" . $fileDisplay . "</td>
                    <td>
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
                            data-attached-file='" . htmlspecialchars($row['attached_file']) . "'>
                        </i>
                           
                        <i class='fas fa-box-archive delete-btn' data-case-no='{$row['case_no']}'></i>
                    </td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='15'>No cases found</td></tr>";
    }

    $conn->close();
    ?>
</tbody>


            </table>


    </div>
    <div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?page=<?= $page - 1 ?>&search=<?= htmlspecialchars($searchTerm) ?>" class="page-link">&lt;</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?= $i ?>&search=<?= htmlspecialchars($searchTerm) ?>" class="page-link <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>

    <?php if ($page < $totalPages): ?>
        <a href="?page=<?= $page + 1 ?>&search=<?= htmlspecialchars($searchTerm) ?>" class="page-link">&gt;</a>
    <?php endif; ?>
</div>

       
    <div id="addCaseModal" class="modal">
    <span class="close-button">&times;</span>
    <h2>Add Case</h2>

        <form id="addCaseForm" method="POST" enctype="multipart/form-data">
            <!-- Complainant -->
            <div class="complainant-container">
                <label> <span style="font-weight:bold;">Complainant:</span></label>
                <div id="complainantFields">
                    <div class="complainant-fields">
                        <input type="text" name="complainant_first_name[]" placeholder="First Name" required>
                        <input type="text" name="complainant_middle_name[]" placeholder="Middle Initial">
                        <input type="text" name="complainant_last_name[]" placeholder="Last Name" required>
                        <input type="text" name="complainant_suffix[]" placeholder="Suffix">
                    </div>
                </div>
                <button type="button" class="add-complainant">+ Add complainant</button>
            </div>

            <!-- Respondent -->
            <div class="respondent-container">
                <label><span style="font-weight:bold;">Respondent:</span></label>
                <div id="respondentFields">
                    <div class="respondent-fields">
                        <input type="text" name="respondent_first_name[]" placeholder="First Name" required>
                        <input type="text" name="respondent_middle_name[]" placeholder="Middle Initial">
                        <input type="text" name="respondent_last_name[]" placeholder="Last Name" required>
                        <input type="text" name="respondent_suffix[]" placeholder="Suffix">
                    </div>
                </div>
                <button type="button" class="add-respondent">+ Add respondent</button>
            </div>

            <!-- Case Details -->
            <div class="case-details-container">
                <div class="case-left">
                    <label <span style = "font-weight:bold">Case Title</span></label>
                    <textarea name="title" placeholder="Title" class="case" required></textarea>
                    <label> <span style="font-weight: bold;">Nature</span></label>
                    <div class="radio-group">
                        <label><input type="radio" name="nature" value="Criminal" required> Criminal</label>
                        <label><input type="radio" name="nature" value="Civil"> Civil</label>
                    </div>
                    <label> <span style="font-weight: bold;">Date Filed</span> <input type="date" name="file_date" required></label>
                </div>

                <div class="case-center">
                <label>
                    <span style="font-weight: bold;">Date of Initial Confrontation</span>
                    <input type="date" name="confrontation_date">
                    </label>
                    <input type="text" name="action_taken" placeholder="Action Taken">
                    
                    <label><span style="font-weight: bold;">Date of Settlement or Award</span> 
                        <input type="text" name="settlement_date" class="date-or-text" 
                            placeholder="YYYY-MM-DD or CFA/N/A" 
                            pattern="(\d{4}-\d{2}-\d{2})|(CFA)|(N/A)"
                            title="Enter a date in YYYY-MM-DD format, or CFA, or N/A">
                    </label>
                    
                    <label><span style="font-weight: bold;">Date of Execution</span>
                        <input type="text" name="exec_settlement_date" class="date-or-text" 
                            placeholder="YYYY-MM-DD or CFA/N/A" 
                            pattern="(\d{4}-\d{2}-\d{2})|(CFA)|(N/A)"
                            title="Enter a date in YYYY-MM-DD format, or CFA, or N/A">
                    </label>
                </div>

                <div class="case-right">
                    <label><span style = "font-weight:bold">Main Point of Agreement</span></label>
                    <textarea name="main_agreement" placeholder="Enter details..."></textarea>

                    <h4>Compliance</h4>
                    <div class="radio-group">
                        <label><input type="radio" name="compliance_status" value="Complete"> Complete</label>
                        <label><input type="radio" name="compliance_status" value="Ongoing"> Ongoing</label>
                    </div>

                    <h4>Remarks</h4>
                    <div class="radio-group">
                        <label><input type="radio" name="remarks" value="Settled"> Settled</label>
                        <label><input type="radio" name="remarks" value="Issued CFA"> Issued CFA </></label>
                    </div>
                </div>
            </div>
            <center>
            <div class="file-attachment-container">
    <label for="attached-file" style="font-weight: bold;">Attach File</label><br>
    <input type="file" name="attached_file" id="attached-file" accept=".pdf,.docx,.jpg,.png" />
</div>
            <center><button type="submit" style = "background-color: green;width: 130px; padding:10px;">ADD CASE</button></center>
        </form>
    </div>

    
    <div id="caseDetailsPopup" class="popup">
    <div class="popup-content">
        <span class="close-button" id="closeCasePopup">&times;</span>
        <h2>CASE DETAILS</h2>
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
            
            <!-- Add this section for the file attachment -->
            <div><strong>Attached File:</strong> <span id="attachedFile"></span></div>

        </div>
    </div>
</div>


<div id="reasonPopup" class="popup">
    <div class="popup-content1">
        <p><strong>Select reason for archiving:</strong></p>
        <label><input type="radio" name="archiveReason" value="irrelevant"> Irrelevant</label><br>
        <label><input type="radio" name="archiveReason" value="test entry"> Test Entry</label><br>
        <label><input type="radio" name="archiveReason" value="other"> Other</label><br><br>
        
        <div style="margin-top: 10px;">
            <label for="archiveNotes"><strong>Notes/Remarks:</strong></label><br>
            <textarea id="archiveNotes" name="archiveNotes" rows="4" style="width: 100%; margin-top: 5px; padding: 5px; border: 1px solid #ccc; border-radius: 4px;"></textarea>
        </div>
        
        <button class="next-btn" onclick="goToArchiveConfirm()" style="margin-top: 15px;">Next</button>
    </div>
</div>

    <div id="deletePopup" class="popup">
        <div class="popup-content">
            <img src="LOGOS/archive.png" alt="Trash Icon" class="trash-icon">
            <p>Archive this case?</p>
            <div class="button-group">
                <button class="yes-btn" onclick="confirmDelete()">YES</button>
                <button class="no-btn" onclick="closePopup()">NO</button>
            </div>
        </div>
    </div>
    
</body>
<script>
// Global variables for archiving
let selectedCaseNo = null;
let selectedReason = null;
let archiveNotes = "";

function formatDateWords(dateString) {
    if (!dateString || dateString === "0000-00-00") return "N/A";
    const date = new Date(dateString);
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}    

function updateTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    document.getElementById('current-time').textContent = timeString;
  }

  // Update time every second
  setInterval(updateTime, 1000);
  updateTime(); // Run once on page load


document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("addCaseModal");
    const addBtn = document.querySelector(".add-btn");
    const closeBtn = document.querySelector(".close-button");
    const modalTitle = modal.querySelector("h2");
    const modalAddButton = modal.querySelector("button[type='submit']");
    const caseDetailsButtons = document.querySelectorAll(".case-details-btn");
    const deleteButtons = document.querySelectorAll(".delete-btn");
    const addComplainantBtn = document.querySelector(".add-complainant");
    const addRespondentBtn = document.querySelector(".add-respondent");
    const searchInput = document.querySelector(".search-bar input");
    const filterSelect = document.querySelector(".filter-btn");

    // Form submission - for adding cases
    document.getElementById("addCaseForm").addEventListener("submit", function(event) {
        event.preventDefault();
        
        let formData = new FormData(this);
        
        fetch("configs/add_case.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            if (data.trim() === "success") {
                alert("Case added successfully!");
                location.reload();
            } else {
                alert("Error: " + data);
            }
        })
        .catch(error => console.error("Error:", error));
    });

    // Function to handle opening the modal for adding a case
    addBtn.addEventListener("click", function () {
        // Reset form and set for add mode
        const form = document.getElementById("addCaseForm");
        form.removeAttribute("data-edit-mode");
        form.removeAttribute("data-case-no");
        
        // Change modal title and button text for adding
        modalTitle.textContent = "ADD CASE";
        modalAddButton.textContent = "ADD CASE";
        
        // Clear all fields
        clearModalFields();
        
        // Show the modal
        modal.style.display = "block";
    });

    // Add functionality for adding complainants
    addComplainantBtn.addEventListener("click", function() {
        const complainantContainer = document.getElementById("complainantFields");
        const newFields = document.createElement("div");
        newFields.className = "complainant-fields";
        newFields.innerHTML = `
            <input type="text" name="complainant_first_name[]" placeholder="First Name" required>
            <input type="text" name="complainant_middle_name[]" placeholder="Middle Initial">
            <input type="text" name="complainant_last_name[]" placeholder="Last Name" required>
            <input type="text" name="complainant_suffix[]" placeholder="Suffix">
            <button type="button" class="remove-person">X</button>
        `;
        complainantContainer.appendChild(newFields);
        
        // Add event listener to the remove button
        newFields.querySelector(".remove-person").addEventListener("click", function() {
            complainantContainer.removeChild(newFields);
        });
    });
    
    // Add functionality for adding respondents
    addRespondentBtn.addEventListener("click", function() {
        const respondentContainer = document.getElementById("respondentFields");
        const newFields = document.createElement("div");
        newFields.className = "respondent-fields";
        newFields.innerHTML = `
            <input type="text" name="respondent_first_name[]" placeholder="First Name" required>
            <input type="text" name="respondent_middle_name[]" placeholder="Middle Initial">
            <input type="text" name="respondent_last_name[]" placeholder="Last Name" required>
            <input type="text" name="respondent_suffix[]" placeholder="Suffix">
            <button type="button" class="remove-person">×</button>
        `;
        respondentContainer.appendChild(newFields);
        
        // Add event listener to the remove button
        newFields.querySelector(".remove-person").addEventListener("click", function() {
            respondentContainer.removeChild(newFields);
        });
    });

    // Delete case handlers    
    deleteButtons.forEach(button => {
        button.addEventListener("click", function () {
            const caseNo = this.getAttribute("data-case-no");
            showDeletePopup(caseNo);
        });
    });

    // Case details popup
    caseDetailsButtons.forEach(button => {
        button.addEventListener("click", function () {
            // Retrieve data from `data-*` attributes
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
            const attachedFile = button.dataset.attachedFile;
            const fileDisplay = attachedFile ? `<a href="./uploads/${attachedFile}" target="_blank">${attachedFile}</a>` : "No file attached";
            document.getElementById("attachedFile").innerHTML = fileDisplay;
            // Show the case details modal
            document.getElementById("caseDetailsPopup").style.display = "block";
        });
    });

    // Close case details popup
    document.getElementById("closeCasePopup").addEventListener("click", function () {
        document.getElementById("caseDetailsPopup").style.display = "none";
    });

    // Close the modal when the close button is clicked
    closeBtn.addEventListener("click", function () {
        const form = document.getElementById("addCaseForm");
        form.removeAttribute("data-edit-mode");
        form.removeAttribute("data-case-no");
        modal.style.display = "none";
    });

    // Close the modal if the user clicks outside of it
    window.addEventListener("click", function (event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
        
        if (event.target === document.getElementById("caseDetailsPopup")) {
            document.getElementById("caseDetailsPopup").style.display = "none";
        }
        
        if (event.target === document.getElementById("deletePopup")) {
            closePopup();
        }
    });

    // Add search functionality
    if (searchInput) {
    const filterButton = document.querySelector(".filter-btn");
    const rows = document.querySelectorAll("tbody tr");

    function filterRows(searchValue, selectedFilter) {
        const searchParts = searchValue.toLowerCase().split(" ").filter(part => part !== "");

        rows.forEach(row => {
            const caseId = row.querySelector("td:nth-child(1)").textContent.toLowerCase();
            const complainant = row.querySelector("td:nth-child(2)").textContent.toLowerCase();
            const respondent = row.querySelector("td:nth-child(3)").textContent.toLowerCase();
            const nature = row.querySelector("td:nth-child(5)").textContent.toLowerCase();

            let complainantMatch = searchParts.every(part => complainant.includes(part));
            let respondentMatch = searchParts.every(part => respondent.includes(part));
            let caseIdMatch = searchParts.every(part => caseId.includes(part));

            const searchMatch = searchParts.length === 0 || complainantMatch || respondentMatch || caseIdMatch;
            const filterMatch = selectedFilter === "all" || nature === selectedFilter;

            if (searchMatch && filterMatch) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    }

    function applyCurrentFilter() {
        const searchValue = searchInput.value.trim();
        const selectedFilter = filterButton.value.toLowerCase();
        filterRows(searchValue, selectedFilter);
    }

    
    filterButton.addEventListener("change", applyCurrentFilter);

    // Initial filter run
    applyCurrentFilter();
}

    // Add filter functionality
    if (filterSelect) {
        filterSelect.addEventListener("change", function() {
            const filterValue = this.value.toLowerCase();
            const rows = document.querySelectorAll("tbody tr");
            
            if (filterValue === "all") {
                rows.forEach(row => row.style.display = "");
                return;
            }
            
            rows.forEach(row => {
                const nature = row.querySelector("td:nth-child(5)").textContent.toLowerCase();
                const visible = nature === filterValue;
                row.style.display = visible ? "" : "none";
            });
        });
    }
});

// Helper function to clear modal fields when adding a new case
function clearModalFields() {
    // Clear the form completely
    document.getElementById("addCaseForm").reset();
    
    // Clear complainant fields - completely remove all fields and add a fresh one
    const complainantContainer = document.getElementById("complainantFields");
    while (complainantContainer.firstChild) {
        complainantContainer.removeChild(complainantContainer.firstChild);
    }
    
    // Add a single empty complainant field
    const newComplainantField = document.createElement("div");
    newComplainantField.className = "complainant-fields";
    newComplainantField.innerHTML = ` 
        <input type="text" name="complainant_first_name[]" placeholder="First Name" required>
        <input type="text" name="complainant_middle_name[]" placeholder="Middle Initial">
        <input type="text" name="complainant_last_name[]" placeholder="Last Name" required>
        <input type="text" name="complainant_suffix[]" placeholder="Suffix">
    `;
    complainantContainer.appendChild(newComplainantField);
    
    // Clear respondent fields - completely remove all fields and add a fresh one
    const respondentContainer = document.getElementById("respondentFields");
    while (respondentContainer.firstChild) {
        respondentContainer.removeChild(respondentContainer.firstChild);
    }
    
    // Add a single empty respondent field
    const newRespondentField = document.createElement("div");
    newRespondentField.className = "respondent-fields";
    newRespondentField.innerHTML = ` 
        <input type="text" name="respondent_first_name[]" placeholder="First Name" required>
        <input type="text" name="respondent_middle_name[]" placeholder="Middle Initial">
        <input type="text" name="respondent_last_name[]" placeholder="Last Name" required>
        <input type="text" name="respondent_suffix[]" placeholder="Suffix">
    `;
    respondentContainer.appendChild(newRespondentField);
}

function redirectToAuthorization(event) {
            event.preventDefault(); 
            window.location.href = "configs/logout.php"; 
        }

// Show delete confirmation popup
function showDeletePopup(caseNo) {
    selectedCaseNo = caseNo;
    selectedReason = null;
    archiveNotes = "";
    
    // Clear the form
    document.querySelectorAll('input[name="archiveReason"]').forEach(input => input.checked = false);
    document.getElementById("archiveNotes").value = "";
    
    document.getElementById("reasonPopup").style.display = "flex";
}

function goToArchiveConfirm() {
    const selected = document.querySelector('input[name="archiveReason"]:checked');
    if (!selected) {
        alert("Please select a reason.");
        return;
    }

    selectedReason = selected.value;
    archiveNotes = document.getElementById("archiveNotes").value;

    // Close reason popup, open confirmation popup
    document.getElementById("reasonPopup").style.display = "none";

    const popup = document.getElementById("deletePopup");
    popup.style.display = "block";
    popup.setAttribute("data-case-no", selectedCaseNo);
}

// Close popup
function closePopup() {
    document.getElementById("deletePopup").style.display = "none";
    document.getElementById("reasonPopup").style.display = "none";
}

// Confirm delete/archive action
function confirmDelete() {
    const popup = document.getElementById("deletePopup");
    const caseNo = popup.getAttribute("data-case-no");

    if (!caseNo || !selectedReason) {
        alert("Error: Missing case number or reason.");
        return;
    }

    fetch("configs/delete_case.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "case_no=" + encodeURIComponent(caseNo) + 
              "&reason=" + encodeURIComponent(selectedReason) + 
              "&notes=" + encodeURIComponent(archiveNotes),
    })
    .then(response => response.text())
    .then(data => {
        if (data.trim() === "success") {
            document.getElementById("row-" + caseNo).remove();
            closePopup();
        } else {
            alert("Error archiving case: " + data);
        }
    })
    .catch(error => console.error("Error:", error));
}

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
    const complianceFilter = document.getElementById('filter-compliance');
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
            const columnIndex = parseInt(this.getAttribute('data-column'));
            const isVisible = this.checked;
            
            // Update table cell visibility for all rows
            // First for the header
            const headerRow = document.querySelector('thead tr');
            if (headerRow && columnIndex < headerRow.cells.length) {
                headerRow.cells[columnIndex].style.display = isVisible ? '' : 'none';
            }
            
            // Then for all data rows
            document.querySelectorAll('tbody tr').forEach(row => {
                if (row.cells && columnIndex < row.cells.length) {
                    row.cells[columnIndex].style.display = isVisible ? '' : 'none';
                }
            });
        });
    });
    
    // Initialize column visibility based on stored preferences (if any)
    function initializeColumnVisibility() {
        // First time - hide some non-essential columns by default
        const nonEssentialColumns = [7, 8, 9, 10, 11, 12, 13]; // Initial Confrontation, Settlement Date, etc.
        
        nonEssentialColumns.forEach(columnIndex => {
            const checkbox = document.querySelector(`.column-toggle[data-column="${columnIndex}"]`);
            if (checkbox) {
                checkbox.checked = false;
                
                // Update table cell visibility
                const headerRow = document.querySelector('thead tr');
                if (headerRow && columnIndex < headerRow.cells.length) {
                    headerRow.cells[columnIndex].style.display = 'none';
                }
                
                document.querySelectorAll('tbody tr').forEach(row => {
                    if (row.cells && columnIndex < row.cells.length) {
                        row.cells[columnIndex].style.display = 'none';
                    }
                });
            }
        });
    }
    
    // Run initialization
    initializeColumnVisibility();
    
    // Search input functionality
  
    
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
        complianceFilter.value = '';
        
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
        const compliance = complianceFilter.value.toLowerCase();
        
        tableRows.forEach(row => {
            if (!row.cells || row.cells.length < 13) return; // Skip rows with incomplete data
            
            const caseId = row.cells[0].textContent.toLowerCase();
            const complainantText = row.cells[1].textContent.toLowerCase();
            const respondentText = row.cells[2].textContent.toLowerCase();
            const title = row.cells[3].textContent.toLowerCase();
            const nature = row.cells[4].textContent.toLowerCase();
            const dateFiledText = row.cells[5].textContent;
            const dateFiled = new Date(dateFiledText);
            const actionTaken = row.cells[6].textContent.toLowerCase();
            const confrontationDate = row.cells[7].textContent.toLowerCase();
            const settlementDate = row.cells[8].textContent.toLowerCase();
            const executionDate = row.cells[9].textContent.toLowerCase();
            const agreement = row.cells[10].textContent.toLowerCase();
            const complianceStatus = row.cells[11].textContent.toLowerCase();
            const remarks = row.cells[12].textContent.toLowerCase();
            
            // Search filter - search across all text columns
            const matchesSearch = searchTerm === '' || 
                caseId.includes(searchTerm) || 
                complainantText.includes(searchTerm) || 
                respondentText.includes(searchTerm) || 
                title.includes(searchTerm) ||
                nature.includes(searchTerm) ||
                actionTaken.includes(searchTerm) ||
                confrontationDate.includes(searchTerm) ||
                settlementDate.includes(searchTerm) ||
                executionDate.includes(searchTerm) ||
                agreement.includes(searchTerm) ||
                complianceStatus.includes(searchTerm) ||
                remarks.includes(searchTerm);
            
            // Advanced filters
            const matchesCaseType = caseType === '' || nature.includes(caseType);
            const matchesComplainant = complainant === '' || complainantText.includes(complainant);
            const matchesRespondent = respondent === '' || respondentText.includes(respondent);
            const matchesDateFrom = !dateFrom || !isNaN(dateFiled.getTime()) && dateFiled >= dateFrom;
            const matchesDateTo = !dateTo || !isNaN(dateFiled.getTime()) && dateFiled <= dateTo;
            const matchesCompliance = compliance === '' || complianceStatus.includes(compliance);
            
            // Combined result
            const isVisible = matchesSearch && matchesCaseType && matchesComplainant && 
                matchesRespondent && matchesDateFrom && matchesDateTo && matchesCompliance;
            
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
    applyAllFilters();
});

</script>

</html>