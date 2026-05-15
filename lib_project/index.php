<?php 
session_start();
include "config.php"; 

$error = "";

if(isset($_POST['login'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = mysqli_real_escape_string($conn, $_POST['pass']);
    $role = $_POST['role'];

    if($role == "admin"){
        if($email == "admin@gmail.com" && $pass == "admin123"){
            $_SESSION['admin'] = "Admin";
            header("location: admin/dashboard.php");
        } else {
            $error = "Invalid Admin Credentials!";
        }
    } else {
        $res = mysqli_query($conn, "SELECT * FROM students WHERE email='$email' AND password='$pass'");
        if(mysqli_num_rows($res) > 0){
            $user = mysqli_fetch_assoc($res);
            $_SESSION['sid'] = $user['id'];
            $_SESSION['sname'] = $user['student_name'];
            header("location: student/dashboard.php");
        } else {
            $error = "Invalid Student Credentials!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library System Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }

        body {
            height: 100vh;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            /* Wahi purana Library Background */
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), 
                        url('https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            overflow: hidden;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 40px;
            width: 450px; /* Sahi balanced width */
            border-radius: 25px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
            text-align: center;
            color: white;
        }

        .brand-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 5px;
        }

        .logo-box {
            width: 40px;
            height: 40px;
            background: #1abc9c;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 18px;
            box-shadow: 0 0 15px rgba(26, 188, 156, 0.5);
        }

        .login-card h1 {
            font-size: 26px;
            font-weight: 600;
            color: #1abc9c;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        .sub-title {
            font-size: 12px;
            margin-bottom: 30px;
            opacity: 0.6;
            letter-spacing: 2px;
        }

        .role-selector {
            background: rgba(255, 255, 255, 0.05);
            padding: 12px;
            border-radius: 15px;
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-bottom: 25px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .role-selector label {
            cursor: pointer;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .role-selector input[type="radio"] {
            accent-color: #1abc9c;
            width: 18px;
            height: 18px;
        }

        .input-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .input-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 11px;
            letter-spacing: 1px;
            opacity: 0.7;
        }

        .input-group input {
            width: 100%;
            padding: 14px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            outline: none;
            border-radius: 10px;
            color: white;
            font-size: 15px;
            transition: 0.3s;
        }

        .input-group input:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: #1abc9c;
        }

        .btn-authenticate {
            width: 100%;
            padding: 15px;
            background: #1abc9c;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.4s;
            box-shadow: 0 8px 20px rgba(26, 188, 156, 0.3);
            margin-top: 5px;
            letter-spacing: 1px;
        }

        .btn-authenticate:hover {
            background: #16a085;
            transform: translateY(-2px);
        }

        .footer {
            margin-top: 30px;
            font-size: 11px;
            opacity: 0.4;
            letter-spacing: 1px;
        }

        .error-msg {
            background: rgba(231, 76, 60, 0.2);
            color: #ffb3b3;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 13px;
            border: 1px solid rgba(231, 76, 60, 0.2);
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="brand-header">
            <h1>Library SYSTEM Login</h1>
        </div>
        <p class="sub-title">SMART INVENTORY ACCESS</p>
        
        <?php if($error != ""): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="role-selector">
                <label>
                    <input type="radio" name="role" value="admin" checked> Admin
                </label>
                <label>
                    <input type="radio" name="role" value="student"> Student
                </label>
            </div>

            <div class="input-group">
                <label>INSTITUTIONAL EMAIL</label>
                <input type="email" name="email" placeholder="email@example.com" required>
            </div>

            <div class="input-group">
                <label>SECURE PASSWORD</label>
                <input type="password" name="pass" placeholder="••••••••" required>
            </div>

            <button type="submit" name="login" class="btn-authenticate">AUTHENTICATE</button>
        </form>

        <div class="footer">
            MANAGED BY DIGITAL LIBRARY SERVICES &copy; 2026
        </div>
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
