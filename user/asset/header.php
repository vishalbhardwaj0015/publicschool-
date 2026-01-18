<link rel="stylesheet" href="user\style\home.css">
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <title>HIM PUBLIC SCHOOL</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style/header.css?v=<?= time()?>">
    
</head>
<body>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$username = $_SESSION['username'] ?? "Guest";

?>

<header class="header shadow-sm">
  <nav class="navbar navbar-expand-lg">
    <div class="container-fluid">

      <a href="" class="d-flex align-items-center">
        <img src="../img/him-logo.png" class="logo" alt="School Logo">
      </a>

      <h2 class="nav-heading">
        <?php 
            if (isset($_SESSION['username'])) {
              echo "Welcome " . htmlspecialchars($_SESSION['username']);
             } else {
              echo "Welcome Guest";
            }
        ?>
      </h2>
      <div class="nav-links">
        <?php
            if(isset($_SESSION['user_id'])){
              echo '<a href="logout.php" class="nav-link text-danger fw-bold">Logout</a>';
            }else {
              echo '<a href="admin/login.php" class="nav-link">Admin Login</a>';
              echo '<a href="login.php" class="nav-link">Login User</a>';
              // echo '<a href="register.php" class="nav-link">Register User</a>';
            } 
        ?>   
      </div>

    </div>
  </nav>
</header>
