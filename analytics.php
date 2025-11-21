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
<style>

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }
    body.dashboard {
        background-color: #f4f7fa;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        min-height: 100vh;
        padding: 40px 20px;
    }

    .analytics-box {
        background: #fff;
        padding: 30px 40px;
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
        width: 100%;
        max-width: 900px;
    }
    .analytics-box h3 {
        text-align: center;
        margin-bottom: 25px;
        color: #333;
    }


    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
        font-size: 16px;
        color: #333;
    }
    table th, table td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #e0e0e0;
    }
    table th {
        background-color: #f1f5f9;
        font-weight: 600;
    }
    table tr:hover {
        background-color: #f9f9f9;
    }

 
    .btn {
        display: inline-block;
        text-decoration: none;
        padding: 10px 25px;
        background-color: #4a90e2;
        color: #fff;
        border-radius: 8px;
        transition: background 0.3s;
    }
    .btn:hover {
        background-color: #357ab8;
    }

    .small {
        text-align: center;
        font-size: 13px;
        color: #888;
        margin-top: 25px;
    }


    @media(max-width: 600px) {
        .analytics-box {
            padding: 20px;
        }
        table th, table td {
            padding: 10px;
            font-size: 14px;
        }
        .btn {
            padding: 8px 20px;
        }
    }
</style>
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

    <a class="btn" href="student_dashboard.php">Back</a>

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

    <a class="btn" href="admin_dashboard.php">Back</a>
  <?php endif; ?>

  <p class="small">2025 ResultVerse | Developed by Tanjina Akter</p>
</div>
</body>
</html>
