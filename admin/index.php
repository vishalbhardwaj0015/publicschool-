<?php
session_start();
include('../config/config.php');


if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit;
}



$admin = $_SESSION['admin_name'] ?? "Admin";


$totalStudents = mysqli_fetch_assoc(
    mysqli_query($con, "SELECT COUNT(*) AS total FROM userform WHERE role_id = 1")
)['total'];

$totalTeachers = mysqli_fetch_assoc(
    mysqli_query($con, "SELECT COUNT(*) AS total FROM userform WHERE role_id = 2")
)['total'];

$totalClasses = mysqli_fetch_assoc(
    mysqli_query($con, "SELECT COUNT(*) AS total FROM classes")
)['total'];

$totalAdmissions = mysqli_fetch_assoc(
    mysqli_query($con, "SELECT COUNT(*) AS total FROM admissions")
)['total'];


$totalFees = mysqli_fetch_assoc(
    mysqli_query($con, "SELECT SUM(paid_fee)  total FROM fees")
)['total'] ?? 0;

// Pending Fees
$totalPending = mysqli_fetch_assoc(
    mysqli_query($con, "SELECT SUM(pending_fee)  total FROM fees")
)['total'] ?? 0;

?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style/index.css?v=<?php echo time(); ?>">
</head>

<body>         

<!-- Sidebar -->
<div class="sidebar">
    <h2 class="logo">ADMIN PANEL</h2>

    <a href="index.php" class="active">Dashboard</a>
    <a href="students_list1.php">Students</a>
    <a href="teachers_list1.php">Teachers</a>
    <a href="class_list.php">Classes</a>
    <a href="admissions.php">Admissions</a>
    <a href="attendance_mark.php">Attendance</a>
    <a href="fee_collection.php">Fees</a>
    <a href="activity_list.php">Activities</a>
    <a href="admin_profile.php">Admin Profile</a>
    <a href="admin_create.php">Create Admin</a>

    <a href="logout.php" class="logout-btn">Logout</a>
</div>


<div class="main-content">

    <?php include("header.php"); ?>   

                <!-- dashboard -->
    <div class="dashboard-grid">

        <div class="card">
            <h3>
                <?php echo $totalStudents; ?>
            </h3>
            <p>Total Students</p>
        </div>
        <div class="card">
            <h3>
                <?php echo $totalTeachers; ?>
            </h3>
            <p>Total Teachers</p>
        </div>
        <div class="card">
            <h3>
                <?php echo $totalClasses; ?>
            </h3>
            <p>Total Classes</p>
        </div>
        <div class="card">
            <h3>
                <?php echo $totalAdmissions; ?>
            </h3>
            <p>Admissions</p>
        </div>
        <div class="card">
            <h3>
                ₹<?php echo number_format($totalFees); ?>
            </h3>
            <p>Fees Collected</p>
        </div>
        <div class="card">
            <h3>
                ₹<?php echo number_format($totalPending); ?>
            </h3>
            <p>Pending Fees</p></div>
        </div>

</div>

</body>
</html>
