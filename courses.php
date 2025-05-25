<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle POST for create/update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_code = $_POST['course_code'] ?? '';
    $course_name = $_POST['course_name'] ?? '';
    $description = $_POST['description'] ?? '';
    $semester = $_POST['semester'] ?? '';
    $id = $_POST['id'] ?? null;

    if ($id) {
        // Update existing course
        $stmt = $conn->prepare("UPDATE courses SET course_code=?, course_name=?, description=?, semester=? WHERE id=?");
        $stmt->bind_param("ssssi", $course_code, $course_name, $description, $semester, $id);
        $stmt->execute();
        $stmt->close();
    } else {
        // Insert new course
        $stmt = $conn->prepare("INSERT INTO courses (course_code, course_name, description, semester) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $course_code, $course_name, $description, $semester);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: courses.php");
    exit();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $del_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM courses WHERE id=?");
    $stmt->bind_param("i", $del_id);
    $stmt->execute();
    $stmt->close();
    header("Location: courses.php");
    exit();
}

// Get all courses
$result = $conn->query("SELECT * FROM courses ORDER BY semester, course_code");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Courses - Student Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-5">
    <h2>Courses</h2>
    <hr>

    <!-- Add/Edit Form -->
    <div class="card mb-4 p-3">
        <h5>Add / Edit Course</h5>
        <form method="POST" id="courseForm">
            <input type="hidden" name="id" id="course_id" />
            <div class="mb-3">
                <label for="course_code" class="form-label">Course Code</label>
                <input type="text" class="form-control" id="course_code" name="course_code" required />
            </div>
            <div class="mb-3">
                <label for="course_name" class="form-label">Course Name</label>
                <input type="text" class="form-control" id="course_name" name="course_name" required />
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="2"></textarea>
            </div>
            <div class="mb-3">
                <label for="semester" class="form-label">Semester</label>
                <input type="text" class="form-control" id="semester" name="semester" required />
            </div>
            <button type="submit" class="btn btn-primary">Save Course</button>
            <button type="button" class="btn btn-secondary" onclick="resetForm()">Clear</button>
        </form>
    </div>

    <!-- Course List Table -->
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Code</th>
                <th>Name</th>
                <th>Description</th>
                <th>Semester</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['course_code']) ?></td>
                <td><?= htmlspecialchars($row['course_name']) ?></td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td><?= htmlspecialchars($row['semester']) ?></td>
                <td>
                    <button class="btn btn-sm btn-warning" onclick='editCourse(<?=json_encode($row)?>)'>Edit</button>
                    <a href="courses.php?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this course?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="home.php" class="btn btn-secondary">Back to Home</a>
</div>

<script>
function editCourse(course) {
    document.getElementById('course_id').value = course.id;
    document.getElementById('course_code').value = course.course_code;
    document.getElementById('course_name').value = course.course_name;
    document.getElementById('description').value = course.description;
    document.getElementById('semester').value = course.semester;
}
function resetForm() {
    document.getElementById('courseForm').reset();
    document.getElementById('course_id').value = '';
}
</script>
</body>
</html>
