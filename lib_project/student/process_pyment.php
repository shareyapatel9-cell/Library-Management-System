<?php
session_start();
include "../config.php";

// 1. Session aur Security check
if(!isset($_SESSION['sid'])){ header("location: ../index.php"); exit(); }
$sid = $_SESSION['sid'];
$id = mysqli_real_escape_string($conn, $_GET['id']);

// 2. Fine aur Student details fetch karein
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT fine FROM issue_records WHERE id='$id'"));
$fine = $data['fine'];

$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT wallet_balance FROM students WHERE id='$sid'"));

// 3. Balance Check aur Payment Processing
if($user['wallet_balance'] >= $fine){
    
    // Start Transaction (Best practice for financial operations)
    mysqli_query($conn, "START TRANSACTION");

    // A. Wallet se balance deduct karein
    $update_wallet = mysqli_query($conn, "UPDATE students SET wallet_balance = wallet_balance - $fine WHERE id='$sid'");
    
    // B. Payment Status 'Paid' karein
    $update_record = mysqli_query($conn, "UPDATE issue_records SET payment_status='Paid' WHERE id='$id'");
    
    // C. History mein entry daalein (Jaisa aapne manga)
    $insert_history = mysqli_query($conn, "INSERT INTO payment_history (student_id, amount, type) VALUES ('$sid', '$fine', 'Fine Deduction')");

    if($update_wallet && $update_record && $insert_history){
        mysqli_query($conn, "COMMIT");
        echo "<script>alert('Fine of ₹$fine Paid Successfully!'); window.location='dashboard.php';</script>";
    } else {
        mysqli_query($conn, "ROLLBACK");
        echo "<script>alert('Error processing payment. Please try again.'); window.location='dashboard.php';</script>";
    }

} else {
    // Balance kam hone par alert
    echo "<script>alert('Insufficient Balance! Current Wallet Balance: ₹{$user['wallet_balance']}. Please Recharge first.'); window.location='recharge_wallet.php';</script>";
}
?>  