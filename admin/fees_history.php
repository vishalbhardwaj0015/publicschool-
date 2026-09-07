<?php
session_start();
include('../config/config.php');

if (!isset($_SESSION['admin_name'])) {
    header("Location: login.php");
    exit;
}

$search = trim($_GET['q'] ?? '');
$start = $_GET['start'] ?? '';
$end = $_GET['end'] ?? '';
$class_id = intval($_GET['class_id'] ?? 0);

/* build where */
$where = "WHERE 1=1";
if ($search !== '') {
    $s = mysqli_real_escape_string($con, $search);
    $where .= " AND (u.name LIKE '%$s%' OR u.email LIKE '%$s%' OR p.id = '$s')";
}
if ($start !== '') {
    $start_esc = mysqli_real_escape_string($con, $start);
    $where .= " AND p.payment_date >= '$start_esc 00:00:00'";
}
if ($end !== '') {
    $end_esc = mysqli_real_escape_string($con, $end);
    $where .= " AND p.payment_date <= '$end_esc 23:59:59'";
}
if ($class_id) {
    $where .= " AND u.student_class = $class_id";
}

/* pagination */
$limit = 20;
$page = max(1, intval($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

/* total count */
$count_q = "SELECT COUNT(*) AS total
            FROM payments p
            LEFT JOIN userform u ON p.student_id = u.id
            $where";
$cres = mysqli_query($con, $count_q);
$total_rows = intval(mysqli_fetch_assoc($cres)['total']);
$total_pages = max(1, ceil($total_rows / $limit));

/* fetch payments */
$sql = "
SELECT p.*, u.name AS student_name, u.student_class, c.class_name
FROM payments p
LEFT JOIN userform u ON p.student_id = u.id
LEFT JOIN classes c ON u.student_class = c.id
$where
ORDER BY p.payment_date DESC
LIMIT $limit OFFSET $offset
";
$res = mysqli_query($con, $sql);

/* fetch classes for filter */
$classes = mysqli_query($con, "SELECT * FROM classes ORDER BY class_name ASC");

include('header.php');
?>
        </div>

        <div class="content-header"><h2>Fees History</h2></div>

        <form method="get" class="controls">
            <input type="text" name="q" placeholder="Search by name, email or payment id" value="<?php echo htmlspecialchars($search); ?>">
            <select name="class_id">
                <option value="0">All Classes</option>
                <?php while ($cl = mysqli_fetch_assoc($classes)): ?>
                    <option value="<?php echo $cl['id']; ?>" <?php if ($class_id == $cl['id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($cl['class_name'] . ($cl['section'] ? ' - ' . $cl['section'] : '')); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <input type="date" name="start" value="<?php echo htmlspecialchars($start); ?>">
            <input type="date" name="end" value="<?php echo htmlspecialchars($end); ?>">
            <button class="btn-admin">Filter</button>
            <a href="fee_collection.php" class="btn-admin" style="background:#666; margin-left:auto;">Back to Collection</a>
        </form>

        <div class="tblWrap">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Payment ID</th>
                        <th>Student</th>
                        <th>Class</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Note</th>
                        <th>Date</th>
                        <th>By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($res && mysqli_num_rows($res) > 0): $i = $offset + 1;
                        while ($r = mysqli_fetch_assoc($res)): ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo $r['id']; ?></td>
                            <td><?php echo htmlspecialchars($r['student_name']); ?></td>
                            <td><?php echo htmlspecialchars($r['class_name']); ?></td>
                            <td>&#8377;<?php echo number_format($r['amount']); ?></td>
                            <td><?php echo htmlspecialchars($r['method']); ?></td>
                            <td><?php echo htmlspecialchars($r['note']); ?></td>
                            <td><?php echo date("d M Y H:i", strtotime($r['payment_date'])); ?></td>
                            <td><?php echo htmlspecialchars($r['created_by']); ?></td>
                        </tr>
                    <?php endwhile; else: ?>
                        <tr><td colspan="9" style="text-align:center; padding:18px; color:#666;">No payments found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="pagination">
            <?php
            $base = '?q=' . urlencode($search) . '&class_id=' . $class_id . '&start=' . urlencode($start) . '&end=' . urlencode($end) . '&';
            if ($page > 1) echo '<a href="' . $base . 'page=' . ($page - 1) . '">&laquo; Prev</a>';
            for ($p = max(1, $page - 3); $p <= min($total_pages, $page + 3); $p++) {
                $cls = $p == $page ? 'active' : '';
                echo '<a class="' . $cls . '" href="' . $base . 'page=' . $p . '">' . $p . '</a>';
            }
            if ($page < $total_pages) echo '<a href="' . $base . 'page=' . ($page + 1) . '">Next &raquo;</a>';
            ?>
        </div>

    </div>
</body>
</html>
