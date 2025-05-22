<?php
include 'config.php'; // update this if your DB config is in a different location

$accounts = [];

// Fetch active accounts
$sql = "SELECT username, email, accountType FROM accounts";
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $row['accountType'] = ucfirst($row['accountType']);
    $row['status'] = 'Active';
    $accounts[] = $row;
}

// Fetch deactivated accounts
$sql = "SELECT username, email, accountType FROM deactivated_accounts";
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $row['accountType'] = ucfirst($row['accountType']);
    $row['status'] = 'Deactivated';
    $accounts[] = $row;
}

echo json_encode($accounts);
?>