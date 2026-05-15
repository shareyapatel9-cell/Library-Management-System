<?php 
session_start();
include "../config.php"; 
if(!isset($_SESSION['admin'])){ header("location: ../index.php"); }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background: #f4f4f4; }
        header { background: #2c3e50; color: white; padding: 20px; text-align: center; }
        nav { background: #34495e; padding: 12px; text-align: center; }
        nav a { color: white; margin: 0 20px; text-decoration: none; font-weight: bold; }
        nav a:hover { color: #1abc9c; }
        .container { padding: 30px; max-width: 1100px; margin: auto; }
        .stats-row { display: flex; justify-content: center; gap: 20px; flex-wrap: wrap; margin-bottom: 30px; }
        .card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 200px; text-align: center; border-top: 5px solid #2c3e50; }
        .card h3 { margin: 0; color: #7f8c8d; font-size: 14px; }
        .card h1 { margin: 10px 0; color: #2c3e50; font-size: 32px; }
        
        /* New Table Style */
        .stock-section { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .stock-section h2 { color: #2c3e50; border-bottom: 2px solid #1abc9c; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background: #f8f9fa; color: #34495e; }
        .low-stock { color: red; font-weight: bold; }
        .in-stock { color: green; font-weight: bold; }
    </style>
</head>
<body>

<header><h1>📚 Library Management - Admin Panel</h1></header>

<nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="manage_books.php">Books</a>
    <a href="manage_students.php">Students</a>
    <a href="approvals.php">Activity Monitor</a>
    <a href="financial_reports.php" style="background:#27ae60; color:white; padding:10px 20px; text-decoration:none;">View Financial Reports</a>
    <a href="../logout.php">Logout</a>
</nav>

<div class="container">
    <div class="stats-row">
        <div class="card">
            <h3>Total Books</h3>
            <h1><?php echo mysqli_num_rows(mysqli_query($conn, "SELECT * FROM books")); ?></h1>
        </div>
        <div class="card" style="border-top-color: #1abc9c;">
            <h3>Active Students</h3>
            <h1><?php echo mysqli_num_rows(mysqli_query($conn, "SELECT * FROM students")); ?></h1>
        </div>
        <div class="card" style="border-top-color: #e74c3c;">
            <h3>Pending Approvals</h3>
            <h1><?php echo mysqli_num_rows(mysqli_query($conn, "SELECT * FROM issue_records WHERE status='Return Requested'")); ?></h1>
        </div>
        <div class="card" style="border-top-color: #f1c40f;">
            <h3>Fine Earned</h3>
            <h1>₹<?php 
                $fine = mysqli_fetch_assoc(mysqli_query($conn, "SELECT sum(fine) as total FROM issue_records WHERE status='Returned'"));
                echo ($fine['total'] ?? 0); 
            ?></h1>
        </div>
    </div>

    <div class="stock-section">
        <h2>📦 Book Inventory & Stock Status</h2>
        <table>
            <thead>
                <tr>
                    <th>Book Name</th>
                    <th>Author</th>
                    <th>Total Stock</th>
                    <th>Available Now</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $books = mysqli_query($conn, "SELECT * FROM books");
                while($b = mysqli_fetch_assoc($books)){
                    $status = ($b['available_stock'] > 0) ? "In Library" : "Out of Stock";
                    $status_class = ($b['available_stock'] > 0) ? "in-stock" : "low-stock";
                    
                    echo "<tr>
                        <td><b>{$b['name']}</b></td>
                        <td>{$b['author']}</td>
                        <td>{$b['total_stock']}</td>
                        <td>{$b['available_stock']}</td>
                        <td><span class='$status_class'>$status</span></td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
<footer style="text-align: center; padding: 30px; margin-top: 50px; border-top: 1px solid #ddd; color: #64748b;">
    <p style="font-size: 15px;">
        Design and Development by: 
        <span style="font-weight: 600; color: #2c3e50;">
            Shreya Patel, Rishita Khushwaha & Kashish Adresa
        </span>
    </p>
    <p style="font-size: 12px;">&copy; 2026 Advanced Library Management System</p>
</footer>
    </div>
</div>

</body>
</html>
