<?php
include 'config.php'; // Include database connection

// SQL to add archive_reason field if it doesn't exist
$sql_archive_reason = "ALTER TABLE archived_cases 
                       ADD COLUMN IF NOT EXISTS archive_reason TEXT AFTER attached_file";

// SQL to add archived_by field if it doesn't exist
$sql_archived_by = "ALTER TABLE archived_cases 
                   ADD COLUMN IF NOT EXISTS archived_by VARCHAR(100) AFTER archive_reason";

// SQL to add archived_date field if it doesn't exist
$sql_archived_date = "ALTER TABLE archived_cases 
                     ADD COLUMN IF NOT EXISTS archived_date DATETIME DEFAULT CURRENT_TIMESTAMP AFTER archived_by";

// Execute queries
try {
    if ($conn->query($sql_archive_reason) === TRUE) {
        echo "Archive reason column added successfully<br>";
    } else {
        echo "Error adding archive_reason column: " . $conn->error . "<br>";
    }
    
    if ($conn->query($sql_archived_by) === TRUE) {
        echo "Archived by column added successfully<br>";
    } else {
        echo "Error adding archived_by column: " . $conn->error . "<br>";
    }
    
    if ($conn->query($sql_archived_date) === TRUE) {
        echo "Archived date column added successfully<br>";
    } else {
        echo "Error adding archived_date column: " . $conn->error . "<br>";
    }
} catch (Exception $e) {
    echo "Error updating database: " . $e->getMessage();
}

$conn->close();
echo "<p>Table update completed. <a href='../archive.php'>Go to Archive page</a></p>";
?> 