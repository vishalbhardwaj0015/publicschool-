<?php
session_start();
include('../config/config.php');

if (!isset($_SESSION['admin_name'])) {
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

    if (!empty($_FILES['image']['name'])) {
        $filename = time() . "_" . basename($_FILES['image']['name']);
        $target = "../img/activities/" . $filename;

        if (!is_dir("../img/activities")) {
            mkdir("../img/activities");
        }

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
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
            $old_title = $title;
            $old_desc  = $description;
            $old_image = $image_path;
        } else {
            $error = "Database error while updating!";
        }
    }
}

include('header.php');
?>
        </div>

        <div class="content-header"><h2>Edit Activity</h2></div>

        <?php if ($msg): ?>
            <div class="msg-success"><?php echo $msg; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="msg-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="form-card">
            <form method="post" enctype="multipart/form-data">
                <label>Activity Title</label>
                <input type="text" name="title" required value="<?php echo htmlspecialchars($old_title); ?>">

                <label>Description</label>
                <textarea name="description" rows="4"><?php echo htmlspecialchars($old_desc); ?></textarea>

                <label>Current Image</label>
                <?php if ($old_image): ?>
                    <img src="../<?php echo htmlspecialchars($old_image); ?>" style="width:180px;height:120px;object-fit:cover;border-radius:6px;margin-bottom:15px;">
                <?php else: ?>
                    <p>No Image Uploaded</p>
                <?php endif; ?>

                <label>Upload New Image (optional)</label>
                <input type="file" name="image" accept="image/*">

                <button class="btn-admin" name="update_activity">Update Activity</button>
            </form>
        </div>
    </div>
</body>
</html>
