<?php
session_start();
include 'asset/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<link rel="stylesheet" href="style\home.css?v=<?= time()?>">
<script>
function toggleSidebar() {
    document.getElementById("sidebar").classList.toggle("open");
}
</script>

<div class="main-container ">

    
    <div class="menu-btn" onclick="toggleSidebar()">☰</div>
    <div class="sidebar" id="sidebar">

        <h5>HIM PUBLIC SCHOOL</h5>

        <a href="home.php"> Home</a>

        <div class="dropdown-side">
            <button class="dropbtn-side"> Register</button>
            <div class="dropdown-content-side">
                <a href="user\register.php">Student Register</a>
                <a href="register.php">Teacher Register</a>
            </div>
        </div>

        <a href="student_list.php"> Student List</a>
        <a href="teacher_list.php"> Teacher List</a>
        <a href="contact.php"> Contact</a>

    </div>

    <div class="content-area">
        <h3>Welcome to Main</h3>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit...</p>

        <div class="row g-4 mt-2">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <img src="../img/img1/simg1.jpg" class="card-img-top">
                    <div class="card-body text-center">
                        <h4>Our School</h4>
                        <p>Our school provides a safe and supportive environment where students learn, grow, and explore their abilities. With experienced teachers and modern facilities, we aim to give every child the best educational experience.
</p>
                        <a href="#" class="btn btn-primary">School Profile</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm">
                    <img src="../img/img1/simg2.jpg" class="card-img-top">
                    <div class="card-body text-center">
                        <h4>Classrooms</h4>
                        <p>Our classrooms are well-equipped, spacious, and designed to create an engaging learning atmosphere. From 1st to +2, students receive quality education with modern teaching methods.
</p>
                        <a href="#" class="btn btn-primary">See Classes</a>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="row g-4 mt-3">
            <div class="col-sm-3">
                <div class="card shadow-sm">
                    <img src="../img/img1/sportsroom1.jpg" class="card-img-top">
                    <div class="card-body text-center">
                        <h4>Activities Hall</h4>
                        <p>The activities hall is the center of creativity and teamwork. Students participate in indoor games, cultural events, art programs, and many co-curricular activities throughout the year.
                        </p>
                        <a href="#" class="btn btn-primary">See Activities</a>
                    </div>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="card shadow-sm">
                    <img src="../img/img1/signing.jpg" class="card-img-top">
                    <div class="card-body text-center">
                        <h4>Singing Competition</h4>
                        <p>
                            Our singing competitions encourage students to showcase their talent and build confidence. It provides a platform for young singers to express their creativity through music.

                        </p>
                        <a href="#" class="btn btn-primary">View</a>
                    </div>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="card shadow-sm">
                    <img src="../img/img1/yoga day.jpg" class="card-img-top">
                    <div class="card-body text-center">
                        <h4>Yoga Day</h4>
                        <p>
                            Yoga Day promotes health, mindfulness, and discipline among students. With guided sessions, children learn relaxation techniques and the importance of a balanced lifestyle.

                        </p>
                        <a href="#" class="btn btn-primary">Yoga Activities</a>
                    </div>
                </div>
            </div>

            <div class="col-sm-3">
                <div class="card shadow-sm">
                    <img src="../img/img1/science.jpg" class="card-img-top">
                    <div class="card-body text-center">
                        <h4>Science Class</h4>
                        <p>
                            Our science classes focus on practical learning through experiments and projects. Students explore scientific concepts in an interactive and exciting way.

                        </p>
                        <a href="#" class="btn btn-primary">Projects</a>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

<?php include 'asset/footer.php'; ?>
