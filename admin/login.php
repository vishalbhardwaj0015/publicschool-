<?php
session_start();
include('../config/config.php');

$error = "";

/* If already logged in */
if (isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

/* When login form submitted */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {

        $query = mysqli_query($con, "SELECT * FROM admin WHERE email='$email' LIMIT 1");

        if (mysqli_num_rows($query) > 0) {

            $row = mysqli_fetch_assoc($query);

            if (password_verify($password, $row['password'])) {

                $_SESSION['admin_id']    = $row['id'];
                $_SESSION['admin_name']  = $row['name'];
                $_SESSION['admin_email'] = $row['email'];
                $_SESSION['admin_photo'] = $row['photo'];
                $_SESSION['admin_role']  = $row['role'];

                mysqli_query($con, "UPDATE admin SET last_login = NOW() WHERE id=" . $row['id']);

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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | HIM PUBLIC SCHOOL</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: "Segoe UI", Poppins, sans-serif; }
        body {
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            background:
                radial-gradient(circle at 15% 20%, rgba(122,84,255,0.18), transparent 45%),
                radial-gradient(circle at 85% 80%, rgba(47,107,255,0.2), transparent 45%),
                #14213d;
        }
        .login-card {
            background: #fff; border-radius: 22px;
            padding: 44px 40px; width: 100%; max-width: 410px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.35);
        }
        .login-head { text-align: center; margin-bottom: 26px; }
        .brand-badge {
            width: 74px; height: 74px; margin: 0 auto 16px;
            border-radius: 18px; background: linear-gradient(135deg,#14213d,#2f6bff);
            display: flex; align-items: center; justify-content: center;
            font-size: 30px; color: #fff; box-shadow: 0 8px 20px rgba(47,107,255,0.35);
        }
        .login-head h2 { font-weight: 800; color: #14213d; margin: 0 0 4px; font-size: 24px; }
        .login-head p { color: #7c8596; font-size: 14px; margin: 0; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 7px; font-size: 14px; color: #14213d; }
        .input-wrap { position: relative; }
        .input-wrap i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #9aa5b8; font-size: 15px; }
        .input-wrap input {
            width: 100%; padding: 13px 14px 13px 42px;
            border: 1px solid #d8dfec; border-radius: 10px;
            background: #f7f9fd; font-size: 15px; transition: 0.25s;
        }
        .input-wrap input:focus { border-color: #2f6bff; background:#fff; outline: none; box-shadow: 0 0 0 4px rgba(47,107,255,0.12); }
        .btn-submit {
            width: 100%; padding: 13px; margin-top: 6px;
            background: linear-gradient(135deg,#2f6bff,#1f4fd8); color:#fff;
            border: none; border-radius: 10px; font-size: 16px; font-weight: 700;
            cursor: pointer; transition: 0.25s; box-shadow: 0 6px 16px rgba(47,107,255,0.3);
        }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 10px 24px rgba(47,107,255,0.4); }
        .msg-error { padding: 12px 14px; background: #fdecec; color: #c62828; border-left: 4px solid #e53935; border-radius: 8px; margin-bottom: 18px; font-size: 14px; }
        .back-home { display:block; text-align:center; margin-top: 20px; color:#7c8596; font-size:14px; text-decoration:none; }
        .back-home:hover { color:#2f6bff; }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-head">
        <div class="brand-badge"><i class="fa-solid fa-user-shield"></i></div>
        <h2>Admin Login</h2>
        <p>HIM PUBLIC SCHOOL — Admin Panel</p>
    </div>

    <?php if ($error): ?>
        <div class="msg-error"><i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label>Email</label>
            <div class="input-wrap"><i class="fa-solid fa-envelope"></i><input type="email" name="email" placeholder="Enter email" required></div>
        </div>
        <div class="form-group">
            <label>Password</label>
            <div class="input-wrap"><i class="fa-solid fa-lock"></i><input type="password" name="password" placeholder="Enter password" required></div>
        </div>
        <button class="btn-submit" type="submit">Login</button>
    </form>

    <a href="../index.php" class="back-home"><i class="fa-solid fa-arrow-left"></i> Back to Website</a>
</div>

</body>
</html>
