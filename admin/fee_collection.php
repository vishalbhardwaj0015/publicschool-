<?php
session_start();
include('../config/config.php');

if(!isset($_SESSION['admin_name'])){
    header("Location: login.php");
    exit;
}

$success = "";
$error = "";

/* --------------------  SEARCH STUDENT  -------------------- */

$student = null;
$fee = null;

if (isset($_GET['search'])) {
    $query = trim($_GET['search']);
    $query = mysqli_real_escape_string($con, $query);

    $sql = "
        SELECT u.*, c.class_name 
        FROM userform u 
        LEFT JOIN classes c ON u.student_class = c.id
        WHERE 
            (u.role = 'student' OR u.role_id = 1)
            AND (u.name LIKE '%$query%' OR u.email LIKE '%$query%' OR u.id = '$query')
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

    // fetch fee row
    $fee_res = mysqli_query($con, "SELECT * FROM fees WHERE student_id = $sid LIMIT 1");
    $fee = mysqli_fetch_assoc($fee_res);

    if ($fee) {
        // insert payment record first
        $ins = mysqli_query($con, "INSERT INTO payments (student_id, amount, method, note, created_by) 
                                   VALUES ($sid, $amount, '$method', '$note', " . ($admin_by ? "'$admin_by'" : "NULL") . ")");

        if ($ins) {
            // update totals in fees table
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

?>

<!DOCTYPE html>
<html>
<head>
    <title>Fee Collection - Admin</title>
    <link rel="stylesheet" href="style/index.css?v=<?php echo time(); ?>">
    <style>
        .wrap { padding:20px; }
        .search-box { display:flex; gap:10px; margin-bottom:20px; }
        .search-box input {
            flex:1; padding:10px; border:1px solid #ddd; border-radius:6px;
        }
        .btn { padding:10px 15px; border-radius:6px; background:#111; color:#fff; text-decoration:none; border:none; cursor:pointer; }
        .card-box { background:#fff; padding:20px; border-radius:8px; box-shadow:0 3px 8px #0002; margin-bottom:20px; }
        table { width:100%; border-collapse:collapse; margin-top:15px; }
        table th, table td { border-bottom:1px solid #eee; padding:10px; text-align:left; }
        .msg-success { padding:12px; background:#c2ffd0; color:#006600; border-radius:6px; margin-bottom:10px; }
        .msg-error { padding:12px; background:#ffd2d2; color:#a10000; border-radius:6px; margin-bottom:10px; }
    </style>
</head>

<body>

<?php include('header.php'); ?>

<div class="main-content">

    <div class="header">
        <h2>Fee Collection</h2>
    </div>

    <div class="wrap">

        <?php if ($success): ?>
            <div class="msg-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="msg-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- SEARCH STUDENT -->
        <form class="search-box" method="get">
            <input type="text" name="search" placeholder="Search student by ID, Name, Email..." required>
            <button class="btn">Search</button>
        </form>


        <?php if ($student): ?>
        <div class="card-box">
            <h3>Student Details</h3>
            <p><strong>Name:</strong> <?php echo $student['name']; ?></p>
            <p><strong>Email:</strong> <?php echo $student['email']; ?></p>
            <p><strong>Class:</strong> <?php echo $student['class_name']; ?></p>
            <p><strong>Phone:</strong> <?php echo $student['phone']; ?></p>

            <hr><br>

            <h3>Fee Details</h3>

            <?php if ($fee): ?>
            <table>
                <tr><th>Total Fee</th><td>₹<?php echo $fee['total_fee']; ?></td></tr>
                <tr><th>Paid Fee</th><td>₹<?php echo $fee['paid_fee']; ?></td></tr>
                <tr><th>Pending Fee</th><td>₹<?php echo $fee['pending_fee']; ?></td></tr>
                <tr><th>Last Payment</th><td><?php echo $fee['last_payment'] ?: '-'; ?></td></tr>
                <tr><th>Status</th><td><?php echo ucfirst($fee['status']); ?></td></tr>
            </table>

            <br>

            <!-- PAY FEE FORM -->
            <form method="post">
                <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
                <input type="number" name="amount" placeholder="Enter amount to pay" required
                       style="padding:10px; width:200px; border:1px solid #ddd; border-radius:6px;">
                <button name="pay_fee" class="btn">Pay Fee</button>
            </form>

            <?php else: ?>
                <p style="color:#666;">No fee record found for this student.</p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

    </div>

</div>

<?php include __DIR__ . '/../user/asset/footer.php'; ?>

</body>
</html>
