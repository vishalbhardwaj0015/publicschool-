<?php
session_start();
include('../config/config.php');

if(!isset($_SESSION['admin_name'])){
    header("Location: login.php");
    exit;
}

$admin = $_SESSION['admin_name'];

$msg = "";
$error = "";

/* -------------------- DELETE ACTIVITY -------------------- */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // fetch image path to delete file
    $img_q = mysqli_query($con, "SELECT image FROM activities WHERE id=$id");
    $img = mysqli_fetch_assoc($img_q);

    if ($img && !empty($img['image'])) {
        $file = "../" . $img['image'];  // convert to actual path
        if (file_exists($file)) {
            unlink($file);
        }
    }

    mysqli_query($con, "DELETE FROM activities WHERE id=$id");
    $msg = "Activity deleted successfully!";
}

/* -------------------- FETCH ACTIVITIES -------------------- */
$sql = "SELECT * FROM activities ORDER BY id DESC";
$result = mysqli_query($con, $sql);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Activities List</title>
    <link rel="stylesheet" href="style/index.css?v=<?php echo time(); ?>">
    <style>
        .wrap { padding:20px; }
        .tbl { width:100%; border-collapse:collapse; background:#fff;
               border-radius:8px; overflow:hidden; box-shadow:0 3px 8px #0002; }
        .tbl th, .tbl td { padding:12px 10px; border-bottom:1px solid #eee; font-size:14px; }
        .tbl th { background:#fafafa; font-weight:600; }
        .btn { padding:8px 12px; border-radius:6px; background:#111; color:#fff; text-decoration:none; }
        .actions a { margin-right:10px; text-decoration:none; font-weight:600; }
        .msg-success { background:#d8ffd8; padding:10px; border-radius:6px; margin-bottom:10px; color:#006600; }
        img.thumb { width:70px; height:55px; border-radius:4px; object-fit:cover; }
    </style>
</head>

<body>

<?php include('header.php'); ?>

<div class="main-content">
    
    <div class="header">
        <h2>Activities</h2>
    </div>

    <div class="wrap">

        <?php if ($msg): ?>
            <div class="msg-success"><?php echo $msg; ?></div>
        <?php endif; ?>

        <div style="text-align:right; margin-bottom:12px;">
            <a href="activity_add.php" class="btn">+ Add Activity</a>
        </div>

        <table class="tbl">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th style="width:150px;">Actions</th>
                </tr>
            </thead>
            <tbody>

                <?php if ($result && mysqli_num_rows($result) > 0): 
                    while ($row = mysqli_fetch_assoc($result)):
                        $id = $row['id'];
                        $title = htmlspecialchars($row['title']);
                        $desc = htmlspecialchars(substr($row['description'], 0, 50)) . "...";
                        $image = "../" . $row['image'];
                ?>
                <tr>
                    <td><?php echo $id; ?></td>
                    <td>
                        <?php if (!empty($row['image'])): ?>
                            <img src="<?php echo $image; ?>" class="thumb">
                        <?php else: ?>
                            No Image
                        <?php endif; ?>
                    </td>
                    <td><?php echo $title; ?></td>
                    <td><?php echo $desc; ?></td>
                    <td><?php echo date("d M Y", strtotime($row['created_at'])); ?></td>

                    <td class="actions">
                        <a href="activity_edit.php?id=<?php echo $id; ?>">Edit</a>
                        <a href="activity_list.php?delete=<?php echo $id; ?>" style="color:#c00;"
                           onclick="return confirm('Delete this activity?');">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
                
                <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align:center; padding:20px;">No activities found.</td>
                </tr>
                <?php endif; ?>

            </tbody>
        </table>
    </div>

</div>

<?php include __DIR__ . '/../user/asset/footer.php'; ?>

</body>
</html>
