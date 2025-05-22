<?php
include 'config.php';

header('Content-Type: application/json');

// Get the earliest year from filed cases
$query = "SELECT YEAR(MIN(file_date)) as earliest_year FROM cases";
$result = $conn->query($query);
$row = $result->fetch_assoc();

// If no cases exist, fallback to 2000 (or you can change this to any reasonable lower bound)
$earliest_year = ($row['earliest_year']) ? $row['earliest_year'] : 2000;

echo json_encode(['earliest_year' => $earliest_year]);
$conn->close();
?> 