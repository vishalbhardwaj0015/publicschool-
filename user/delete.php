<?php
session_start();
include('config/config.php');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid Request!");
}

$id = intval($_GET['id']);

$stmt = $con->prepare("SELECT role_id FROM userform WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();

if (!$res) {
    die("Record not found!");
}

$role_id = $res['role_id'];

$stmt = $con->prepare("DELETE FROM userform WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    if ($role_id == 0) {
        header("Location: student_list.php?deleted=1");
    } else {
        header("Location: teacher_list.php?deleted=1");
    }
    exit;

} else {
    echo "Error deleting record!";
}
?>
