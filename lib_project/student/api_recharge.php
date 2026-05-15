<?php
session_start();
header('Content-Type: application/json');
include "../config.php";

if(!isset($_SESSION['sid'])){
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);
if(!$data){
    $data = $_POST;
}

$amount = isset($data['amount']) ? floatval($data['amount']) : 0;
if($amount <= 0){
    echo json_encode(['status' => 'error', 'message' => 'Invalid amount']);
    exit();
}

$sid = intval($_SESSION['sid']);
$update = mysqli_query($conn, "UPDATE students SET wallet_balance = wallet_balance + $amount WHERE id='$sid'");
if(!$update){
    echo json_encode(['status' => 'error', 'message' => 'Failed to update wallet balance']);
    exit();
}

$history = mysqli_query($conn, "INSERT INTO payment_history (student_id, amount, type, date) VALUES ('$sid', '$amount', 'Deposit', NOW())");
if(!$history){
    echo json_encode(['status' => 'error', 'message' => 'Failed to save payment history']);
    exit();
}

echo json_encode(['status' => 'success', 'message' => 'Wallet recharged successfully']);
exit();
