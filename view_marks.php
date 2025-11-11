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
<link rel="stylesheet" href="style.css">
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
