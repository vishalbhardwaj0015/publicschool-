



<?php include 'asset/header.php'; ?>
<link rel="stylesheet" href="style/index.css?v=<?= time()?>">


<main class="landing-page">

    <section class="section">
        <div class="section-text">
            <h1>HIM PUBLIC SCHOOL</h1>
            <p class="section-tagline">
                A safe, modern and inspiring place for students from Nursery to +2.
            </p>

            <div class="section-buttons">
                <a href="admin.login.php" class="lp-btn lp-btn-outline">Admin Login</a>
                <a href="login.php" class="lp-btn lp-btn-primary">Student / Parent Login</a>
                <a href="register.php" class="lp-btn lp-btn-vs">New Admission</a>
            </div>

            <div class="section-highlights">
                <div class="school">
                     Holistic Development
                </div>
                <div class="school">
                     Smart Classrooms
                </div>
                <div class="school">
                     Co-curricular Focus
                </div>
            </div>
        </div>

        <div class="section-buttons">
            <div class="hero-image-card">
                <img src="img/img1/simg1.jpg" alt="School Building">
            </div>
        </div>
    </section>

    
    <section class="section-features">
        <h2 class="section-title">Why Choose Our School?</h2>
        <p class="section-subtitle">
            We focus on academic excellence, discipline, and character building.
        </p>

        <div class="feature-grid">
            <div class="feature-card">
                <h3>Experienced Teachers</h3>
                <p>
                    Highly qualified and dedicated staff to guide every student personally.
                </p>
            </div>

            <div class="feature-card">
                <h3>Modern Learning</h3>
                <p>
                    Smart classes, activity-based learning and regular assessments.
                </p>
            </div>

            <div class="feature-card">
                <h3>Safe Campus</h3>
                <p>
                    Child-friendly, secure environment with proper supervision everywhere.
                </p>
            </div>
        </div>
    </section>

    
    <section class="section-gallery">
        <h2 class="section-title">Life at HIM PUBLIC SCHOOL</h2>
        <p class="section-subtitle">
            A glimpse of our classrooms, activities and events.
        </p>

        <div class="gallery-grid">

            <article class="gallery-card">
                <div class="gallery-image-wrapper">
                    <img src="img/img1/simg2.jpg" alt="Classrooms">
                </div>
                <div class="gallery-content">
                    <h3>Classrooms</h3>
                    <p>From 5th to +2, bright and spacious classrooms for focused learning.</p>
                    <a href="#" class="gallery-link">See Classes →</a>
                </div>
            </article>

            <article class="gallery-card">
                <div class="gallery-image-wrapper">
                    <img src="img/img1/sportsroom1.jpg" alt="Activities Hall">
                </div>
                <div class="gallery-content">
                    <h3>Activities Hall</h3>
                    <p>Indoor activities, cultural programmes and events under one roof.</p>
                    <a href="#" class="gallery-link">See Activities →</a>
                </div>
            </article>

            <article class="gallery-card">
                <div class="gallery-image-wrapper">
                    <img src="img/img1/signing.jpg" alt="Singing Competition">
                </div>
                <div class="gallery-content">
                    <h3>Singing Competition</h3>
                    <p>Regular competitions to boost confidence and stage presence.</p>
                    <a href="#" class="gallery-link">View More →</a>
                </div>
            </article>

            <article class="gallery-card">
                <div class="gallery-image-wrapper">
                    <img src="img/img1/yoga day.jpg" alt="Yoga Day">
                </div>
                <div class="gallery-content">
                    <h3>Yoga & Wellness</h3>
                    <p>Yoga day celebrations to promote physical and mental well-being.</p>
                    <a href="#" class="gallery-link">Yoga Activities →</a>
                </div>
            </article>

            <article class="gallery-card">
                <div class="gallery-image-wrapper">
                    <img src="img/img1/science.jpg" alt="Science Class">
                </div>
                <div class="gallery-content">
                    <h3>Science Projects</h3>
                    <p>Hands-on experiments and project work to encourage curiosity.</p>
                    <a href="#" class="gallery-link">Projects →</a>
                </div>
            </article>

            <article class="gallery-card">
                <div class="gallery-image-wrapper">
                    <img src="img/img1/playground.jpg" alt="children-Playing">
                </div>
                <div class="gallery-content">
                    <h3>playground</h3>
                    <p><p>Our school playground is a vibrant space where children learn, play, and grow together. It's designed to encourage teamwork, physical activity, and joyful exploration.</p></p>
                    <a href="#" class="gallery-link">Projects →</a>
                </div>
            </article>

        </div>
    </section>

    
    <section class="Admissions-section">
        <div class="Admissions-open">
            <div>
                <h2>Admissions Open</h2>
                <p>Ready to give your child the right start? Contact us or register online.</p>
            </div>
            <div class="Admissions-buttons">
                <a href="contact.php" class="lp-btn lp-btn-primary">Contact School</a>
                <a href="register.php" class="lp-btn lp-btn-primary">Online Registration</a>
            </div>
        </div>
    </section>

</main>

<?php include 'asset/footer.php'; ?>
