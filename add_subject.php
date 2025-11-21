<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  header("Location: login.php?role=admin");
  exit;
}

$err = $msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name']);
  $code = trim($_POST['code']);

  if ($name && $code) {
    $check = $conn->prepare("SELECT id FROM subjects WHERE code=?");
    $check->bind_param("s", $code);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows > 0) {
      $err = "Subject code already exists!";
    } else {
      $ins = $conn->prepare("INSERT INTO subjects (name, code) VALUES (?, ?)");
      $ins->bind_param("ss", $name, $code);
      if ($ins->execute()) {
        $msg = "Subject added successfully!";
      } else {
        $err = "Something went wrong!";
      }
    }
  } else {
    $err = "Please fill all fields.";
  }
}
?>
<!doctype html>
<html>
<head>
  <title>Add Subject</title>

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

  input {
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
  <h3>Add Subject</h3>

  <?php if($err) echo "<p style='color:red;'>$err</p>"; ?>
  <?php if($msg) echo "<p style='color:green;'>$msg</p>"; ?>

  <form method="post">
    <label>Subject Name</label>
    <input name="name" required>

    <label>Subject Code</label>
    <input name="code" required>

    <button class="btn" type="submit">Add Subject</button>
  </form>

  <p><a href="admin_dashboard.php">Back to Dashboard</a></p>
</div>
</body>
</html>
