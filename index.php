<?php
include 'db.php';

$message = "";

if (isset($_POST['save_attendance'])) {
    $course_id = $_POST['course_id'];
    $attendance_date = $_POST['attendance_date'];
    $status_array = $_POST['status']; 

    foreach ($status_array as $student_id => $status) {
        // 'ON DUPLICATE KEY UPDATE' prevents errors if a user submits logs for the same day twice
        $sql = "INSERT INTO attendance (student_id, course_id, attendance_date, status) 
                VALUES ('$student_id', '$course_id', '$attendance_date', '$status')
                ON DUPLICATE KEY UPDATE status='$status'";
        $conn->query($sql);
    }
    $message = "<div style='color: green; font-weight: bold; margin-bottom: 15px;'>Attendance recorded successfully!</div>";
}

$students = $conn->query("SELECT * FROM students");
$courses = $conn->query("SELECT * FROM courses");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Management System</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background-color: #f4f4f9; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: #fff; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #0056b3; color: white; }
        .btn { background-color: #28a745; color: white; padding: 10px 15px; border: none; cursor: pointer; border-radius: 4px; }
        .btn:hover { background-color: #218838; }
        nav { margin-bottom: 20px; background: #333; padding: 10px; border-radius: 4px; }
        nav a { color: white; margin-right: 15px; text-decoration: none; font-weight: bold; }
        nav a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<nav>
    <a href="index.php">Record Attendance</a> | 
    <a href="report.php">Attendance Report</a> | 
    <a href="absentees.php">Identify Absentees</a>
</nav>

<h2>Record Student Attendance</h2>
<?php echo $message; ?>

<form action="index.php" method="POST">
    <label>Select Course:</label>
    <select name="course_id" required>
        <option value="">-- Select Course --</option>
        <?php while($row = $courses->fetch_assoc()): ?>
            <option value="<?php echo $row['course_id']; ?>"><?php echo $row['course_code'] . " - " . $row['course_name']; ?></option>
        <?php endwhile; ?>
    </select>

    <label style="margin-left: 20px;">Date:</label>
    <input type="date" name="attendance_date" value="<?php echo date('Y-m-d'); ?>" required>

    <table>
        <thead>
            <tr>
                <th>Reg Number</th>
                <th>Full Name</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if($students->num_rows > 0): ?>
                <?php while($row = $students->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['reg_number']; ?></td>
                        <td><?php echo $row['full_name']; ?></td>
                        <td>
                            <input type="radio" name="status[<?php echo $row['student_id']; ?>]" value="Present" checked> Present
                            <input type="radio" name="status[<?php echo $row['student_id']; ?>]" value="Absent" style="margin-left: 15px;"> Absent
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="3">No students found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <br>
    <button type="submit" name="save_attendance" class="btn">Submit Attendance</button>
</form>

</body>
</html>