-- 1. Create Database
CREATE DATABASE IF NOT EXISTS library_db;
USE library_db;

-- 2. Books Table (Stock management ke liye)
CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    price INT NOT NULL,
    total_stock INT NOT NULL,
    available_stock INT NOT NULL
);

-- 3. Students Table (Login credentials aur Membership ke liye)
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    college_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    membership_status ENUM('Active', 'Expired') DEFAULT 'Active'
);

-- 4. Issue & Return Records (Fine aur Admin Approval ke liye)
CREATE TABLE IF NOT EXISTS issue_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    book_id INT NOT NULL,
    issue_date DATE NOT NULL,
    due_date DATE NOT NULL,
    return_date DATE DEFAULT NULL,
    status ENUM('Issued', 'Return Requested', 'Returned') DEFAULT 'Issued',
    fine INT DEFAULT 0,
    payment_status ENUM('Pending', 'Paid') DEFAULT 'Pending',
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
);

-- 5. Dummy Data for Testing (Optional)
-- Admin ka login index.php mein hardcoded hai (admin@gmail.com / admin123)
-- Ek student testing ke liye:
INSERT INTO students (student_name, email, password, college_name, phone) 
VALUES ('Rahul Sharma', 'rahul@gmail.com', 'rahul123', 'L.D. College', '9876543210');


ALTER TABLE issue_records ADD COLUMN payment_method VARCHAR(50) DEFAULT 'Cash';
ALTER TABLE issue_records ADD COLUMN transaction_id VARCHAR(100) DEFAULT NULL;

-- Students table mein balance aur membership add karein
ALTER TABLE students ADD COLUMN wallet_balance INT DEFAULT 500; -- Security Deposit
ALTER TABLE students ADD COLUMN membership_date DATE;

-- Payment history track karne ke liye
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    amount INT,
    reason VARCHAR(255), -- 'Membership Fee', 'Late Fine', 'Security Deposit'
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);