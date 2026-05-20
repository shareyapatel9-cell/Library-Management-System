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

    // Backend Wallet API Call Simulation
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
        $close = curl_close($ch);
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
        echo "<script>alert('₹$amt successfully added to your virtual wallet!'); window.location='dashboard.php';</script>";
    } else {
        $message = $api_result['message'] ?? $error ?: 'API response invalid';
        echo "<script>alert('Payment verification failed: $message'); window.location='recharge_wallet.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Secure Wallet Recharge</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: #0f172a; min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; color: white; padding: 20px; }
        
        .main-container { display: flex; flex-direction: column; align-items: center; justify-content: center; flex: 1; width: 100%; }
        
        /* Unified Glassmorphism Card */
        .premium-card { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(16px); padding: 35px; border-radius: 24px; border: 1px solid rgba(255, 255, 255, 0.08); width: 100%; max-width: 400px; text-align: center; box-shadow: 0 20px 40px rgba(0,0,0,0.4); }
        
        .badge { background: rgba(26, 188, 156, 0.15); color: #1abc9c; padding: 6px 16px; border-radius: 50px; font-size: 12px; display: inline-block; margin-bottom: 15px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; border: 1px solid rgba(26, 188, 156, 0.3); }
        
        .qr-wrapper { background: white; padding: 15px; border-radius: 16px; display: inline-block; margin: 20px 0; box-shadow: 0 10px 25px rgba(0,0,0,0.2); transition: 0.3s; }
        .qr-wrapper:hover { transform: scale(1.02); }
        .qr-wrapper img { width: 180px; height: 180px; display: block; }
        
        .instruction { color: #94a3b8; font-size: 13px; line-height: 1.6; margin-bottom: 20px; padding: 0 10px; }
        
        input { width: 100%; padding: 14px; margin-bottom: 20px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.06); color: white; outline: none; font-size: 16px; text-align: center; transition: 0.3s; }
        input:focus { border-color: #e67e22; background: rgba(255,255,255,0.1); box-shadow: 0 0 10px rgba(230, 126, 34, 0.2); }
        
        .btn-pay { width: 100%; padding: 14px; background: linear-gradient(135deg, #e67e22, #d35400); border: none; color: white; border-radius: 12px; font-weight: 600; font-size: 16px; cursor: pointer; transition: 0.3s; box-shadow: 0 4px 15px rgba(230, 126, 34, 0.3); }
        .btn-pay:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(230, 126, 34, 0.4); }
        
        .back-link { display: inline-block; margin-top: 20px; color: #94a3b8; text-decoration: none; font-size: 14px; transition: 0.3s; }
        .back-link:hover { color: white; }
        
        /* Fixed Branding Footer */
        footer { width: 100%; text-align: center; padding: 25px 20px; border-top: 1px solid rgba(255,255,255,0.05); margin-top: 40px; }
        .dev-team { font-weight: 600; color: #ffffff; letter-spacing: 0.5px; }
    </style>
</head>
<body>

    <div class="main-container">
        <div class="premium-card">
            <div class="badge">UPI Secure Gateway</div>
            <h3 style="font-weight: 600; font-size: 22px;">Digital Wallet Recharge</h3>
            
            <div class="qr-wrapper">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=upi://pay?pa=library@upi&pn=SmartLib&am=0" alt="Payment QR Code">
            </div>
            
            <p class="instruction">Scan the secure QR code using any UPI app (GPay, PhonePe, Paytm). Once paid, enter the exact amount below to sync your ledger.</p>
            
            <form method="POST">
                <input type="number" name="amount" placeholder="Enter Amount Paid (₹)" min="1" step="any" required>
                <button type="submit" name="recharge" class="btn-pay">Confirm & Sync Balance</button>
            </form>
            
            <a href="dashboard.php" class="back-link">← Cancel & Go Back</a>
        </div>
    </div>

    <footer>
        <p style="font-size: 14px; color: #94a3b8; margin-bottom: 6px;">
            Design and Development by: 
            <span class="dev-team">Shreya Patel, Rishita Khushwaha & Kashish Adresa</span>
        </p>
        <p style="font-size: 11px; color: #64748b; letter-spacing: 1px;">&copy; 2026 Advanced Library Management System | Version 2.0</p>
    </footer>

</body>
</html>
