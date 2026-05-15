<?php
session_start();
header('Content-Type: application/json');
include "../config.php";

if(!isset($_SESSION['admin'])){
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$total_dep = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(amount), 0) AS total FROM payment_history WHERE type='Deposit'"));
$total_fine = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(amount), 0) AS total FROM payment_history WHERE type='Fine Deduction'"));

$transactions = [];
$res = mysqli_query($conn, "SELECT * FROM payment_history ORDER BY date DESC LIMIT 100");
while($row = mysqli_fetch_assoc($res)){
    $transactions[] = [
        'student_id' => intval($row['student_id']),
        'type' => $row['type'],
        'amount' => floatval($row['amount']),
        'date' => $row['date'],
    ];
}

echo json_encode([
    'status' => 'success',
    'total_deposits' => floatval($total_dep['total']),
    'total_fines' => floatval($total_fine['total']),
    'transactions' => $transactions,
]);
exit();
