<?php
include 'db.php';

$course_id = isset($_GET['course_id']) ? $_GET['course_id'] : '';
$attendance_date = isset($_GET['attendance_date']) ? $_GET['attendance_date'] : date('Y-m-d');

$courses = $conn->query("SELECT * FROM courses");

// Query ya kuvuta wanafunzi waliokosa tu (Status = Absent)
$absentees_query = "SELECT s.reg_number, s.full_name, c.course_code, a.attendance_date, a.status 
                    FROM attendance a
                    JOIN students s ON a.student_id = s.student_id
                    JOIN courses c ON a.course_id = c.course_id
                    WHERE a.status = 'Absent' AND a.course_id = '$course_id' AND a.attendance_date = '$attendance_date'";

$result = $conn->query($absentees_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Absentees List</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background-color: #f4f4f9; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: #fff; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #dc3545; color: white; }
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

<h2>Identify Students Who Missed Sessions (Absentees)</h2>

<form action="absentees.php" method="GET">
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

    <button type="submit" class="btn" style="margin-left: 10px;">Search Absentees</button>
</form>

<table>
    <thead>
        <tr>
            <th>Reg Number</th>
            <th>Full Name</th>
            <th>Course Code</th>
            <th>Date</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php if($course_id != '' && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr style="background-color: #fdf2f2;">
                    <td><?php echo $row['reg_number']; ?></td>
                    <td><?php echo $row['full_name']; ?></td>
                    <td><?php echo $row['course_code']; ?></td>
                    <td><?php echo $row['attendance_date']; ?></td>
                    <td style="color: red; font-weight: bold;"><?php echo $row['status']; ?></td>
                </tr>
            <?php endwhile; ?>
        <?php elseif($course_id == ''): ?>
            <tr><td colspan="5">Please select a course and date to view absentees.</td></tr>
        <?php else: ?>
            <tr><td colspan="5" style="color: green; font-weight: bold;">Great! No students missed this session.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>