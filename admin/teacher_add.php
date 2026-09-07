<?php
session_start();
include('../config/config.php');
if (!isset($_SESSION['admin_name'])) { header("Location: login.php"); exit; }

$msg = ""; $error = "";

if (isset($_POST['add_teacher'])) {
    $name   = mysqli_real_escape_string($con, $_POST['name']);
    $email  = mysqli_real_escape_string($con, $_POST['email']);
    $phone  = mysqli_real_escape_string($con, $_POST['phone']);
    $gender = mysqli_real_escape_string($con, $_POST['gender']);
    $dob    = mysqli_real_escape_string($con, $_POST['dob']);
    $subject = mysqli_real_escape_string($con, $_POST['subject']);
    $status = mysqli_real_escape_string($con, $_POST['status']);
    $join_date = date("Y-m-d");
    $password = password_hash("12345", PASSWORD_DEFAULT);

    $photo = "";
    if (!empty($_FILES['photo']['name'])) {
        $filename = time()."_".$_FILES['photo']['name'];
        $target = "../img/teachers/".$filename;
        if (!is_dir("../img/teachers")) mkdir("../img/teachers");
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target)) $photo = "img/teachers/".$filename;
    }

    $check = mysqli_query($con, "SELECT id FROM userform WHERE email='$email' LIMIT 1");
    if (mysqli_num_rows($check) > 0) {
        $error = "A user with this email already exists.";
    } else {
        $sql = "INSERT INTO userform (name,email,phone,gender,dob,teacher_subject,photo,role,role_id,status,password,admission_date)
                VALUES ('$name','$email','$phone','$gender','$dob','$subject','$photo','teacher',2,'$status','$password','$join_date')";
        if (mysqli_query($con, $sql)) { $msg = "Teacher added successfully!"; }
        else { $error = "Database error! Unable to add teacher."; }
    }
}
include('header.php');
?>
<div class="content-header"><h2>Add Teacher</h2></div>

<?php if ($msg): ?><div class="msg-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
<?php if ($error): ?><div class="msg-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="form-card">
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

        <button name="add_teacher" class="btn-admin">Add Teacher</button>
    </form>
</div>

</div>
</body>
</html>
