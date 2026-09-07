<?php
session_start();
include('../config/config.php');

if (!isset($_SESSION['admin_name'])) {
    header("Location: login.php");
    exit;
}

/* Fetch all classes with student counts */
$sql = "
SELECT c.*, 
(SELECT COUNT(*) FROM userform u WHERE u.student_class = c.id) AS total_students
FROM classes c
ORDER BY c.id ASC
";
$result = mysqli_query($con, $sql);

include('header.php');
?>
        </div>

        <div class="content-header"><h2>Class List</h2></div>

        <div class="tblWrap">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Class Name</th>
                        <th>Section</th>
                        <th>Total Students</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && mysqli_num_rows($result) > 0):
                        while ($row = mysqli_fetch_assoc($result)):
                    ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['class_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['section']); ?></td>
                        <td><?php echo $row['total_students']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align:center; padding:20px;">No classes found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
