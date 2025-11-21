<?php
session_start();
?>
<!doctype html>
<html>
<head>
  <title>ResultVerse - Online Result Management</title>

  <style>
    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      background: #f0f2f5;
      margin: 0;
      padding: 0;
      color: #333;
    }

    body:not(.dashboard) .container {
      width: 100%;
      max-width: 400px;
      background: #ffffff;
      padding: 40px 35px;
      border-radius: 16px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
      text-align: center;
      margin: 60px auto;
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
      margin-top: 20px;
      transition: 0.3s;
      text-decoration: none;
      display: inline-block;
      text-align: center;
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
  <h2> ResultVerse</h2>
  <h3>Online Result Management System</h3>
  <p style="text-align:center; color:#444;">A simple and efficient way to manage and view student results online.</p>

  <nav>
    <a class="btn" href="login.php?role=admin">Admin Login</a>
    <a class="btn" href="login.php?role=teacher">Teacher Login</a>
    <a class="btn" href="login.php?role=student">Student Login</a>
  </nav>
  <footer>
    2025 ResultVerse | Developed by Tanjina Akter
  </footer>
</div>

</body>
</html>
