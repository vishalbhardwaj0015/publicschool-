<?php
session_start();
include('../config/config.php');

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn-login'])) {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email)) $errors[] = "Email is required.";
    if (empty($password)) $errors[] = "Password is required.";

    if (empty($errors)) {

        $query = mysqli_query($con, "SELECT * FROM userform WHERE email='$email'");

        if (mysqli_num_rows($query) > 0) {
            $user = mysqli_fetch_assoc($query);

            if (password_verify($password, $user['password'])) {

                $_SESSION['user_id']   = $user['id'];
                $_SESSION['username']  = $user['name'];
                $_SESSION['email']     = $user['email'];
                $_SESSION['role']      = $user['role'];
                $_SESSION['role_id']   = $user['role_id'];

                // Route by role: teachers to their own dashboard, students to student portal
                if (intval($user['role_id']) === 2) {
                    header("Location: teacher_home.php");
                } else {
                    header("Location: home.php");
                }
                exit();

            } else {
                $errors[] = "Incorrect password.";
            }

        } else {
            $errors[] = "Email not found.";
        }
    }
}
?>

<?php include 'asset/header.php'; ?>

<link rel="stylesheet" href="style/login.css?v=<?php echo time(); ?>">

<div class="login-page">
    <div class="login-card">

        <div class="login-head">
            <div class="brand-badge"><i class="fa-solid fa-school"></i></div>
            <h2>Teacher / Student Login</h2>
            <p>HIM PUBLIC SCHOOL — Parent &amp; Student Portal</p>
        </div>

        <?php if (!empty($errors)): ?>
        <div class="error-box">
            <?php foreach ($errors as $e): ?>
                <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($e); ?><br>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <form action="" method="post">

            <div class="form-group">
                <label>Email Address</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-envelope"></i>
                    <input type="email" name="email" placeholder="Enter your Email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group">
                <label>Password</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password" placeholder="Enter your Password">
                </div>
            </div>

            <button type="submit" name="btn-login" class="btn-login">Login</button>

        </form>

        <div class="login-foot">
            <span>New student?</span>
            <a href="register.php"> &nbsp;Apply for Admission</a>
        </div>

    </div>
</div>

<?php include 'asset/footer.php'; ?>
