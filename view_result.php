<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
  header("Location: login.php?role=student");
  exit;
}

$student = $_SESSION['user'];

$sql = $conn->prepare("
  SELECT s.name AS subject, s.code, m.marks, a.gpa
  FROM marks m
  JOIN subjects s ON m.subject_id = s.id
  LEFT JOIN analytics a ON a.student_id = m.student_id AND a.subject_id = m.subject_id
  WHERE m.student_id=?
");
$sql->bind_param("i", $student['id']);
$sql->execute();
$res = $sql->get_result();

$total_gpa = 0;
$count = 0;
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Your Results</title>

<!-- FULL CSS ADDED HERE -->
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

  .result-container {
    width: 100%;
    max-width: 480px;
    background: #ffffff;
    padding: 30px 25px;
    border-radius: 16px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    margin: 30px auto;
    text-align: center;
  }

  h3 {
    font-size: 26px;
    color: #1f4e79;
    margin-bottom: 18px;
  }

  /* SMALL BACK BUTTON */
  .top-back-btn {
    background: #1f4e79;
    color: #fff;
    padding: 8px 14px;
    font-size: 13px;
    border-radius: 15px;
    text-decoration: none;
    display: inline-block;
    margin-bottom: 15px;
    transition: 0.3s;
  }

  .top-back-btn:hover {
    background: #3c78a4;
    transform: scale(1.05);
  }

  .btn {
    background: #1f4e79;
    color: #fff;
    border: none;
    padding: 10px;
    border-radius: 22px;
    cursor: pointer;
    font-size: 15px;
    font-weight: 600;
    width: 100%;
    margin-top: 12px;
    transition: 0.3s;
    text-decoration: none;
    display: inline-block;
  }

  .btn:hover {
    background: #3c78a4;
    transform: scale(1.03);
  }

  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
    font-size: 14px;
  }

  th, td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: center;
  }

  th {
    background: #1f4e79;
    color: white;
    font-weight: 600;
  }

  tr:nth-child(even) {
    background: #f2f7fb;
  }

  tr:hover {
    background: #e0efff;
    transition: 0.3s;
  }

  .small {
    text-align: center;
    color: #777;
    margin-top: 25px;
    font-size: 0.9em;
  }
</style>

</head>
<body class="dashboard">

<div class="result-container">

  <a class="top-back-btn" href="student_dashboard.php">Back</a>

  <h3>Your Results</h3>

  <a class="btn" href="download_pdf.php">Download PDF</a>

  <table>
    <tr>
      <th>Subject</th>
      <th>Code</th>
      <th>Marks</th>
      <th>GPA</th>
    </tr>

    <?php if ($res->num_rows == 0): ?>
      <tr><td colspan="4">No results available.</td></tr>

    <?php else: ?>
      <?php while($row = $res->fetch_assoc()):
        $total_gpa += $row['gpa'];
        $count++;
      ?>
      <tr>
        <td><?= htmlspecialchars($row['subject']); ?></td>
        <td><?= htmlspecialchars($row['code']); ?></td>
        <td><?= htmlspecialchars($row['marks']); ?></td>
        <td><?= number_format($row['gpa'], 2); ?></td>
      </tr>
      <?php endwhile; ?>

      <tr>
        <th colspan="3" style="text-align:right;">CGPA</th>
        <th><?= $count ? number_format($total_gpa / $count, 2) : "0.00"; ?></th>
      </tr>
    <?php endif; ?>

  </table>

  <p class="small">2025 ResultVerse | Developed by Tanjina Akter</p>
</div>

</body>
</html>
