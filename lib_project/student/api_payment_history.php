<?php
session_start();
header('Content-Type: application/json');
include "../config.php";

if(!isset($_SESSION['sid'])){
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$sid = intval($_SESSION['sid']);

$total_dep = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(amount), 0) AS total FROM payment_history WHERE student_id='$sid' AND type='Deposit'"));
$total_fine = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(amount), 0) AS total FROM payment_history WHERE student_id='$sid' AND type='Fine Deduction'"));

$history = [];
$res = mysqli_query($conn, "SELECT * FROM payment_history WHERE student_id='$sid' ORDER BY date DESC LIMIT 100");
while($row = mysqli_fetch_assoc($res)){
    $history[] = [
        'type' => $row['type'],
        'amount' => floatval($row['amount']),
        'date' => $row['date'],
    ];
}

echo json_encode([
    'status' => 'success',
    'total_deposits' => floatval($total_dep['total']),
    'total_fines' => floatval($total_fine['total']),
    'history' => $history,
]);
exit();
