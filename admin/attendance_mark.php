<?php
session_start();
include('../config/config.php');

if (!isset($_SESSION['admin_name'])) {
    header("Location: login.php");
    exit;
}

$msg = "";
$error = "";

/* ---------- handle form submit (save attendance) ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_attendance'])) {
    $att_date = !empty($_POST['date']) ? mysqli_real_escape_string($con, $_POST['date']) : date('Y-m-d');
    $class_id = intval($_POST['class_id']);

    if (!$class_id) {
        $error = "Please select a class.";
    } else {
        $saved = 0;
        foreach ($_POST as $key => $val) {
            if (strpos($key, 'student_') === 0) {
                $sid = intval(str_replace('student_', '', $key));
                $status = mysqli_real_escape_string($con, $val);

                $check_q = "SELECT id FROM attendance WHERE student_id = $sid AND date = '$att_date' LIMIT 1";
                $check_r = mysqli_query($con, $check_q);
                if ($check_r && mysqli_num_rows($check_r) > 0) {
                    $row = mysqli_fetch_assoc($check_r);
                    $aid = intval($row['id']);
                    $upd = "UPDATE attendance SET status = '$status', class_id = $class_id WHERE id = $aid";
                    mysqli_query($con, $upd);
                } else {
                    $ins = "INSERT INTO attendance (student_id, class_id, date, status) VALUES ($sid, $class_id, '$att_date', '$status')";
                    mysqli_query($con, $ins);
                }
                $saved++;
            }
        }

        if ($saved > 0) {
            $msg = "Attendance saved for $saved students on " . date("d M Y", strtotime($att_date));
        } else {
            $error = "No attendance data to save.";
        }
    }
}

/* ---------- get classes for select ---------- */
$classes_q = "SELECT * FROM classes ORDER BY id ASC";
$classes_r = mysqli_query($con, $classes_q);

/* ---------- if class selected, fetch its students ---------- */
$selected_class = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;
$selected_date = isset($_GET['date']) ? mysqli_real_escape_string($con, $_GET['date']) : date('Y-m-d');

$students = [];
if ($selected_class) {
    $stu_q = "SELECT u.id, u.name, u.photo, u.admission_date
             FROM userform u
             WHERE (u.role = 'student' OR u.role_id = 1 OR u.role = 'Student' OR u.role IS NULL)
               AND u.student_class = $selected_class
             ORDER BY u.name ASC";
    $stu_r = mysqli_query($con, $stu_q);
    if ($stu_r) {
        while ($row = mysqli_fetch_assoc($stu_r)) {
            $students[] = $row;
        }
    }
}

/* ---------- fetch existing attendance map for selected date/class ---------- */
$existing_map = [];
if ($selected_class && $selected_date) {
    $map_q = "SELECT student_id, status FROM attendance WHERE class_id = $selected_class AND date = '$selected_date'";
    $map_r = mysqli_query($con, $map_q);
    if ($map_r) {
        while ($m = mysqli_fetch_assoc($map_r)) {
            $existing_map[intval($m['student_id'])] = $m['status'];
        }
    }
}

include('header.php');
?>
        </div>

        <div class="content-header"><h2>Attendance Marking</h2></div>

        <?php if ($msg): ?>
            <div class="msg-success"><?php echo htmlspecialchars($msg); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="msg-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="form-card" style="max-width:100%;">
            <form method="get" class="controls">
                <label>Class:</label>
                <select name="class_id" required onchange="this.form.submit()">
                    <option value="">-- Select class --</option>
                    <?php while ($c = mysqli_fetch_assoc($classes_r)):
                        $cid = intval($c['id']);
                        $sel = $cid === $selected_class ? 'selected' : '';
                    ?>
                        <option value="<?php echo $cid; ?>" <?php echo $sel; ?>><?php echo htmlspecialchars($c['class_name'] . ($c['section'] ? ' - ' . $c['section'] : '')); ?></option>
                    <?php endwhile; ?>
                </select>

                <label>Date:</label>
                <input type="date" name="date" value="<?php echo htmlspecialchars($selected_date); ?>" onchange="this.form.submit()">

                <a class="btn-admin" href="attendance_view.php?date=<?php echo urlencode($selected_date); ?>">View all (<?php echo date('d M Y', strtotime($selected_date)); ?>)</a>
            </form>
        </div>

        <?php if ($selected_class): ?>
            <form method="post">
                <input type="hidden" name="class_id" value="<?php echo $selected_class; ?>">
                <input type="hidden" name="date" value="<?php echo htmlspecialchars($selected_date); ?>">

                <div class="form-card" style="max-width:100%;">
                    <h3 style="margin-bottom:6px;">Students in Selected Class</h3>
                    <p style="color:#666; font-size:13px;">Mark present / absent / leave for <strong><?php echo date('d M Y', strtotime($selected_date)); ?></strong></p>

                    <?php if (count($students) === 0): ?>
                        <p>No students found in this class.</p>
                    <?php else: ?>
                        <div class="tblWrap">
                            <table class="tbl">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Admission Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1; foreach ($students as $s):
                                        $sid = intval($s['id']);
                                        $name = htmlspecialchars($s['name']);
                                        $adm = $s['admission_date'] ? date('d M Y', strtotime($s['admission_date'])) : '-';
                                        $existing = isset($existing_map[$sid]) ? $existing_map[$sid] : 'Present';
                                    ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        <td><?php echo $name; ?></td>
                                        <td><?php echo $adm; ?></td>
                                        <td>
                                            <label style="margin-right:12px; font-weight:600;"><input type="radio" name="student_<?php echo $sid; ?>" value="Present" <?php echo $existing === 'Present' ? 'checked' : ''; ?>> Present</label>
                                            <label style="margin-right:12px; font-weight:600;"><input type="radio" name="student_<?php echo $sid; ?>" value="Absent" <?php echo $existing === 'Absent' ? 'checked' : ''; ?>> Absent</label>
                                            <label style="margin-right:12px; font-weight:600;"><input type="radio" name="student_<?php echo $sid; ?>" value="Leave" <?php echo $existing === 'Leave' ? 'checked' : ''; ?>> Leave</label>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div style="margin-top:12px;">
                            <button class="btn btn-primary" name="save_attendance" type="submit" style="padding:9px 18px; border-radius:8px; background:#1a7a3d; color:#fff; border:none; cursor:pointer; font-weight:600;">Save Attendance</button>
                        </div>
                    <?php endif; ?>
                </div>
            </form>
        <?php endif; ?>

    </div>
</body>
</html>
