<?php
/**
 * Logger class for CCRMS
 * Handles logging of system events, user actions, and errors
 */
class Logger {
    private $logFile;
    private $logDir;
    private $lastError;
    private $useDatabase = false;
    private $useFile = true;
    private $conn = null;
    
    /**
     * Constructor - initializes the logger
     * 
     * @param bool|null $useDatabase - Whether to use database for logging (defaults to null)
     * @param bool|null $useFile - Whether to use file for logging (defaults to null)
     */
    public function __construct($useDatabase = null, $useFile = null) {
        // Set timezone to Asia/Manila for Philippines
        date_default_timezone_set('Asia/Manila');
        
        // Set log directory and ensure it exists
        $this->logDir = __DIR__ . '/../logs';
        if (!file_exists($this->logDir)) {
            // Add error handling for directory creation
            if (!@mkdir($this->logDir, 0777, true)) {
                $this->lastError = "Failed to create logs directory: " . $this->logDir;
                error_log($this->lastError);
            }
        }
        
        // Ensure directory has proper permissions
        if (file_exists($this->logDir) && !is_writable($this->logDir)) {
            @chmod($this->logDir, 0777);
        }
        
        // Use date-based log files (one per day)
        $this->logFile = $this->logDir . '/system_' . date('Y-m-d') . '.log';
        
        // Test if the log file is writable
        if (file_exists($this->logFile) && !is_writable($this->logFile)) {
            @chmod($this->logFile, 0666); // Try to make it writable
            if (!is_writable($this->logFile)) {
                $this->lastError = "Log file exists but is not writable: " . $this->logFile;
                error_log($this->lastError);
            }
        } else if (!file_exists($this->logFile)) {
            // Try to create an empty log file and check if it's writable
            $testWrite = @file_put_contents($this->logFile, '');
            if ($testWrite === false) {
                $this->lastError = "Cannot create or write to log file: " . $this->logFile;
                error_log($this->lastError);
            } else {
                @chmod($this->logFile, 0666); // Ensure it's writable
            }
        }
        
        // Set logging modes
        if ($useDatabase !== null) {
            $this->useDatabase = (bool)$useDatabase;
        }
        
        if ($useFile !== null) {
            $this->useFile = (bool)$useFile;
        }
        
        // Setup database logging if requested
        if ($this->useDatabase) {
            $this->setupDatabaseLogging();
        }
    }
    
    /**
     * Setup database connection and ensure log table exists
     */
    private function setupDatabaseLogging() {
        try {
            // Include database connection
            include_once __DIR__ . '/config.php';
            
            // Get a fresh connection - this is necessary because the included $conn 
            // might not be accessible in the current scope
            $servername = "127.0.0.1";  // Use IP instead of 'localhost' to force TCP connection
            $port = 3306;
            $username = "root";
            $password = "brgyMolino3";
            $database = "lt_ccrms";
            
            $this->conn = new mysqli($servername, $username, $password, $database, $port);
            
            if ($this->conn->connect_error) {
                $this->lastError = "Database connection failed: " . $this->conn->connect_error;
                error_log($this->lastError);
                $this->useDatabase = false;
                return;
            }
            
            // Check if the log table exists, if not create it
            $tableExists = $this->conn->query("SHOW TABLES LIKE 'system_logs'");
            if ($tableExists->num_rows == 0) {
                $createTable = "CREATE TABLE IF NOT EXISTS system_logs (
                    log_id INT AUTO_INCREMENT PRIMARY KEY,
                    log_date DATE NOT NULL,
                    log_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    log_type VARCHAR(20) NOT NULL,
                    log_action VARCHAR(50) NOT NULL,
                    log_details TEXT,
                    username VARCHAR(50),
                    ip_address VARCHAR(50)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
                
                if (!$this->conn->query($createTable)) {
                    $this->lastError = "Failed to create log table: " . $this->conn->error;
                    error_log($this->lastError);
                    $this->useDatabase = false;
                } else {
                    // Log the table creation to the file
                    $message = "SYSTEM: created_log_table - System logs table created in database";
                    $this->writeLog($message);
                }
            }
        } catch (Exception $e) {
            $this->lastError = "Error setting up database logging: " . $e->getMessage();
            error_log($this->lastError);
            $this->useDatabase = false;
        }
    }
    
