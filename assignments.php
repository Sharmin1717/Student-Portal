<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch courses for dropdown
$courses_result = $conn->query("SELECT id, course_code, course_name FROM courses ORDER BY course_code");

// Handle POST for create/update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $course_id = $_POST['course_id'] ?? '';
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $due_date = $_POST['due_date'] ?? '';

    if ($id) {
        $stmt = $conn->prepare("UPDATE assignments SET course_id=?, title=?, description=?, due_date=? WHERE id=?");
        $stmt->bind_param("isssi", $course_id, $title, $description, $due_date, $id);
        $stmt->execute();
        $stmt->close();
    } else {
        $stmt = $conn->prepare("INSERT INTO assignments (course_id, title, description, due_date) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $course_id, $title, $description, $due_date);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: assignments.php");
    exit();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $del_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM assignments WHERE id=?");
    $stmt->bind_param("i", $del_id);
    $stmt->execute();
    $stmt->close();
    header("Location: assignments.php");
    exit();
}

// Get all assignments with course info
$sql = "SELECT a.*, c.course_code, c.course_name FROM assignments a JOIN courses c ON a.course_id = c.id ORDER BY a.due_date DESC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Assignments - Student Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-5">
    <h2>Assignments</h2>
    <hr>

    <!-- Add/Edit Form -->
    <div class="card mb-4 p-3">
        <h5>Add / Edit Assignment</h5>
        <form method="POST" id="assignmentForm">
            <input type="hidden" name="id" id="assignment_id" />
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
                <label for="title" class="form-label">Assignment Title</label>
                <input type="text" class="form-control" id="title" name="title" required />
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="2"></textarea>
            </div>
            <div class="mb-3">
                <label for="due_date" class="form-label">Due Date</label>
                <input type="date" class="form-control" id="due_date" name="due_date" />
            </div>
            <button type="submit" class="btn btn-primary">Save Assignment</button>
            <button type="button" class="btn btn-secondary" onclick="resetForm()">Clear</button>
        </form>
    </div>

    <!-- Assignment List Table -->
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Course</th>
                <th>Title</th>
                <th>Description</th>
                <th>Due Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['course_code'] . " - " . $row['course_name']) ?></td>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td><?= $row['due_date'] ?></td>
                <td>
                    <button class="btn btn-sm btn-warning" onclick='editAssignment(<?=json_encode($row)?>)'>Edit</button>
                    <a href="assignments.php?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this assignment?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="home.php" class="btn btn-secondary">Back to Home</a>
</div>

<script>
function editAssignment(assignment) {
    document.getElementById('assignment_id').value = assignment.id;
    document.getElementById('course_id').value = assignment.course_id;
    document.getElementById('title').value = assignment.title;
    document.getElementById('description').value = assignment.description;
    document.getElementById('due_date').value = assignment.due_date;
}
function resetForm() {
    document.getElementById('assignmentForm').reset();
    document.getElementById('assignment_id').value = '';
}
</script>
</body>
</html>
