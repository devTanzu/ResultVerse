<?php
session_start();
require 'db.php';

// Load Composer autoloader
require_once 'vendor/autoload.php';

use Dompdf\Dompdf;

// Check student
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
    die("Unauthorized access");
}

$student = $_SESSION['user'];

$sql = $conn->prepare("
  SELECT s.name AS subject, s.code, m.marks, a.gpa
  FROM marks m
  JOIN subjects s ON m.subject_id = s.id
  LEFT JOIN analytics a ON a.student_id = m.student_id AND a.subject_id = m.subject_id
  WHERE m.student_id=?
");
$sql->bind_param("i", $student['id']);
$sql->execute();
$res = $sql->get_result();

// Build HTML for PDF
$html = "
<h2 style='text-align:center;'>Result Sheet</h2>
<p><strong>Name:</strong> {$student['name']}</p>
<p><strong>Username:</strong> {$student['username']}</p>
<br>

<table border='1' cellspacing='0' cellpadding='8' width='100%'>
<tr>
  <th>Subject</th>
  <th>Code</th>
  <th>Marks</th>
  <th>GPA</th>
</tr>
";

$total_gpa = 0;
$count = 0;

while ($row = $res->fetch_assoc()) {
    $html .= "
    <tr>
      <td>{$row['subject']}</td>
      <td>{$row['code']}</td>
      <td>{$row['marks']}</td>
      <td>{$row['gpa']}</td>
    </tr>
    ";

    $total_gpa += $row['gpa'];
    $count++;
}

$cgpa = $count ? number_format($total_gpa / $count, 2) : "0.00";

$html .= "
<tr>
  <th colspan='3' style='text-align:right;'>CGPA</th>
  <th>$cgpa</th>
</tr>
</table>
";

// Generate PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("result.pdf", ["Attachment" => true]);
exit;
