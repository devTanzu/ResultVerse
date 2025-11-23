<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
  header("Location: login.php?role=student");
  exit;
}

$student = $_SESSION['user'];
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Student Dashboard</title>

<style>
  body {
    font-family: 'Segoe UI', Arial, sans-serif;
    background: #f0f2f5;
    margin: 0;
    padding: 0;
    color: #333;
  }

  body.dashboard {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding: 40px 0;
    min-height: 100vh;
  }

  .container {
    width: 100%;
    max-width: 400px;
    background: #ffffff;
    padding: 40px 35px;
    border-radius: 16px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    text-align: center;
    margin: 60px auto;
  }

  nav {
    text-align: right;
    margin-bottom: 20px;
  }

  .logout-btn {
    background: #d9534f;
    color: #fff;
    border: none;
    padding: 10px 18px;
    border-radius: 20px;
    cursor: pointer;
    text-decoration: none;
    font-weight: 600;
  }

  .logout-btn:hover {
    background: #b52b27;
  }

  .section-box {
    background: #f9fbfd;
    border: 1px solid #dbe3ec;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 25px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
  }

  .section-box h4 {
    margin-top: 0;
    color: #1f4e79;
  }

  .btn {
    background: #1f4e79;
    color: #fff;
    border: none;
    padding: 12px;
    border-radius: 25px;
    cursor: pointer;
    font-size: 16px;
    font-weight: 600;
    width: 100%;
    margin-top: 15px;
    transition: 0.3s;
    text-decoration: none;
    display: inline-block;
  }

  .btn:hover {
    background: #3c78a4;
    transform: scale(1.03);
  }

  .small {
    text-align: center;
    color: #777;
    margin-top: 30px;
    font-size: 0.9em;
  }

  .student-dashboard-box {
    max-width: 550px;
    width: 95%;
    padding: 25px;
    margin: 50px auto;
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    text-align: center;
  }

  .student-dashboard-title {
    font-size: 27px;
    color: #1f4e79;
    margin-bottom: 20px;
    text-align: center;
  }

  .student-dashboard-box .btn {
    width: 80%;
    margin-bottom: 15px;
  }
</style>

</head>
<body class="dashboard">

<div class="container student-dashboard-box">
  <h3 class="student-dashboard-title">
      Welcome, <?= htmlspecialchars($student['name']); ?>
  </h3>

  <nav>
    <a class="logout-btn" href="logout.php">Logout</a>
  </nav>

  <div class="section-box">
    <h4>Your Options</h4>
    <a class="btn" href="view_result.php">View Result</a>
  </div>

  <p class="small">2025 ResultVerse | Developed by Tanjina Akter</p>
</div>

</body>
</html>
