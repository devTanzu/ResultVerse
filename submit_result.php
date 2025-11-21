<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
    header("Location: login.php");
    exit;
}

$teacher = $_SESSION['user'];

if (!isset($_GET['subject_id'])) {
    die("Invalid subject.");
}

$subject_id = (int)$_GET['subject_id'];

if (isset($_GET['id'])) {  
    $id = (int)$_GET['id'];

    $stmt = $conn->prepare("
        UPDATE marks
        SET status='submitted'
        WHERE id=? AND subject_id=?
    ");
    $stmt->bind_param("ii", $id, $subject_id);
    $stmt->execute();

    echo "<script>
        alert('This result has been submitted for admin approval.');
        window.location='view_marks.php?subject_id=$subject_id';
    </script>";
    exit;
}

$stmt = $conn->prepare("
    UPDATE marks
    SET status='submitted'
    WHERE subject_id=?
");
$stmt->bind_param("i", $subject_id);
$stmt->execute();

echo "<script>
    alert('All results for this subject have been submitted for admin approval.');
    window.location='view_marks.php?subject_id=$subject_id';
</script>";
?>
