ALTER TABLE issue_records MODIFY COLUMN payment_status ENUM('Pending', 'Paid') DEFAULT 'Pending';
ALTER TABLE students ADD COLUMN wallet_balance INT DEFAULT 500;