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

  <!-- PAGE-SPECIFIC CSS -->
  <style>
    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      background: #f0f2f5;
      margin: 0;
      padding: 0;
      color: #333;
    }

    body:not(.dashboard) .container {
      width: 100%;
      max-width: 400px;
      background: #ffffff;
      padding: 40px 35px;
      border-radius: 16px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
      text-align: center;
      margin: 60px auto;
    }

    form {
      margin-top: 10px;
      text-align: left;
    }

    label {
      display: block;
      margin-top: 15px;
      font-weight: 600;
      color: #333;
    }

    input {
      width: 100%;
      padding: 12px;
      margin-top: 6px;
      border: 1px solid #ccc;
      border-radius: 8px;
      box-sizing: border-box;
      transition: 0.3s;
      background: #f8f9fa;
      font-size: 15px;
    }

    input:focus {
      border-color: #1f4e79;
      background: #eef5ff;
      outline: none;
    }

    .btn {
      background: #1f4e79;
      color: #fff;
      border: none;
      padding: 12px;
      border-radius: 25px;
      cursor: pointer;
      font-size: 16px;
      font-weight: 600;
      width: 100%;
      margin-top: 20px;
      transition: 0.3s;
      text-decoration: none;
      display: inline-block;
      text-align: center;
    }

    .btn:hover {
      background: #3c78a4;
      transform: scale(1.03);
    }

    p[style*='color:red'] {
      background: #fde7e7;
      border: 1px solid #f5b5b5;
      padding: 10px;
      border-radius: 6px;
      margin-bottom: 10px;
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
