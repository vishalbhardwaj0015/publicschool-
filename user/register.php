<?php
session_start();
include('../config/config.php');

$errors = [];
$success = "";

$classes_q = mysqli_query($con, "SELECT * FROM classes ORDER BY class_name ASC");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn-register'])) {

    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);
    $gender   = $_POST['gender'] ?? '';
    $dob      = $_POST['dob'];
    $class    = intval($_POST['student_class']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    if (empty($name)) $errors[] = "Name is required.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Enter a valid email.";
    if (empty($phone) || !preg_match('/^[0-9]{10}$/', $phone)) $errors[] = "Enter a valid 10-digit phone number.";
    if (empty($gender)) $errors[] = "Please select gender.";
    if (empty($dob)) $errors[] = "Date of birth is required.";
    if (!$class) $errors[] = "Please select a class.";
    if (strlen($password) < 4) $errors[] = "Password must be at least 4 characters.";
    if ($password !== $confirm) $errors[] = "Passwords do not match.";

    if (empty($errors)) {
        $check = mysqli_query($con, "SELECT id FROM userform WHERE email='$email' LIMIT 1");
        if (mysqli_num_rows($check) > 0) {
            $errors[] = "This email is already registered.";
        }
    }

    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $admission_date = date("Y-m-d");

        $sql = "INSERT INTO userform
                (name, email, phone, gender, dob, student_class, photo, role, role_id, status, password, admission_date)
                VALUES
                ('$name', '$email', '$phone', '$gender', '$dob', $class, '', 'student', 1, 'active', '$hashed', '$admission_date')";

        if (mysqli_query($con, $sql)) {
            $sid = mysqli_insert_id($con);
            mysqli_query($con, "INSERT INTO admissions (student_id, class_id, admission_date, status) VALUES ($sid, $class, '$admission_date', 'active')");
            mysqli_query($con, "INSERT INTO fees (student_id, total_fee, paid_fee, pending_fee, status) VALUES ($sid, 24000, 0, 24000, 'pending')");
            $success = "Registration successful! You can now login with your email and password.";
            $_POST = [];
        } else {
            $errors[] = "Database error. Please try again.";
        }
    }
}
?>

<?php include 'asset/header.php'; ?>

<link rel="stylesheet" href="style/register.css?v=<?= time() ?>">

<div class="register-page">
    <div class="register-card">

        <div class="register-head">
            <div class="reg-badge"><i class="fa-solid fa-user-plus"></i></div>
            <h2>New Student Admission</h2>
            <p>HIM PUBLIC SCHOOL — Register your child today</p>
        </div>

        <?php if ($success): ?>
            <div class="reg-ok">
                <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($success) ?>
                <div class="reg-ok-actions">
                    <a href="login.php" class="btn-reg-ok">Login Now</a>
                </div>
            </div>
        <?php else: ?>

            <?php if (!empty($errors)): ?>
                <div class="reg-errors">
                    <?php foreach ($errors as $e): ?><div><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($e) ?></div><?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="post" action="">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="reg-input" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="reg-input" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="reg-input" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="reg-input" required>
                            <option value="">-- Select --</option>
                            <option <?= (($_POST['gender'] ?? '') === 'Male') ? 'selected' : '' ?>>Male</option>
                            <option <?= (($_POST['gender'] ?? '') === 'Female') ? 'selected' : '' ?>>Female</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="dob" class="reg-input" value="<?= htmlspecialchars($_POST['dob'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Select Class</label>
                        <select name="student_class" class="reg-input" required>
                            <option value="">-- Choose class --</option>
                            <?php while ($c = mysqli_fetch_assoc($classes_q)): ?>
                                <option value="<?= $c['id'] ?>" <?= ($_POST['student_class'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['class_name'] . ($c['section'] ? ' - ' . $c['section'] : '')) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="reg-input" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="confirm_password" class="reg-input" required>
                    </div>
                </div>

                <button type="submit" name="btn-register" class="btn-register">Register</button>
            </form>

            <div class="login-link">
                Already have an account? <a href="login.php">Login here</a>
            </div>

        <?php endif; ?>
    </div>
</div>

<?php include 'asset/footer.php'; ?>
