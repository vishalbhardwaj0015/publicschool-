<?php
session_start();
include('config/config.php');

if (!isset($_SESSION['admin_name'])) {
    header("Location: admin.login.php");
    exit;
}
if (isset($_SESSION['admin_name'])) {
    include 'components/admin_header.php';
} else {
    include 'components/header.php';
}

$limit = 5;


$query = mysqli_query($con, "SELECT * FROM userform WHERE role_id='0' ");

include 'user/header.php';
?>
<link rel="stylesheet" href="style/student_list.css">

<div class="container-table">
    <?php if(isset($_GET['deleted'])): ?>
        <div style="text-align:center; color:green; margin-bottom:10px;">
            Record deleted successfully!
        </div>
    <?php endif; ?>
        
    <h3>Student List</h3>

    <table class="Stable">
        <thead class="Shead">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>DOB</th>
                <th>Gender</th>
                <th>Hobbies</th>
                <th>Bio</th>
                <th>Operation</th>
            </tr>
        </thead>

        <tbody>
            <?php
            if(mysqli_num_rows($query) > 0){
                while($row = mysqli_fetch_assoc($query)){
                    echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['email']}</td>
                        <td>{$row['dob']}</td>
                        <td>{$row['gender']}</td>
                        <td>{$row['hobbies']}</td>
                        <td>{$row['bio']}</td>
                        <td>
                            <a href='update.php?id={$row['id']}' class='btn btn-primary btn-sm'>Update</a>
                            <a href='delete.php?id={$row['id']}' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure?')\">Delete</a>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No Teachers Found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include 'user/footer.php'; ?>
