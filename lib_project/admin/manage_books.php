<?php 
session_start();
include "../config.php"; 
if(!isset($_SESSION['admin'])){ header("location: ../index.php"); }

// 1. ADD BOOK
if(isset($_POST['add_book'])){
    $n = $_POST['bn']; $a = $_POST['ba']; $p = $_POST['bp']; $s = $_POST['bs'];
    mysqli_query($conn, "INSERT INTO books (name, author, price, total_stock, available_stock) VALUES ('$n','$a','$p','$s','$s')");
    echo "<script>alert('Book Added!'); window.location='manage_books.php';</script>";
}

// 2. DELETE BOOK
if(isset($_GET['del'])){
    $id = $_GET['del'];
    mysqli_query($conn, "DELETE FROM books WHERE id='$id'");
    header("location: manage_books.php");
}

// 3. EDIT BOOK (UPDATE)
if(isset($_POST['update_book'])){
    $id = $_POST['bid']; $n = $_POST['bn']; $a = $_POST['ba']; $p = $_POST['bp']; $s = $_POST['bs'];
    // Update query: stock management ke liye hum total stock aur available stock dono update karte hain
    mysqli_query($conn, "UPDATE books SET name='$n', author='$a', price='$p', total_stock='$s', available_stock='$s' WHERE id='$id'");
    echo "<script>alert('Book Updated!'); window.location='manage_books.php';</script>";
}

// Fetch data for Edit
$edit_data = null;
if(isset($_GET['edit'])){
    $id = $_GET['edit'];
    $edit_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM books WHERE id='$id'"));
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Books & Stock</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background: #f4f4f4; }
        header, nav { text-align: center; background: #2c3e50; color: white; padding: 15px; }
        nav { background: #34495e; padding: 10px; }
        nav a { color: white; text-decoration: none; margin: 0 15px; font-weight: bold; }
        .content { padding: 20px; max-width: 1100px; margin: auto; }
        .form-box { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 30px; border-top: 5px solid #1abc9c; }
        input { padding: 10px; margin: 5px; border: 1px solid #ddd; border-radius: 4px; width: 18%; }
        .btn { background: #1abc9c; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .btn-update { background: #f39c12; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: center; }
        th { background: #2c3e50; color: white; }
        .edit-link { color: #f39c12; text-decoration: none; font-weight: bold; margin-right: 10px; }
        .del-link { color: #e74c3c; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
<header><h1>📚 Books & Inventory Management</h1></header>
<nav><a href="dashboard.php">Dashboard</a><a href="manage_books.php">Books</a><a href="manage_students.php">Students</a><a href="approvals.php">Approvals</a></nav>

<div class="content">
    <?php if($edit_data): ?>
    <div class="form-box" style="border-top-color: #f39c12;">
        <h3>✏️ Edit Book</h3>
        <form method="POST">
            <input type="hidden" name="bid" value="<?php echo $edit_data['id']; ?>">
            <input type="text" name="bn" value="<?php echo $edit_data['name']; ?>" required>
            <input type="text" name="ba" value="<?php echo $edit_data['author']; ?>" required>
            <input type="number" name="bp" value="<?php echo $edit_data['price']; ?>" required>
            <input type="number" name="bs" value="<?php echo $edit_data['total_stock']; ?>" required>
            <button type="submit" name="update_book" class="btn btn-update">Update Changes</button>
            <a href="manage_books.php" style="margin-left:10px; color:gray;">Cancel</a>
        </form>
    </div>
    <?php else: ?>
    <div class="form-box">
        <h3>➕ Add New Book</h3>
        <form method="POST">
            <input type="text" name="bn" placeholder="Book Title" required>
            <input type="text" name="ba" placeholder="Author" required>
            <input type="number" name="bp" placeholder="Price" required>
            <input type="number" name="bs" placeholder="Total Stock" required>
            <button type="submit" name="add_book" class="btn">Add Book</button>
        </form>
    </div>
    <?php endif; ?>

    <table>
        <tr>
            <th>ID</th><th>Title</th><th>Author</th><th>Price</th><th>Stock (Avail/Total)</th><th>Actions</th>
        </tr>
        <?php 
        $res = mysqli_query($conn, "SELECT * FROM books");
        while($r = mysqli_fetch_assoc($res)){
            $stock_color = ($r['available_stock'] <= 0) ? "red" : "green";
            echo "<tr>
                <td>{$r['id']}</td>
                <td>{$r['name']}</td>
                <td>{$r['author']}</td>
                <td>₹{$r['price']}</td>
                <td style='color:$stock_color; font-weight:bold;'>{$r['available_stock']} / {$r['total_stock']}</td>
                <td>
                    <a href='?edit={$r['id']}' class='edit-link'>Edit</a>
                    <a href='?del={$r['id']}' class='del-link' onclick='return confirm(\"Delete this book?\")'>Delete</a>
                </td>
            </tr>";
        }
        ?>
    </table>
</div>
</body>
</html>
