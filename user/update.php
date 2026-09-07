<?php
session_start();
include('../config/config.php');
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

if (!isset($_GET['id']) || empty($_GET['id'])) { die("Invalid Request!"); }
$id = intval($_GET['id']);

$stmt = $con->prepare("SELECT * FROM userform WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$fdata = $stmt->get_result()->fetch_assoc();
if (!$fdata) die("Record not found!");

$name = $fdata["name"];
$email = $fdata["email"];
$gender = $fdata["gender"];
$phone = $fdata["phone"];
$dob_raw = $fdata["dob"];
$role_id = $fdata["role_id"];
$student_class = $fdata["student_class"];
$teacher_subject = $fdata["teacher_subject"];
$hobbies = $fdata["hobbies"];
$bio = $fdata["bio"];

$errors = [];
$success = isset($_GET['success']) && $_GET['success'] == 1;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btnupdate'])) {
    $name = trim($_POST['name']);
    $email_new = trim($_POST['email']);
    $gender = $_POST['gender'] ?? '';
    $phone = trim($_POST['phone']);
    $dob = $_POST['dob'];
    $new_role = intval($_POST['role_id']);
    $new_class = intval($_POST['student_class']);
    $new_subject = !empty($_POST['teacher_subject']) ? implode(",", (array)$_POST['teacher_subject']) : '';
    $new_hobbies = !empty($_POST['hobbies']) ? implode(",", (array)$_POST['hobbies']) : '';
    $new_bio = trim($_POST['bio']);

    if (!$name) $errors[] = "Name is required.";
    if (empty($email_new) || !filter_var($email_new, FILTER_VALIDATE_EMAIL)) $errors[] = "Enter a valid email.";
    if (empty($dob)) $errors[] = "Date of birth is required.";
    if (!$gender) $errors[] = "Gender is required.";
    if (!in_array($new_role, [1, 2])) $errors[] = "Please select a role.";
    if ($new_role == 1 && !$new_class) $errors[] = "Please select a class for the student.";
    if ($new_role == 2 && !$new_subject) $errors[] = "Please select at least one subject for the teacher.";

    if (empty($errors)) {
        $check = $con->prepare("SELECT id FROM userform WHERE email = ? AND id != ?");
        $check->bind_param("si", $email_new, $id);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) $errors[] = "Email already exists.";
        $check->close();
    }

    if (empty($errors)) {
        $upd = $con->prepare("UPDATE userform SET name=?, email=?, dob=?, gender=?, phone=?, role_id=?, student_class=?, teacher_subject=?, hobbies=?, bio=? WHERE id=?");
        $class_val = ($new_role == 1) ? $new_class : null;
        $subj_val = ($new_role == 2) ? $new_subject : '';
        $upd->bind_param("sssssissssi", $name, $email_new, $dob, $gender, $phone, $new_role, $class_val, $subj_val, $new_hobbies, $new_bio, $id);
        if ($upd->execute()) {
            header("Location: update.php?id=$id&success=1");
            exit;
        } else {
            $errors[] = "Error updating record.";
        }
    }
}
include 'asset/header.php';
?>
<link rel="stylesheet" href="style/update.css?v=<?= time() ?>">

<div class="container mt-4 mb-5" style="max-width:640px;">
    <h3 class="mb-3">Update User</h3>

    <?php if ($success): ?>
        <div class="alert alert-success">Record updated successfully!</div>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded shadow-sm p-4">
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Role</label>
                <select name="role_id" id="role_id" class="form-select">
                    <option value="">Select Role</option>
                    <option value="1" <?= $role_id == 1 ? 'selected' : '' ?>>Student</option>
                    <option value="2" <?= $role_id == 2 ? 'selected' : '' ?>>Teacher</option>
                </select>
            </div>

            <div id="student_div" class="mb-3" style="display:<?= $role_id == 1 ? 'block' : 'none' ?>;">
                <label class="form-label">Student Class</label>
                <select name="student_class" class="form-select">
                    <option value="">Select Class</option>
                    <?php
                    $cres = mysqli_query($con, "SELECT id, class_name, section FROM classes ORDER BY class_name ASC");
                    while ($c = mysqli_fetch_assoc($cres)) {
                        $lab = $c['class_name'] . ($c['section'] ? ' - ' . $c['section'] : '');
                        echo '<option value="' . $c['id'] . '" ' . ($student_class == $c['id'] ? 'selected' : '') . '>' . htmlspecialchars($lab) . '</option>';
                    }
                    ?>
                </select>
            </div>

            <div id="teacher_div" class="mb-3" style="display:<?= $role_id == 2 ? 'block' : 'none' ?>;">
                <label class="form-label">Teacher Subjects</label>
                <?php $subjects = ["Maths","English","Science","Physics","Chemistry","Biology","Computer","Hindi","Social Studies","Physical Education"]; ?>
                <div class="d-flex flex-wrap gap-3">
                    <?php $sel = explode(",", $teacher_subject); foreach ($subjects as $sub): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="teacher_subject[]" value="<?= $sub ?>" <?= in_array($sub, $sel) ? 'checked' : '' ?>>
                        <label class="form-check-label"><?= $sub ?></label>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="mb-3"><label class="form-label">Name</label><input type="text" name="name" class="form-control" value="<?= htmlspecialchars($name) ?>" required></div>
            <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>" required></div>
            <div class="mb-3"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($phone ?? '') ?>"></div>
            <div class="mb-3"><label class="form-label">Date of Birth</label><input type="date" name="dob" class="form-control" value="<?= $dob_raw ?>"></div>

            <div class="mb-3">
                <label class="form-label me-3">Gender</label>
                <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="gender" value="Male" <?= $gender == "Male" ? 'checked' : '' ?>><label class="form-check-label">Male</label></div>
                <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="gender" value="Female" <?= $gender == "Female" ? 'checked' : '' ?>><label class="form-check-label">Female</label></div>
            </div>

            <div class="mb-3">
                <label class="form-label me-3">Hobbies</label>
                <?php $hsel = explode(",", $hobbies); foreach (["Reading","Sports","Music"] as $h): ?>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="hobbies[]" value="<?= $h ?>" <?= in_array($h, $hsel) ? 'checked' : '' ?>>
                    <label class="form-check-label"><?= $h ?></label>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="mb-3"><label class="form-label">Bio</label><textarea name="bio" class="form-control" rows="3"><?= htmlspecialchars($bio) ?></textarea></div>

            <button type="submit" name="btnupdate" class="btn btn-primary w-100">Update</button>
        </form>
    </div>
</div>

<script>
document.getElementById("role_id").addEventListener("change", function () {
    var s = document.getElementById("student_div");
    var t = document.getElementById("teacher_div");
    if (this.value === "1") { s.style.display = "block"; t.style.display = "none"; }
    else if (this.value === "2") { s.style.display = "none"; t.style.display = "block"; }
    else { s.style.display = "none"; t.style.display = "none"; }
});
</script>

<?php include 'asset/footer.php'; ?>
