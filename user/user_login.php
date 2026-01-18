<?php
session_start();
include('config/config.php');

$error = "";

// LOGIN SUBMIT
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btnlogin'])) {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if ($email == "" || $password == "") {
        $error = "All fields are required!";
    } else {

        $query = "SELECT * FROM userform WHERE email='$email' LIMIT 1";
        $result = mysqli_query($con, $query);

        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);

            // Password check
            if (password_verify($password, $row['password'])) {

                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_name'] = $row['name'];

                header("Location: user/home.php");
                exit;

            } else {
                $error = "Incorrect password!";
            }
        } else {
            $error = "No account found with this email!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>

    <style>
        body {
            background: url('images/bg.jpg') no-repeat center center/cover;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
        }

        .overlay {
            background: rgba(0, 0, 0, 0.5);
            position: absolute;
            height: 100%;
            width: 100%;
            top: 0;
            left: 0;
        }

        .container {
            position: relative;
            z-index: 10;
            width: 360px;
            background: rgba(255,255,255,0.95);
            padding: 30px 35px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        h2 {
            margin-bottom: 20px;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-top: 12px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: #28a745;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            margin-top: 15px;
            cursor: pointer;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .error {
            background: #ffcccc;
            color: #d00;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 12px;
        }

        a {
            text-decoration: none;
            display: block;
            margin-top: 15px;
            color: #007bff;
        }
    </style>

</head>
<body>

<div class="overlay"></div>

<div class="container">
    <h2>User Login</h2>

    <?php if ($error != "") { ?>
        <div class="error"><?php echo $error; ?></div>
    <?php } ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Enter Email">
        <input type="password" name="password" placeholder="Enter Password">

        <button type="submit" name="btnlogin" class="btn">Login</button>
    </form>

    <a href="user/index.php">← Back</a>
</div>

</body>
</html>
