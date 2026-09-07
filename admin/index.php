<?php
session_start();
include('../config/config.php');

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit;
}

$admin = $_SESSION['admin_name'] ?? "Admin";

$totalStudents = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM userform WHERE role_id = 1"))['total'];
$totalTeachers = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM userform WHERE role_id = 2"))['total'];
$totalClasses  = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM classes"))['total'];
$totalAdmissions = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM admissions"))['total'];

$feesRow = mysqli_fetch_assoc(mysqli_query($con, "SELECT IFNULL(SUM(paid_fee),0) AS paid, IFNULL(SUM(pending_fee),0) AS pending FROM fees"));

include('header.php');
?>
<div class="content-header">
    <h2>Dashboard</h2>
    <a href="students_list1.php" class="btn-admin">+ Manage Students</a>
</div>

<div class="dashboard-grid">
    <div class="stat-card">
        <div class="stat-ico" style="background:#e5edff;color:#2f6bff;"><i class="fa-solid fa-user-graduate"></i></div>
        <h3><?= number_format($totalStudents) ?></h3>
        <p>Total Students</p>
    </div>
    <div class="stat-card">
        <div class="stat-ico" style="background:#e8f7ec;color:#1a7a3d;"><i class="fa-solid fa-chalkboard-user"></i></div>
        <h3><?= number_format($totalTeachers) ?></h3>
        <p>Total Teachers</p>
    </div>
    <div class="stat-card">
        <div class="stat-ico" style="background:#fff3d6;color:#9a6700;"><i class="fa-solid fa-building-columns"></i></div>
        <h3><?= number_format($totalClasses) ?></h3>
        <p>Total Classes</p>
    </div>
    <div class="stat-card">
        <div class="stat-ico" style="background:#fde7f0;color:#c2185b;"><i class="fa-solid fa-file-circle-check"></i></div>
        <h3><?= number_format($totalAdmissions) ?></h3>
        <p>Admissions</p>
    </div>
    <div class="stat-card">
        <div class="stat-ico" style="background:#e5edff;color:#2f6bff;"><i class="fa-solid fa-indian-rupee-sign"></i></div>
        <h3>₹<?= number_format($feesRow['paid']) ?></h3>
        <p>Fees Collected</p>
    </div>
    <div class="stat-card">
        <div class="stat-ico" style="background:#fdecec;color:#e53935;"><i class="fa-solid fa-clock"></i></div>
        <h3>₹<?= number_format($feesRow['pending']) ?></h3>
        <p>Pending Fees</p>
    </div>
</div>

</div>
</body>
</html>
