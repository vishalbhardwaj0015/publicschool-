<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$username = $_SESSION['username'] ?? "Guest";
$isLoggedIn = isset($_SESSION['user_id']);
$isTeacher = isset($_SESSION['role_id']) && intval($_SESSION['role_id']) === 2;
if ($isLoggedIn) {
    $homeLink = $isTeacher ? 'teacher_home.php' : 'home.php';
} else {
    $homeLink = '../index.php';
}
$roleLabel = $isTeacher ? 'Teacher' : 'Student';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>HIM PUBLIC SCHOOL</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style/header.css?v=<?= time() ?>">
    <link rel="stylesheet" href="style/dark.css?v=<?= time() ?>">
    <script>
    (function(){
        var t = localStorage.getItem('schoolTheme');
        if (t === 'dark') document.documentElement.setAttribute('data-theme','dark');
    })();
    function applyThemeIcon(){
        var btn = document.getElementById('themeBtn');
        if (!btn) return;
        var isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        btn.innerHTML = isDark ? '<i class="fa-solid fa-sun"></i>' : '<i class="fa-solid fa-moon"></i>';
    }
    function toggleTheme(){
        var html = document.documentElement;
        var isDark = html.getAttribute('data-theme') === 'dark';
        if (isDark) { html.removeAttribute('data-theme'); localStorage.setItem('schoolTheme','light'); }
        else { html.setAttribute('data-theme','dark'); localStorage.setItem('schoolTheme','dark'); }
        applyThemeIcon();
    }
    </script>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark school-navbar">
  <div class="container">
    <a href="<?= $homeLink ?>" class="navbar-brand d-flex align-items-center gap-2">
      <img src="../img/him-logo.png" alt="School Logo" class="brand-logo" onerror="this.style.display='none'">
      <div class="brand-text">
        <span class="brand-name">HIM PUBLIC SCHOOL</span>
        <small class="brand-tag">Education that inspires</small>
      </div>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu" aria-controls="navMenu" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
        <li class="nav-item"><a href="<?= $homeLink ?>" class="nav-link">Home</a></li>
        <?php if ($isLoggedIn): ?>
          <li class="nav-item"><a href="student_list.php" class="nav-link">Students</a></li>
          <li class="nav-item"><a href="teacher_list.php" class="nav-link">Teachers</a></li>
          <?php if ($isTeacher): ?>
            <li class="nav-item"><a href="teacher_home.php" class="nav-link">My Dashboard</a></li>
          <?php endif; ?>
          <span class="nav-welcome"><?= $roleLabel ?>: <?= htmlspecialchars($username) ?></span>
          <button type="button" class="theme-toggle ms-2" id="themeBtn" title="Toggle dark mode" onclick="toggleTheme()"><i class="fa-solid fa-moon"></i></button>
          <a href="logout.php" class="btn btn-light btn-sm fw-semibold ms-2">Logout</a>
        <?php else: ?>
          <li class="nav-item"><a href="login.php" class="nav-link">Student / Teacher Login</a></li>
          <li class="nav-item"><a href="register.php" class="nav-link">New Admission</a></li>
          <li class="nav-item"><a href="../admin/login.php" class="nav-link">Admin</a></li>
          <li class="nav-item"><button type="button" class="theme-toggle ms-1" id="themeBtn" title="Toggle dark mode" onclick="toggleTheme()"><i class="fa-solid fa-moon"></i></button></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
