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
  <link rel="stylesheet" href="style.css">
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
