<?php
session_start();
include "../config.php";

$id = $_GET['id'];
$sid = $_SESSION['sid'];
$today = date('Y-m-d');

// Record fetch karein fine check karne ke liye
$res = mysqli_query($conn, "SELECT ir.*, s.wallet_balance FROM issue_records ir 
                            JOIN students s ON ir.student_id = s.id WHERE ir.id='$id'");
$data = mysqli_fetch_assoc($res);
$due = $data['due_date'];
$wallet = $data['wallet_balance'];
$fine = 0;

if($today > $due){
    $days = (strtotime($today) - strtotime($due)) / (60*60*24);
    $fine = $days * 10; // ₹10 per day fine
}

if($fine > 0){
    if($wallet >= $fine){
        // 1. Wallet se balance deduct karein
        mysqli_query($conn, "UPDATE students SET wallet_balance = wallet_balance - $fine WHERE id='$sid'");
        
        // 2. Transaction history mein entry
        mysqli_query($conn, "INSERT INTO payment_history (student_id, amount, type, date) VALUES ('$sid', '$fine', 'Fine Deduction', NOW())");
        
        // 3. Book Return karein
        mysqli_query($conn, "UPDATE issue_records SET status='Returned', fine='$fine', return_date='$today', payment_status='Paid' WHERE id='$id'");
        mysqli_query($conn, "UPDATE books SET available_stock = available_stock + 1 WHERE id='{$data['book_id']}'");
        
        echo "<script>alert('₹$fine fine deducted from your deposit. Book Returned!'); window.location='dashboard.php';</script>";
    } else {
        // Balance kam hai toh alert de
        echo "<script>alert('Insufficient Balance! Fine is ₹$fine, but your balance is ₹$wallet. Please recharge.'); window.location='dashboard.php';</script>";
    }
} else {
    // No Fine Return
    mysqli_query($conn, "UPDATE issue_records SET status='Returned', return_date='$today', payment_status='Paid' WHERE id='$id'");
    mysqli_query($conn, "UPDATE books SET available_stock = available_stock + 1 WHERE id='{$data['book_id']}'");
    echo "<script>alert('Book Returned Successfully!'); window.location='dashboard.php';</script>";
}
?>