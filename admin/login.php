<?php
session_start();
include('../config/config.php');

// --- FIRST TIME INSTALL MODE ---
// If admin table is empty → redirect to create admin page
$chk = mysqli_query($con, "SELECT id FROM admin LIMIT 1");
if(mysqli_num_rows($chk) == 0){
    header("Location: admin_create.php");
    exit;
}

$error = "";

// If already logged in
if (isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

// When login form submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {

        $query = mysqli_query($con, "SELECT * FROM admin WHERE email='$email' LIMIT 1");

        if (mysqli_num_rows($query) > 0) {

            $row = mysqli_fetch_assoc($query);

            // PASSWORD VERIFY
            if (password_verify($password, $row['password'])) {

                // SET SESSIONS
                $_SESSION['admin_id']    = $row['id'];
                $_SESSION['admin_name']  = $row['name'];
                $_SESSION['admin_email'] = $row['email'];
                $_SESSION['admin_photo'] = $row['photo'];
                $_SESSION['admin_role']  = $row['role'];

                mysqli_query($con, "UPDATE admin SET last_login = NOW() WHERE id=".$row['id']);

                header("Location: index.php");
                exit;

            } else {
                $error = "Incorrect Password!";
            }

        } else {
            $error = "Admin with this email not found!";
        }

    } else {
        $error = "All fields are required!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link rel="stylesheet" href="style/login.css?v=<?php echo time(); ?>">
</head>

<body>

<div class="login-container">

    <div class="login-box">

        <h2>Admin Login</h2>

        <?php if($error): ?>
            <div class="error-box"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="post">

            <label>Email</label>
            <input type="email" name="email" placeholder="Enter email" required>

            <label>Password</label>
            <input type="password" name="password" placeholder="Enter password" required>

            <button class="btn-login">Login</button>

        </form>

    </div>

</div>

</body>
</html>
