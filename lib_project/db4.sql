CREATE TABLE IF NOT EXISTS payment_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    amount INT,
    type VARCHAR(50), -- 'Deposit' ya 'Fine'
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

mysqli_query($conn, "INSERT INTO payment_history (student_id, amount, type) VALUES ('$sid', '$fine', 'Fine Deduction')");