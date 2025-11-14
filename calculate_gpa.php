<?php
session_start();
require 'db.php';

$marksData = $conn->query("
  SELECT m.student_id, m.subject_id, m.marks
  FROM marks m
  JOIN subjects s ON m.subject_id = s.id
");

while($row = $marksData->fetch_assoc()) {
    $student_id = $row['student_id'];
    $subject_id = $row['subject_id'];
    $marks = $row['marks'];

    if ($marks >= 80) $gpa = 4.00;
    elseif ($marks >= 75) $gpa = 3.75;
    elseif ($marks >= 70) $gpa = 3.50;
    elseif ($marks >= 65) $gpa = 3.25;
    elseif ($marks >= 60) $gpa = 3.00;
    elseif ($marks >= 55) $gpa = 2.75;
    elseif ($marks >= 50) $gpa = 2.50;
    elseif ($marks >= 45) $gpa = 2.25;
    elseif ($marks >= 40) $gpa = 2.00;
    else $gpa = 0.00;

    $check = $conn->prepare("SELECT id FROM analytics WHERE student_id=? AND subject_id=?");
    $check->bind_param("ii", $student_id, $subject_id);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows > 0) {
        $upd = $conn->prepare("UPDATE analytics SET gpa=? WHERE student_id=? AND subject_id=?");
        $upd->bind_param("dii", $gpa, $student_id, $subject_id);
        $upd->execute();
    } else {
        $ins = $conn->prepare("INSERT INTO analytics (student_id, subject_id, gpa) VALUES (?,?,?)");
        $ins->bind_param("iid", $student_id, $subject_id, $gpa);
        $ins->execute();
    }
}
echo "<h3 style='text-align:center;color:green;margin-top:80px;'>GPA calculation completed successfully!</h3>";
echo "<p style='text-align:center;'><a href='analytics.php'>View Analytics</a></p>";
?>
