<?php
session_start();
include('config/config.php');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid Request!");
}

$id = intval($_GET['id']);

$stmt = $con->prepare("SELECT * FROM userform WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$fdata = $stmt->get_result()->fetch_assoc();

if (!$fdata) die("Record not found!");

$role_id = $fdata["role_id"];  
$student_class = $fdata["student_class"];
$teacher_subject = $fdata["teacher_subject"];
$selected_subjects = explode(",", $teacher_subject);

$name = $fdata["name"];
$email = $fdata["email"];
$gender = $fdata["gender"];
$bio = $fdata["bio"];

$hobbies = $fdata["hobbies"];
$selected_hobbies = explode(",", $hobbies);

list($year, $month, $day) = explode("-", $fdata["dob"]);

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btnupdate'])) {

    $role_id = $_POST['role_id'];  
    $student_class = $_POST['student_class'] ?? "";
    $teacher_subject = !empty($_POST['teacher_subject']) ? implode(",", $_POST['teacher_subject']) : "";

    $name = trim($_POST['name']);
    $email_new = trim($_POST['email']);
    $gender = $_POST['gender'] ?? "";
    $hobbies = !empty($_POST['hobbies']) ? implode(",", $_POST['hobbies']) : "";
    $bio = trim($_POST['bio']);

    $day = $_POST['dob_day'];
    $month = $_POST['dob_month'];
    $year = $_POST['dob_year'];

    if ($day && $month && $year && checkdate($month, $day, $year)) {
        $dob = "$year-$month-$day";
    } else {
        $errors[] = "Invalid Date of Birth.";
    }

    if ($role_id == "") $errors[] = "Please select a role.";
    if ($role_id == "0" && !$student_class) $errors[] = "Please select student class.";
    if ($role_id == "1" && !$teacher_subject) $errors[] = "Please select teacher subjects.";

    if (!$name) $errors[] = "Name is required.";
    if (!$email_new) $errors[] = "Email is required.";

    $stmt_check = $con->prepare("SELECT id FROM userform WHERE email = ? AND id != ?");
    $stmt_check->bind_param("si", $email_new, $id);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        $errors[] = "Email already exists.";
    }

    $stmt_check->close();

    if (!$gender) $errors[] = "Gender is required.";
    if (!$hobbies) $errors[] = "Please select hobbies.";
    if (!$bio) $errors[] = "Bio is required.";

    if (empty($errors)) {

        $stmt_u = $con->prepare("
            UPDATE userform SET 
            name=?, email=?, dob=?, gender=?, role_id=?, student_class=?,
            teacher_subject=?, hobbies=?, bio=?
            WHERE id=?
        ");

        $stmt_u->bind_param("ssssissssi",
            $name, $email_new, $dob, $gender, $role_id,
            $student_class, $teacher_subject, $hobbies, $bio, $id
        );

        if ($stmt_u->execute()) {
           header("Location: update.php?id=" . $id . "&success=1");
           
            $role_id = "";
            $student_class = "";
            $teacher_subject = "";
            $selected_subjects = [];
            $name = "";
            $email_new = "";
            $gender = "";
            $hobbies = "";
            $selected_hobbies = [];
            $bio = "";
            $day = $month = $year = "";
             exit;
        } else {
            $errors[] = "Error updating record.";
        }
    }
}

include 'user/header.php';
?>

<link rel="stylesheet" href="style/update.css?v=<?php echo time(); ?>">

