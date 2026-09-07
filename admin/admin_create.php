<?php
session_start();
include('../config/config.php');

$success = "";
$error = "";

/* If admin already exists, allow only logged-in admin */
$check = mysqli_query($con, "SELECT id FROM admin LIMIT 1");
$adminExists = mysqli_num_rows($check) > 0;

if ($adminExists && !isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_POST['create_admin'])) {

    $name  = mysqli_real_escape_string($con, trim($_POST['name']));
    $email = mysqli_real_escape_string($con, trim($_POST['email']));
    $password = trim($_POST['password']);

    if (empty($name) || empty($email) || empty($password)) {
        $error = "All fields are required!";
    } else {

        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $photo_path = "img/admin/default.png";

        if (!empty($_FILES['photo']['name'])) {
            $filename = time() . "_" . $_FILES['photo']['name'];
            $target = "../img/admin/" . $filename;

            if (!is_dir("../img/admin")) mkdir("../img/admin");

            if (move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
                $photo_path = "img/admin/" . $filename;
            }
        }

        $sql = "INSERT INTO admin(name, email, password, photo, role, status)
                VALUES('$name', '$email', '$hashed', '$photo_path', 'superadmin', 'active')";

        if (mysqli_query($con, $sql)) {
            $success = "Admin Created Successfully!";

            $_SESSION['admin_id']    = mysqli_insert_id($con);
            $_SESSION['admin_name']  = $name;
            $_SESSION['admin_email'] = $email;
            $_SESSION['admin_photo'] = $photo_path;
            $_SESSION['admin_role']  = 'superadmin';

            header("Location: index.php");
            exit;
        } else {
            $error = "Error creating admin!";
        }
    }
}

include('header.php');
?>
        </div>

        <div class="content-header"><h2>Create Admin</h2></div>

        <?php if ($success): ?>
            <div class="msg-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="msg-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="form-card">
            <form method="post" enctype="multipart/form-data">
                <label>Name</label>
                <input type="text" name="name" required>

                <label>Email</label>
                <input type="email" name="email" required>

                <label>Password</label>
                <input type="password" name="password" required>

                <label>Photo (optional)</label>
                <input type="file" name="photo" accept="image/*">

                <button class="btn-admin" name="create_admin">Create Admin</button>
            </form>
        </div>
    </div>
</body>
</html>
