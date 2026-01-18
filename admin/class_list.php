<?php
session_start();
include('../config/config.php');

if(!isset($_SESSION['admin_name'])){
    header("Location: login.php");
    exit;
}

$admin = $_SESSION['admin_name'];

// Fetch all classes
$sql = "
SELECT c.*, 
(SELECT COUNT(*) FROM userform u WHERE u.student_class = c.id) AS total_students
FROM classes c
ORDER BY c.id ASC
";
$result = mysqli_query($con, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Class List - Admin</title>
    <link rel="stylesheet" href="style/index.css?v=<?php echo time(); ?>">
    <style>
        .page-wrap { padding:20px; }
        .tbl { width:100%; border-collapse:collapse; background:#fff;
               border-radius:8px; overflow:hidden; box-shadow:0 3px 8px #0002; }
        .tbl th, .tbl td { padding:12px 10px; border-bottom:1px solid #eee; font-size:14px; }
        .tbl th { background:#fafafa; font-weight:600; }
        .btn { padding:8px 12px; border-radius:6px; text-decoration:none; background:#111; color:#fff; }
        .actions a { margin-right:10px; color:#000; text-decoration:none; font-weight:600; }
    </style>
</head>
<body>

<?php include('header.php'); ?>

<div class="main-content">

    <div class="header">
        <h2>Class List</h2>
    </div>

    <div class="page-wrap">

        <div style="text-align:right; margin-bottom:12px;">
            <a href="class_add.php" class="btn">+ Add Class</a>
        </div>

        <table class="tbl">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Class Name</th>
                    <th>Section</th>
                    <th>Total Students</th>
                    <th style="width:150px;">Actions</th>
                </tr>
            </thead>
            <tbody>

                <?php if ($result && mysqli_num_rows($result) > 0): 
                    while ($row = mysqli_fetch_assoc($result)):
                ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['class_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['section']); ?></td>
                    <td><?php echo $row['total_students']; ?></td>

                    <td class="actions">
                        <a href="class_edit.php?id=<?php echo $row['id']; ?>">Edit</a>
                        <a href="class_delete.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Delete this class?');" style="color:#c00;">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>

                <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align:center; padding:20px;">No classes found.</td>
                </tr>
                <?php endif; ?>

            </tbody>
        </table>

    </div>
</div>

<?php include __DIR__ . '/../user/asset/footer.php'; ?>

</body>
</html>
