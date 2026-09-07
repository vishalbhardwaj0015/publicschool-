<?php
session_start();
include('../config/config.php');

if (!isset($_SESSION['admin_name'])) {
    header("Location: login.php");
    exit;
}

/* ---------- search + pagination ---------- */
$limit = 15;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

$search = "";
$where_clauses = [];

if (!empty($_GET['q'])) {
    $search = trim($_GET['q']);
    $q = mysqli_real_escape_string($con, $search);
    $where_clauses[] = "(u.name LIKE '%$q%' OR u.email LIKE '%$q%' OR u.phone LIKE '%$q%')";
}

/* only students (role_id=1) */
$where_clauses[] = "(u.role = 'student' OR u.role_id = 1)";

$where_sql = "WHERE " . implode(" AND ", $where_clauses);

/* total count */
$count_sql = "SELECT COUNT(*) AS total FROM userform u $where_sql";
$count_res = mysqli_query($con, $count_sql);
$total_rows = mysqli_fetch_assoc($count_res)['total'];
$total_pages = max(1, ceil($total_rows / $limit));

/* fetch students */
$sql = "
SELECT u.*, c.class_name
FROM userform u
LEFT JOIN classes c ON u.student_class = c.id
$where_sql
ORDER BY u.id DESC
LIMIT $limit OFFSET $offset
";
$result = mysqli_query($con, $sql);

include('header.php');
?>
        </div>

        <div class="content-header">
            <h2>Students</h2>
            <a href="student_add.php" class="btn-admin">+ Add Student</a>
        </div>

        <form method="get" class="controls">
            <input type="text" name="q" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by name, email or phone..." style="flex:1;">
            <button type="submit" class="btn-admin">Search</button>
        </form>

        <div class="tblWrap">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Class</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && mysqli_num_rows($result) > 0):
                        while ($row = mysqli_fetch_assoc($result)):
                            $id = $row['id'];
                            $status = htmlspecialchars($row['status'] ?: 'active');
                            $status_badge = ($status === 'active') ? 'badge-success' : 'badge-secondary';
                    ?>
                    <tr>
                        <td><?php echo $id; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                        <td><?php echo htmlspecialchars($row['class_name']); ?></td>
                        <td><span class="badge <?php echo $status_badge; ?>"><?php echo $status; ?></span></td>
                        <td>
                            <a href="../user/update.php?id=<?php echo $id; ?>" class="btn-admin btn-sm">Edit</a>
                            <a href="../user/delete.php?id=<?php echo $id; ?>" class="btn-admin btn-sm btn-danger" onclick="return confirm('Delete this student?');">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align:center; padding:20px; color:#777;">No students found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="pagination">
            <?php
            $base = "?q=" . urlencode($search) . "&";
            if ($page > 1)
                echo '<a href="' . $base . 'page=' . ($page - 1) . '">&laquo; Prev</a>';
            for ($p = max(1, $page - 3); $p <= min($total_pages, $page + 3); $p++) {
                $active = $p == $page ? 'active' : '';
                echo '<a class="' . $active . '" href="' . $base . 'page=' . $p . '">' . $p . '</a>';
            }
            if ($page < $total_pages)
                echo '<a href="' . $base . 'page=' . ($page + 1) . '">Next &raquo;</a>';
            ?>
        </div>

    </div>
</body>
</html>
