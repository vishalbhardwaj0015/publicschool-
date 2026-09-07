<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>HIM PUBLIC SCHOOL</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style/header.css?v=<?= time() ?>">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark school-navbar">
  <div class="container">
    <a href="index.php" class="navbar-brand d-flex align-items-center gap-2">
      <img src="img/him-logo.png" alt="School Logo" class="brand-logo" onerror="this.style.display='none'">
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
        <li class="nav-item"><a href="index.php" class="nav-link">Home</a></li>
        <li class="nav-item"><a href="user/login.php" class="nav-link">Student / Teacher Login</a></li>
        <li class="nav-item"><a href="user/register.php" class="nav-link">New Admission</a></li>
        <li class="nav-item"><a href="admin/login.php" class="nav-link">Admin</a></li>
      </ul>
    </div>
  </div>
</nav>