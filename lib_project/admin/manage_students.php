<?php 
session_start();
include "../config.php"; 
if(!isset($_SESSION['admin'])){ header("location: ../index.php"); }

// Delete Logic
if(isset($_GET['del'])){
    $id = $_GET['del'];
    mysqli_query($conn, "DELETE FROM students WHERE id='$id'");
    header("location: manage_students.php");
}

// Add Logic
// Add Student Logic update karein
if(isset($_POST['add'])){
    $n = mysqli_real_escape_string($conn, $_POST['n']);
    $e = mysqli_real_escape_string($conn, $_POST['e']);
    $p = mysqli_real_escape_string($conn, $_POST['p']);
    $c = mysqli_real_escape_string($conn, $_POST['c']);
    $ph = mysqli_real_escape_string($conn, $_POST['ph']);
    $initial_deposit = 500; // Sir ki requirement

    // Pehle student bin wallet balance ke insert karein
    $q1 = mysqli_query($conn, "INSERT INTO students (student_name, email, password, college_name, phone, wallet_balance) VALUES ('$n','$e','$p','$c','$ph', '0')");

    if($q1){
        $student_id = mysqli_insert_id($conn);
        
        // API call karein deposit process ke liye
        $api_url = 'http://localhost/lib_project/admin/api_deposit.php';
        $payload = json_encode(['student_id' => $student_id, 'amount' => $initial_deposit]);

        $cookie = 'PHPSESSID=' . session_id();
        if(function_exists('curl_version')){
            $ch = curl_init($api_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', "Cookie: $cookie"]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            $response = curl_exec($ch);
            $error = curl_error($ch);
            curl_close($ch);
        } else {
            $context = stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => "Content-Type: application/json\r\nCookie: $cookie\r\n",
                    'content' => $payload,
                ]
            ]);
            $response = file_get_contents($api_url, false, $context);
            $error = ($response === false) ? 'Unable to call API' : '';
        }

        $api_result = json_decode($response, true);

        if($api_result && $api_result['status'] === 'success'){
            echo "<script>alert('Student Added and ₹$initial_deposit deposit processed via API!'); window.location='manage_students.php';</script>";
        } else {
            $message = $api_result['message'] ?? $error ?: 'API response invalid';
            echo "<script>alert('Student added but deposit failed: $message'); window.location='manage_students.php';</script>";
        }
    } else {
        echo "<script>alert('Student add failed. Please check input.'); window.location='manage_students.php';</script>";
    }
}
<!DOCTYPE html>
<html>
<head>
    <title>Manage Students</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background: #f4f4f4; }
        header, nav { text-align: center; background: #2c3e50; color: white; padding: 15px; }
        nav { background: #34495e; padding: 10px; }
        nav a { color: white; text-decoration: none; margin: 0 15px; font-weight: bold; }
        .content { padding: 20px; max-width: 1000px; margin: auto; }
        .form-box { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 30px; }
        input { padding: 10px; margin: 5px; border: 1px solid #ddd; border-radius: 4px; width: 18%; }
        .btn { background: #1abc9c; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: center; }
        th { background: #2c3e50; color: white; }
        .del-btn { color: #e74c3c; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
<header><h1>Manage Students</h1></header>
<nav><a href="dashboard.php">Dashboard</a><a href="manage_books.php">Books</a><a href="manage_students.php">Students</a><a href="approvals.php">Approvals</a></nav>

<div class="content">
    <div class="form-box">
        <h3>Add New Student</h3>
        <form method="POST">
            <input type="text" name="n" placeholder="Student Name" required>
            <input type="email" name="e" placeholder="Email" required>
            <input type="text" name="p" placeholder="Password" required>
            <input type="text" name="c" placeholder="College Name" required>
            <input type="text" name="ph" placeholder="Phone Number" required>
            <button type="submit" name="add" class="btn">Add Student</button>
        </form>
    </div>

   
    <table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Password</th> <th>College</th>
        <th>Phone</th>
        <th>Action</th>
    </tr>
    <?php 
    $res = mysqli_query($conn, "SELECT * FROM students");
    while($r = mysqli_fetch_assoc($res)){
        echo "<tr>
            <td>{$r['id']}</td>
            <td>{$r['student_name']}</td>
            <td>{$r['email']}</td>
            <td style='color:#16a085; font-weight:bold;'>{$r['password']}</td> <td>{$r['college_name']}</td>
            <td>{$r['phone']}</td>
            <td>
                <a href='edit_student.php?id={$r['id']}'>Edit</a> | 
                <a href='?del={$r['id']}' style='color:red;' onclick='return confirm(\"Delete student?\")'>Delete</a>
            </td>
        </tr>";
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

