<?php 
session_start();
include "../config.php"; 
if(!isset($_SESSION['sid'])){ header("location: ../index.php"); }
?>
<!DOCTYPE html>
<html>
<head><title>Browse Books</title>
<style>
    body { font-family: Arial; background: #f4f4f4; margin: 0; }
    header { background: #1abc9c; color: white; padding: 15px; text-align: center; }
    .container { padding: 20px; display: flex; flex-wrap: wrap; gap: 20px; justify-content: center; }
    .book-card { background: white; padding: 20px; border-radius: 10px; width: 250px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-top: 5px solid #1abc9c; }
    .status { font-weight: bold; padding: 3px 8px; border-radius: 4px; font-size: 12px; }
    .avail { background: #d4edda; color: #155724; }
    .out { background: #f8d7da; color: #721c24; }
</style></head>
<body>
<header><h1>📖 Library Book List</h1></header>
<div style="text-align:center; padding: 10px;">
    <a href="dashboard.php">Back to Dashboard</a>
</div>
<div class="container">
    <?php 
    $res = mysqli_query($conn, "SELECT * FROM books");
    while($r = mysqli_fetch_assoc($res)){
        $status_class = ($r['available_stock'] > 0) ? "avail" : "out";
        $status_text = ($r['available_stock'] > 0) ? "Available" : "Currently Unavailable";
    ?>
    <div class="book-card">
        <h3><?php echo $r['name']; ?></h3>
        <p><b>Author:</b> <?php echo $r['author']; ?></p>
        <p><b>Price:</b> ₹<?php echo $r['price']; ?></p>
        <span class="status <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
        <p style="font-size: 13px; color: gray;">Stock Left: <?php echo $r['available_stock']; ?></p>
    </div>
    <?php } ?>
</div>
    <p style="font-size: 12px;">&copy; 2026 Advanced Library Management System</p>
<footer style="text-align: center; padding: 30px; margin-top: 50px; border-top: 2px solid #1abc9c; background: #fdfdfd; color: #64748b;">
    <p style="font-size: 16px; margin-bottom: 10px;">
        🚀 <b>Design and Development by:</b> 
    </p>
    <p style="font-size: 18px; font-weight: 700; color: #2c3e50; text-transform: uppercase; letter-spacing: 1px;">
        Shreya Patel, Rishita Khushwaha & Kashish Adresa
    </p>
    <p style="font-size: 12px; margin-top: 10px;">&copy; 2026 Advanced Library Management System | Version 2.0 (Internal Wallet API)</p>
</footer>
</body>
</html>
