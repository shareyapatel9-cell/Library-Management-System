<?php
session_start();
include "../config.php";
if(!isset($_SESSION['sid'])){ header("location: ../index.php"); exit(); }

if(isset($_POST['recharge'])){
    $amt = floatval($_POST['amount']);
    $sid = $_SESSION['sid'];

    if($amt <= 0){
        echo "<script>alert('Please enter a valid amount greater than zero.'); window.location='recharge_wallet.php';</script>";
        exit();
    }

    $api_url = 'http://localhost/lib_project/student/api_recharge.php';
    $payload = json_encode(['amount' => $amt]);

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
        echo "<script>alert('₹$amt wallet mein add ho gaye!'); window.location='dashboard.php';</script>";
    } else {
        $message = $api_result['message'] ?? $error ?: 'API response invalid';
        echo "<script>alert('Payment failed: $message'); window.location='recharge_wallet.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Recharge Wallet</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: #0f172a; height: 100vh; display: flex; align-items: center; justify-content: center; color: white; }
        .card { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(15px); padding: 40px; border-radius: 20px; border: 1px solid rgba(255, 255, 255, 0.1); width: 350px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        .info { background: #1abc9c; padding: 10px; border-radius: 10px; margin-bottom: 20px; font-weight: bold; }
        input { width: 100%; padding: 12px; margin-bottom: 20px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.08); color: white; outline: none; font-size: 16px; text-align: center; }
        .btn-pay { width: 100%; padding: 12px; background: #e67e22; border: none; color: white; border-radius: 10px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .btn-pay:hover { background: #d35400; }
    </style>
</head>
<body>
    <div class="card">
        <h3>💰 Add Money to Wallet</h3>
        <p style="font-size: 12px; color: #94a3b8; margin-bottom: 20px;">Secure Payment Simulation</p>
        <form method="POST">
            <input type="number" name="amount" placeholder="Enter Amount (₹)" required>
            <button type="submit" name="recharge" class="btn-pay">Proceed to Pay</button>
        </form>
        <a href="dashboard.php" style="display:block; margin-top:15px; color:#94a3b8; text-decoration:none; font-size: 14px;">← Back</a>
    </div>
    <div class="card" style="text-align: center;">
    <h3>💰 Recharge via UPI</h3>
    <img src="../assets/my_qr_code.png" style="width: 200px; height: 200px; margin-bottom: 20px; border: 2px solid #ddd; padding: 5px;">
    <p style="color: #64748b; margin-bottom: 20px;">Scan this QR to pay. After payment, enter the amount below:</p>
    
    <form method="POST">
        <input type="number" name="amount" placeholder="Enter Amount Paid (₹)" required>
        <button type="submit" name="recharge" class="btn-pay">Confirm Payment</button>
    </form>
</div>
<footer style="text-align: center; padding: 30px; margin-top: 50px; border-top: 1px solid #ddd; color: #64748b; background: #0f172a;">
    <p style="font-size: 15px; margin-bottom: 8px;">
        Design and Development by:
        <span style="font-weight: 600; color: #ffffff;">
            Shreya Patel, Rishita Khushwaha & Kashish Adresa
        </span>
    </p>
    <p style="font-size: 12px; color: #94a3b8;">&copy; 2026 Advanced Library Management System</p>
</footer>
</body>
</html>