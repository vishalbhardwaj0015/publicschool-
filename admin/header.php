<?php
if(!isset($_SESSION)) session_start();
?>
<link rel="stylesheet" href="style/header.css?v=<?php echo time(); ?>">

<div class="top-header">

    <div class="left-title">
        <h2>Dashboard</h2>
    </div>

    <div class="right-profile">

        <img src="<?php 
            echo isset($_SESSION['admin_photo']) && $_SESSION['admin_photo'] 
                ? '../'.$_SESSION['admin_photo'] 
                : '../img/admin/default.png';
        ?>" class="top-admin-img" />

        <span class="admin-name">
            <?php echo $_SESSION['admin_name']; ?>
        </span>

        <div class="dropdown">
            <button class="dropdown-btn">▼</button>
            <div class="dropdown-content">
                <a href="admin_profile.php">Profile</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>

    </div>

</div>
