<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) {

  $_SESSION['user'] = [
    'id' => 1,
    'name' => 'Admin',
    'username' => 'admin',
    'email' => 'admin@example.com',
    'role' => 'admin'
  ];
}


if (isset($_GET['delete'])) {
  $id = (int)$_GET['delete'];
  $conn->query("DELETE FROM users WHERE id=$id AND role!='admin'");
}


$users = $conn->query("SELECT id,name,username,email,password,role FROM users WHERE role IN ('teacher','student') ORDER BY role DESC, name ASC");


$subjects = $conn->query("
    SELECT s.id, s.name, s.code, s.teacher_id, u.name as teacher_name 
    FROM subjects s 
    LEFT JOIN users u ON s.teacher_id = u.id
");

$pending = $conn->query("
    SELECT m.id, m.marks,
        COALESCE(s.name,'Unknown Subject') AS subject,
        COALESCE(u.name,'Unknown Student') AS student
    FROM marks m
    LEFT JOIN subjects s ON m.subject_id = s.id
    LEFT JOIN users u ON m.student_id = u.id
    ORDER BY s.name, u.name
");
?>
<!doctype html>
<html>

<head>
  <meta charset="utf-8">
  <title>Admin Dashboard</title>
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


.dashboard .container {
  max-width: 900px;
  width: 95%;
  background: #fff;
  padding: 18px 20px;
  border-radius: 12px;
  box-shadow: 0 6px 15px rgba(0,0,0,0.05);
}

.dashboard h3 {
  text-align: center;
  font-size: 22px;
  color: #1f4e79;
  margin-bottom: 8px;
  font-weight: 700;
}

.section-box {
  background: #fff;
  border: 1px solid #d7e3f0;
  border-radius: 10px;
  padding: 16px;
  margin: 15px 0;
  box-shadow: 0 4px 12px rgba(0,0,0,0.05);
  transition: 0.3s;
  overflow-x: auto;
}

.section-box h4 {
  margin: 0 0 10px;
  color: #1f4e79;
  font-size: 17px;
  font-weight: 600;
}

.section-box:hover {
  box-shadow: 0 7px 18px rgba(0,0,0,0.09);
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 10px;
  border-radius: 10px;
  overflow: hidden;
  font-size: 13px;
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

th:last-child, td:last-child {
  width: 110px;
  white-space: nowrap;
}

.btn {
  display: inline-block;
  padding: 7px 16px;
  background: #1f4e79;
  color: white;
  border-radius: 20px;
  font-size: 13px;
  font-weight: 600;
  margin-bottom: 8px;
  text-decoration: none;
  transition: 0.3s;
}

.btn:hover {
  background: #3c78a4;
  transform: translateY(-2px);
}

.logout-btn {
  background: #d9534f;
  padding: 7px 16px;
  border-radius: 20px;
  font-size: 13px;
  font-weight: 600;
  color: #fff;
}

.logout-btn:hover {
  background: #b52b27;
}

.action-buttons {
  display: flex;
  gap: 5px;
  flex-wrap: nowrap;
}

.update-btn, .delete-btn {
  padding: 5px 8px;
  border-radius: 5px;
  font-size: 12px;
  font-weight: 600;
}

.update-btn {
  background: #4caf50;
  color: white;
}

.update-btn:hover {
  background: #3e8e41;
}

.delete-btn {
  background: #d9534f;
  color: white;
}

.delete-btn:hover {
  background: #b52b27;
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
    <h3>Admin Dashboard</h3>
    <nav>
      <a class="logout-btn" href="logout.php">Logout</a>
    </nav>

    <div class="section-box">
      <h4>All Users (Teachers & Students)</h4>
      <a class="btn" href="add_user.php">Add New User</a>
      <table>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Username</th>
          <th>Email</th>
          <th>Password</th>
          <th>Role</th>
          <th>Action</th>
        </tr>
        <?php while ($u = $users->fetch_assoc()): ?>
          <tr>
            <td><?= $u['id']; ?></td>
            <td><?= htmlspecialchars($u['name']); ?></td>
            <td><?= htmlspecialchars($u['username']); ?></td>
            <td><?= htmlspecialchars($u['email']); ?></td>
            <td><?= str_repeat('*', strlen($u['password'])); ?></td>
            <td><?= ucfirst($u['role']); ?></td>
            <td>
              <div class="action-buttons">
                <a class="update-btn" href="update_user.php?id=<?= $u['id']; ?>">Update</a>
                <a class="delete-btn" href="?delete=<?= $u['id']; ?>" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
              </div>
            </td>
          </tr>
        <?php endwhile; ?>
      </table>
    </div>

    <div class="section-box">
      <h4>Subjects</h4>
      <a class="btn" href="add_subject.php">Add Subject</a>
      <a class="btn" href="assign_subject.php">Assign to Teacher</a>
      <table>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Code</th>
          <th>Assigned Teacher</th>
        </tr>
        <?php while ($s = $subjects->fetch_assoc()): ?>
          <tr>
            <td><?= $s['id']; ?></td>
            <td><?= htmlspecialchars($s['name']); ?></td>
            <td><?= htmlspecialchars($s['code']); ?></td>
            <td><?= $s['teacher_name'] ?: 'Not assigned'; ?></td>
          </tr>
        <?php endwhile; ?>
      </table>
    </div>

<div class="section-box">
  <h4> Pending Results (Need Approval)</h4>

  <?php
  $pending = $conn->query("
      SELECT m.id, m.marks, m.status,
          COALESCE(s.name,'Unknown Subject') AS subject,
          COALESCE(u.name,'Unknown Student') AS student
      FROM marks m
      LEFT JOIN subjects s ON m.subject_id = s.id
      LEFT JOIN users u ON m.student_id = u.id
      WHERE m.status='submitted'
      ORDER BY s.name, u.name
  ");
  ?>

  <?php if ($pending->num_rows == 0): ?>
    <p style="text-align:center; color:#777;">No new submissions.</p>
  <?php else: ?>
    <table>
      <tr>
        <th>Subject</th>
        <th>Student</th>
        <th>Marks</th>
        <th>Status</th>
        <th>Action</th>
      </tr>

      <?php while ($row = $pending->fetch_assoc()): ?>
      <tr>
        <td><?= $row['subject']; ?></td>
        <td><?= $row['student']; ?></td>
        <td><?= $row['marks']; ?></td>
        <td><span style="color:#ff9800; font-weight:bold;">Submitted</span></td>
        <td>
          <div class="action-buttons">
            <a class="update-btn"
               href="admin_approve.php?id=<?= $row['id']; ?>&status=approved">
               Approve
            </a>

            <a class="delete-btn"
               href="admin_approve.php?id=<?= $row['id']; ?>&status=rejected">
               Reject
            </a>
          </div>
        </td>
      </tr>
      <?php endwhile; ?>
    </table>
  <?php endif; ?>
</div>


<div class="section-box">
  <h4> Approved Results</h4>

  <?php
  $approved = $conn->query("
      SELECT m.id, m.marks, m.status,
          COALESCE(s.name,'Unknown Subject') AS subject,
          COALESCE(u.name,'Unknown Student') AS student
      FROM marks m
      LEFT JOIN subjects s ON m.subject_id = s.id
      LEFT JOIN users u ON m.student_id = u.id
      WHERE m.status='approved'
      ORDER BY s.name, u.name
  ");
  ?>

  <?php if ($approved->num_rows == 0): ?>
    <p style="text-align:center; color:#777;">No approved results.</p>
  <?php else: ?>
    <table>
      <tr>
        <th>Subject</th>
        <th>Student</th>
        <th>Marks</th>
        <th>Status</th>
      </tr>

      <?php while ($row = $approved->fetch_assoc()): ?>
      <tr>
        <td><?= $row['subject']; ?></td>
        <td><?= $row['student']; ?></td>
        <td><?= $row['marks']; ?></td>
        <td><span style="color:#2ea44f; font-weight:bold;">Approved</span></td>
      </tr>
      <?php endwhile; ?>
    </table>
  <?php endif; ?>
</div>

<div class="section-box">
  <h4> Rejected Results</h4>

  <?php
  $rejected = $conn->query("
      SELECT m.id, m.marks, m.status,
          COALESCE(s.name,'Unknown Subject') AS subject,
          COALESCE(u.name,'Unknown Student') AS student
      FROM marks m
      LEFT JOIN subjects s ON m.subject_id = s.id
      LEFT JOIN users u ON m.student_id = u.id
      WHERE m.status='rejected'
      ORDER BY s.name, u.name
  ");
  ?>

  <?php if ($rejected->num_rows == 0): ?>
    <p style="text-align:center; color:#777;">No rejected results.</p>
  <?php else: ?>
    <table>
      <tr>
        <th>Subject</th>
        <th>Student</th>
        <th>Marks</th>
        <th>Status</th>
      </tr>

      <?php while ($row = $rejected->fetch_assoc()): ?>
      <tr>
        <td><?= $row['subject']; ?></td>
        <td><?= $row['student']; ?></td>
        <td><?= $row['marks']; ?></td>
        <td><span style="color:#d9534f; font-weight:bold;">Rejected</span></td>
      </tr>
      <?php endwhile; ?>
    </table>
  <?php endif; ?>
</div>

    <div class="section-box">
      <h4>Results & Analytics</h4>
      <a class="btn" href="calculate_gpa.php">Recalculate GPA</a>
      <a class="btn" href="analytics.php">Analytics</a>
    </div>

    <p class="small">2025 ResultVerse | Developed by Tanjina Akter</p>
  </div>
</body>
</html>