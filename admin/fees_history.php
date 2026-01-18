<?php
session_start();
include('../config/config.php');

if(!isset($_SESSION['admin_name'])){
    header("Location: login.php");
    exit;
}

$search = trim($_GET['q'] ?? '');
$start = $_GET['start'] ?? '';
$end = $_GET['end'] ?? '';
$class_id = intval($_GET['class_id'] ?? 0);

// build where
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
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Fees History - Admin</title>
    <link rel="stylesheet" href="style/index.css?v=<?php echo time(); ?>">
    <style>
        .wrap{padding:20px;}
        .controls{display:flex;gap:8px;align-items:center;margin-bottom:12px;flex-wrap:wrap;}
        input, select { padding:8px 10px; border:1px solid #ddd; border-radius:6px; }
        .btn{background:#111;color:#fff;padding:8px 12px;border-radius:6px;text-decoration:none;border:none;cursor:pointer;}
        .tbl{width:100%;border-collapse:collapse;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 3px 8px #0002;}
        .tbl th, .tbl td{padding:10px;border-bottom:1px solid #eee;}
        .tbl th{background:#fafafa;font-weight:600;}
        .badge{padding:6px 8px;border-radius:6px;background:#f0f0f0;}
    </style>
</head>
<body>

<?php include('header.php'); ?>

<div class="main-content">
    <div class="header"><h2>Fees History</h2></div>

    <div class="wrap">
        <form method="get" class="controls">
            <input type="text" name="q" placeholder="Search by name, email or payment id" value="<?php echo htmlspecialchars($search); ?>">
            <select name="class_id">
                <option value="0">All Classes</option>
                <?php while($cl = mysqli_fetch_assoc($classes)): ?>
                    <option value="<?php echo $cl['id']; ?>" <?php if ($class_id==$cl['id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($cl['class_name'] . ($cl['section'] ? ' - '.$cl['section'] : '')); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <input type="date" name="start" value="<?php echo htmlspecialchars($start); ?>">
            <input type="date" name="end" value="<?php echo htmlspecialchars($end); ?>">
            <button class="btn">Filter</button>
            <a href="fee_collection.php" class="btn" style="background:#666;margin-left:auto;">Back to Collection</a>
        </form>

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
                    while($r = mysqli_fetch_assoc($res)): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo $r['id']; ?></td>
                        <td><?php echo htmlspecialchars($r['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($r['class_name']); ?></td>
                        <td>₹<?php echo number_format($r['amount']); ?></td>
                        <td><?php echo htmlspecialchars($r['method']); ?></td>
                        <td><?php echo htmlspecialchars($r['note']); ?></td>
                        <td><?php echo date("d M Y H:i", strtotime($r['payment_date'])); ?></td>
                        <td><?php echo htmlspecialchars($r['created_by']); ?></td>
                    </tr>
                <?php endwhile; else: ?>
                    <tr><td colspan="9" style="text-align:center;padding:18px;color:#666;">No payments found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- pagination -->
        <div style="margin-top:12px;">
            <?php
            $base = '?q='.urlencode($search).'&class_id='.$class_id.'&start='.urlencode($start).'&end='.urlencode($end).'&';
            if ($page>1) echo '<a class="btn" href="'.$base.'page='.($page-1).'">Prev</a> ';
            for ($p=max(1,$page-3); $p<=min($total_pages,$page+3); $p++) {
                $cls = $p==$page ? 'style="background:#111;color:#fff;padding:6px 9px;border-radius:6px;margin-right:6px;"' : 'style="margin-right:6px;padding:6px 9px;border-radius:6px;border:1px solid #ddd;"';
                echo '<a '.$cls.' href="'.$base.'page='.$p.'">'.$p.'</a>';
            }
            if ($page<$total_pages) echo ' <a class="btn" href="'.$base.'page='.($page+1).'">Next</a>';
            ?>
        </div>

    </div>
</div>

<?php include __DIR__ . '/../user/asset/footer.php'; ?>

</body>
</html>
