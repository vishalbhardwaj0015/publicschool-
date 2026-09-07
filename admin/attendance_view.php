<?php
session_start();
include('../config/config.php');

if (!isset($_SESSION['admin_name'])) {
    header("Location: login.php");
    exit;
}

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

include('header.php');
?>
        </div>

        <div class="content-header"><h2>Attendance Records</h2></div>

        <div class="form-card" style="max-width:100%;">
            <form method="get" class="controls">
                <label>Date:</label>
                <input type="date" name="date" value="<?php echo htmlspecialchars($date); ?>">

                <label>Class:</label>
                <select name="class_id">
                    <option value="0">All Classes</option>
                    <?php while ($c = mysqli_fetch_assoc($classes_r)):
                        $cid = intval($c['id']);
                        $sel = $cid === $class_id ? "selected" : "";
                    ?>
                    <option value="<?php echo $cid; ?>" <?php echo $sel; ?>>
                        <?php echo htmlspecialchars($c['class_name'] . ($c['section'] ? ' - ' . $c['section'] : '')); ?>
                    </option>
                    <?php endwhile; ?>
                </select>

                <button class="btn-admin">Filter</button>
            </form>
        </div>

        <div class="tblWrap">
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
                            $status = $row['status'];
                            $badge_class = 'badge-secondary';
                            if (strtolower($status) === 'present') $badge_class = 'badge-success';
                            elseif (strtolower($status) === 'absent') $badge_class = 'badge-danger';
                            elseif (strtolower($status) === 'leave') $badge_class = 'badge-warning';
                    ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['class_name']); ?></td>
                        <td><span class="badge <?php echo $badge_class; ?>"><?php echo htmlspecialchars($status); ?></span></td>
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
</body>
</html>
