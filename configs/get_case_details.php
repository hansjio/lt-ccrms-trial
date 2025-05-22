<?php
include 'config.php';

header('Content-Type: application/json');

if (!isset($_GET['case_no'])) {
    echo json_encode(['error' => 'No case number provided']);
    exit;
}

$case_no = $_GET['case_no'];

// 1. Get the main case information
$case_sql = "SELECT 
    case_no, title, nature, 
    DATE_FORMAT(file_date, '%Y-%m-%d') as file_date, 
    DATE_FORMAT(confrontation_date, '%Y-%m-%d') as confrontation_date, 
    action_taken, settlement_date, exec_settlement_date, main_agreement, 
    compliance_status, remarks 
    FROM cases 
    WHERE case_no = ?";

$case_stmt = $conn->prepare($case_sql);
$case_stmt->bind_param("s", $case_no);
$case_stmt->execute();
$case_result = $case_stmt->get_result();

if ($case_result->num_rows === 0) {
    echo json_encode(['error' => 'Case not found']);
    exit;
}

$case_data = $case_result->fetch_assoc();
$case_stmt->close();

// 2. Get all complainants
$complainants_sql = "SELECT 
    cp.person_id, p.first_name, p.middle_name, p.last_name, p.suffix
    FROM case_persons cp
    JOIN persons p ON cp.person_id = p.person_id
    WHERE cp.case_no = ? AND cp.role = 'Complainant'";

$complainants_stmt = $conn->prepare($complainants_sql);
$complainants_stmt->bind_param("s", $case_no);
$complainants_stmt->execute();
$complainants_result = $complainants_stmt->get_result();
$complainants = [];

while ($row = $complainants_result->fetch_assoc()) {
    $complainants[] = $row;
}
$complainants_stmt->close();

// 3. Get all respondents
$respondents_sql = "SELECT 
    cp.person_id, p.first_name, p.middle_name, p.last_name, p.suffix
    FROM case_persons cp
    JOIN persons p ON cp.person_id = p.person_id
    WHERE cp.case_no = ? AND cp.role = 'Respondent'";

$respondents_stmt = $conn->prepare($respondents_sql);
$respondents_stmt->bind_param("s", $case_no);
$respondents_stmt->execute();
$respondents_result = $respondents_stmt->get_result();
$respondents = [];

while ($row = $respondents_result->fetch_assoc()) {
    $respondents[] = $row;
}
$respondents_stmt->close();

// 4. Combine all data and return
$response = [
    'case' => $case_data,
    'complainants' => $complainants,
    'respondents' => $respondents
];

echo json_encode($response);
$conn->close();
?>