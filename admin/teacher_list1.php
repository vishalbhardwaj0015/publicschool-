<?php
session_start();
include('../config/config.php');

if(!isset($_SESSION['admin_name'])){
    header("Location: login.php");
    exit;
}

$admin = $_SESSION['admin_name'];

/* ---------- search + pagination ---------- */
$limit = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

$search = "";
$where_clauses = [];

// search by q (name/email/subject)
if (!empty($_GET['q'])) {
    $search = trim($_GET['q']);
    $q = mysqli_real_escape_string($con, $search);
    $where_clauses[] = "(name LIKE '%$q%' OR email LIKE '%$q%' OR teacher_subject LIKE '%$q%')";
}

// only teachers
$where_clauses[] = "(role = 'teacher' OR role_id = 2)";

$where_sql = "WHERE " . implode(" AND ", $where_clauses);

/* total count */
$count_sql = "SELECT COUNT(*) AS total FROM userform $where_sql";
$count_res = mysqli_query($con, $count_sql);
$total_rows = mysqli_fetch_assoc($count_res)['total'];
$total_pages = max(1, ceil($total_rows / $limit));

/* fetch data */
$sql = "
SELECT id, name, email, phone, teacher_subject, photo, status
FROM userform
$where_sql
ORDER BY id DESC
LIMIT $limit OFFSET $offset
";
$result = mysqli_query($con, $sql);

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Teachers List - Admin</title>
    <link rel="stylesheet" href="style/index.css?v=<?php echo time(); ?>">
    <style>
        .page-wrap { padding: 20px; }
        .controls { display:flex; gap:10px; align-items:center; margin-bottom:15px; }
        .controls .search { flex:1; }
        .tbl { width:100%; border-collapse:collapse; background:#fff; 
               border-radius:8px; overflow:hidden; box-shadow:0 3px 8px #0002; }
        .tbl th, .tbl td { padding:12px 10px; border-bottom:1px solid #eee; font-size:14px; }
        .tbl th { background:#fafafa; font-weight:600; }
        .avatar { width:48px; height:48px; border-radius:6px; object-fit:cover; }
        .actions a { margin-right:8px; text-decoration:none; color:#000; font-weight:600; }
        .pagination { margin-top:12px; display:flex; gap:8px; flex-wrap:wrap; }
        .pagination a { padding:6px 10px; background:#fff; border:1px solid #ddd; border-radius:6px; text-decoration:none; color:#111; }
        .pagination a.active { background:#111; color:#fff; border-color:#111; }
        .btn { padding:8px 12px; border-radius:6px; background:#111; color:#fff; text-decoration:none; display:inline-block; }
    </style>
</head>
<body>

<?php include('header.php'); ?>

<div class="main-content">

    <div class="header">
        <h2>Teachers</h2>
    </div>

    <div class="page-wrap">

        <div style="display:flex; gap:12px; align-items:center;">
            <form method="get" style="flex:1; display:flex;" class="controls">
                <input type="text" name="q" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search name, email or subject..." class="search" style="padding:9px 10px; border:1px solid #ddd; border-radius:6px;">
                <button type="submit" class="btn">Search</button>
            </form>

            <a href="teacher_add.php" class="btn">+ Add Teacher</a>
        </div>

        <table class="tbl">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Photo</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th style="width:150px;">Actions</th>
                </tr>
            </thead>
            <tbody>

                <?php if ($result && mysqli_num_rows($result) > 0): 
                    while ($row = mysqli_fetch_assoc($result)):
                        $id = $row['id'];
                        $name = htmlspecialchars($row['name']);
                        $email = htmlspecialchars($row['email']);
                        $phone = htmlspecialchars($row['phone']);
                        $sub = htmlspecialchars($row['teacher_subject']);
                        $status = htmlspecialchars($row['status'] ?: "active");
                        $photo = !empty($row['photo']) ? ('../' . ltrim($row['photo'], '/')) : '../img/default-user.png';
                ?>
                <tr>
                    <td><?php echo $id; ?></td>
                    <td><img src="<?php echo $photo; ?>" class="avatar"></td>
                    <td><?php echo $name; ?></td>
                    <td><?php echo $email; ?></td>
                    <td><?php echo $sub; ?></td>
                    <td><?php echo $phone; ?></td>
                    <td><span class="badge"><?php echo $status; ?></span></td>

                    <td class="actions">
                        <a href="teacher_view.php?id=<?php echo $id; ?>">View</a>
                        <a href="teacher_edit.php?id=<?php echo $id; ?>">Edit</a>
                        <a href="teacher_delete.php?id=<?php echo $id; ?>" onclick="return confirm('Delete this teacher?');" style="color:#c00;">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>

                <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align:center; padding:20px; color:#777;">No teachers found.</td>
                </tr>
                <?php endif; ?>

            </tbody>
        </table>

        <!-- Pagination -->
        <div class="pagination">
            <?php
                $base = "?q=" . urlencode($search) . "&";
                if ($page > 1)
                    echo '<a href="'.$base.'page='.($page-1).'">&laquo; Prev</a>';
                
                for ($p = max(1, $page-3); $p <= min($total_pages, $page+3); $p++) {
                    $active = $p == $page ? 'active' : '';
                    echo '<a class="'.$active.'" href="'.$base.'page='.$p.'">'.$p.'</a>';
                }

                if ($page < $total_pages)
                    echo '<a href="'.$base.'page='.($page+1).'">Next &raquo;</a>';
            ?>
        </div>

    </div>
</div>

<?php include __DIR__ . '/../user/asset/footer.php'; ?>

</body>
</html>
