<?php
include 'config.php'; // Ensure database connection
require_once 'logger.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["case_no"])) {
    $case_no = $conn->real_escape_string($_POST["case_no"]);
    
    // Get archive reason and notes if provided
    $reason = isset($_POST["reason"]) ? $conn->real_escape_string($_POST["reason"]) : "Not specified";
    $notes = isset($_POST["notes"]) ? $conn->real_escape_string($_POST["notes"]) : "";
    
    // Create archive remarks by combining reason and notes
    $archive_remarks = "Reason: $reason";
    if (!empty($notes)) {
        $archive_remarks .= " - Notes: $notes";
    }
    
    // Get logger instance
    $logger = getLogger();
    $username = $_SESSION['username'] ?? 'Unknown';
    
    try {
        // Start transaction
        $conn->begin_transaction();
        
        // First, fetch the case data to be archived
        $sql = "SELECT * FROM cases WHERE case_no = '$case_no'";
        $result = $conn->query($sql);
        
        if ($result->num_rows === 0) {
            throw new Exception("Case not found");
        }
        
        $case = $result->fetch_assoc();
        
        // Insert into archived_cases table (adding archive_reason field)
        $stmt = $conn->prepare("INSERT INTO archived_cases 
            (case_no, title, nature, file_date, confrontation_date, action_taken, 
            settlement_date, exec_settlement_date, main_agreement, compliance_status, 
            remarks, attached_file, archive_reason, archived_by, archived_date)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            
        $stmt->bind_param("ssssssssssssss", 
            $case['case_no'], $case['title'], $case['nature'], $case['file_date'],
            $case['confrontation_date'], $case['action_taken'], $case['settlement_date'],
            $case['exec_settlement_date'], $case['main_agreement'], $case['compliance_status'],
            $case['remarks'], $case['attached_file'], $archive_remarks, $username);
            
        if (!$stmt->execute()) {
            throw new Exception("Error archiving case: " . $stmt->error);
        }
        $stmt->close();
        
        // Delete from the cases table
        $delete_sql = "DELETE FROM cases WHERE case_no = '$case_no'";
        if (!$conn->query($delete_sql)) {
            throw new Exception("Error removing case from active cases: " . $conn->error);
        }
        
        // Use the specialized archive logging method
        $logger->logArchive($case_no, $reason, $notes, $username);
        
        // Commit transaction
        $conn->commit();
        echo "success";
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        
        // Log the error
        $logger->logError('archive_case_failed', $e->getMessage());
        
        echo "Error: " . $e->getMessage();
    }
}

$conn->close();
?>
