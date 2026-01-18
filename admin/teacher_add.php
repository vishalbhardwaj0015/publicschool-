<?php
session_start();
include('../config/config.php');

if (!isset($_SESSION['admin_name'])) {
    header("Location: login.php");
    exit;
}

$msg = "";
$error = "";

/* -------------------- FORM SUBMIT -------------------- */
if (isset($_POST['add_teacher'])) {

    $name   = mysqli_real_escape_string($con, $_POST['name']);
    $email  = mysqli_real_escape_string($con, $_POST['email']);
    $phone  = mysqli_real_escape_string($con, $_POST['phone']);
    $gender = mysqli_real_escape_string($con, $_POST['gender']);
    $dob    = mysqli_real_escape_string($con, $_POST['dob']);
    $subject = mysqli_real_escape_string($con, $_POST['subject']);
    $status = mysqli_real_escape_string($con, $_POST['status']);
    $join_date = date("Y-m-d");

    // Auto-generate password (simple)
    $password = password_hash("12345", PASSWORD_DEFAULT);

    /* -------------------- Photo Upload -------------------- */
    $photo = "";
    if (!empty($_FILES['photo']['name'])) {
        $filename = time() . "_" . $_FILES['photo']['name'];
        $target = "../img/teachers/" . $filename;

        if (!is_dir("../img/teachers")) {
            mkdir("../img/teachers");
        }

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
            $photo = "img/teachers/" . $filename;
        }
    }

    /* -------------------- INSERT INTO userform -------------------- */
    $sql = "
        INSERT INTO userform
        (name, email, phone, gender, dob, teacher_subject, photo, role, role_id, status, password, admission_date)
        VALUES
        ('$name', '$email', '$phone', '$gender', '$dob', '$subject', '$photo', 'teacher', 2, '$status', '$password', '$join_date')
    ";

    if (mysqli_query($con, $sql)) {
        $msg = "Teacher added successfully!";
    } else {
        $error = "Database error! Unable to add teacher.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Teacher</title>
    <link rel="stylesheet" href="style/index.css?v=<?php echo time(); ?>">
    
    <style>
        .wrap { padding:20px; }
        .card {
            background:#fff; padding:22px; border-radius:8px; 
            box-shadow:0 3px 8px #0002; width:650px;
        }
        label { display:block; margin-bottom:6px; font-weight:600; }
        input, select, textarea {
            width:100%; padding:10px; border:1px solid #ddd; 
            border-radius:6px; margin-bottom:15px; font-size:14px;
        }
        .btn { 
            padding:10px 14px; background:#111; color:#fff;
            border:none; border-radius:6px; cursor:pointer;
        }
        .msg-success { padding:10px; background:#d8ffd8; color:#006600; margin-bottom:10px; border-radius:6px; }
        .msg-error { padding:10px; background:#ffdada; color:#a10000; margin-bottom:10px; border-radius:6px; }
    </style>
</head>

<body>

<?php include('header.php'); ?>

<div class="main-content">

    <div class="header">
        <h2>Add Teacher</h2>
    </div>

    <div class="wrap">
        <div class="card">

            <?php if ($msg): ?>
                <div class="msg-success"><?= $msg ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="msg-error"><?= $error ?></div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">

                <label>Full Name</label>
                <input type="text" name="name" required>

                <label>Email</label>
                <input type="email" name="email" required>

                <label>Phone</label>
                <input type="text" name="phone" required>

                <label>Gender</label>
                <select name="gender" required>
                    <option value="">-- Select gender --</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>

                <label>Date of Birth</label>
                <input type="date" name="dob" required>

                <label>Subject</label>
                <input type="text" name="subject" placeholder="e.g. English, Math, Science" required>

                <label>Upload Photo (optional)</label>
                <input type="file" name="photo" accept="image/*">

                <label>Status</label>
                <select name="status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>

                <button name="add_teacher" class="btn">Add Teacher</button>

            </form>

        </div>
    </div>

</div>

<?php include __DIR__ . '/../user/asset/footer.php'; ?>

</body>
</html>
