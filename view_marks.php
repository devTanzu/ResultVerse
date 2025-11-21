<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
  header("Location: login.php?role=teacher");
  exit;
}

$teacher = $_SESSION['user'];
$subject_id = isset($_GET['subject_id']) ? (int)$_GET['subject_id'] : 0;

$check = $conn->prepare("SELECT name, code FROM subjects WHERE id=? AND teacher_id=?");
$check->bind_param("ii", $subject_id, $teacher['id']);
$check->execute();
$subject = $check->get_result()->fetch_assoc();

if (!$subject) {
  die("<h3 style='text-align:center;color:red;margin-top:100px;'>Invalid subject access.</h3>");
}

$marks = $conn->prepare("
  SELECT m.id, u.name AS student_name, m.marks 
  FROM marks m
  JOIN users u ON m.student_id = u.id
  WHERE m.subject_id=?
  ORDER BY u.name ASC
");
$marks->bind_param("i", $subject_id);
$marks->execute();
$res = $marks->get_result();
?>
<!doctype html>
<html>
<head>
<title>View Marks</title>
<style>
body.dashboard {
    font-family: 'Segoe UI', Arial, sans-serif;
    background: #eef3f8;
    padding: 40px 0;
    color: #333;
}
.result-container {
    max-width: 800px;
    width: 95%;
    margin: 0 auto;
    background: #fff;
    padding: 30px 35px;
    border-radius: 16px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    text-align: center;
}
.result-container h3 {
    color: #1f4e79;
    margin-bottom: 20px;
}
.result-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
    border-radius: 12px;
    overflow: hidden;
}
.result-table th {
    background: linear-gradient(to right, #1f4e79, #2f6aa6);
    color: white;
    padding: 12px;
    text-align: left;
}
.result-table td {
    background: #ffffff;
    padding: 12px;
    border-bottom: 1px solid #e5eef5;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.result-table tr:nth-child(even) td {
    background: #f5f9ff;
}
.result-table tr:hover td {
    background: #e7f1ff;
}
.btn {
    display: inline-block;
    background: #1f4e79;
    color: #fff;
    padding: 12px 20px;
    border-radius: 25px;
    font-weight: 600;
    text-decoration: none;
    margin: 10px 0;
    transition: 0.3s;
}
.btn:hover {
    background: #3c78a4;
    transform: translateY(-2px);
}
.bottom-btns {
    margin-top: 20px;
}
.small {
    text-align: center;
    margin-top: 30px;
    color: #777;
}
</style>
</head>
<body class="dashboard">

<div class="result-container">
  <h3>Marks - <?= htmlspecialchars($subject['name']); ?> (<?= htmlspecialchars($subject['code']); ?>)</h3>

  <a class="btn" href="add_marks.php?subject_id=<?= $subject_id; ?>">Add More Marks</a>

  <table class="result-table">
    <tr>
      <th>ID</th>
      <th>Student Name</th>
      <th>Marks</th>
    </tr>
    <?php if ($res->num_rows == 0): ?>
      <tr><td colspan="3">No marks entered yet.</td></tr>
    <?php else: ?>
      <?php while($row = $res->fetch_assoc()): ?>
      <tr>
        <td><?= $row['id']; ?></td>
        <td><?= htmlspecialchars($row['student_name']); ?></td>
        <td><?= htmlspecialchars($row['marks']); ?></td>
      </tr>
      <?php endwhile; ?>
    <?php endif; ?>
  </table>

  <div class="bottom-btns">
    <a class="btn" href="teacher_dashboard.php">Back</a>
  </div>

  <p class="small">2025 ResultVerse | Developed by Tanjina Akter</p>
</div>

</body>
</html>
