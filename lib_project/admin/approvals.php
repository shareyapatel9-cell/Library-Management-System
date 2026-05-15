<?php 
session_start();
include "../config.php"; 
if(!isset($_SESSION['admin'])){ header("location: ../index.php"); exit(); }

// --- APPROVE LOGIC ---
if(isset($_GET['approve_id'])){
    $id = $_GET['approve_id'];
    $today = date('Y-m-d');
    
    // Book ID nikalna taaki stock wapas badha sakein
    $data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT book_id FROM issue_records WHERE id='$id'"));
    $bid = $data['book_id'];

    // Status update aur Payment Done karna
    mysqli_query($conn, "UPDATE issue_records SET status='Returned', return_date='$today', payment_status='Paid' WHERE id='$id'");
    
    // Stock ko +1 karna
    mysqli_query($conn, "UPDATE books SET available_stock = available_stock + 1 WHERE id='$bid'");
    
    echo "<script>alert('Cash Received & Return Approved!'); window.location='approvals.php';</script>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin - Activity Monitor</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background: #f4f4f4; }
        header { background: #2c3e50; color: white; padding: 15px; text-align: center; }
        nav { background: #34495e; padding: 10px; text-align: center; }
        nav a { color: white; margin: 0 15px; text-decoration: none; font-weight: bold; }
        .container { padding: 30px; max-width: 1100px; margin: auto; }
        table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: center; }
        th { background: #2c3e50; color: white; }
        tr:nth-child(even) { background: #f9f9f9; }
        .btn-approve { background: #27ae60; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px; font-weight: bold; font-size: 13px; }
        .status-badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .issued { background: #d1ecf1; color: #0c5460; }
        .requested { background: #fff3cd; color: #856404; }
        .returned { background: #d4edda; color: #155724; }
    </style>
</head>
<body>

<header><h1>Activity Monitor & Approvals</h1></header>
<nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="manage_books.php">Books</a>
    <a href="manage_students.php">Students</a>
    <a href="approvals.php">Activity Monitor</a>
    <a href="../index.php">Logout</a>
</nav>

<div class="container">
    <h2 style="color: #2c3e50;">📊 All Student Activity</h2>
    
    <table>
        <tr>
            <th>Student Name</th>
            <th>Book Title</th>
            <th>Issue Date</th>
            <th>Status</th>
            <th>Fine Details</th>
            <th>Action</th>
        </tr>
        <?php 
        $q = "SELECT ir.*, s.student_name, b.name as bname 
              FROM issue_records ir 
              JOIN students s ON ir.student_id = s.id 
              JOIN books b ON ir.book_id = b.id 
              ORDER BY ir.id DESC";
        $monitor = mysqli_query($conn, $q);
        
        while($m = mysqli_fetch_assoc($monitor)){
            $class = "";
            if($m['status'] == 'Issued') $class = "issued";
            elseif($m['status'] == 'Return Requested') $class = "requested";
            else $class = "returned";

            echo "<tr>
                <td>{$m['student_name']}</td>
                <td>{$m['bname']}</td>
                <td>{$m['issue_date']}</td>
                <td><span class='status-badge $class'>{$m['status']}</span></td>
                <td>
                    ₹{$m['fine']} <br>
                    <small style='color:".($m['payment_status'] == 'Paid' ? "#2ecc71" : "#e74c3c")."'>
                        (".($m['payment_status'] ?? 'Pending').")
                    </small>
                </td>
                <td>";
                
                if($m['status'] == 'Return Requested'){
                    echo "<a href='?approve_id={$m['id']}' class='btn-approve' onclick='return confirm(\"Receive Cash and Approve?\")'>Approve & Cash Received</a>";
                } else {
                    echo "<span style='color:gray; font-size:12px;'>Verified</span>";
                }

            echo "</td></tr>";
        }
        ?>
    </table>
</div>

</body>
</html>