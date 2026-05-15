<?php 
session_start();
include "../config.php"; 
if(!isset($_SESSION['admin'])){ header("location: ../index.php"); }

$id = $_GET['id'];
$s = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM students WHERE id='$id'"));

if(isset($_POST['update'])){
    $n = $_POST['n']; $e = $_POST['e']; $c = $_POST['c']; $ph = $_POST['ph'];
    mysqli_query($conn, "UPDATE students SET student_name='$n', email='$e', college_name='$c', phone='$ph' WHERE id='$id'");
    echo "<script>alert('Student Updated!'); window.location='manage_students.php';</script>";
}
?>
<!DOCTYPE html>
<html>
<head><title>Edit Student</title>
<style>
    body { font-family: Arial; background: #f4f4f4; margin: 0; padding: 20px; }
    .form-box { background: white; padding: 30px; border-radius: 10px; max-width: 500px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    input { width: 100%; padding: 10px; margin: 10px 0; box-sizing: border-box; }
    .btn { background: #2c3e50; color: white; border: none; padding: 12px; width: 100%; cursor: pointer; }
</style></head>
<body>
    <div class="form-box">
        <h2>✏️ Edit Student Details</h2>
        <form method="POST">
            <input type="text" name="n" value="<?php echo $s['student_name']; ?>" required>
            <input type="email" name="e" value="<?php echo $s['email']; ?>" required>
            <input type="text" name="c" value="<?php echo $s['college_name']; ?>" required>
            <input type="text" name="ph" value="<?php echo $s['phone']; ?>" required>
            <button type="submit" name="update" class="btn">Update Student</button>
            <br><br><a href="manage_students.php">Back to List</a>
        </form>
    </div>
</body>
</html>
