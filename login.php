<?php
session_start();
require 'db.php';

$role = isset($_GET['role']) ? $_GET['role'] : '';
$err = '';

$admin_email = "tanz.akter@gmail.com";
$admin_username = "tanjina";
$admin_pass = "Admin@123";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usern = $_POST['username'];
    $pass = $_POST['password'];
    $role_post = $_POST['role'];

    if ($role_post === 'admin') {
        if ($usern === $admin_username && $pass === $admin_pass) {
            $check = $conn->prepare("SELECT id FROM users WHERE username=? AND role='admin'");
            $check->bind_param("s", $admin_username);
            $check->execute();
            $result = $check->get_result();

            if ($result->num_rows == 0) {
                $ins = $conn->prepare("INSERT INTO users (name, email, username, password, role) VALUES ('Admin User', ?, ?, ?, 'admin')");
                $ins->bind_param("sss", $admin_email, $admin_username, $admin_pass);
                $ins->execute();
            }

            $_SESSION['user'] = [
                'id' => 1,
                'name' => 'Admin User',
                'username' => $admin_username,
                'role' => 'admin',
                'email' => $admin_email
            ];
            header("Location: admin_dashboard.php");
            exit;
        } else {
            $err = "Wrong admin username or password!";
        }
    } else {
        $stmt = $conn->prepare("SELECT id,name,username,password,role,email FROM users WHERE username=? AND role=? LIMIT 1");
        $stmt->bind_param("ss", $usern, $role_post);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 1) {
            $u = $res->fetch_assoc();
            if ($pass === $u['password']) {
                $_SESSION['user'] = [
                    'id'=>$u['id'],
                    'name'=>$u['name'],
                    'username'=>$u['username'],
                    'role'=>$u['role'],
                    'email'=>$u['email']
                ];
                header("Location: {$u['role']}_dashboard.php");
                exit;
            } else {
                $err = "Wrong password.";
            }
        } else {
            $err = "User not found.";
        }
    }
}
?>
<!doctype html>
<html>
<head>
  <title>Login</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
  <h3>Welcome Back</h3>
  <h4>Sign in to your ResultVerse account</h4>
  <?php if($err) echo "<p style='color:red;'>$err</p>"; ?>
  <form method="post">
    <input type="hidden" name="role" value="<?php echo htmlspecialchars($role ?: 'student'); ?>">
    <label>Username</label>
    <input name="username" required>
    <label>Password</label>
    <input type="password" name="password" required>
    <button class="btn" type="submit">Sign In</button>
  </form>
  <p><a href="index.php">Back</a></p>
</div>
</body>
</html>
