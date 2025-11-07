<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  header("Location: login.php?role=admin");
  exit;
}

$err = $msg = "";
$subjects = $conn->query("SELECT * FROM subjects ORDER BY name ASC");
$teachers = $conn->query("SELECT * FROM users WHERE role='teacher' ORDER BY name ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $subject_id = $_POST['subject_id'];
  $teacher_id = $_POST['teacher_id'];

  if ($subject_id && $teacher_id) {
    $update = $conn->prepare("UPDATE subjects SET teacher_id=? WHERE id=?");
    $update->bind_param("ii", $teacher_id, $subject_id);
    if ($update->execute()) {
      $msg = "Teacher assigned successfully!";
    } else {
      $err = "Failed to assign teacher.";
    }
  } else {
    $err = "Please select both subject and teacher.";
  }
}
?>
<!doctype html>
<html>
<head>
<title>Assign Subject</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
  <h3>Assign Subject to Teacher</h3>

  <?php if($err) echo "<p style='color:red;'>$err</p>"; ?>
  <?php if($msg) echo "<p style='color:green;'>$msg</p>"; ?>

  <form method="post">
    <label>Select Subject</label>
    <select name="subject_id" required>
      <option value="">Choose Subject</option>
      <?php while($s = $subjects->fetch_assoc()): ?>
        <option value="<?= $s['id']; ?>"><?= htmlspecialchars($s['name']); ?> (<?= htmlspecialchars($s['code']); ?>)</option>
      <?php endwhile; ?>
    </select>

    <label>Select Teacher</label>
    <select name="teacher_id" required>
      <option value="">Choose Teacher</option>
      <?php while($t = $teachers->fetch_assoc()): ?>
        <option value="<?= $t['id']; ?>"><?= htmlspecialchars($t['name']); ?></option>
      <?php endwhile; ?>
    </select>

    <button class="btn" type="submit">Assign</button>
  </form>

  <p><a href="admin_dashboard.php"> Back to Dashboard</a></p>
</div>
</body>
</html>
