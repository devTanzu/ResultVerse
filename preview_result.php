<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
    header("Location: login.php");
    exit;
}

$subject_id = (int)$_GET['subject_id'];

$res = $conn->query("
    SELECT m.id, m.marks, u.name AS student, m.status
    FROM marks m
    JOIN users u ON m.student_id=u.id
    WHERE m.subject_id=$subject_id
");
?>
<!doctype html>
<html>
<head>
<title>Preview Results</title>
<style>

body.dashboard {
  background: #eef3f8;
  padding: 30px 0;
  font-family: 'Segoe UI', Arial, sans-serif;
}

.container {
  max-width: 900px;
  width: 92%;
  background: #ffffff;
  padding: 22px 28px;
  border-radius: 14px;
  box-shadow: 0 8px 20px rgba(0,0,0,0.07);
  margin: auto;
}

h3 {
  text-align: center;
  font-size: 24px;
  color: #1f4e79;
  margin-bottom: 15px;
  font-weight: 600;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 10px;
  border-radius: 10px;
  overflow: hidden;
  word-break: break-word;
}

th {
  background: linear-gradient(to right, #1f4e79, #2f6aa6);
  color: white;
  padding: 10px;
  font-size: 14px;
  text-align: left;
}

td {
  background: #ffffff;
  padding: 9px;
  font-size: 14px;
  border-bottom: 1px solid #e5eef5;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

tr:nth-child(even) td { background: #f5f9ff; }
tr:hover td { background: #e7f1ff; }

.btn {
  background: #1f4e79;
  color: #fff;
  border: none;
  padding: 8px 14px;
  border-radius: 20px;
  cursor: pointer;
  font-size: 13px;
  font-weight: 600;
  text-decoration: none;
  transition: 0.3s;
}

.btn:hover {
  background: #3c78a4;
  transform: scale(1.03);
}

.btn-disabled {
  background: #cccccc !important;
  cursor: not-allowed;
  transform: none !important;
}

/* Submit All Button */
.submit-all {
  display: inline-block;
  margin-top: 18px;
  padding: 10px 20px;
  font-size: 14px;
  border-radius: 25px;
}

.status {
  padding: 6px 12px;
  border-radius: 16px;
  font-size: 12px;
  font-weight: 600;
  color: white;
}

.status-submitted { background: #3c78a4; }
.status-approved { background: #2ea44f; }
.status-rejected { background: #d9534f; }

</style>
</head>

<body class="dashboard">
<div class="container">
<h3>Preview Results</h3>
<a class="btn back-btn" href="teacher_dashboard.php">← Back</a>


<table>
  <tr>
    <th>Student</th>
    <th>Marks</th>
    <th>Action</th>
  </tr>

<?php while($row = $res->fetch_assoc()): ?>
<tr>
  <td><?= htmlspecialchars($row['student']); ?></td>
  <td><?= htmlspecialchars($row['marks']); ?></td>
  <td>

    <?php if ($row['status'] == 'submitted'): ?>
        <span class="status status-submitted">Submitted</span>

    <?php elseif ($row['status'] == 'approved'): ?>
        <span class="status status-approved">Approved</span>

    <?php elseif ($row['status'] == 'rejected'): ?>
        <span class="status status-rejected">Rejected</span>

    <?php else: ?>
        <a class="btn" 
           href="submit_result.php?subject_id=<?= $subject_id ?>&id=<?= $row['id'] ?>">
           Submit
        </a>
    <?php endif; ?>

  </td>
</tr>
<?php endwhile; ?>
</table>

<a class="btn submit-all" href="submit_result.php?subject_id=<?= $subject_id ?>">
  Submit All for Approval
</a>

</div>
</body>
</html>
