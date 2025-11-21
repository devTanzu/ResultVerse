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

  <style>

    body.dashboard {
      background: #eef3f8;
      font-family: 'Segoe UI', Arial, sans-serif;
      font-size: 14px;

      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;

      margin: 0;
      padding: 0;
    }

    .container {
      max-width: 900px;
      width: 95%;
      background: #fff;
      padding: 18px 20px;
      border-radius: 12px;
      box-shadow: 0 6px 15px rgba(0, 0, 0, 0.05);
    }

    h3 {
      text-align: center;
      font-size: 22px;
      color: #1f4e79;
      margin-bottom: 8px;
      font-weight: 700;
    }

    nav {
      display: flex;
      justify-content: flex-end;
      margin-bottom: 10px;
    }

    .logout-btn {
      background: #d9534f;
      padding: 7px 16px;
      border-radius: 20px;
      font-size: 13px;
      font-weight: 600;
      color: #fff;
      text-decoration: none;
    }

    .logout-btn:hover {
      background: #b52b27;
    }

    .section-box {
      background: #fff;
      border: 1px solid #d7e3f0;
      border-radius: 10px;
      padding: 16px;
      margin: 15px 0;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      transition: 0.3s;
      overflow-x: auto;
    }

    .section-box:hover {
      box-shadow: 0 7px 18px rgba(0, 0, 0, 0.09);
    }

    .section-box h4 {
      margin: 0 0 10px;
      color: #1f4e79;
      font-size: 17px;
      font-weight: 600;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
      font-size: 13px;
      border-radius: 10px;
      overflow: hidden;
    }

    th {
      background: linear-gradient(to right, #1f4e79, #2f6aa6);
      color: white;
      padding: 8px;
      font-size: 13px;
      text-align: left;
    }

    td {
      background: #fff;
      padding: 8px;
      font-size: 13px;
      border-bottom: 1px solid #e5eef5;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    tr:nth-child(even) td {
      background: #f5f9ff;
    }

    tr:hover td {
      background: #e7f1ff;
    }

    .update-btn,
    .delete-btn,
    .btn {
      display: inline-block;
      padding: 7px 14px;
      border-radius: 6px;
      font-size: 12px;
      font-weight: 600;
      text-decoration: none;
      color: white;
      margin-right: 5px;
    }

    .update-btn {
      background: #4caf50;
    }

    .update-btn:hover {
      background: #3d8e41;
    }

    .delete-btn {
      background: #d9534f;
    }

    .delete-btn:hover {
      background: #b52b27;
    }

    .btn {
      background: #1f4e79;
    }

    .btn:hover {
      background: #3c78a4;
    }

    .small {
      text-align: center;
      margin-top: 18px;
      font-size: 12px;
      color: #777;
    }
  </style>

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
              <a class="btn" href="preview_result.php?subject_id=<?= $s['id']; ?>">Preview</a>
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
