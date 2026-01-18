<?php
session_start();
include('config/config.php');

if (!isset($_SESSION['admin_name'])) {
    header("Location: admin/login.php");
    exit;
}

include 'admin/header.php';

$query = mysqli_query($con, "SELECT * FROM userform WHERE role_id='1'");
?>

<link rel="stylesheet" href="style/teacher_list.css?v=<?php echo time(); ?>">

<div class="container-table">

    <?php if(isset($_GET['deleted'])): ?>
        <div class="msg-success">
            Record deleted successfully!
        </div>
    <?php endif; ?>
        
    <h3>Teacher List</h3>

    <table class="Stable">
        <thead class="Shead">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Subjects</th>
                <th>Gender</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            <?php
            if(mysqli_num_rows($query) > 0){
                while($row = mysqli_fetch_assoc($query)){
                    echo '
                    <tr>
                        <td>'.$row['id'].'</td>
                        <td>'.$row['name'].'</td>
                        <td>'.$row['email'].'</td>
                        <td>'.$row['teacher_subject'].'</td>
                        <td>'.$row['gender'].'</td>
                        <td>
                            <a href="update.php?id='.$row['id'].'" class="btn edit">Edit</a>
                            <a href="delete.php?id='.$row['id'].'" class="btn delete" onclick="return confirm(\'Are you sure?\')">Delete</a>
                        </td>
                    </tr>';
                }
            } else {
                echo "<tr><td colspan='6'>No Teachers Found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include 'user/footer.php'; ?>
