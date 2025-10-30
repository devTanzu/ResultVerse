<?php
session_start();
?>
<!doctype html>
<html>
<head>
  <title>ResultVerse - Online Result Management</title>
  <link rel="stylesheet" href="style.css">
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
