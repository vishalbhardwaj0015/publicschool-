<?php
session_start();
include('../config/config.php');
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
include 'asset/header.php';
?>
<link rel="stylesheet" href="style/student_list.css?v=<?= time() ?>">

<div class="container mt-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="m-0">Student List</h3>
        <a href="register.php" class="btn btn-primary btn-sm">+ Register New</a>
    </div>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success">Record deleted successfully!</div>
    <?php endif; ?>

    <div class="row g-2 mb-3 align-items-center">
        <div class="col-md-4">
            <form method="get" class="d-flex gap-2">
                <select name="class_id" class="form-select" onchange="this.form.submit()">
                    <option value="">All Classes</option>
                    <?php
                    $cl = mysqli_query($con, "SELECT * FROM classes ORDER BY id");
                    $selClass = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;
                    while ($c = mysqli_fetch_assoc($cl)): ?>
                        <option value="<?= $c['id'] ?>" <?= $selClass == $c['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['class_name'].' '.$c['section']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <?php if ($selClass): ?>
                    <a href="student_list.php" class="btn btn-outline-danger text-nowrap" title="Clear filter"><i class="fa-solid fa-xmark"></i> Clear</a>
                <?php endif; ?>
            </form>
        </div>
        <div class="col-md-4">
            <?php if ($selClass): ?>
                <?php
                $cln = mysqli_fetch_assoc(mysqli_query($con, "SELECT c.class_name, c.section,
                    (SELECT COUNT(*) FROM userform u WHERE u.student_class=$selClass AND u.role_id=1) AS total
                    FROM classes c WHERE c.id=$selClass"));
                ?>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-primary fs-6">Class: <?= htmlspecialchars(trim(($cln['class_name'] ?? '').' '.($cln['section'] ?? ''))) ?></span>
                    <span class="badge bg-success fs-6"><i class="fa-solid fa-users"></i> Total Students: <?= (int)($cln['total'] ?? 0) ?></span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php
    $where = "u.role_id = 1";
    if ($selClass) { $where .= " AND u.student_class = $selClass"; }
    $query = mysqli_query($con, "SELECT u.id, u.name, u.email, u.dob, u.gender, u.hobbies, u.bio,
        COALESCE(c.class_name,'') cls, COALESCE(c.section,'') sec
        FROM userform u LEFT JOIN classes c ON u.student_class = c.id
        WHERE $where ORDER BY c.id, u.name");
    ?>
    <div class="table-responsive bg-white rounded shadow-sm p-3">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th><th>Name</th><th>Class</th><th>Email</th><th>DOB</th><th>Gender</th><th>Hobbies</th><th>Bio</th><th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($query && mysqli_num_rows($query) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($query)): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><span class="badge bg-light text-dark border"><?= htmlspecialchars(trim($row['cls'].' '.$row['sec'])) ?: '—' ?></span></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= $row['dob'] ?></td>
                        <td><?= htmlspecialchars($row['gender']) ?></td>
                        <td><?= htmlspecialchars($row['hobbies']) ?></td>
                        <td><?= nl2br(htmlspecialchars(mb_strimwidth($row['bio'] ?? '', 0, 40, '...'))) ?></td>
                        <td>
                            <a href="update.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">Update</a>
                            <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="9" class="text-center text-muted py-4">No students found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'asset/footer.php'; ?>
