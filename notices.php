<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle POST create/update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';

    if ($id) {
        $stmt = $conn->prepare("UPDATE notices SET title=?, content=? WHERE id=?");
        $stmt->bind_param("ssi", $title, $content, $id);
        $stmt->execute();
        $stmt->close();
    } else {
        $stmt = $conn->prepare("INSERT INTO notices (title, content) VALUES (?, ?)");
        $stmt->bind_param("ss", $title, $content);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: notices.php");
    exit();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $del_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM notices WHERE id=?");
    $stmt->bind_param("i", $del_id);
    $stmt->execute();
    $stmt->close();
    header("Location: notices.php");
    exit();
}

// Fetch all notices
$result = $conn->query("SELECT * FROM notices ORDER BY posted_on DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Notices - Student Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-5">
    <h2>Notices</h2>
    <hr>

    <!-- Notice Form -->
    <div class="card mb-4 p-3">
        <h5>Add/Edit Notice</h5>
        <form method="POST" id="noticeForm">
            <input type="hidden" name="id" id="notice_id" />
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" id="title" name="title" class="form-control" required />
            </div>
            <div class="mb-3">
                <label for="content" class="form-label">Content</label>
                <textarea id="content" name="content" class="form-control" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Save Notice</button>
            <button type="button" class="btn btn-secondary" onclick="resetForm()">Clear</button>
        </form>
    </div>

    <!-- Notices Table -->
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Content</th>
                <th>Posted On</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= nl2br(htmlspecialchars($row['content'])) ?></td>
                <td><?= $row['posted_on'] ?></td>
                <td>
                    <button class="btn btn-sm btn-warning" onclick='editNotice(<?=json_encode($row)?>)'>Edit</button>
                    <a href="notices.php?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this notice?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="home.php" class="btn btn-secondary">Back to Home</a>
</div>

<script>
function editNotice(notice) {
    document.getElementById('notice_id').value = notice.id;
    document.getElementById('title').value = notice.title;
    document.getElementById('content').value = notice.content;
}
function resetForm() {
    document.getElementById('noticeForm').reset();
    document.getElementById('notice_id').value = '';
}
</script>
</body>
</html>
