<?php
session_start();
require 'db.php';
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin'){
    header("Location: login.php?role=admin");
    exit;
}

if(!isset($_GET['id'])){
    die("<h3 style='text-align:center;color:red;margin-top:100px;'>No user selected!</h3>");
}

$id = (int)$_GET['id'];
$err = $msg = "";

$getUser = $conn->prepare("SELECT * FROM users WHERE id=? AND role!='admin' LIMIT 1");
$getUser->bind_param("i", $id);
$getUser->execute();
$res = $getUser->get_result();

if($res->num_rows == 0){
    die("<h3 style='text-align:center;color:red;margin-top:100px;'>User not found.</h3>");
}
$user = $res->fetch_assoc();

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $name = $_POST['name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = $_POST['password'];

    $check = $conn->prepare("SELECT id FROM users WHERE (username=? OR email=?) AND id!=?");
    $check->bind_param("ssi", $username, $email, $id);
    $check->execute();

    if($check->get_result()->num_rows > 0){
        $err = "Username or email already used by someone else.";
    } else {
        $update = $conn->prepare("UPDATE users SET name=?, username=?, email=?, password=?, role=? WHERE id=?");
        $update->bind_param("sssssi", $name, $username, $email, $password, $role, $id);
        if($update->execute()){
            $msg = "User updated successfully!";
            $getUser = $conn->prepare("SELECT * FROM users WHERE id=?");
            $getUser->bind_param("i", $id);
            $getUser->execute();
            $user = $getUser->get_result()->fetch_assoc();
        } else {
            $err = "Something went wrong while updating.";
        }
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Update User</title>
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
input, select {
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
  <h3>Update User Info</h3>

  <?php if($err) echo "<p style='color:red;'>$err</p>"; ?>
  <?php if($msg) echo "<p style='color:green;'>$msg</p>"; ?>

  <form method="post">
    <label>Name</label>
    <input type="text" name="name" value="<?= htmlspecialchars($user['name']); ?>" required>

    <label>Username</label>
    <input type="text" name="username" value="<?= htmlspecialchars($user['username']); ?>" required>

    <label>Email</label>
    <input type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>

    <label>Role</label>
    <select name="role" required>
      <option value="student" <?= $user['role']=='student'?'selected':''; ?>>Student</option>
      <option value="teacher" <?= $user['role']=='teacher'?'selected':''; ?>>Teacher</option>
    </select>

    <label>Password</label>
    <input type="text" name="password" value="<?= htmlspecialchars($user['password']); ?>" required>

    <button class="btn" type="submit">Save Changes</button>
  </form>

  <p><a href="admin_dashboard.php">← Back to Dashboard</a></p>
</div>
</body>
</html>
