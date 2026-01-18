<?php
session_start();
include('E:\XAMPP\htdocs\PUBLICSCHOOL\config\config.php');

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

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['name'];
                $_SESSION['role'] = $user['role'];

                header("Location: home.php");
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

<?php include 'asset\header.php'; ?>

<link rel="stylesheet" href="style/login.css?v=<?php echo time(); ?>">

<div class="login-wrapper">
    <div class="login-card">

        <form action="" method="post">

            <h2>Login</h2>

            <?php if (!empty($errors)): ?>
            <div class="error-box">
                <?php foreach ($errors as $e): ?>
                    • <?= htmlspecialchars($e); ?><br>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="Enter your Email">
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your Password">
            </div>

            <div class="buttons-row">
              <button type="submit" name="btn-login" class="btn-login">Login</button>
              <button type="button" name="btn-forget" class="btn-forget">Forget</button>
            </div>
            

        </form>

    </div>
</div>

<?php include 'asset\footer.php'; ?>
