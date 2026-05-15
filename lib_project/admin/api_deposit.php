<?php
session_start();
header('Content-Type: application/json');
include "../config.php";

if(!isset($_SESSION['admin'])){
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);
if(!$data){
    $data = $_POST;
}

$student_id = isset($data['student_id']) ? intval($data['student_id']) : 0;
$amount = isset($data['amount']) ? floatval($data['amount']) : 0;

if($student_id <= 0 || $amount <= 0){
    echo json_encode(['status' => 'error', 'message' => 'Invalid student ID or amount']);
    exit();
}

// Deposit amount into student's wallet and save history
$update = mysqli_query($conn, "UPDATE students SET wallet_balance = wallet_balance + $amount WHERE id='$student_id'");
if(!$update){
    echo json_encode(['status' => 'error', 'message' => 'Failed to update wallet balance']);
    exit();
}

$history = mysqli_query($conn, "INSERT INTO payment_history (student_id, amount, type, date) VALUES ('$student_id', '$amount', 'Deposit', NOW())");
if(!$history){
    echo json_encode(['status' => 'error', 'message' => 'Failed to record payment history']);
    exit();
}

echo json_encode(['status' => 'success', 'message' => 'Deposit completed successfully']);
exit();
