<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];

if ($user['role'] === 'student') {
    $stmt = $conn->prepare("
        SELECT s.name AS subject, s.code, a.gpa
        FROM analytics a
        JOIN subjects s ON a.subject_id = s.id
        WHERE a.student_id=?
    ");
    $stmt->bind_param("i", $user['id']);
} else {
    $stmt = $conn->prepare("
        SELECT u.name AS student, AVG(a.gpa) AS cgpa
        FROM analytics a
        JOIN users u ON a.student_id = u.id
        GROUP BY a.student_id
    ");
}

$stmt->execute();
$res = $stmt->get_result();
?>
<!doctype html>
<html>
<head>
<title>Analytics</title>
<link rel="stylesheet" href="style.css">
</head>
<body class="dashboard">
<div class="analytics-box">
  <h3>Performance Analytics</h3>

  <?php if ($user['role'] === 'student'): ?>
    <table>
      <tr>
        <th>Subject</th>
        <th>Code</th>
        <th>GPA</th>
      </tr>
      <?php while($row = $res->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['subject']); ?></td>
        <td><?= htmlspecialchars($row['code']); ?></td>
        <td><?= $row['gpa']; ?></td>
      </tr>
      <?php endwhile; ?>
    </table>

    <a class="btn" href="student_dashboard.php" style="margin-top:20px;">Back</a>

  <?php else: ?>
    <table>
      <tr>
        <th>Student</th>
        <th>CGPA</th>
      </tr>
      <?php while($row = $res->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['student']); ?></td>
        <td><?= number_format($row['cgpa'], 2); ?></td>
      </tr>
      <?php endwhile; ?>
    </table>

    <a class="btn" href="admin_dashboard.php" style="margin-top:20px;">Back</a>
  <?php endif; ?>

  <p class="small">2025 ResultVerse | Developed by Tanjina Akter</p>
</div>
</body>
</html>