    /**
     * Log a user authentication event (login, logout, failed login)
     * 
     * @param string $action - The action performed (login, logout, failed_login)
     * @param string $username - The username involved
     * @param string $accountType - The account type (Lupon, Official)
     * @param string $ip - IP address of the user
     * @return bool - Success or failure
     */
    public function logAuth($action, $username, $accountType, $ip = null) {
        $ip = $ip ?? $this->getClientIP();
        $message = "AUTH: $action by $username ($accountType) from IP: $ip";
        
        // Write to file if enabled
        $fileResult = true;
        if ($this->useFile) {
            $fileResult = $this->writeLog($message);
        }
        
        // If using database, also log to database
        $dbResult = true;
        if ($this->useDatabase && $this->conn) {
            $dbResult = $this->logToDatabase('AUTH', $action, "$accountType from IP: $ip", $username, $ip);
        }
        
        return $fileResult && $dbResult;
    }
    
    /**
     * Log a case-related action
     * 
     * @param string $action - The action performed (add, edit, delete, restore, etc.)
     * @param string $caseNo - The case number or ID
     * @param string $username - The username who performed the action
     * @return bool - Success or failure
     */
    public function logCase($action, $caseNo, $username) {
        $message = "CASE: $action on case #$caseNo by $username";
        
        // Write to file if enabled
        $fileResult = true;
        if ($this->useFile) {
            $fileResult = $this->writeLog($message);
        }
        
        // If using database, also log to database
        $dbResult = true;
        if ($this->useDatabase && $this->conn) {
            $dbResult = $this->logToDatabase('CASE', $action, "Case #$caseNo", $username);
        }
        
        return $fileResult && $dbResult;
    }
    
    /**
     * Log user or account management actions
     * 
     * @param string $action - The action performed (add_user, change_password, etc.)
     * @param string $target - The user affected by the action
     * @param string $username - The username who performed the action
     * @return bool - Success or failure
     */
    public function logUser($action, $target, $username) {
        $message = "USER: $action for $target by $username";
        
        // Write to file if enabled
        $fileResult = true;
        if ($this->useFile) {
            $fileResult = $this->writeLog($message);
        }
        
        // If using database, also log to database
        $dbResult = true;
        if ($this->useDatabase && $this->conn) {
            $dbResult = $this->logToDatabase('USER', $action, $target, $username);
        }
        
        return $fileResult && $dbResult;
    }
    
    /**
     * Log data-related actions (backup, restore)
     * 
     * @param string $action - The action performed
     * @param string $details - Additional details about the action
     * @param string $username - The username who performed the action
     * @return bool - Success or failure
     */
    public function logData($action, $details, $username) {
        $message = "DATA: $action - $details by $username";
        
        // Write to file if enabled
        $fileResult = true;
        if ($this->useFile) {
            $fileResult = $this->writeLog($message);
        }
        
        // If using database, also log to database
        $dbResult = true;
        if ($this->useDatabase && $this->conn) {
            $dbResult = $this->logToDatabase('DATA', $action, $details, $username);
        }
        
        return $fileResult && $dbResult;
    }
    
    /**
     * Log system errors
     * 
     * @param string $error - Error message or code
     * @param string $details - Additional error details
     * @return bool - Success or failure
     */
    public function logError($error, $details = '') {
        $message = "ERROR: $error - $details";
        
        // Write to file if enabled
        $fileResult = true;
        if ($this->useFile) {
            $fileResult = $this->writeLog($message);
        }
        
        // If using database, also log to database
        $dbResult = true;
        if ($this->useDatabase && $this->conn) {
            $dbResult = $this->logToDatabase('ERROR', $error, $details);
        }
        
        return $fileResult && $dbResult;
    }
    
    /**
     * Log generic system events
     * 
     * @param string $action - The system action
     * @param string $details - Action details
     * @return bool - Success or failure
     */
    public function logSystem($action, $details = '') {
        $message = "SYSTEM: $action - $details";
        
        // Write to file if enabled
        $fileResult = true;
        if ($this->useFile) {
            $fileResult = $this->writeLog($message);
        }
        
        // If using database, also log to database
        $dbResult = true;
        if ($this->useDatabase && $this->conn) {
            $dbResult = $this->logToDatabase('SYSTEM', $action, $details);
        }
        
        return $fileResult && $dbResult;
    }
    
