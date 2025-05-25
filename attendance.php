<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch all courses
$courses_result = $conn->query("SELECT id, course_code, course_name FROM courses ORDER BY course_code");

// Fetch all students (users)
$students_result = $conn->query("SELECT id, name FROM users ORDER BY name");

// Handle POST (Add attendance)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'] ?? '';
    $course_id = $_POST['course_id'] ?? '';
    $attendance_date = $_POST['attendance_date'] ?? '';
    $status = $_POST['status'] ?? 'present';

    $stmt = $conn->prepare("INSERT INTO attendance (student_id, course_id, attendance_date, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $student_id, $course_id, $attendance_date, $status);
    $stmt->execute();
    $stmt->close();
    header("Location: attendance.php");
    exit();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $del_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM attendance WHERE id=?");
    $stmt->bind_param("i", $del_id);
    $stmt->execute();
    $stmt->close();
    header("Location: attendance.php");
    exit();
}

// Fetch all attendance records with student and course names
$sql = "SELECT a.*, u.name AS student_name, c.course_code, c.course_name 
        FROM attendance a 
        JOIN users u ON a.student_id = u.id 
        JOIN courses c ON a.course_id = c.id 
        ORDER BY a.attendance_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Attendance - Student Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-5">
    <h2>Attendance</h2>
    <hr>

    <!-- Add attendance form -->
    <div class="card mb-4 p-3">
        <h5>Mark Attendance</h5>
        <form method="POST">
            <div class="mb-3">
                <label for="student_id" class="form-label">Student</label>
                <select name="student_id" id="student_id" class="form-select" required>
                    <option value="">Select Student</option>
                    <?php while($student = $students_result->fetch_assoc()): ?>
                        <option value="<?= $student['id'] ?>"><?= htmlspecialchars($student['name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="course_id" class="form-label">Course</label>
                <select name="course_id" id="course_id" class="form-select" required>
                    <option value="">Select Course</option>
                    <?php while($course = $courses_result->fetch_assoc()): ?>
                        <option value="<?= $course['id'] ?>"><?= htmlspecialchars($course['course_code'] . " - " . $course['course_name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="attendance_date" class="form-label">Date</label>
                <input type="date" id="attendance_date" name="attendance_date" class="form-control" required />
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select" required>
                    <option value="present">Present</option>
                    <option value="absent">Absent</option>
                    <option value="late">Late</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add Attendance</button>
        </form>
    </div>

    <!-- Attendance Table -->
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Student</th>
                <th>Course</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['student_name']) ?></td>
                <td><?= htmlspecialchars($row['course_code'] . " - " . $row['course_name']) ?></td>
                <td><?= $row['attendance_date'] ?></td>
                <td><?= ucfirst($row['status']) ?></td>
                <td>
                    <a href="attendance.php?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this attendance record?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="home.php" class="btn btn-secondary">Back to Home</a>
</div>
</body>
</html>
