<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT name, email, course, semester, phone, verified FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || !$user['verified']) {
    echo "<div class='alert alert-danger text-center mt-5'>Please verify your email before accessing the portal.</div>";
    echo "<div class='text-center'><a href='logout.php' class='btn btn-secondary mt-3'>Logout</a></div>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Student Portal - Home</title>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

<style>
  body {
    background-color: #f4f6f8;
  }
  .profile-img {
    width: 120px;
    height: 120px;
    object-fit: cover;
    border-radius: 50%;
    border: 4px solid #0d6efd;
  }
  .card-link {
    text-decoration: none;
    color: #0d6efd;
  }
  .card-link:hover {
    text-decoration: underline;
  }
  .welcome-text {
    font-weight: 600;
    color: #212529;
  }
  .feature-card {
    transition: box-shadow 0.3s ease;
    cursor: pointer;
  }
  .feature-card:hover {
    box-shadow: 0 4px 20px rgba(13, 110, 253, 0.2);
  }
  body.dark-mode {
  background-color: #121212;
  color: #ffffff;
}
body.dark-mode .navbar,
body.dark-mode footer {
  background-color: #1f1f1f;
}
body.dark-mode .card {
  background-color: #1e1e1e;
  color: #ffffff;
}
body.dark-mode .feature-card:hover {
  box-shadow: 0 4px 20px rgba(255, 255, 255, 0.2);
}
body.dark-mode .text-muted {
  color: #ccc !important;
}

</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
  <div class="container">
    <a class="navbar-brand" href="#">Student Portal</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav align-items-center">
        <li class="nav-item">
          <a class="nav-link" href="profile.php">My Profile</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="courses.php">Courses</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="assignments.php">Assignments</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="attendance.php">Attendance</a>
        </li>
        <li class="nav-item">
          <a class="nav-link btn btn-danger text-white ms-3 px-3" href="logout.php">Logout</a>
        </li>
        <li class="nav-item">
  <button id="theme-toggle" class="btn btn-sm btn-light ms-3" title="Toggle Dark Mode">
    <svg id="theme-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-moon" viewBox="0 0 16 16">
      <path d="M6 0a7 7 0 107 7 7.001 7.001 0 00-7-7zM5.74 1.007A6 6 0 0115 8a6 6 0 01-8.01 5.657A7 7 0 005.74 1.007z"/>
    </svg>
  </button>
</li>


        
      </ul>
    </div>
  </div>
</nav>

<!-- Main Content -->
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-10 text-center">
      <img src="https://dps.mksu.ac.ke/wp-content/uploads/2019/04/portal.jpg" alt="Portal Logo" class="profile-img mb-3" />
      <h2 class="welcome-text mb-4">Welcome back, <?php echo htmlspecialchars($user['name']); ?>!</h2>
      <p class="mb-4 lead text-muted">
        <strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?><br />
        <strong>Course:</strong> <?php echo htmlspecialchars($user['course'] ?? 'Not Provided'); ?><br />
        <strong>Semester:</strong> <?php echo htmlspecialchars($user['semester'] ?? 'Not Provided'); ?><br />
        <strong>Phone:</strong> <?php echo htmlspecialchars($user['phone'] ?? 'Not Provided'); ?>
      </p>

      <div class="row g-4 mt-4">

        <div class="col-12 col-sm-6 col-lg-3">
          <a href="courses.php" class="card feature-card p-3 h-100 text-decoration-none text-center bg-white rounded shadow-sm">
            <div class="mb-2">
              <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="#0d6efd" class="bi bi-journal-bookmark" viewBox="0 0 16 16">
                <path d="M6 8V1h1v7l-1 1z"/>
                <path d="M5 2.5v11h7V2.5H5zM4 1h9a1 1 0 011 1v12a1 1 0 01-1 1H4a2 2 0 01-2-2V2a1 1 0 011-1z"/>
              </svg>
            </div>
            <h5>My Courses</h5>
            <p class="text-muted">View and manage your enrolled courses</p>
          </a>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
          <a href="assignments.php" class="card feature-card p-3 h-100 text-decoration-none text-center bg-white rounded shadow-sm">
            <div class="mb-2">
              <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="#0d6efd" class="bi bi-file-earmark-text" viewBox="0 0 16 16">
                <path d="M14 4.5V14a2 2 0 01-2 2H4a2 2 0 01-2-2V2a2 2 0 012-2h6.5L14 4.5zM10.5 1v3a1 1 0 001 1h3"/>
                <path d="M6 7h4v1H6V7zM6 9h4v1H6V9zM6 11h4v1H6v-1z"/>
              </svg>
            </div>
            <h5>Assignments</h5>
            <p class="text-muted">Check your assignments and deadlines</p>
          </a>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
          <a href="attendance.php" class="card feature-card p-3 h-100 text-decoration-none text-center bg-white rounded shadow-sm">
            <div class="mb-2">
              <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="#0d6efd" class="bi bi-check2-square" viewBox="0 0 16 16">
                <path d="M14 1H2a1 1 0 00-1 1v12a1 1 0 001 1h12a1 1 0 001-1V2a1 1 0 00-1-1zM6.854 9.854l-2.5-2.5L4.707 6.5 6.854 8.646l4.146-4.147 1.146 1.147-5.292 5.292z"/>
              </svg>
            </div>
            <h5>Attendance</h5>
            <p class="text-muted">View your attendance records</p>
          </a>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
          <a href="notices.php" class="card feature-card p-3 h-100 text-decoration-none text-center bg-white rounded shadow-sm">
            <div class="mb-2">
              <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="#0d6efd" class="bi bi-bell" viewBox="0 0 16 16">
                <path d="M8 16a2 2 0 001.985-1.75H6.015A2 2 0 008 16zm.104-13.183a1 1 0 00-.708.292 1 1 0 00-.291.708v3.35l-2.6 2.6A1 1 0 005.996 11h4.01a1 1 0 00.707-1.707l-2.6-2.6v-3.35a1 1 0 00-.709-1z"/>
              </svg>
            </div>
            <h5>Notices</h5>
            <p class="text-muted">Check latest announcements and updates</p>
          </a>
        </div>

      </div>
    </div>
  </div>
</div>

<!-- Footer -->
<footer class="bg-light text-center py-3 mt-auto shadow-sm">
  <small class="text-muted">&copy; <?php echo date("Y"); ?> Student Portal. All rights reserved.</small>
</footer>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const toggleBtn = document.getElementById('theme-toggle');
  const icon = document.getElementById('theme-icon');

  // Check saved theme
  if (localStorage.getItem('theme') === 'dark') {
    document.body.classList.add('dark-mode');
    icon.classList.replace('bi-moon', 'bi-sun');
  }

  toggleBtn.addEventListener('click', () => {
    document.body.classList.toggle('dark-mode');
    const darkModeOn = document.body.classList.contains('dark-mode');

    if (darkModeOn) {
      icon.classList.replace('bi-moon', 'bi-sun');
      localStorage.setItem('theme', 'dark');
    } else {
      icon.classList.replace('bi-sun', 'bi-moon');
      localStorage.setItem('theme', 'light');
    }
  });
</script>


</body>
</html>
