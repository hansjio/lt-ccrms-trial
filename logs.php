<?php
require_once 'configs/auth.php';
checkAuth();

// Allow only 'lupon' role
if (isset($_SESSION['accountType']) && $_SESSION['accountType'] !== 'lupon') {
    header("Location: index.php");
    exit();
}

require_once 'configs/logger.php';
$logger = getLogger();
$loggerError = $logger->getLastError();
$isDbLoggingEnabled = $logger->isDatabaseLoggingEnabled();
$isFileLoggingEnabled = $logger->isFileLoggingEnabled();

// Date selection
$availableDates = $logger->getLogDates();
$selectedDate = isset($_GET['date']) ? $_GET['date'] : (count($availableDates) > 0 ? $availableDates[0] : date('Y-m-d'));
$viewAllDates = isset($_GET['view_all']) && $_GET['view_all'] === 'true';

// Filter type
$filterType = isset($_GET['type']) ? strtoupper($_GET['type']) : 'ALL';
$validTypes = ['ALL', 'AUTH', 'CASE', 'USER', 'DATA', 'ERROR', 'SYSTEM'];
if (!in_array($filterType, $validTypes)) {
    $filterType = 'ALL';
}

// Sort direction
$sortOrder = isset($_GET['sort']) ? strtoupper($_GET['sort']) : 'DESC';
$validSortOrders = ['ASC', 'DESC'];
if (!in_array($sortOrder, $validSortOrders)) {
    $sortOrder = 'DESC';
}

// Search term
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';

// Get logs for the selected date(s)
$logs = [];
if ($viewAllDates) {
    // Merge logs from all available dates
    foreach ($availableDates as $date) {
        $dateLogs = $logger->getLogs($date);
        $logs = array_merge($logs, $dateLogs);
    }
} else {
    $logs = $logger->getLogs($selectedDate);
}

// Apply filters (type and search)
$filteredLogs = [];
foreach ($logs as $log) {
    // Skip log records for log viewing itself
    if (strpos($log, 'SYSTEM: view_logs') !== false) {
        continue;
    }

    // Extract log type
    preg_match('/^\[(.*?)\] (AUTH|CASE|USER|DATA|ERROR|SYSTEM): (.*)$/', $log, $typeMatches);
    $logType = isset($typeMatches[2]) ? $typeMatches[2] : '';
    
    // Check if it matches the selected type filter
    $typeMatch = ($filterType === 'ALL' || $logType === $filterType);
    
    // Check if it matches the search term
    $searchMatch = empty($searchTerm) || stripos($log, $searchTerm) !== false;
    
    // Add to filtered logs if both conditions are met
    if ($typeMatch && $searchMatch) {
        $filteredLogs[] = $log;
    }
}

// Sort logs by timestamp
usort($filteredLogs, function($a, $b) use ($sortOrder) {
    preg_match('/^\[(.*?)\]/', $a, $matchesA);
    preg_match('/^\[(.*?)\]/', $b, $matchesB);
    
    $timeA = isset($matchesA[1]) ? strtotime($matchesA[1]) : 0;
    $timeB = isset($matchesB[1]) ? strtotime($matchesB[1]) : 0;
    
    return ($sortOrder === 'DESC') ? ($timeB - $timeA) : ($timeA - $timeB);
});

// No longer log the viewing of logs to reduce clutter
// Removed: $logger->logSystem('view_logs', ...);

