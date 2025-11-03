<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php?role=admin");
    exit;
}
$err = $msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = $_POST['password']; 
    
    $stmt = $conn->prepare("SELECT id FROM users WHERE username=? OR email=?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $err = "Username or email already exists.";
    } else {
        $ins = $conn->prepare("INSERT INTO users (name,email,username,password,role) VALUES (?,?,?,?,?)");
        $ins->bind_param("sssss", $name, $email, $username, $password, $role);
        if ($ins->execute()) {
            $msg = "User added.";
        } else {
            $err = "DB error.";
        }
    }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Add User</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="container">
  <h3>Add User</h3>
  <?php if($err) echo "<p style='color:red;'>$err</p>"; ?>
  <?php if($msg) echo "<p style='color:green;'>$msg</p>"; ?>
  <form method="post">
    <label>Name</label><input name="name" required>
    <label>Username</label><input name="username" required>
    <label>Email</label><input name="email" type="email" required>
    <label>Role</label>
    <select name="role"><option value="student">Student</option><option value="teacher">Teacher</option></select>
    <label>Password</label><input name="password" required>
    <button class="btn" type="submit">Add</button>
  </form>
  <p><a href="admin_dashboard.php">Back</a></p>
</div>
</body></html>
