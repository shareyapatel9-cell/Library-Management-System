# 📚 Advanced Library Management System (v2.0)
### *Internal Virtual Wallet & Automated Fine Deduction API Logic*

An advanced, web-based Library Management System built to modernize library operations and automate financial transactions. This project features a custom-built **Internal Virtual Wallet** that eliminates the need for manual cash handling for library fines.

---

## 👩‍💻 Developer Team
This project was developed by:
* **Shreya Patel**
* **Rishita Khushwaha**
* **Kashish Adresa**

---

## 🚀 Key Features

* **Virtual Wallet API Logic:** Every student is automatically registered with a **₹500.00** welcome deposit.
* **Auto-Fine Deduction:** System calculates fines (₹10/day) and automatically deducts them from the student's wallet upon book return.
* **Professional Student Dashboard:** Modern UI featuring a Wallet Balance Card and a **QR Code** for quick recharge simulation.
* **Real-time Inventory Tracking:** Dynamic "Stock Available" status that updates automatically on every issue and return.
* **Transaction Audit Trail:** A full history of all deposits and fine deductions for transparency.
* **Glassmorphism UI:** A sleek, modern design for a better user experience.

---

## 🛠️ Technology Stack

* **Backend:** PHP (Server-side scripting)
* **Frontend:** HTML5, CSS3 (Glassmorphism), JavaScript
* **Database:** MySQL (Relational Database)
* **Server:** XAMPP (Apache & MySQL)
* **IDE:** Visual Studio Code

---

## 📂 System Modules

### 👑 Admin Module
1. **Dashboard:** Centralized view of total books, active students, and library stats.
2. **Book Management:** CRUD operations to manage the library catalog and stock.
3. **Student Management:** Logic to add students and initialize their ₹500 virtual deposit.
4. **Activity Logs:** Monitoring active issues and fine collections.

### 🎓 Student Module
1. **Personal Dashboard:** View current virtual balance and active book issues.
2. **Library Catalog:** Browse and search for available books.
3. **Internal Payment:** One-click "Pay Fine & Return" button that triggers the internal wallet deduction.
4. **Payment History:** Complete record of all virtual money transactions.

---

## ⚙️ Core Implementation (Technical Logic)

### **Internal Payment API Simulation:**
The system uses a custom PHP logic to handle financial transactions without external gateways:
```php
// Fine Calculation Logic
$days_late = (strtotime($today) - strtotime($due_date)) / (60*60*24);
$fine = $days_late * 10;

// Internal Deduction Logic
if ($wallet_balance >= $fine) {
    // 1. Deduct from Student Wallet
    // 2. Record in Payment History Table
    // 3. Mark Book as Returned
}