<?php
session_start();
include('../config/config.php');

if (!isset($_SESSION['admin_name'])) {
    header("Location: login.php");
    exit;
}

$msg = "";

/* -------------------- DELETE ACTIVITY -------------------- */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $img_q = mysqli_query($con, "SELECT image FROM activities WHERE id=$id");
    $img = mysqli_fetch_assoc($img_q);

    if ($img && !empty($img['image'])) {
        $file = "../" . $img['image'];
        if (file_exists($file)) {
            unlink($file);
        }
    }

    mysqli_query($con, "DELETE FROM activities WHERE id=$id");
    $msg = "Activity deleted successfully!";
}

/* -------------------- FETCH ACTIVITIES -------------------- */
$result = mysqli_query($con, "SELECT * FROM activities ORDER BY id DESC");

include('header.php');
?>
        </div>

        <div class="content-header"><h2>Activities</h2></div>

        <?php if ($msg): ?>
            <div class="msg-success"><?php echo $msg; ?></div>
        <?php endif; ?>

        <div style="text-align:right; margin-bottom:12px;">
            <a href="activity_add.php" class="btn-admin">+ Add Activity</a>
        </div>

        <div class="tblWrap">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Date</th>
                        <th>Actions</th>
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
                        <td>
                            <a href="activity_edit.php?id=<?php echo $id; ?>" class="btn-admin btn-sm">Edit</a>
                            <a href="activity_list.php?delete=<?php echo $id; ?>" class="btn-admin btn-sm btn-danger" onclick="return confirm('Delete this activity?');">Delete</a>
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
</body>
</html>