    /**
     * Log case archiving specifically - ensures consistent format
     * 
     * @param string $caseNo - The case number or ID
     * @param string $reason - The reason for archiving
     * @param string $notes - Any additional notes
     * @param string $username - The username who performed the action
     * @return bool - Success or failure
     */
    public function logArchive($caseNo, $reason = 'Not specified', $notes = '', $username = 'System') {
        // First log the main archive action with standard format
        $mainLog = $this->logCase('archive on case', $caseNo, $username);
        
        // Then log the details separately for better searching
        $detailsMessage = "SYSTEM: archive_details - Case #$caseNo";
        if (!empty($reason) || !empty($notes)) {
            $details = "Reason: $reason";
            if (!empty($notes)) {
                $details .= " - Notes: $notes";
            }
            $detailsMessage .= " - $details";
        }
        
        // Write to file if enabled
        $fileResult = true;
        if ($this->useFile) {
            $fileResult = $this->writeLog($detailsMessage);
        }
        
        // If using database, also log to database
        $dbResult = true;
        if ($this->useDatabase && $this->conn) {
            $dbResult = $this->logToDatabase('SYSTEM', 'archive_details', "Case #$caseNo - Reason: $reason" . (!empty($notes) ? " - Notes: $notes" : ""), $username);
        }
        
        return $mainLog && $fileResult && $dbResult;
    }
    
    /**
     * Write the log entry to file
     * 
     * @param string $message - The message to log
     * @return bool - Success or failure
     */
    private function writeLog($message) {
        // Ensure timezone is set
        date_default_timezone_set('Asia/Manila');
        
        // Format timestamp as YYYY-MM-DD HH:MM:SS
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[$timestamp] $message" . PHP_EOL;
        
        try {
            $result = @file_put_contents($this->logFile, $logEntry, FILE_APPEND);
            if ($result === false) {
                $error = error_get_last();
                $this->lastError = "Failed to write to log file: " . ($error ? $error['message'] : 'Unknown error');
                error_log($this->lastError);
                return false;
            }
            return true;
        } catch (Exception $e) {
            $this->lastError = "Exception writing to log file: " . $e->getMessage();
            error_log($this->lastError);
            return false;
        }
    }
    
