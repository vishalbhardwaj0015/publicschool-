<?php
session_start();
include('../config/config.php');

if(!isset($_SESSION['admin_name'])){
    header("Location: login.php");
    exit;
}

$msg = "";
$error = "";

/* ----------------- CHECK & GET ID ----------------- */
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid Activity ID");
}

$id = intval($_GET['id']);

/* -------- FETCH CURRENT ACTIVITY -------- */
$select = mysqli_query($con, "SELECT * FROM activities WHERE id = $id LIMIT 1");
$activity = mysqli_fetch_assoc($select);

if (!$activity) {
    die("Activity not found!");
}

$old_title = $activity['title'];
$old_desc  = $activity['description'];
$old_image = $activity['image'];

/* ----------------- UPDATE FORM SUBMIT ----------------- */
if (isset($_POST['update_activity'])) {

    $title = mysqli_real_escape_string($con, $_POST['title']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $image_path = $old_image;

    /* ------- IMAGE HANDLING ------- */
    if (!empty($_FILES['image']['name'])) {

        $filename = time() . "_" . basename($_FILES['image']['name']);
        $target = "../img/activities/" . $filename;

        if (!is_dir("../img/activities")) {
            mkdir("../img/activities");
        }

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            // Delete old image
            if (!empty($old_image) && file_exists("../" . $old_image)) {
                unlink("../" . $old_image);
            }
            $image_path = "img/activities/" . $filename;
        } else {
            $error = "Image upload failed!";
        }
    }

    if (empty($error)) {
        $update = mysqli_query($con, "
            UPDATE activities
            SET title = '$title',
                description = '$description',
                image = '$image_path'
            WHERE id = $id
        ");

        if ($update) {
            $msg = "Activity updated successfully!";
            // Refresh values
            $old_title = $title;
            $old_desc  = $description;
            $old_image = $image_path;
        } else {
            $error = "Database error while updating!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Activity</title>
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
        .preview-img { width:180px; height:120px; object-fit:cover; border-radius:6px; margin-bottom:10px; }
    </style>
</head>

<body>

<?php include('header.php'); ?>

<div class="main-content">

    <div class="header">
        <h2>Edit Activity</h2>
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
                <input type="text" name="title" required value="<?php echo htmlspecialchars($old_title); ?>">

                <label>Description</label>
                <textarea name="description" rows="4"><?php echo htmlspecialchars($old_desc); ?></textarea>

                <label>Current Image</label>
                <?php if ($old_image): ?>
                    <img src="../<?php echo $old_image; ?>" class="preview-img">
                <?php else: ?>
                    <p>No Image Uploaded</p>
                <?php endif; ?>

                <label>Upload New Image (optional)</label>
                <input type="file" name="image" accept="image/*">

                <button class="btn" name="update_activity">Update Activity</button>
            </form>

        </div>
    </div>

</div>

<?php include __DIR__ . '/../user/asset/footer.php'; ?>

</body>
</html>
