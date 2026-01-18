<?php
session_start();
include('../config/config.php');

if(!isset($_SESSION['admin_name'])){
    header("Location: login.php");
    exit;
}

$msg = "";
$error = "";

// FORM SUBMIT
if (isset($_POST['add_activity'])) {

    $title = mysqli_real_escape_string($con, $_POST['title']);
    $description = mysqli_real_escape_string($con, $_POST['description']);

    // ---- IMAGE UPLOAD ----
    $image_path = "";

    if (!empty($_FILES['image']['name'])) {
        $filename = time() . "_" . basename($_FILES['image']['name']);
        $target = "../img/activities/" . $filename;

        // folder check
        if (!is_dir("../img/activities")) {
            mkdir("../img/activities");
        }

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $image_path = "img/activities/" . $filename;
        } else {
            $error = "Image upload failed!";
        }
    }

    if (empty($error)) {
        $sql = "INSERT INTO activities (title, description, image) 
                VALUES ('$title', '$description', '$image_path')";

        if (mysqli_query($con, $sql)) {
            $msg = "Activity added successfully!";
        } else {
            $error = "Database error!";
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Activity</title>
    <link rel="stylesheet" href="style/index.css?v=<?php echo time(); ?>">
    <style>
        .wrap { padding:20px; }
        .card { background:#fff; padding:22px; border-radius:8px; 
                box-shadow:0 3px 8px #0002; width:650px; }
        label { display:block; margin-bottom:6px; font-weight:600; }
        input, textarea {
            width:100%; padding:10px; border:1px solid #ddd;
            border-radius:6px; margin-bottom:15px; font-size:14px;
        }
        .btn { padding:10px 14px; background:#111; border-radius:6px;
               color:#fff; border:none; cursor:pointer; }
        .msg-success { padding:10px; background:#d8ffd8; color:#006600; margin-bottom:10px; border-radius:6px; }
        .msg-error { padding:10px; background:#ffdada; color:#a10000; margin-bottom:10px; border-radius:6px; }
    </style>
</head>

<body>

<?php include('header.php'); ?>

<div class="main-content">

    <div class="header">
        <h2>Add Activity</h2>
    </div>

    <div class="wrap">
        <div class="card">

            <?php if ($msg): ?>
                <div class="msg-success"><?php echo $msg; ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="msg-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">

                <label>Activity Title</label>
                <input type="text" name="title" required placeholder="Enter activity title">

                <label>Description</label>
                <textarea name="description" rows="4" placeholder="Write activity details..."></textarea>

                <label>Upload Image</label>
                <input type="file" name="image" accept="image/*">

                <button class="btn" name="add_activity">Add Activity</button>
            </form>
        </div>
    </div>

</div>

<?php include __DIR__ . '/../user/asset/footer.php'; ?>

</body>
</html>
