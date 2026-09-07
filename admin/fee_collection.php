<?php
session_start();
include('../config/config.php');

if (!isset($_SESSION['admin_name'])) {
    header("Location: login.php");
    exit;
}

$success = "";
$error = "";

$student = null;
$fee = null;

/* --------------------  SEARCH STUDENT  -------------------- */
if (isset($_GET['search'])) {
    $query = trim($_GET['search']);
    $query_esc = mysqli_real_escape_string($con, $query);

    $sql = "
        SELECT u.*, c.class_name 
        FROM userform u 
        LEFT JOIN classes c ON u.student_class = c.id
        WHERE 
            (u.role = 'student' OR u.role_id = 1)
            AND (u.name LIKE '%$query_esc%' OR u.email LIKE '%$query_esc%' OR u.id = '$query_esc')
        LIMIT 1
    ";

    $result = mysqli_query($con, $sql);
    $student = mysqli_fetch_assoc($result);

    if ($student) {
        $sid = $student['id'];
        $fee_res = mysqli_query($con, "SELECT * FROM fees WHERE student_id = $sid LIMIT 1");
        $fee = mysqli_fetch_assoc($fee_res);
    } else {
        $error = "No student found!";
    }
}

/* --------------------  PAY FEE  -------------------- */
if (isset($_POST['pay_fee'])) {
    $sid = intval($_POST['student_id']);
    $amount = intval($_POST['amount']);
    $method = mysqli_real_escape_string($con, $_POST['method'] ?? 'cash');
    $note = mysqli_real_escape_string($con, $_POST['note'] ?? '');
    $admin_by = isset($_SESSION['admin_name']) ? mysqli_real_escape_string($con, $_SESSION['admin_name']) : null;

    $fee_res = mysqli_query($con, "SELECT * FROM fees WHERE student_id = $sid LIMIT 1");
    $fee = mysqli_fetch_assoc($fee_res);

    if ($fee) {
        $ins = mysqli_query($con, "INSERT INTO payments (student_id, amount, method, note, created_by) 
                                   VALUES ($sid, $amount, '$method', '$note', " . ($admin_by ? "'$admin_by'" : "NULL") . ")");

        if ($ins) {
            $new_paid = $fee['paid_fee'] + $amount;
            $new_pending = max(0, $fee['total_fee'] - $new_paid);

            $update = mysqli_query($con, "
                UPDATE fees 
                SET paid_fee = '$new_paid', 
                    pending_fee = '$new_pending', 
                    last_payment = NOW(),
                    status = IF($new_pending = 0, 'paid', 'partial')
                WHERE student_id = $sid
            ");

            if ($update) {
                $success = "Payment recorded successfully!";
            } else {
                $error = "Error updating fee totals.";
            }
        } else {
            $error = "Error recording payment.";
        }
    } else {
        $error = "No fee record found for this student.";
    }
}

include('header.php');
?>
        </div>

        <div class="content-header"><h2>Fee Collection</h2></div>

        <?php if ($success): ?>
            <div class="msg-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="msg-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="get" class="controls" style="max-width:700px; margin-bottom:20px;">
            <input type="text" name="search" placeholder="Search student by ID, Name, Email..." required style="flex:1;">
            <button class="btn-admin">Search</button>
        </form>

        <?php if ($student): ?>
        <div class="form-card">
            <h3 style="margin-bottom:12px;">Student Details</h3>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($student['name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($student['email']); ?></p>
            <p><strong>Class:</strong> <?php echo htmlspecialchars($student['class_name']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($student['phone']); ?></p>

            <hr style="margin:16px 0;">

            <h3 style="margin-bottom:12px;">Fee Details</h3>

            <?php if ($fee): ?>
            <div class="tblWrap" style="max-width:500px;">
                <table class="tbl">
                    <tr><th style="width:160px;">Total Fee</th><td>&#8377;<?php echo number_format($fee['total_fee']); ?></td></tr>
                    <tr><th>Paid Fee</th><td>&#8377;<?php echo number_format($fee['paid_fee']); ?></td></tr>
                    <tr><th>Pending Fee</th><td>&#8377;<?php echo number_format($fee['pending_fee']); ?></td></tr>
                    <tr><th>Last Payment</th><td><?php echo $fee['last_payment'] ? htmlspecialchars($fee['last_payment']) : '-'; ?></td></tr>
                    <tr><th>Status</th><td><?php echo htmlspecialchars(ucfirst($fee['status'])); ?></td></tr>
                </table>
            </div>

            <br>
            <h3 style="margin-bottom:12px;">Pay Fee</h3>
            <form method="post" class="controls">
                <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
                <input type="number" name="amount" placeholder="Enter amount" required style="padding:10px; border:1px solid #d7dee9; border-radius:8px;">
                <button name="pay_fee" class="btn-admin btn-green">Pay Fee</button>
            </form>

            <?php else: ?>
                <p style="color:#666;">No fee record found for this student.</p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

    </div>
</body>
</html>
