<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    echo "<h2 style='text-align:center;margin-top:100px;color:#d00;'>Please login first to access Admin Dashboard.</h2>";
    exit;
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM users WHERE id=$id AND role!='admin'");
}

$users = $conn->query("SELECT id,name,username,email,password,role FROM users WHERE role!='admin' ORDER BY role DESC, name ASC");

$subjects = $conn->query("
    SELECT s.id, s.name, s.code, s.teacher_id, u.name as teacher_name 
    FROM subjects s 
    LEFT JOIN users u ON s.teacher_id = u.id
");
?>
<!doctype html>
<html>
<head>
<title>Admin Dashboard</title>
<link rel="stylesheet" href="style.css">
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
      <?php while($u = $users->fetch_assoc()): ?>
      <tr>
        <td><?= $u['id']; ?></td>
        <td><?= htmlspecialchars($u['name']); ?></td>
        <td><?= htmlspecialchars($u['username']); ?></td>
        <td><?= htmlspecialchars($u['email']); ?></td>

        <td><?= str_repeat('*', strlen($u['password'])); ?></td>

        <td><?= ucfirst($u['role']); ?></td>
        <td>
          <a class="update-btn" href="update_user.php?id=<?= $u['id']; ?>">Update</a>
          <a class="delete-btn" href="?delete=<?= $u['id']; ?>" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
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
      <?php while($s = $subjects->fetch_assoc()): ?>
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
    <h4>Results & Analytics</h4>

    <a class="btn" href="calculate_gpa.php">Recalculate GPA</a>
    <a class="btn" href="analytics.php">Analytics</a>
  </div>
  <p class="small">2025 ResultVerse | Developed by Tanjina Akter</p>
</div>
</body>
</html>
