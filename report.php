<?php
include 'db.php';

$course_id = isset($_GET['course_id']) ? $_GET['course_id'] : '';
$attendance_date = isset($_GET['attendance_date']) ? $_GET['attendance_date'] : date('Y-m-d');

$courses = $conn->query("SELECT * FROM courses");

$report_query = "SELECT s.reg_number, s.full_name, a.status 
                 FROM attendance a
                 JOIN students s ON a.student_id = s.student_id
                 WHERE a.course_id = '$course_id' AND a.attendance_date = '$attendance_date'";

$result = $conn->query($report_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background-color: #f4f4f9; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: #fff; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #17a2b8; color: white; }
        .btn { background-color: #0056b3; color: white; padding: 8px 12px; border: none; cursor: pointer; border-radius: 4px; }
        nav { margin-bottom: 20px; background: #333; padding: 10px; border-radius: 4px; }
        nav a { color: white; margin-right: 15px; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

<nav>
    <a href="index.php">Record Attendance</a> | 
    <a href="report.php">Attendance Report</a> | 
    <a href="absentees.php">Identify Absentees</a>
</nav>

<h2>General Attendance Report</h2>

<form action="report.php" method="GET">
    <label>Select Course:</label>
    <select name="course_id" required>
        <option value="">-- Select Course --</option>
        <?php while($row = $courses->fetch_assoc()): ?>
            <option value="<?php echo $row['course_id']; ?>" <?php echo ($course_id == $row['course_id']) ? 'selected' : ''; ?>>
                <?php echo $row['course_code']; ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label style="margin-left: 20px;">Date:</label>
    <input type="date" name="attendance_date" value="<?php echo $attendance_date; ?>" required>

    <button type="submit" class="btn" style="margin-left: 10px;">Generate Report</button>
</form>

<table>
    <thead>
        <tr>
            <th>Reg Number</th>
            <th>Full Name</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php if($course_id != '' && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['reg_number']; ?></td>
                    <td><?php echo $row['full_name']; ?></td>
                    <td style="font-weight: bold; color: <?php echo ($row['status'] == 'Present') ? 'green' : 'red'; ?>;">
                        <?php echo $row['status']; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php elseif($course_id == ''): ?>
            <tr><td colspan="3">Please select parameters above to generate report.</td></tr>
        <?php else: ?>
            <tr><td colspan="3">No attendance records found for this date.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>