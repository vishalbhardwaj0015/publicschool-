<?php
session_start();
include '../config/config.php';
include 'asset/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
if (intval($_SESSION['role_id']) === 2) {
    header("Location: teacher_home.php");
    exit;
}

$sid = intval($_SESSION['user_id']);

// Student profile + class
$stu = mysqli_fetch_assoc(mysqli_query($con, "SELECT u.*, c.class_name, c.section
    FROM userform u LEFT JOIN classes c ON u.student_class=c.id WHERE u.id=$sid"));

// Admission
$adm = mysqli_fetch_assoc(mysqli_query($con, "SELECT a.*, c.class_name, c.section
    FROM admissions a LEFT JOIN classes c ON a.class_id=c.id
    WHERE a.student_id=$sid ORDER BY a.id DESC LIMIT 1"));

// Fee summary
$fee = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM fees WHERE student_id=$sid ORDER BY id DESC LIMIT 1"));

// Payment history
$pays = mysqli_query($con, "SELECT * FROM payments WHERE student_id=$sid ORDER BY payment_date DESC");

// Marks (grouped)
$marks = mysqli_query($con, "SELECT subject, exam, marks, total_marks FROM marks WHERE student_id=$sid ORDER BY exam, subject");

// Attendance summary
$att = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) total,
    SUM(status='Present') present, SUM(status='Absent') absent FROM attendance WHERE student_id=$sid"));
$attTotal = (int)($att['total'] ?? 0);
$attPresent = (int)($att['present'] ?? 0);
$attPct = $attTotal > 0 ? round(($attPresent / $attTotal) * 100) : 0;
$recentAtt = mysqli_query($con, "SELECT date, status FROM attendance WHERE student_id=$sid ORDER BY date DESC LIMIT 7");

// Distinct subjects (from marks) or fallback by class
$subjects = [];
$sr = mysqli_query($con, "SELECT DISTINCT subject FROM marks WHERE student_id=$sid");
if ($sr && mysqli_num_rows($sr) > 0) { while ($x = mysqli_fetch_assoc($sr)) $subjects[] = $x['subject']; }
if (empty($subjects)) {
    $levelMap = array_fill_keys([1,2,3,4,5,6,7,8], ['English','Maths','Hindi','EVS']);
    $levelMap += array_fill_keys([9,10,11,12,13], ['English','Maths','Science','Hindi','Social Studies','Computer']);
    $levelMap += array_fill_keys([14,15], ['English','Maths','Physics','Chemistry','Biology','Computer','Physical Education']);
    $cid = (int)($stu['student_class'] ?? 0);
    $subjects = $levelMap[$cid] ?? ['English','Maths','Hindi'];
}

// Group marks by exam
$marksByExam = [];
while ($m = mysqli_fetch_assoc($marks)) $marksByExam[$m['exam']][] = $m;

// profile photo
$photo = ($stu && !empty($stu['photo']) && is_file($stu['photo'])) ? $stu['photo'] : '';
?>
<link rel="stylesheet" href="style/home.css?v=<?= time() ?>">

<section class="home-hero">
    <div class="container">
        <h1>Welcome, <?= htmlspecialchars($stu['name'] ?? $_SESSION['username']) ?>!</h1>
        <p><?= htmlspecialchars(trim(($stu['class_name'] ?? '').' '.($stu['section'] ?? ''))) ?: 'Student' ?> — School Portal</p>
    </div>
</section>

<div class="container py-4">

    <!-- QUICK OVERVIEW CARDS (click to view details) -->
    <div class="row g-4 mb-4" id="overviewTabs">
        <div class="col-md-3 col-sm-6">
            <div class="dash-panel ov-tab active" data-tab="admission" onclick="showTab('admission')">
                <div class="dash-icon" style="background:linear-gradient(135deg,#2f6bff,#1f4fd8);"><i class="fa-solid fa-id-card"></i></div>
                <h5>Admission</h5>
                <p class="dash-muted">Class &amp; status</p>
                <div class="dash-big"><?= htmlspecialchars(trim(($adm['class_name'] ?? $stu['class_name'] ?? '').' '.($adm['section'] ?? ''))) ?: '—' ?></div>
                <span class="badge bg-success"><?= ucfirst(htmlspecialchars($adm['status'] ?? ($stu['status'] ?? ''))) ?: 'Active' ?></span>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="dash-panel ov-tab" data-tab="fee" onclick="showTab('fee')">
                <div class="dash-icon" style="background:linear-gradient(135deg,#0f9d58,#0b7a45);"><i class="fa-solid fa-money-bill-wave"></i></div>
                <h5>Pending Fee</h5>
                <div class="dash-big">₹<?= number_format((float)($fee['pending_fee'] ?? 0), 0) ?></div>
                <span class="badge <?= ((int)($fee['pending_fee'] ?? 0) > 0) ? 'bg-warning text-dark' : 'bg-success' ?>"><?= htmlspecialchars($fee['status'] ?? 'pending') ?></span>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="dash-panel ov-tab" data-tab="marks" onclick="showTab('marks')">
                <div class="dash-icon" style="background:linear-gradient(135deg,#7a54ff,#5b3df0);"><i class="fa-solid fa-chart-simple"></i></div>
                <h5>Marks</h5>
                <p class="dash-muted"><?= count($marksByExam) ?> exam(s) recorded</p>
                <div class="dash-big"><?= array_sum(array_map('count', $marksByExam)) ?></div>
                <span class="badge bg-info text-dark">Results</span>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="dash-panel ov-tab" data-tab="attendance" onclick="showTab('attendance')">
                <div class="dash-icon" style="background:linear-gradient(135deg,#ff7a3c,#e85d1f);"><i class="fa-solid fa-calendar-check"></i></div>
                <h5>Attendance</h5>
                <div class="dash-big att-pct"><?= $attPct ?>%</div>
                <span class="badge bg-secondary"><?= $attPresent ?>/<?= $attTotal ?> days</span>
            </div>
        </div>
    </div>

    <div id="detailArea" role="dialog" aria-modal="true" aria-label="Student details">
    <div class="row">
        <!-- LEFT: ADMISSION + FEE -->
        <div class="col-lg-6">
            <div class="section-block ov-panel" id="panel-admission" style="display:none;">
                <button class="modal-close" type="button" onclick="closeModal()" aria-label="Close">&times;</button>
                <div class="sec-head"><i class="fa-solid fa-user-graduate"></i> My Admission Details</div>
                <div class="dash-panel">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <?php if ($photo): ?>
                            <img src="../<?= htmlspecialchars($photo) ?>" alt="Photo" style="width:64px;height:64px;border-radius:50%;object-fit:cover;border:3px solid #2f6bff;">
                        <?php else: ?>
                            <div class="dash-icon" style="width:64px;height:64px;margin:0;font-size:26px;"><i class="fa-solid fa-user-graduate"></i></div>
                        <?php endif; ?>
                        <div>
                            <div style="font-size:1.15rem;font-weight:800;"><?= htmlspecialchars($stu['name'] ?? '') ?></div>
                            <div class="dash-muted"><?= htmlspecialchars(trim(($stu['class_name'] ?? '').' '.($stu['section'] ?? ''))) ?: 'Class not assigned' ?></div>
                        </div>
                    </div>
                    <?php
                    $admissionDate = $adm['admission_date'] ?? ($stu['admission_date'] ?? '—');
                    $infoRows = [
                        'Admission Date' => $admissionDate,
                        'Roll / Student ID' => '#STU-' . str_pad((string)$sid, 4, '0', STR_PAD_LEFT),
                        'Phone' => $stu['phone'] ?? '—',
                        'Email' => $stu['email'] ?? '—',
                        'Date of Birth' => $stu['dob'] ?? '—',
                        'Gender' => $stu['gender'] ?? '—',
                        'Status' => ucfirst(htmlspecialchars($adm['status'] ?? ($stu['status'] ?? 'active'))),
                    ];
                    foreach ($infoRows as $k => $v): ?>
                        <div class="info-row"><span class="info-label"><?= $k ?></span><span class="info-value"><?= htmlspecialchars($v) ?></span></div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="section-block ov-panel" id="panel-fee" style="display:none;">
                <button class="modal-close" type="button" onclick="closeModal()" aria-label="Close">&times;</button>
                <div class="sec-head"><i class="fa-solid fa-receipt"></i> My Fee Details</div>
                <div class="dash-panel">
                    <div class="row text-center mb-3">
                        <div class="col-4"><div class="dash-muted">Total</div><div class="info-value">₹<?= number_format((float)($fee['total_fee'] ?? 0),0) ?></div></div>
                        <div class="col-4"><div class="dash-muted" style="color:#0f9d58;">Paid</div><div class="info-value" style="color:#0f9d58;">₹<?= number_format((float)($fee['paid_fee'] ?? 0),0) ?></div></div>
                        <div class="col-4"><div class="dash-muted" style="color:#e53935;">Pending</div><div class="info-value" style="color:#e53935;">₹<?= number_format((float)($fee['pending_fee'] ?? 0),0) ?></div></div>
                    </div>
                    <table class="table table-sm align-middle">
                        <thead><tr><th>Date</th><th>Amount</th><th>Method</th><th>Status</th></tr></thead>
                        <tbody>
                            <?php if ($pays && mysqli_num_rows($pays) > 0): ?>
                                <?php while ($p = mysqli_fetch_assoc($pays)): ?>
                                    <tr>
                                        <td><?= htmlspecialchars(date('d-m-Y', strtotime($p['payment_date']))) ?></td>
                                        <td class="fw-semibold">₹<?= number_format((float)$p['amount'],0) ?></td>
                                        <td><?= ucfirst(htmlspecialchars($p['method'])) ?></td>
                                        <td><span class="badge bg-success">Paid</span></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center text-muted py-3">No payments recorded yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- RIGHT: MARKS + ATTENDANCE + SUBJECTS -->
        <div class="col-lg-6">
            <div class="section-block ov-panel" id="panel-marks" style="display:none;">
                <button class="modal-close" type="button" onclick="closeModal()" aria-label="Close">&times;</button>
                <div class="sec-head"><i class="fa-solid fa-file-pen"></i> My Marks / Results</div>
                <div class="dash-panel">
                    <?php if (!empty($marksByExam)): ?>
                        <?php foreach ($marksByExam as $exam => $rows): ?>
                            <h6 class="fw-bold mb-2"><?= htmlspecialchars($exam) ?></h6>
                            <table class="table table-sm align-middle">
                                <thead><tr><th>Subject</th><th>Marks</th><th>Total</th><th class="text-end">%</th></tr></thead>
                                <tbody>
                                    <?php foreach ($rows as $m): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($m['subject']) ?></td>
                                            <td class="fw-semibold"><?= (float)$m['marks'] ?></td>
                                            <td><?= (float)$m['total_marks'] ?></td>
                                            <td class="text-end"><?= $m['total_marks'] > 0 ? round(((float)$m['marks'] / (float)$m['total_marks']) * 100) : 0 ?>%</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">No results recorded yet. Your teachers will add marks after exams.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="section-block ov-panel" id="panel-attendance" style="display:none;">
                <button class="modal-close" type="button" onclick="closeModal()" aria-label="Close">&times;</button>
                <div class="sec-head"><i class="fa-solid fa-calendar-days"></i> My Attendance</div>
                <div class="dash-panel">
                    <div class="row align-items-center">
                        <div class="col-4 text-center">
                            <div class="att-pct"><?= $attPct ?>%</div>
                            <div class="dash-muted">Overall</div>
                        </div>
                        <div class="col-8">
                            <div class="d-flex justify-content-between"><span class="dash-muted">Present: <?= $attPresent ?></span><span class="dash-muted">Absent: <?= $attTotal - $attPresent ?></span></div>
                            <div class="att-bar"><div class="att-bar-fill" style="width:<?= $attPct ?>%"></div></div>
                        </div>
                    </div>
                    <?php if ($recentAtt && mysqli_num_rows($recentAtt) > 0): ?>
                        <table class="table table-sm align-middle mt-3 mb-0">
                            <thead><tr><th>Date</th><th>Status</th></tr></thead>
                            <tbody>
                                <?php while ($a = mysqli_fetch_assoc($recentAtt)): ?>
                                    <tr>
                                        <td><?= htmlspecialchars(date('d-m-Y', strtotime($a['date']))) ?></td>
                                        <td><span class="badge <?= $a['status']==='Present' ? 'bg-success' : 'bg-danger' ?>"><?= htmlspecialchars($a['status']) ?></span></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-muted mb-0 mt-2">No attendance recorded yet.</p>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
    </div>

    <!-- QUICK LINKS -->
    <div class="section-block mt-4">
        <div class="sec-head"><i class="fa-solid fa-link"></i> Quick Links</div>
        <div class="row g-3">
            <div class="col-md-3 col-6"><a class="portal-card" href="update.php?id=<?= $sid ?>"><div class="portal-icon"><i class="fa-solid fa-user-pen"></i></div><h4>My Profile</h4><p>Update your details</p></a></div>
            <div class="col-md-3 col-6"><a class="portal-card" href="student_list.php"><div class="portal-icon"><i class="fa-solid fa-user-graduate"></i></div><h4>Students</h4><p>View student list</p></a></div>
            <div class="col-md-3 col-6"><a class="portal-card" href="teacher_list.php"><div class="portal-icon"><i class="fa-solid fa-chalkboard-user"></i></div><h4>Teachers</h4><p>Meet our staff</p></a></div>
            <div class="col-md-3 col-6"><a class="portal-card" href="logout.php"><div class="portal-icon" style="background:linear-gradient(135deg,#e53935,#c62828);"><i class="fa-solid fa-right-from-bracket"></i></div><h4>Logout</h4><p>Sign out</p></a></div>
        </div>
    </div>

</div>

<script>
function closeModal() {
    var detailArea = document.getElementById('detailArea');
    if (detailArea) detailArea.classList.remove('active');
    document.querySelectorAll('.ov-panel').forEach(function (panel) {
        panel.style.display = 'none';
        panel.classList.remove('active');
    });
    document.querySelectorAll('.ov-tab').forEach(function (tab) {
        tab.classList.remove('active');
    });
    document.body.classList.remove('modal-open');
}

function showTab(tab){
    var panels = document.querySelectorAll('.ov-panel');
    panels.forEach(function(p){ p.style.display = 'none'; p.classList.remove('active'); });
    var activePanel = document.getElementById('panel-' + tab);
    if (activePanel) {
        activePanel.style.display = 'block';
        activePanel.classList.add('active');
    }
    document.getElementById('detailArea').classList.add('active');
    document.body.classList.add('modal-open');
    var tabs = document.querySelectorAll('.ov-tab');
    tabs.forEach(function(t){ t.classList.remove('active'); });
    var activeTab = document.querySelector('.ov-tab[data-tab="' + tab + '"]');
    if (activeTab) activeTab.classList.add('active');
}

window.addEventListener('DOMContentLoaded', function () {
    document.getElementById('detailArea').addEventListener('click', function (event) {
        if (event.target === this) closeModal();
    });
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') closeModal();
    });
});
</script>
<?php include 'asset/footer.php'; ?>
