<?php
session_start();
include('../config/config.php');

if (!isset($_SESSION['admin_name'])) {
    header("Location: login.php");
    exit;
}

$msg = "";
$error = "";

// Fetch classes
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

    // PHOTO UPLOAD
    $photo = "";
    if (!empty($_FILES['photo']['name'])) {

        $filename = time() . "_" . $_FILES['photo']['name'];
        $target = "../img/students/" . $filename;

        if (!is_dir("../img/students")) {
            mkdir("../img/students");
        }

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
            $photo = "img/students/" . $filename;
        }
    }

   
    $sql = "
        INSERT INTO userform
        (name, email, phone, gender, dob, student_class, photo, role_id, status, password, admission_date)
        VALUES
        ('$name', '$email', '$phone', '$gender', '$dob', $class, '$photo', 1, '$status', '$password', '$admission_date')
    ";

    if (mysqli_query($con, $sql)) {

        $student_id = mysqli_insert_id($con);

   
        mysqli_query($con, "
            INSERT INTO admissions (student_id, class_id, admission_date, status)
            VALUES ($student_id, $class, '$admission_date', 'active')
        ");

        $msg = "Student added successfully!";
    } else {
        $error = "Database error! Unable to add student.";
    }
}
?>

<link rel="stylesheet" href="style/student_add.css?v=<?php echo time(); ?>">
<?php include('header.php'); ?>

<div class="main-content">

    <div class="header">
        <h2>Add Student</h2>
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

                <label>Select Class</label>
                <select name="class" required>
                    <option value="">-- Choose class --</option>
                    <?php while ($c = mysqli_fetch_assoc($class_q)): ?>
                        <option value="<?= $c['id'] ?>"><?= $c['class_name'] ?><?= $c['section'] ? " - " . $c['section'] : "" ?></option>
                    <?php endwhile; ?>
                </select>

                <label>Upload Photo (optional)</label>
                <input type="file" name="photo" accept="image/*">

                <label>Status</label>
                <select name="status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>

                <button name="add_student" class="btn">Add Student</button>
            </form>

        </div>
    </div>

</div>

<?php include __DIR__ . '/../user/asset/footer.php'; ?>
