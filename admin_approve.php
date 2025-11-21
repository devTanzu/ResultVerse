<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("Unauthorized access.");
}

if (!isset($_GET['status'])) {
    die("Invalid request.");
}

$status = $_GET['status'];

if ($status === 'approved') {
    $conn->query("UPDATE marks SET status='approved' WHERE status='submitted'");
    $msg = "All results approved & published!";
}
else if ($status === 'rejected') {
    $conn->query("UPDATE marks SET status='draft' WHERE status='submitted'");
    $msg = "Pending results rejected!";
}

echo "<script>alert('$msg'); window.location='admin_dashboard.php';</script>";
?>
