<?php
session_start();
include('../config/config.php');

if(!isset($_SESSION['admin_name'])){
    header("Location: login.php");
    exit;
}

$admin = $_SESSION['admin_name'];

$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$class_id = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;

/* ---------- Get Classes ---------- */
$classes_q = "SELECT * FROM classes ORDER BY id ASC";
$classes_r = mysqli_query($con, $classes_q);

/* ---------- Build Attendance Query ---------- */
$where = " WHERE a.date = '$date' ";

if ($class_id) {
    $where .= " AND a.class_id = $class_id ";
}

$sql = "
SELECT a.*, u.name, u.student_class, c.class_name
FROM attendance a
LEFT JOIN userform u ON a.student_id = u.id
LEFT JOIN classes c ON a.class_id = c.id
$where
ORDER BY a.class_id, u.name ASC
";

$result = mysqli_query($con, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Attendance View - Admin</title>
    <link rel="stylesheet" href="style/index.css?v=<?php echo time(); ?>">

    <style>
        .wrap { padding:20px; }
        .card { background:#fff; padding:18px; border-radius:8px; box-shadow:0 3px 8px rgba(0,0,0,0.05); margin-bottom:16px; }
        .controls { display:flex; gap:10px; align-items:center; }
        .controls select, .controls input[type="date"] {
            padding:8px 10px; border:1px solid #ddd; border-radius:6px;
        }
        .btn { padding:9px 12px; border-radius:6px; background:#111; color:#fff; border:none; cursor:pointer; }
        .tbl { width:100%; border-collapse:collapse; background:#fff;
               border-radius:8px; overflow:hidden; box-shadow:0 3px 8px #0002; }
        .tbl th, .tbl td { padding:12px 10px; border-bottom:1px solid #eee; font-size:14px; }
        .tbl th { background:#fafafa; font-weight:600; }
        .badge {
            padding:6px 10px; border-radius:6px; font-size:13px; font-weight:600; color:#fff;
        }
        .present { background:#2ecc71; }
        .absent { background:#e74c3c; }
        .leave { background:#f1c40f; color:#000; }
    </style>
</head>

<body>

<?php include('header.php'); ?>

<div class="main-content">

    <div class="header">
        <h2>Attendance Records</h2>
    </div>

    <div class="wrap">

        <div class="card">
            <form method="get" class="controls">
                <label>Date:</label>
                <input type="date" name="date" value="<?php echo $date; ?>">

                <label>Class:</label>
                <select name="class_id">
                    <option value="0">All Classes</option>
                    <?php while ($c = mysqli_fetch_assoc($classes_r)): 
                        $cid = intval($c['id']);
                        $sel = $cid === $class_id ? "selected" : "";
                    ?>
                    <option value="<?php echo $cid; ?>" <?php echo $sel; ?>>
                        <?php echo $c['class_name'] . ($c['section'] ? ' - ' . $c['section'] : ''); ?>
                    </option>
                    <?php endwhile; ?>
                </select>

                <button class="btn">Filter</button>
            </form>
        </div>

        <table class="tbl">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student</th>
                    <th>Class</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>

                <?php
                if ($result && mysqli_num_rows($result) > 0):
                    $i = 1;
                    while ($row = mysqli_fetch_assoc($result)):

                        $status = strtolower($row['status']);
                        $badge_class = ($status == "present") ? "present" : (($status == "absent") ? "absent" : "leave");

                ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['class_name']); ?></td>
                    <td><span class="badge <?php echo $badge_class; ?>"><?php echo $row['status']; ?></span></td>
                    <td><?php echo date("d M Y", strtotime($row['date'])); ?></td>
                </tr>
                <?php endwhile; ?>

                <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align:center; padding:20px;">No attendance records found.</td>
                </tr>
                <?php endif; ?>

            </tbody>
        </table>

    </div>
</div>

<?php include __DIR__ . '/../user/asset/footer.php'; ?>

</body>
</html>