<div class="container">
    <div class="container-form">

        <form method="post" class="form">

            <h2>UPDATE USER</h2>

            <?php if (!empty($errors)): ?>
                <?php foreach ($errors as $e): ?>
                    <div class="error-box"><?= $e ?></div>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="success-box"><?= $success ?></div>
            <?php endif; ?>

            <div class="form-div">
                <label>Role</label>
                <select name="role_id" id="role_id" class="form-control">
                    <option value="">Select Role</option>
                    <option value="0" <?= ($role_id=="0"?"selected":"") ?>>Student</option>
                    <option value="1" <?= ($role_id=="1"?"selected":"") ?>>Teacher</option>
                </select>
            </div>

            <div id="student_div" class="form-div" style="display: <?= ($role_id=="0"?"block":"none") ?>;">
                <label>Student Class</label>
                <select name="student_class">
                    <option value="">Select Class</option>
                    <?php
                    $classes = ["Class 6","Class 7","Class 8","Class 9","Class 10","Class +1","Class +2"];
                    foreach ($classes as $c) {
                        echo "<option ".($student_class==$c?"selected":"").">$c</option>";
                    }
                    ?>
                </select>
            </div>

            <div id="teacher_div" class="form-div" style="display: <?= ($role_id=="1"?"block":"none") ?>;">
                <label>Teacher Subjects</label>

                <?php
                $subjects = [
                    "Maths","English","Science","Physics","Chemistry",
                    "Biology","Computer","Hindi","Social Studies","Physical Education"
                ];
                ?>

                <div class="checkbox-group">
                    <?php foreach ($subjects as $sub): ?>
                        <label class="cb-item">
                            <input type="checkbox" name="teacher_subject[]" value="<?= $sub ?>"
                                <?= in_array($sub, $selected_subjects) ? "checked" : "" ?>>
                            <?= $sub ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-div">
                <label>Name</label>
                <input type="text" name="name" value="<?= $name ?>" class="form-control">
            </div>

            <div class="form-div">
                <label>Email</label>
                <input type="email" name="email" value="<?= $email ?>" class="form-control">
            </div>

            <div class="form-div">
                <label>Date of Birth</label>
                <div class="dob-inputs">
                    <select name="dob_day" class="dob-select">
                        <option value="">Day</option>
                        <?php for ($i=1;$i<=31;$i++): ?>
                            <option value="<?= $i ?>" <?= ($day==$i?"selected":"") ?>><?= $i ?></option>
                        <?php endfor; ?>
                    </select>

                    <select name="dob_month" class="dob-select">
                        <option value="">Month</option>
                        <?php for ($m=1;$m<=12;$m++): ?>
                            <option value="<?= $m ?>" <?= ($month==$m?"selected":"") ?>>
                                <?= date("F", mktime(0,0,0,$m,10)) ?>
                            </option>
                        <?php endfor; ?>
                    </select>

                    <select name="dob_year" class="dob-select">
                        <option value="">Year</option>
                        <?php for ($y=1990;$y<=2025;$y++): ?>
                            <option value="<?= $y ?>" <?= ($year==$y?"selected":"") ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <div class="form-div">
                <label>Gender</label>
                <input type="radio" name="gender" value="Male" <?= ($gender=="Male"?"checked":"") ?>> Male
                <input type="radio" name="gender" value="Female" <?= ($gender=="Female"?"checked":"") ?>> Female
            </div>

            <div class="form-div">
                <label>Hobbies</label>

                <?php $hobby_list = ["Reading","Sports","Music"]; ?>
                <?php foreach ($hobby_list as $h): ?>
                    <input type="checkbox" name="hobbies[]" value="<?= $h ?>"
                        <?= in_array($h, $selected_hobbies) ? "checked" : "" ?>> <?= $h ?>
                <?php endforeach; ?>
            </div>

            <div class="form-div">
                <label>Bio</label>
                <textarea name="bio" class="form-control"><?= $bio ?></textarea>
            </div>

            <button type="submit" name="btnupdate" class="btn btn-primary w-100">Update</button>

        </form>

    </div>
</div>

<script>
document.getElementById("role_id").addEventListener("change", function () {
    let student = document.getElementById("student_div");
    let teacher = document.getElementById("teacher_div");

    if (this.value === "0") {
        student.style.display = "block";
        teacher.style.display = "none";
    } 
    else if (this.value === "1") {
        student.style.display = "none";
        teacher.style.display = "block";
    } 
    else {
        student.style.display = "none";
        teacher.style.display = "none";
    }
});
</script>

<?php include 'user/footer.php'; ?>
