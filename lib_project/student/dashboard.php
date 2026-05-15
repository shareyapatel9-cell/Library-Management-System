<?php 
session_start();
include "../config.php"; 

// Authentication Check
if(!isset($_SESSION['sid'])){ header("location: ../index.php"); exit(); }
$sid = $_SESSION['sid'];

// Student ka data fetch karein (Wallet ke liye)
$user_res = mysqli_query($conn, "SELECT * FROM students WHERE id='$sid'");
$user = mysqli_fetch_assoc($user_res);

// --- SELF ISSUE LOGIC ---
if(isset($_POST['self_issue'])){
    $bid = mysqli_real_escape_string($conn, $_POST['book_id']);
    $issue_date = date('Y-m-d');
    $due_date = date('Y-m-d', strtotime('+15 days')); 

    $check = mysqli_query($conn, "SELECT * FROM issue_records WHERE student_id='$sid' AND status='Issued'");
    
    if(mysqli_num_rows($check) >= 2){
        echo "<script>alert('You cannot issue more than 2 books!');</script>";
    } else {
        // Status 'Issued' aur payment 'Pending' default rakha hai
        mysqli_query($conn, "INSERT INTO issue_records (student_id, book_id, issue_date, due_date, status, payment_status) VALUES ('$sid', '$bid', '$issue_date', '$due_date', 'Issued', 'Pending')");
        mysqli_query($conn, "UPDATE books SET available_stock = available_stock - 1 WHERE id='$bid'");
        echo "<script>alert('Book Issued for 15 Days!'); window.location='dashboard.php';</script>";
    }
if($user['wallet_balance'] < 50): ?>
    <div style="background: #fee2e2; color: #991b1b; padding: 15px; border-left: 5px solid #f87171; margin-bottom: 20px;">
        <strong>⚠️ Low Balance Alert:</strong> Aapka deposit ₹<?php echo $user['wallet_balance']; ?> reh gaya hai. 
        Agli baar fine hone par recharge ki zaroorat padegi.
    </div>

?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background: #f4f4f4; }
        header { background: #1abc9c; color: white; padding: 20px; text-align: center; }
        nav { background: #16a085; padding: 10px; text-align: center; }
        nav a { color: white; margin: 0 15px; text-decoration: none; font-weight: bold; }
        .container { padding: 20px; max-width: 1000px; margin: auto; }
        .wallet-box { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; border-left: 5px solid #e67e22; display: flex; justify-content: space-between; align-items: center; }
        .issue-section { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; border-left: 5px solid #16a085; }
        select, button { padding: 10px; border-radius: 5px; border: 1px solid #ddd; }
        .btn-issue { background: #16a085; color: white; border: none; cursor: pointer; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: center; }
        th { background: #34495e; color: white; }
    </style>
</head>
<body>
<header><h1>Student Dashboard</h1></header>
<nav>
    <a href="dashboard.php">My Account</a>
    <a href="../logout.php">Logout</a>
</nav>

<div class="container">
    <div class="wallet-box">
        <h3>Wallet Balance (Security Deposit): <span style="color:#e67e22;">₹<?php echo $user['wallet_balance']; ?></span></h3>
        <a href="recharge_wallet.php" style="background:#e67e22; color:white; padding:8px 15px; text-decoration:none; border-radius:5px;">+ Recharge</a>
    </div>

    <div class="issue-section">
        <h3>📖 Issue a New Book</h3>
        <form method="POST">
            <select name="book_id" required style="width: 70%;">
                <option value="">-- Select a Book --</option>
                <?php 
                $books = mysqli_query($conn, "SELECT * FROM books WHERE available_stock > 0");
                while($b = mysqli_fetch_assoc($books)){
                    echo "<option value='{$b['id']}'>{$b['name']} (Stock: {$b['available_stock']})</option>";
                }
                ?>
            </select>
            <button type="submit" name="self_issue" class="btn-issue">Issue Book Now</button>
        </form>
    </div>
    <div class="wallet-box">
    <h3>Wallet: ₹<?php echo $user['wallet_balance']; ?></h3>
    <div>
        <a href="payment_history.php" style="color: #3498db; margin-right: 15px;">View History</a>
        <a href="recharge_wallet.php" style="background:#e67e22; color:white; padding:8px 15px; text-decoration:none; border-radius:5px;">+ Recharge</a>
    </div>
    </div>

    <h3>Your Issued Books & History</h3>
    <table>
        <tr>
            <th>Book Name</th><th>Due Date</th><th>Fine</th><th>Status</th><th>Action</th>
        </tr>
        <?php 
        $res = mysqli_query($conn, "SELECT ir.*, b.name FROM issue_records ir JOIN books b ON ir.book_id=b.id WHERE ir.student_id='$sid'");
        while($r = mysqli_fetch_assoc($res)){
            echo "<tr>
                <td>{$r['name']}</td>
                <td>{$r['due_date']}</td>
                <td>₹{$r['fine']}</td>
                <td><b>{$r['status']}</b></td>
                <td>";
                
                if($r['status'] == 'Issued'){
                    echo "<a href='return_logic.php?id={$r['id']}' style='color:orange; font-weight:bold;'>Request Return</a>";
                } 
                elseif($r['status'] == 'Return Requested' && $r['payment_status'] == 'Pending'){
                    echo "<a href='process_payment.php?id={$r['id']}' style='color:#e74c3c; font-weight:bold;'>Pay ₹{$r['fine']} Now</a>";
                }
                else {
                    echo $r['status'];
                }
            echo "</td></tr>";
        }
        ?>
    </table>
</div>
<footer style="text-align: center; padding: 30px; margin-top: 50px; border-top: 1px solid #ddd; color: #64748b;">
    <p style="font-size: 15px;">
        Design and Development by: 
        <span style="font-weight: 600; color: #2c3e50;">
            Shreya Patel, Rishita Khushwaha & Kashish Adresa
        </span>
    </p>
    <p style="font-size: 12px;">&copy; 2026 Advanced Library Management System</p>
</footer>
</body>
</html>