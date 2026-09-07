<?php
$db_host  = getenv('DATABASE_HOST') !== false && getenv('DATABASE_HOST') !== '' ? getenv('DATABASE_HOST') : null;
$db_port  = getenv('DATABASE_PORT') !== false && getenv('DATABASE_PORT') !== '' ? getenv('DATABASE_PORT') : '4000';
$db_user  = getenv('DATABASE_USER') !== false && getenv('DATABASE_USER') !== '' ? getenv('DATABASE_USER') : null;
$db_pass  = getenv('DATABASE_PASSWORD') !== false ? getenv('DATABASE_PASSWORD') : '';
$db_name  = getenv('DATABASE_NAME') !== false && getenv('DATABASE_NAME') !== '' ? getenv('DATABASE_NAME') : 'test';

$con = null;

if ($db_host && $db_user) {
    // Online / TiDB connection (production).
    // TiDB Cloud Starter requires TLS, so enable ssl and trust the CA bundle.
    mysqli_report(MYSQLI_REPORT_OFF);
    $con = mysqli_init();
    if ($con) {
        mysqli_ssl_set($con, NULL, NULL, NULL, NULL, NULL);
        mysqli_options($con, MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, false);
        @mysqli_real_connect($con, $db_host, $db_user, $db_pass, $db_name, (int)$db_port, NULL, MYSQLI_CLIENT_SSL);
    }
}

if (!$con || mysqli_connect_errno()) {
    // Local development fallback (XAMPP/WAMP). Only used when NOT on Render.
    $is_render = getenv('RENDER') !== false;
    if ($is_render) {
        http_response_code(500);
        exit('Database connection failed. Please check the DATABASE_* settings.');
    }
    mysqli_report(MYSQLI_REPORT_OFF);
    $con = @mysqli_connect('localhost', 'root', '', 'publicschool')
        or die('Unable to connect to database');
}

mysqli_set_charset($con, 'utf8');
?>