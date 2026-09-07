<?php
session_start();
include('../config/config.php');

if (!isset($_SESSION['admin_name'])) {
    header("Location: login.php");
    exit;
}

$msg = "";
$error = "";

if (isset($_POST['add_activity'])) {

    $title = mysqli_real_escape_string($con, $_POST['title']);
    $description = mysqli_real_escape_string($con, $_POST['description']);

    $image_path = "";

    if (!empty($_FILES['image']['name'])) {
        $filename = time() . "_" . basename($_FILES['image']['name']);
        $target = "../img/activities/" . $filename;

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

include('header.php');
?>
        </div>

        <div class="content-header"><h2>Add Activity</h2></div>

        <?php if ($msg): ?>
            <div class="msg-success"><?php echo $msg; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="msg-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="form-card">
            <form method="post" enctype="multipart/form-data">
                <label>Activity Title</label>
                <input type="text" name="title" required placeholder="Enter activity title">

                <label>Description</label>
                <textarea name="description" rows="4" placeholder="Write activity details..."></textarea>

                <label>Upload Image</label>
                <input type="file" name="image" accept="image/*">

                <button class="btn-admin" name="add_activity">Add Activity</button>
            </form>
        </div>
    </div>
</body>
</html>