// Pagination setup
$logsPerPage = 25; // Increased from 10 to 25 for better usability
$totalLogs = count($filteredLogs);
$totalPages = ceil($totalLogs / $logsPerPage);
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$startIndex = ($currentPage - 1) * $logsPerPage;
$pagedLogs = array_slice($filteredLogs, $startIndex, $logsPerPage);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Logs</title>
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
            color: rgb(16, 69, 205);
            background-color: white;
            padding: 12px 24px;
            border-radius: 12px;
            border: 2px solid rgb(15, 1, 97);
            font-weight: bold;
            width: 150px;
            text-align: center;
            white-space: nowrap; 
        }

        /* Logs Container */
        .logs-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        /* Search and Filter Controls */
        .controls-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
            align-items: center;
            padding: 15px;
            background-color: rgb(245, 245, 255);
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .search-box {
            flex-grow: 1;
            display: flex;
            position: relative;
        }
        
        .search-box input {
            width: 100%;
            padding: 10px 15px 10px 40px;
            border: 2px solid rgb(3, 3, 83);
            border-radius: 6px;
            font-size: 16px;
        }
        
        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: rgb(3, 3, 83);
        }
        
        .search-box button {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            background: rgb(3, 62, 189);
            color: white;
            border: none;
            border-radius: 4px;
            padding: 5px 15px;
            cursor: pointer;
        }

        .date-selector {
            min-width: 200px;
        }

        .table-scroll {
            max-height: 600px; /* Increased height for better visibility */
            overflow-y: auto;
            overflow-x: auto;
            margin-top: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .controls-container select,
        .controls-container input {
            padding: 10px 12px;
            border: 2px solid rgb(3, 3, 83);
            border-radius: 6px;
            background-color: white;
            color: rgb(8, 36, 106);
            font-weight: bold;
            cursor: pointer;
        }

        .controls-container button {
            padding: 10px 15px;
            background-color: rgb(3, 62, 189);
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .controls-container button:hover {
            background-color: rgb(31, 4, 181);
        }

        /* Log Type Pills */
        .log-type-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            width: 100%;
            margin-top: 15px;
            padding: 12px;
            background-color: #f8f9ff;
            border-radius: 8px;
            border: 1px solid #e0e4f5;
        }

        .type-pill {
            padding: 10px 18px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .type-pill.all {
            background-color: #e0e0e0;
            color: #424242;
        }

        .type-pill.auth {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .type-pill.case {
            background-color: #e3f2fd;
            color: #1976d2;
        }

        .type-pill.user {
            background-color: #f3e5f5;
            color: #7b1fa2;
        }

        .type-pill.data {
            background-color: #ffebee;
            color: #c62828;
        }

        .type-pill.error {
            background-color: #ffcdd2;
            color: #d32f2f;
        }

        .type-pill.system {
            background-color: #eceff1;
            color: #455a64;
        }

        .type-pill.active {
            transform: scale(1.05);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .type-pill.all.active {
            background-color: #9e9e9e;
            color: white;
        }

        .type-pill.auth.active {
            background-color: #2e7d32;
            color: white;
        }

        .type-pill.case.active {
            background-color: #1976d2;
            color: white;
        }

        .type-pill.user.active {
            background-color: #7b1fa2;
            color: white;
        }

        .type-pill.data.active {
            background-color: #c62828;
            color: white;
        }

        .type-pill.error.active {
            background-color: #d32f2f;
            color: white;
        }

        .type-pill.system.active {
            background-color: #455a64;
            color: white;
        }

        /* Log Table */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            font-size: 14px;
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
            vertical-align: middle;
        }
        
        th {
            padding: 15px;
            text-align: left;
            background-color: rgb(209, 215, 247);
            color: rgb(9, 5, 219);
            font-weight: bold;
            font-size: 15px;
            position: sticky;
            top: 0;
            z-index: 10;
            border-bottom: 2px solid rgb(9, 5, 219);
        }

        tr:hover {
            background-color: rgb(235, 235, 255);
        }

        /* Log Entry Types */
        .log-auth { color: #2e7d32; } /* Green for auth */
        .log-case { color: #1976d2; } /* Blue for case */
        .log-user { color: #7b1fa2; } /* Purple for user */
        .log-data { color: #c62828; } /* Red for data */
        .log-error { color: #d32f2f; font-weight: bold; } /* Bold red for errors */
        .log-system { color: #455a64; } /* Dark gray for system */

        .log-type-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 12px;
            margin-right: 8px;
        }

        .log-type-badge.auth {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .log-type-badge.case {
            background-color: #e3f2fd;
            color: #1976d2;
        }

        .log-type-badge.user {
            background-color: #f3e5f5;
            color: #7b1fa2;
        }

        .log-type-badge.data {
            background-color: #ffebee;
            color: #c62828;
        }

        .log-type-badge.error {
            background-color: #ffcdd2;
            color: #d32f2f;
        }

        .log-type-badge.system {
            background-color: #eceff1;
            color: #455a64;
        }

        /* Empty state */
        .empty-logs {
            text-align: center;
            padding: 40px;
            color: #888;
            font-style: italic;
        }

        /* Error alert */
        .error-alert {
            background-color: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 5px solid #c62828;
        }

        /* Info/Status Alert */
        .status-alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left-width: 5px;
            border-left-style: solid;
        }
        
        .status-alert.success {
            background-color: #e8f5e9;
            color: #2e7d32;
            border-left-color: #2e7d32;
        }
        
        .status-alert.info {
            background-color: #e3f2fd;
            color: #0d47a1;
            border-left-color: #0d47a1;
        }
        
        .status-alert.warning {
            background-color: #fff3e0;
            color: #e65100;
            border-left-color: #e65100;
        }
        
        .status-alert.error {
            background-color: #ffebee;
            color: #c62828;
            border-left-color: #c62828;
        }
        
        .status-alert a {
            color: inherit;
            font-weight: bold;
            text-decoration: underline;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            gap: 5px;
        }
        
        .pagination a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 35px;
            height: 35px;
            padding: 0 10px;
            margin: 0 2px;
            border: 1px solid #ccc;
            border-radius: 4px;
            text-decoration: none;
            color: #333;
            font-size: 14px;
            transition: all 0.2s ease;
        }
        
        .pagination a:hover {
            background-color: #f5f5f5;
            border-color: #007bff;
        }
        
        .pagination a.active-page {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }
        
        /* Time column with consistent width */
        .time-column {
            width: 120px;
        }
        
        /* When viewing all dates, the time column needs to be wider */
        .all-dates .time-column {
            width: 150px;
        }
        
        /* Type column with consistent width */
        .type-column {
            width: 100px;
        }
        
        /* Action column */
        .action-column {
            width: 150px;
        }
        
        /* User column with consistent width */
        .user-column {
            width: 120px;
        }
        
        /* Date label for all dates view */
        .date-label {
            font-size: 11px;
            color: #666;
            background-color: #f5f5f5;
            padding: 2px 6px;
            border-radius: 10px;
            display: inline-block;
            margin-bottom: 3px;
        }

        /* Tooltip for long text */
        .tooltip {
            position: relative;
            display: inline-block;
            cursor: pointer;
        }
        
        .tooltip .tooltip-text {
            visibility: hidden;
            width: 300px;
            background-color: #333;
            color: #fff;
            text-align: left;
            border-radius: 6px;
            padding: 10px;
            position: absolute;
            z-index: 1;
            top: 125%;
            left: 50%;
            transform: translateX(-50%);
            opacity: 0;
            transition: opacity 0.3s;
            word-wrap: break-word;
        }
        
        .tooltip:hover .tooltip-text {
            visibility: visible;
            opacity: 1;
        }
        
        /* Small indicator for truncated text */
        .truncated::after {
            content: '...';
            color: #007bff;
            font-weight: bold;
        }

        .sort-button {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 10px 15px;
            background-color: #f0f0f8;
            color: #333;
            border: 2px solid rgb(3, 3, 83);
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .sort-button.active {
            background-color: rgb(3, 62, 189);
            color: white;
        }
        
        .sort-button:hover {
            background-color: rgb(31, 4, 181);
            color: white;
        }
    </style>
</head>
<?php include 'sidebar.php'; ?>

<div class="main-content">
    <div class="dashboard-header">
        <span>System Logs</span>
        <div class="header-right">
            <div id="current-time" class="current-time"></div>
            <button onclick="redirectToAuthorization(event)" class="lupon-btn">
                <?php echo htmlspecialchars($_SESSION['username']); ?> <i class="fas fa-sign-out-alt"></i>
            </button>
        </div>
    </div>

    <div class="logs-container">
        <?php if ($loggerError): ?>
            <div class="status-alert error">
                <strong>Logger Error:</strong> <?php echo htmlspecialchars($loggerError); ?>
            </div>
        <?php endif; ?>

        <?php if ($isDbLoggingEnabled && $isFileLoggingEnabled): ?>
            <div class="status-alert success">
                <strong>Info:</strong> Both file and database logging are enabled. Logs are being stored in both file system and database for maximum reliability.
            </div>
        <?php elseif ($isDbLoggingEnabled): ?>
            <div class="status-alert info">
                <strong>Info:</strong> Database-only logging is enabled. Logs are stored in the database only.
                Go to <a href="settings.php">Settings</a> to change logging preferences.
            </div>
        <?php elseif ($isFileLoggingEnabled): ?>
            <div class="status-alert info">
                <strong>Info:</strong> File-only logging is enabled. Logs are stored in files only.
                Go to <a href="settings.php">Settings</a> to enable database logging for better log management.
            </div>
        <?php else: ?>
            <div class="status-alert error">
                <strong>Warning:</strong> Both file and database logging are disabled. No logs are being recorded!
                Go to <a href="settings.php">Settings</a> to enable logging.
            </div>
        <?php endif; ?>

        <?php if ($viewAllDates): ?>
            <div class="status-alert info">
                <strong>Info:</strong> Viewing logs from all available dates. This may include a large number of entries. 
                <a href="?date=<?php echo urlencode($selectedDate); ?>&type=<?php echo $filterType; ?>&search=<?php echo urlencode($searchTerm); ?>">Return to single date view</a>.
            </div>
        <?php endif; ?>

        <div class="controls-container">
            <!-- Date Selector -->
            <div class="filter-group">
                <label for="date-selector"><strong>Log Date:</strong></label>
                <select id="date-selector" class="date-selector" onchange="changeDateSelection(this.value)">
                    <option value="all" <?php echo $viewAllDates ? 'selected' : ''; ?>>All Dates</option>
                    <?php foreach ($availableDates as $date): ?>
                        <option value="<?php echo $date; ?>" <?php echo (!$viewAllDates && $date === $selectedDate) ? 'selected' : ''; ?>>
                            <?php echo date('F j, Y', strtotime($date)); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Sort Order Toggle -->
            <div class="filter-group">
                <button class="sort-button <?php echo $sortOrder === 'DESC' ? 'active' : ''; ?>" onclick="changeSortOrder('<?php echo $sortOrder === 'DESC' ? 'ASC' : 'DESC'; ?>')">
                    <i class="fas fa-sort-amount-<?php echo strtolower($sortOrder) === 'desc' ? 'down' : 'up'; ?>"></i>
                    <?php echo $sortOrder === 'DESC' ? 'Newest First' : 'Oldest First'; ?>
                </button>
            </div>

            <!-- Search Box -->
            <form class="search-box" method="GET">
                <?php if ($viewAllDates): ?>
                <input type="hidden" name="view_all" value="true">
                <?php else: ?>
                <input type="hidden" name="date" value="<?php echo htmlspecialchars($selectedDate); ?>">
                <?php endif; ?>
                <input type="hidden" name="type" value="<?php echo htmlspecialchars($filterType); ?>">
                <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sortOrder); ?>">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Search logs..." value="<?php echo htmlspecialchars($searchTerm); ?>">
                <button type="submit">Search</button>
            </form>
        </div>

        <!-- Log Type Filters -->
        <div class="log-type-filters">
            <?php foreach ($validTypes as $type): ?>
                <a href="?<?php echo $viewAllDates ? 'view_all=true' : 'date=' . urlencode($selectedDate); ?>&type=<?php echo $type; ?>&search=<?php echo urlencode($searchTerm); ?>&sort=<?php echo $sortOrder; ?>" 
                   class="type-pill <?php echo strtolower($type); ?> <?php echo ($filterType === $type) ? 'active' : ''; ?>">
                    <?php echo ($type === 'ALL') ? 'All' : ucfirst(strtolower($type)); ?>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if (empty($pagedLogs)): ?>
            <div class="empty-logs">
                <p>No logs found for the selected date and filters.</p>
            </div>
        <?php else: ?>
            <div class="table-scroll">
                <table id="logs-table" <?php echo $viewAllDates ? 'class="all-dates"' : ''; ?>>
                    <thead>
                        <tr>
                            <th class="time-column">Time</th>
                            <th class="type-column">Type</th>
                            <th class="action-column">Action</th>
                            <th>Details</th>
                            <th class="user-column">User</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pagedLogs as $log): ?>
                            <?php
                            // Extract timestamp and log type
                            preg_match('/^\[(.*?)\] (AUTH|CASE|USER|DATA|ERROR|SYSTEM): (.*)$/', $log, $matches);
                            if (count($matches) >= 4) {
                                $timestamp = $matches[1];
                                $logType = $matches[2];
                                $message = $matches[3];
                                $action = $detail = $user = '';

                                // Parse different log formats
                                if ($logType === 'AUTH' && preg_match('/^(login|logout|failed_login) by (.*?) \((.*?)\)/', $message, $authMatches)) {
                                    $action = $authMatches[1];
                                    $user = $authMatches[2];
                                    $detail = $authMatches[3];
                                } 
                                // Updated CASE log parsing to handle all archive formats
                                elseif ($logType === 'CASE') {
                                    // Standard format: "action on case #case-id by username"
                                    if (preg_match('/^(.*?) on case #(.*?) by (.*)$/', $message, $caseMatches)) {
                                        $action = $caseMatches[1];
                                        $detail = 'Case #' . $caseMatches[2];
                                        $user = $caseMatches[3];
                                    }
                                    // Newer format with "Case #" in the case ID
                                    elseif (preg_match('/^(.*?) on [Cc]ase #(.*?) by (.*)$/', $message, $caseMatches)) {
                                        $action = $caseMatches[1];
                                        $detail = 'Case #' . $caseMatches[2];
                                        $user = $caseMatches[3];
                                    }
                                    // Direct format: "action Case #case-id - details"
                                    elseif (preg_match('/^(.*?) Case #(\d+-\d+)(?:\s*-\s*(.*))?$/', $message, $archiveMatches)) {
                                        $action = $archiveMatches[1];
                                        $detail = 'Case #' . $archiveMatches[2];
                                        if (isset($archiveMatches[3])) {
                                            $detail .= ' - ' . $archiveMatches[3];
                                        }
                                    }
                                    // Archive with reason: "archive #case-id - reason"
                                    elseif (preg_match('/^(.*?) #(.*?) - (.*)$/', $message, $reasonMatches)) {
                                        $action = $reasonMatches[1];
                                        $detail = 'Case #' . $reasonMatches[2];
                                        if (isset($reasonMatches[3])) {
                                            $detail .= ' - ' . $reasonMatches[3];
                                        }
                                    }
                                } 
                                elseif ($logType === 'USER' && preg_match('/^(.*?) for (.*?) by (.*)$/', $message, $userMatches)) {
                                    $action = $userMatches[1];
                                    $detail = $userMatches[2];
                                    $user = $userMatches[3];
                                } 
                                elseif ($logType === 'DATA' && preg_match('/^(.*?) - (.*?) by (.*)$/', $message, $dataMatches)) {
                                    $action = $dataMatches[1];
                                    $detail = $dataMatches[2];
                                    $user = $dataMatches[3];
                                } 
                                elseif (in_array($logType, ['ERROR', 'SYSTEM']) && preg_match('/^(.*?) - (.*)$/', $message, $matches2)) {
                                    $action = $matches2[1];
                                    $detail = $matches2[2];
                                } 
                                else {
                                    $action = $message;
                                }

                                // Extract username from the log if not already found
                                if (empty($user) && strpos($message, ' by ') !== false) {
                                    $parts = explode(' by ', $message);
                                    if (count($parts) > 1) {
                                        $user = trim(end($parts));
                                    }
                                }

                                $logClass = 'log-' . strtolower($logType);
                                $typeClass = strtolower($logType);
                                
                                // Format time for better readability
                                $formattedTime = date('h:i:s A', strtotime($timestamp));
                                $formattedDate = '';
                                
                                // Add date to the display if viewing all dates
                                if ($viewAllDates) {
                                    $formattedDate = date('M j, Y', strtotime($timestamp));
                                }
                                
                                // Truncate long details and add tooltip
                                $detailDisplay = $detail;
                                $hasTooltip = false;
                                if (strlen($detail) > 80) {
                                    $detailDisplay = substr($detail, 0, 77) . '...';
                                    $hasTooltip = true;
                                }
                                ?>
                                <tr>
                                    <td class="time-column">
                                        <?php if ($viewAllDates): ?>
                                            <span class="date-label"><?php echo htmlspecialchars($formattedDate); ?></span><br>
                                        <?php endif; ?>
                                        <?php echo htmlspecialchars($formattedTime); ?>
                                    </td>
                                    <td class="type-column"><span class="log-type-badge <?php echo $typeClass; ?>"><?php echo htmlspecialchars($logType); ?></span></td>
                                    <td class="action-column <?php echo $logClass; ?>"><?php echo htmlspecialchars($action); ?></td>
                                    <td>
                                        <?php if ($hasTooltip): ?>
                                            <div class="tooltip">
                                                <?php echo htmlspecialchars($detailDisplay); ?>
                                                <span class="tooltip-text"><?php echo htmlspecialchars($detail); ?></span>
                                            </div>
                                        <?php else: ?>
                                            <?php echo htmlspecialchars($detail); ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="user-column"><?php echo htmlspecialchars($user); ?></td>
                                </tr>
                                <?php
                            }
                            ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Controls -->
            <div class="pagination">
                <?php if ($currentPage > 1): ?>
                    <a href="?<?php echo $viewAllDates ? 'view_all=true' : 'date=' . urlencode($selectedDate); ?>&type=<?php echo $filterType; ?>&search=<?php echo urlencode($searchTerm); ?>&sort=<?php echo $sortOrder; ?>&page=<?php echo $currentPage - 1; ?>">
                        <i class="fas fa-chevron-left"></i> Prev
                    </a>
                <?php endif; ?>

                <?php
                // Display limited page numbers for better UI when there are many pages
                $maxPagesToShow = 5;
                $startPage = max(1, min($currentPage - floor($maxPagesToShow / 2), $totalPages - $maxPagesToShow + 1));
                $endPage = min($startPage + $maxPagesToShow - 1, $totalPages);

                // Always show first page
                if ($startPage > 1): ?>
                    <a href="?<?php echo $viewAllDates ? 'view_all=true' : 'date=' . urlencode($selectedDate); ?>&type=<?php echo $filterType; ?>&search=<?php echo urlencode($searchTerm); ?>&sort=<?php echo $sortOrder; ?>&page=1">1</a>
                    <?php if ($startPage > 2): ?>
                        <a class="pagination-ellipsis">...</a>
                    <?php endif; ?>
                <?php endif; ?>

                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <a href="?<?php echo $viewAllDates ? 'view_all=true' : 'date=' . urlencode($selectedDate); ?>&type=<?php echo $filterType; ?>&search=<?php echo urlencode($searchTerm); ?>&sort=<?php echo $sortOrder; ?>&page=<?php echo $i; ?>" 
                       class="<?php echo ($i == $currentPage) ? 'active-page' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>

                <?php 
                // Always show last page
                if ($endPage < $totalPages): 
                    if ($endPage < $totalPages - 1): ?>
                        <a class="pagination-ellipsis">...</a>
                    <?php endif; ?>
                    <a href="?<?php echo $viewAllDates ? 'view_all=true' : 'date=' . urlencode($selectedDate); ?>&type=<?php echo $filterType; ?>&search=<?php echo urlencode($searchTerm); ?>&sort=<?php echo $sortOrder; ?>&page=<?php echo $totalPages; ?>"><?php echo $totalPages; ?></a>
                <?php endif; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="?<?php echo $viewAllDates ? 'view_all=true' : 'date=' . urlencode($selectedDate); ?>&type=<?php echo $filterType; ?>&search=<?php echo urlencode($searchTerm); ?>&sort=<?php echo $sortOrder; ?>&page=<?php echo $currentPage + 1; ?>">
                        Next <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        document.getElementById('current-time').textContent = timeString;
    }

    setInterval(updateTime, 1000);
    updateTime();

    function redirectToAuthorization(event) {
        event.preventDefault();
        window.location.href = "configs/logout.php";
    }
    
    function changeDateSelection(value) {
        if (value === 'all') {
            window.location.href = '?view_all=true&type=<?php echo $filterType; ?>&search=<?php echo urlencode($searchTerm); ?>';
        } else {
            window.location.href = '?date=' + value + '&type=<?php echo $filterType; ?>&search=<?php echo urlencode($searchTerm); ?>';
        }
    }

    function changeSortOrder(newOrder) {
        window.location.href = '?date=' + '<?php echo urlencode($selectedDate); ?>' + '&type=' + '<?php echo urlencode($filterType); ?>' + '&search=' + '<?php echo urlencode($searchTerm); ?>' + '&sort=' + newOrder;
    }
</script>
</body>
</html>
