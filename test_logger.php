<?php
// Test script to verify logger database functionality
require_once 'configs/logger.php';

// Force database logging by passing true
$logger = getLogger(true);

// Check if logging to database is enabled
echo "Database logging enabled: " . ($logger->isDatabaseLoggingEnabled() ? "Yes" : "No") . "<br>";

// Show last error if any
$error = $logger->getLastError();
if ($error) {
    echo "Logger error: " . htmlspecialchars($error) . "<br>";
}

// Log a test entry
$result = $logger->logSystem('test_log', 'Testing database logging');
echo "Logging result: " . ($result ? "Success" : "Failed") . "<br>";

// Get the latest logs
$logs = $logger->getLogs();
echo "<h3>Latest Logs:</h3>";
echo "<pre>";
foreach ($logs as $log) {
    echo htmlspecialchars($log) . "\n";
}
echo "</pre>";

// Check connection directly
echo "<h3>Database Connection Check:</h3>";
try {
    require_once 'configs/config.php';
    if (isset($conn) && $conn instanceof mysqli) {
        echo "Database connection: Success<br>";
        
        // Check if the table exists
        $result = $conn->query("SHOW TABLES LIKE 'system_logs'");
        echo "system_logs table exists: " . ($result->num_rows > 0 ? "Yes" : "No") . "<br>";
        
        // Count logs in database
        if ($result->num_rows > 0) {
            $result = $conn->query("SELECT COUNT(*) as count FROM system_logs");
            $row = $result->fetch_assoc();
            echo "Total logs in database: " . $row['count'] . "<br>";
        }
    } else {
        echo "Database connection: Failed<br>";
    }
} catch (Exception $e) {
    echo "Database error: " . htmlspecialchars($e->getMessage()) . "<br>";
}
?> 