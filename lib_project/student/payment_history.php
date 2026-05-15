<?php 
session_start(); 
include "../config.php"; 
if(!isset($_SESSION['sid'])){ header("location: ../index.php"); exit(); }
$sid = $_SESSION['sid'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Transaction History</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: #f8fafc; padding: 40px 20px; color: #1e293b; }
        .container { max-width: 800px; margin: auto; background: white; padding: 30px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        h3 { margin-bottom: 20px; color: #0f172a; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; }
        table { width: 100%; border-collapse: separate; border-spacing: 0; margin-top: 20px; }
        th { background: #0f172a; color: white; padding: 15px; }
        th:first-child { border-radius: 10px 0 0 10px; }
        th:last-child { border-radius: 0 10px 10px 0; }
        td { padding: 15px; border-bottom: 1px solid #f1f5f9; text-align: center; }
        tr:hover { background: #f8fafc; }
        .type-badge { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .deposit { background: #dcfce7; color: #166534; }
        .fine { background: #fee2e2; color: #991b1b; }
        .btn-back { display: inline-block; margin-top: 20px; color: #64748b; text-decoration: none; font-size: 14px; font-weight: 500; }
        .btn-back:hover { color: #0f172a; }
        .summary-row { display: flex; gap: 15px; margin-bottom: 25px; flex-wrap: wrap; }
        .small-card { flex: 1; background: #f1f5f9; padding: 18px; border-radius: 15px; box-shadow: 0 6px 18px rgba(15, 23, 42, 0.05); }
        .small-card h4 { margin-bottom: 8px; color: #475569; font-size: 14px; }
        .small-card p { font-size: 22px; font-weight: 700; color: #0f172a; }
    </style>
</head>
<body>
<div class="container">
    <h3>📜 Transaction History</h3>
    <div class="summary-row">
        <div class="small-card">
            <h4>Total Deposits</h4>
            <p id="student-total-deposits">₹0</p>
        </div>
        <div class="small-card">
            <h4>Total Fines</h4>
            <p id="student-total-fines">₹0</p>
        </div>
    </div>
    <table>
        <tr>
            <th>Date & Time</th>
            <th>Type</th>
            <th>Amount</th>
        </tr>
        <tbody id="history-body">
        <?php 
        $hist = mysqli_query($conn, "SELECT * FROM payment_history WHERE student_id='$sid' ORDER BY date DESC");
        if(mysqli_num_rows($hist) > 0){
            while($h = mysqli_fetch_assoc($hist)){
                $badge_class = ($h['type'] == 'Deposit') ? 'deposit' : 'fine';
                echo "<tr>
                        <td>{$h['date']}</td>
                        <td><span class='type-badge $badge_class'>{$h['type']}</span></td>
                        <td style='font-weight:600;'>₹{$h['amount']}</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='3' style='padding:20px; color:#94a3b8;'>No transactions found.</td></tr>";
        }
        ?>
        </tbody>
    </table>
    <a href="dashboard.php" class="btn-back">← Back to Dashboard</a>
    <script>
        async function refreshStudentHistory() {
            try {
                const res = await fetch('api_payment_history.php', { credentials: 'same-origin' });
                if (!res.ok) return;
                const data = await res.json();
                if (data.status !== 'success') return;

                document.getElementById('student-total-deposits').textContent = `₹${data.total_deposits}`;
                document.getElementById('student-total-fines').textContent = `₹${data.total_fines}`;

                const body = document.getElementById('history-body');
                body.innerHTML = '';
                if (data.history.length === 0) {
                    body.innerHTML = '<tr><td colspan="3" style="padding:20px; color:#94a3b8;">No transactions found.</td></tr>';
                    return;
                }

                data.history.forEach(tx => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${tx.date}</td>
                        <td><span class="type-badge ${tx.type === 'Deposit' ? 'deposit' : 'fine'}">${tx.type}</span></td>
                        <td style="font-weight:600;">₹${tx.amount}</td>
                    `;
                    body.appendChild(row);
                });
            } catch (e) {
                console.error('Student history fetch error', e);
            }
        }
        window.addEventListener('load', refreshStudentHistory);
    </script>
</div>
</body>
</html>