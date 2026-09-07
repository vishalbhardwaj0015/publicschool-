<?php
session_start();
include('../config/config.php');
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
include 'asset/header.php';
?>
<link rel="stylesheet" href="style/teacher_list.css?v=<?= time() ?>">

<div class="container mt-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="m-0">Teacher List</h3>
        <a href="../admin/teacher_add.php" class="btn btn-primary btn-sm">+ Add Teacher</a>
    </div>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success">Record deleted successfully!</div>
    <?php endif; ?>

    <?php
    $query = mysqli_query($con, "SELECT id, name, email, teacher_subject, gender FROM userform WHERE role_id = 2 ORDER BY id DESC");
    ?>
    <div class="table-responsive bg-white rounded shadow-sm p-3">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th><th>Name</th><th>Email</th><th>Subjects</th><th>Gender</th><th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($query && mysqli_num_rows($query) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($query)): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['teacher_subject']) ?></td>
                        <td><?= htmlspecialchars($row['gender']) ?></td>
                        <td>
                            <a href="update.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                            <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">No teachers found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'asset/footer.php'; ?>
