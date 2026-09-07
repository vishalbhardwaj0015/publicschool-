<?php
$db_host  = getenv('DATABASE_HOST') !== false ? getenv('DATABASE_HOST') : 'localhost';
$db_port  = getenv('DATABASE_PORT') !== false ? getenv('DATABASE_PORT') : '3306';
$db_user  = getenv('DATABASE_USER') !== false ? getenv('DATABASE_USER') : 'root';
$db_pass  = getenv('DATABASE_PASSWORD') !== false ? getenv('DATABASE_PASSWORD') : '';
$db_name  = getenv('DATABASE_NAME') !== false ? getenv('DATABASE_NAME') : 'publicschool';

$con = @mysqli_connect($db_host, $db_user, $db_pass, $db_name, (int)$db_port);
if (!$con) {
    // Local dev: fall back to the original settings so XAMPP/WAMP keeps working
    $con = mysqli_connect('localhost', 'root', '', 'publicschool') or die('Unable to connect to database');
}
mysqli_set_charset($con, 'utf8');
?>
