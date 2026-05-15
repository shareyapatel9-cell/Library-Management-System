<?php 
session_start(); include "../config.php"; 
if(!isset($_SESSION['admin'])){ header("location: ../index.php"); exit(); }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin - Financial Reports</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: #f8fafc; padding: 40px; color: #1e293b; }
        .container { max-width: 1000px; margin: auto; }
        
        /* Summary Cards */
        .summary-row { display: flex; gap: 20px; margin-bottom: 30px; }
        .card { flex: 1; background: white; padding: 20px; border-radius: 15px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border-left: 5px solid #2c3e50; }
        .card h4 { color: #64748b; font-size: 14px; }
        .card p { font-size: 24px; font-weight: 600; color: #0f172a; }

        .table-container { background: white; padding: 20px; border-radius: 15px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #f1f5f9; color: #475569; padding: 15px; text-transform: uppercase; font-size: 12px; }
        td { padding: 15px; border-bottom: 1px solid #f1f5f9; text-align: center; }
        .type-badge { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .deposit { background: #dcfce7; color: #166534; }
        .fine { background: #fee2e2; color: #991b1b; }
        .back-btn { display: inline-block; margin-top: 20px; color: #64748b; text-decoration: none; font-weight: 500; }
    </style>
</head>
<body>
<div class="container">
    <h2>💰 Financial Dashboard</h2>
    
    <?php
    // Calculate Totals
    $total_dep = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as total FROM payment_history WHERE type='Deposit'"));
    $total_fine = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as total FROM payment_history WHERE type='Fine Deduction'"));
    ?>

    <div class="summary-row">
        <div class="card"><h4>Total Deposits</h4><p id="total-deposits">₹<?php echo $total_dep['total'] ?? 0; ?></p></div>
        <div class="card" style="border-color:#e74c3c;"><h4>Total Fines Collected</h4><p id="total-fines">₹<?php echo $total_fine['total'] ?? 0; ?></p></div>
    </div>

    <div class="table-container">
        <h3>Recent Transactions</h3>
        <table>
            <tr><th>Student ID</th><th>Type</th><th>Amount</th><th>Date</th></tr>
            <tbody id="transactions-body">
            <?php 
            $res = mysqli_query($conn, "SELECT * FROM payment_history ORDER BY date DESC");
            while($r = mysqli_fetch_assoc($res)){
                $class = ($r['type'] == 'Deposit') ? 'deposit' : 'fine';
                echo "<tr>
                        <td>#{$r['student_id']}</td>
                        <td><span class='type-badge $class'>{$r['type']}</span></td>
                        <td style='font-weight:600;'>₹{$r['amount']}</td>
                        <td style='color:#64748b;'>{$r['date']}</td>
                      </tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
    <a href="dashboard.php" class="back-btn">← Back to Admin Panel</a>
    <script>
        async function refreshAdminReport() {
            try {
                const res = await fetch('api_payment_data.php', { credentials: 'same-origin' });
                if (!res.ok) return;
                const data = await res.json();
                if (data.status !== 'success') return;

                document.getElementById('total-deposits').textContent = `₹${data.total_deposits}`;
                document.getElementById('total-fines').textContent = `₹${data.total_fines}`;

                const body = document.getElementById('transactions-body');
                body.innerHTML = '';
                data.transactions.forEach(tx => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>#${tx.student_id}</td>
                        <td><span class="type-badge ${tx.type === 'Deposit' ? 'deposit' : 'fine'}">${tx.type}</span></td>
                        <td style="font-weight:600;">₹${tx.amount}</td>
                        <td style="color:#64748b;">${tx.date}</td>
                    `;
                    body.appendChild(row);
                });
            } catch (e) {
                console.error('Admin report fetch error', e);
            }
        }
        window.addEventListener('load', refreshAdminReport);
    </script>
</div>
</body>
</html>