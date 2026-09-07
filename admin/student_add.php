<?php
session_start();
include('../config/config.php');

if (!isset($_SESSION['admin_name'])) {
    header("Location: login.php");
    exit;
}

$msg = "";
$error = "";

$class_q = mysqli_query($con, "SELECT * FROM classes ORDER BY class_name ASC");

if (isset($_POST['add_student'])) {

    $name   = mysqli_real_escape_string($con, $_POST['name']);
    $email  = mysqli_real_escape_string($con, $_POST['email']);
    $phone  = mysqli_real_escape_string($con, $_POST['phone']);
    $gender = mysqli_real_escape_string($con, $_POST['gender']);
    $dob    = mysqli_real_escape_string($con, $_POST['dob']);
    $class  = intval($_POST['class']);
    $status = mysqli_real_escape_string($con, $_POST['status']);
    $admission_date = date("Y-m-d");
    $password = password_hash("12345", PASSWORD_DEFAULT);

    $photo = "";
    if (!empty($_FILES['photo']['name'])) {
        $filename = time()."_".$_FILES['photo']['name'];
        $target = "../img/students/".$filename;
        if (!is_dir("../img/students")) mkdir("../img/students");
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target)) $photo = "img/students/".$filename;
    }

    $check = mysqli_query($con, "SELECT id FROM userform WHERE email='$email' LIMIT 1");
    if (mysqli_num_rows($check) > 0) {
        $error = "A user with this email already exists.";
    } else {
        $sql = "INSERT INTO userform (name,email,phone,gender,dob,student_class,photo,role_id,status,password,admission_date)
                VALUES ('$name','$email','$phone','$gender','$dob',$class,'$photo',1,'$status','$password','$admission_date')";
        if (mysqli_query($con, $sql)) {
            $student_id = mysqli_insert_id($con);
            mysqli_query($con, "INSERT INTO admissions (student_id,class_id,admission_date,status) VALUES ($student_id,$class,'$admission_date','active')");
            mysqli_query($con, "INSERT INTO fees (student_id,total_fee,paid_fee,pending_fee,status) VALUES ($student_id,24000,0,24000,'pending')");
            $msg = "Student added successfully!";
        } else {
            $error = "Database error! Unable to add student.";
        }
    }
}
include('header.php');
?>
<div class="content-header"><h2>Add Student</h2></div>

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

        <label>Select Class</label>
        <select name="class" required>
            <option value="">-- Choose class --</option>
            <?php while ($c = mysqli_fetch_assoc($class_q)): ?>
            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['class_name'].($c['section'] ? " - ".$c['section'] : "")) ?></option>
            <?php endwhile; ?>
        </select>

        <label>Upload Photo (optional)</label>
        <input type="file" name="photo" accept="image/*">

        <label>Status</label>
        <select name="status">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>

        <button name="add_student" class="btn-admin">Add Student</button>
    </form>
</div>

</div>
</body>
</html>
