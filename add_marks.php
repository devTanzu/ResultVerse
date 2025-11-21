<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
  header("Location: login.php?role=teacher");
  exit;
}

$teacher = $_SESSION['user'];
$subject_id = isset($_GET['subject_id']) ? (int)$_GET['subject_id'] : 0;
$err = $msg = "";

$check = $conn->prepare("SELECT name, code FROM subjects WHERE id=? AND teacher_id=?");
$check->bind_param("ii", $subject_id, $teacher['id']);
$check->execute();
$subject = $check->get_result()->fetch_assoc();

if (!$subject) {
  die("<h3 style='text-align:center;color:red;margin-top:100px;'>Invalid subject access.</h3>");
}

$students = $conn->query("SELECT id, name FROM users WHERE role='student' ORDER BY name ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $student_id = $_POST['student_id'];
  $marks = $_POST['marks'];

  if ($student_id && $marks !== '') {
    if ($marks >= 0 && $marks <= 100) {
      $check = $conn->prepare("SELECT id FROM marks WHERE student_id=? AND subject_id=?");
      $check->bind_param("ii", $student_id, $subject_id);
      $check->execute();
      $res = $check->get_result();

      if ($res->num_rows > 0) {
        $upd = $conn->prepare("UPDATE marks SET marks=? WHERE student_id=? AND subject_id=?");
        $upd->bind_param("dii", $marks, $student_id, $subject_id);
        $upd->execute();
        $msg = "Marks updated successfully!";
      } else {
        $ins = $conn->prepare("INSERT INTO marks (student_id, subject_id, marks) VALUES (?,?,?)");
        $ins->bind_param("iid", $student_id, $subject_id, $marks);
        $ins->execute();
        $msg = "Marks added successfully!";
      }
    } else {
      $err = "Marks must be between 0 and 100.";
    }
  } else {
    $err = "Please select a student and enter marks.";
  }
}
?>
<!doctype html>
<html>
<head>
<title>Add Marks</title>

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
  select, input {
    width: 100%;
    padding: 10px;
    margin-top: 8px;
    border-radius: 10px;
    border: 1px solid #ccc;
    outline: none;
    font-size: 15px;
    background: #f8f9fa;
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
  <h3>Add Marks - <?= htmlspecialchars($subject['name']); ?> (<?= htmlspecialchars($subject['code']); ?>)</h3>

  <?php if($err) echo "<p style='color:red;'>$err</p>"; ?>
  <?php if($msg) echo "<p style='color:green;'>$msg</p>"; ?>

  <form method="post">
    <label>Select Student</label>
    <select name="student_id" required>
      <option value="">-- Choose Student --</option>
      <?php while($st = $students->fetch_assoc()): ?>
        <option value="<?= $st['id']; ?>"><?= htmlspecialchars($st['name']); ?></option>
      <?php endwhile; ?>
    </select>

    <label>Marks (0 - 100)</label>
    <input type="number" name="marks" min="0" max="100" required>

    <button class="btn" type="submit">Save Marks</button>
  </form>

  <p><a href="teacher_dashboard.php">Back to Dashboard</a></p>
</div>
</body>
</html>
