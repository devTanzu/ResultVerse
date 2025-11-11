<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
  header("Location: login.php?role=teacher");
  exit;
}

$teacher = $_SESSION['user'];
$subjects = $conn->prepare("SELECT id, name, code FROM subjects WHERE teacher_id=?");
$subjects->bind_param("i", $teacher['id']);
$subjects->execute();
$res = $subjects->get_result();
?>
<!doctype html>
<html>
<head>
  <title>Teacher Dashboard</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="dashboard">
<div class="container">
  <h3>Teacher Dashboard</h3>
  <nav>
    <a class="logout-btn" href="logout.php">Logout</a>
  </nav>

  <div class="section-box">
    <h4>Your Assigned Subjects</h4>
    <?php if ($res->num_rows == 0): ?>
      <p>No subjects assigned yet.</p>
    <?php else: ?>
      <table>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Code</th>
          <th>Actions</th>
        </tr>
        <?php while($s = $res->fetch_assoc()): ?>
          <tr>
            <td><?= $s['id']; ?></td>
            <td><?= htmlspecialchars($s['name']); ?></td>
            <td><?= htmlspecialchars($s['code']); ?></td>
            <td>
              <a class="update-btn" href="add_marks.php?subject_id=<?= $s['id']; ?>">Add Marks</a>
              <a class="delete-btn" href="view_marks.php?subject_id=<?= $s['id']; ?>">View Marks</a>
            </td>
          </tr>
        <?php endwhile; ?>
      </table>
    <?php endif; ?>
  </div>

  <p class="small">2025 ResultVerse | Developed by Tanjina Akter</p>
</div>
</body>
</html>
