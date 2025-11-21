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
<html>
<head>
<meta charset="utf-8">
<title>Add User</title>

<style>
  body {
    font-family: 'Segoe UI', Arial, sans-serif;
    background: #f0f2f5;
    margin: 0;
    padding: 0;
    color: #333;
  }

  .container {
    width: 100%;
    max-width: 450px;
    background: #fff;
    padding: 40px 35px;
    border-radius: 16px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    text-align: center;
    margin: 60px auto;
  }

  label {
    display: block;
    text-align: left;
    margin-top: 15px;
    font-weight: 600;
  }

  input, select {
    width: 100%;
    padding: 10px;
    margin-top: 8px;
    border-radius: 10px;
    border: 1px solid #ccc;
    outline: none;
  }

  .btn {
    background: #1f4e79;
    color: #fff;
    padding: 12px;
    border-radius: 25px;
    cursor: pointer;
    font-size: 16px;
    font-weight: 600;
    width: 100%;
    margin-top: 20px;
    border: none;
    transition: 0.3s;
  }

  .btn:hover {
    background: #3c78a4;
    transform: scale(1.03);
  }

  p a {
    color: #1f4e79;
    text-decoration: none;
  }

  p a:hover {
    text-decoration: underline;
  }
</style>

</head>
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
    <select name="role">
        <option value="student">Student</option>
        <option value="teacher">Teacher</option>
    </select>
    <label>Password</label><input name="password" required>
    <button class="btn" type="submit">Add</button>
  </form>

  <p><a href="admin_dashboard.php">Back</a></p>
</div>
</body>
</html>
