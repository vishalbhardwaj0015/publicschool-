<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$adminName = $_SESSION['admin_name'] ?? 'Admin';
$adminPhoto = (!empty($_SESSION['admin_photo']) && is_file('../'.$_SESSION['admin_photo'])) ? '../'.$_SESSION['admin_photo'] : '../img/admin/default.png';
$active = basename($_SERVER['PHP_SELF']);
function isActive($p) { global $active; return $active === $p ? 'active' : ''; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | HIM PUBLIC SCHOOL</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style/index.css?v=<?= time() ?>">
</head>
<body>

<div class="sidebar">
    <div class="logo"><i class="fa-solid fa-school"></i> ADMIN PANEL</div>
    <a href="index.php" class="<?= isActive('index.php') ?>"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
    <a href="students_list1.php" class="<?= isActive('students_list1.php') ?>"><i class="fa-solid fa-user-graduate"></i> Students</a>
    <a href="teacher_list1.php" class="<?= isActive('teacher_list1.php') ?>"><i class="fa-solid fa-chalkboard-user"></i> Teachers</a>
    <a href="class_list.php" class="<?= isActive('class_list.php') ?>"><i class="fa-solid fa-building-columns"></i> Classes</a>
    <a href="attendance_mark.php" class="<?= isActive('attendance_mark.php') ?>"><i class="fa-solid fa-calendar-check"></i> Attendance</a>
    <a href="attendance_view.php" class="<?= isActive('attendance_view.php') ?>"><i class="fa-solid fa-list-check"></i> Attendance View</a>
    <a href="fee_collection.php" class="<?= isActive('fee_collection.php') ?>"><i class="fa-solid fa-indian-rupee-sign"></i> Fees</a>
    <a href="fees_history.php" class="<?= isActive('fees_history.php') ?>"><i class="fa-solid fa-receipt"></i> Fees History</a>
    <a href="activity_list.php" class="<?= isActive('activity_list.php') ?>"><i class="fa-solid fa-star"></i> Activities</a>
    <a href="admin_create.php" class="<?= isActive('admin_create.php') ?>"><i class="fa-solid fa-user-shield"></i> Admins</a>
    <a href="logout.php" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
</div>

<div class="main-content">
    <div class="top-header">
        <div class="left-title"><h2>HIM PUBLIC SCHOOL — Admin</h2></div>
        <div class="right-profile">
            <img src="<?= htmlspecialchars($adminPhoto) ?>" class="top-admin-img" alt="Admin">
            <span class="admin-name"><?= htmlspecialchars($adminName) ?></span>
            <a href="logout.php" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
    </div>