    /**
     * Log an entry to the database
     * 
     * @param string $type - Log type (AUTH, CASE, etc.)
     * @param string $action - The action performed
     * @param string $details - Additional details
     * @param string $username - Username (if applicable)
     * @param string $ip - IP address (if applicable)
     * @return bool - Success or failure
     */
    private function logToDatabase($type, $action, $details = '', $username = null, $ip = null) {
        // If connection is null or closed, try to reconnect
        if (!$this->conn || !($this->conn instanceof mysqli) || $this->conn->connect_errno) {
            $this->setupDatabaseLogging();
            if (!$this->conn || !($this->conn instanceof mysqli)) {
                $this->lastError = "Database connection not available for logging";
                error_log($this->lastError);
                return false;
            }
        }
        
        try {
            // Ensure timezone is set for database operations
            $this->conn->query("SET time_zone = '+08:00'");
            
            $stmt = $this->conn->prepare("INSERT INTO system_logs 
                (log_date, log_type, log_action, log_details, username, ip_address) 
                VALUES (CURDATE(), ?, ?, ?, ?, ?)");
                
            if (!$stmt) {
                $this->lastError = "Error preparing database log entry: " . $this->conn->error;
                error_log($this->lastError);
                return false;
            }
            
            // Get IP if not provided
            if ($ip === null) {
                $ip = $this->getClientIP();
            }
            
            $stmt->bind_param("sssss", $type, $action, $details, $username, $ip);
            $result = $stmt->execute();
            $stmt->close();
            
            if (!$result) {
                $this->lastError = "Error inserting log into database: " . $this->conn->error;
                error_log($this->lastError);
                return false;
            }
            
            return true;
        } catch (Exception $e) {
            $this->lastError = "Exception logging to database: " . $e->getMessage();
            error_log($this->lastError);
            return false;
        }
    }
    
    /**
     * Get client IP address
     * 
     * @return string - The client IP address
     */
    private function getClientIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        }
    }
    
    /**
     * Get all logs for a specific date
     * 
     * @param string $date - Date in YYYY-MM-DD format
     * @return array - Array of log entries
     */
    public function getLogs($date = null) {
        $date = $date ?? date('Y-m-d');
        $logs = [];
        
        // Get logs from file
        $fileLogs = $this->getLogsFromFile($date);
        $logs = $fileLogs;
        
        // If using database, also get logs from database
        if ($this->useDatabase && $this->conn) {
            $dbLogs = $this->getLogsFromDatabase($date);
            
            // Merge and sort logs if we have logs from both sources
            if (!empty($dbLogs)) {
                // If file logs are empty, just use DB logs
                if (empty($fileLogs)) {
                    $logs = $dbLogs;
                } else {
                    // Otherwise merge and sort by timestamp
                    $logs = array_merge($fileLogs, $dbLogs);
                    usort($logs, function($a, $b) {
                        preg_match('/^\[(.*?)\]/', $a, $matchesA);
                        preg_match('/^\[(.*?)\]/', $b, $matchesB);
                        
                        $timeA = isset($matchesA[1]) ? strtotime($matchesA[1]) : 0;
                        $timeB = isset($matchesB[1]) ? strtotime($matchesB[1]) : 0;
                        
                        return $timeA - $timeB;
                    });
                }
            }
        }
        
        return $logs;
    }
    
    /**
     * Get logs from file for a specific date
     * 
     * @param string $date - Date in YYYY-MM-DD format
     * @return array - Array of log entries
     */
    private function getLogsFromFile($date) {
        $logFile = $this->logDir . '/system_' . $date . '.log';
        
        if (!file_exists($logFile)) {
            return [];
        }
        
        $logs = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        return $logs ?? [];
    }
    
    /**
     * Get logs from database for a specific date
     * 
     * @param string $date - Date in YYYY-MM-DD format
     * @param string $type - Optional log type filter
     * @return array - Array of formatted log entries
     */
    private function getLogsFromDatabase($date, $type = null) {
        if (!$this->conn) {
            return [];
        }
        
        try {
            $sql = "SELECT log_time, log_type, log_action, log_details, username, ip_address 
                  FROM system_logs WHERE log_date = ?";
                  
            $params = [$date];
            $types = "s";
            
            if ($type && $type !== 'ALL') {
                $sql .= " AND log_type = ?";
                $params[] = $type;
                $types .= "s";
            }
            
            $sql .= " ORDER BY log_time ASC";
            
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                $this->lastError = "Error preparing query for logs: " . $this->conn->error;
                error_log($this->lastError);
                return [];
            }
            
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $logs = [];
            while ($row = $result->fetch_assoc()) {
                $timestamp = $row['log_time'];
                $logType = $row['log_type'];
                $action = $row['log_action'];
                $details = $row['log_details'];
                $username = $row['username'] ? " by " . $row['username'] : "";
                
                // Format to match file-based log format
                $message = "";
                switch ($logType) {
                    case 'AUTH':
                        $message = "AUTH: $action by $username ({$details})";
                        break;
                    case 'CASE':
                        $message = "CASE: $action on $details$username";
                        break;
                    case 'USER':
                        $message = "USER: $action for $details$username";
                        break;
                    case 'DATA':
                        $message = "DATA: $action - $details$username";
                        break;
                    case 'ERROR':
                        $message = "ERROR: $action - $details";
                        break;
                    case 'SYSTEM':
                        $message = "SYSTEM: $action - $details";
                        break;
                    default:
                        $message = "$logType: $action - $details$username";
                }
                
                $logs[] = "[$timestamp] $message";
            }
            
            $stmt->close();
            return $logs;
            
        } catch (Exception $e) {
            $this->lastError = "Exception getting logs from database: " . $e->getMessage();
            error_log($this->lastError);
            return [];
        }
    }
    
    /**
     * Get list of available log dates
     * 
     * @return array - Array of dates with logs
     */
    public function getLogDates() {
        $dates = [];
        
        // Get dates from files
        $fileDates = $this->getLogDatesFromFiles();
        $dates = $fileDates;
        
        // If using database, also get dates from database
        if ($this->useDatabase && $this->conn) {
            $dbDates = $this->getLogDatesFromDatabase();
            
            // Merge unique dates
            if (!empty($dbDates)) {
                foreach ($dbDates as $date) {
                    if (!in_array($date, $dates)) {
                        $dates[] = $date;
                    }
                }
                
                // Sort dates in descending order (newest first)
                rsort($dates);
            }
        }
        
        return $dates;
    }
    
    /**
     * Get list of available log dates from files
     * 
     * @return array - Array of dates with logs
     */
    private function getLogDatesFromFiles() {
        $dates = [];
        $logFiles = glob($this->logDir . '/system_*.log');
        
        foreach ($logFiles as $file) {
            preg_match('/system_(\d{4}-\d{2}-\d{2})\.log$/', $file, $matches);
            if (isset($matches[1])) {
                $dates[] = $matches[1];
            }
        }
        
        // Sort dates in descending order (newest first)
        rsort($dates);
        return $dates;
    }
    
    /**
     * Get list of available log dates from database
     * 
     * @return array - Array of dates with logs
     */
    private function getLogDatesFromDatabase() {
        if (!$this->conn) {
            return [];
        }
        
        try {
            $stmt = $this->conn->prepare("SELECT DISTINCT log_date FROM system_logs ORDER BY log_date DESC");
            if (!$stmt) {
                $this->lastError = "Error preparing query for log dates: " . $this->conn->error;
                error_log($this->lastError);
                return [];
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            
            $dates = [];
            while ($row = $result->fetch_assoc()) {
                $dates[] = $row['log_date'];
            }
            
            $stmt->close();
            return $dates;
            
        } catch (Exception $e) {
            $this->lastError = "Exception getting log dates from database: " . $e->getMessage();
            error_log($this->lastError);
            return [];
        }
    }
    
    /**
     * Get the last error encountered
     * 
     * @return string|null - The last error message or null if no error
     */
    public function getLastError() {
        return $this->lastError;
    }
    
    /**
     * Enable or disable database logging
     * 
     * @param bool $enable - Whether to enable database logging
     */
    public function setDatabaseLogging($enable) {
        $this->useDatabase = $enable;
        if ($enable && !$this->conn) {
            $this->setupDatabaseLogging();
        }
    }
    
    /**
     * Enable or disable file logging
     * 
     * @param bool $enable - Whether to enable file logging
     */
    public function setFileLogging($enable) {
        $this->useFile = $enable;
    }
    
    /**
     * Check if database logging is enabled and working
     * 
     * @return bool - Whether database logging is enabled and working
     */
    public function isDatabaseLoggingEnabled() {
        return $this->useDatabase && $this->conn !== null;
    }
    
    /**
     * Check if file logging is enabled
     * 
     * @return bool - Whether file logging is enabled
     */
    public function isFileLoggingEnabled() {
        return $this->useFile;
    }
}

// Global function to get logger instance
function getLogger($useDatabase = null, $useFile = null) {
    static $logger = null;
    
    // Check if we need to read the configuration
    if ($useDatabase === null || $useFile === null) {
        $configFile = __DIR__ . '/../logs/config.php';
        if (file_exists($configFile)) {
            include_once $configFile;
            if ($useDatabase === null) {
                $useDatabase = isset($LOGGING_USE_DATABASE) ? $LOGGING_USE_DATABASE : false;
            }
            if ($useFile === null) {
                $useFile = isset($LOGGING_USE_FILE) ? $LOGGING_USE_FILE : true;
            }
        } else {
            if ($useDatabase === null) $useDatabase = false;
            if ($useFile === null) $useFile = true;
        }
    }
    
    if ($logger === null) {
        $logger = new Logger($useDatabase, $useFile);
    } else {
        // Update settings if needed
        if ($useDatabase !== null && $useDatabase != $logger->isDatabaseLoggingEnabled()) {
            $logger->setDatabaseLogging($useDatabase);
        }
        if ($useFile !== null && $useFile != $logger->isFileLoggingEnabled()) {
            $logger->setFileLogging($useFile);
        }
    }
    
    return $logger;
} 