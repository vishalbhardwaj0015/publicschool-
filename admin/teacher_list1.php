<?php
session_start();
include('../config/config.php');

if (!isset($_SESSION['admin_name'])) {
    header("Location: login.php");
    exit;
}

/* ---------- search + pagination ---------- */
$limit = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

$search = "";
$where_clauses = [];

if (!empty($_GET['q'])) {
    $search = trim($_GET['q']);
    $q = mysqli_real_escape_string($con, $search);
    $where_clauses[] = "(name LIKE '%$q%' OR email LIKE '%$q%' OR teacher_subject LIKE '%$q%')";
}

/* only teachers (role_id=2) */
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

include('header.php');
?>
        </div>

        <div class="content-header"><h2>Teachers</h2></div>

        <form method="get" class="controls">
            <input type="text" name="q" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search name, email or subject..." style="flex:1;">
            <button type="submit" class="btn-admin">Search</button>
        </form>

        <div class="tblWrap">
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
                            $status_badge = ($status === 'active') ? 'badge-success' : 'badge-secondary';
                            $photo = !empty($row['photo']) ? ('../' . ltrim($row['photo'], '/')) : '../img/default-user.png';
                    ?>
                    <tr>
                        <td><?php echo $id; ?></td>
                        <td><img src="<?php echo $photo; ?>" class="avatar"></td>
                        <td><?php echo $name; ?></td>
                        <td><?php echo $email; ?></td>
                        <td><?php echo $sub; ?></td>
                        <td><?php echo $phone; ?></td>
                        <td><span class="badge <?php echo $status_badge; ?>"><?php echo $status; ?></span></td>
                    </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align:center; padding:20px; color:#777;">No teachers found.</td>
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
