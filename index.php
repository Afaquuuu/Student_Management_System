<?php
include 'header.php';

// Dashboard defaults: these values are shown before any database rows exist.
$dashboard_stats = [
    'students' => 0,
    'courses' => 0,
    'latest' => 'No students yet'
];
$recent_students = [];

// Logged-in dashboard data: small counts and the latest records.
if (isset($_SESSION['admin_logged_in'])) {
    include 'db_config.php';

    $student_result = $conn->query("SELECT COUNT(*) AS total FROM students");
    if ($student_result) {
        $dashboard_stats['students'] = (int) $student_result->fetch_assoc()['total'];
    }

    $course_result = $conn->query("SELECT COUNT(DISTINCT course) AS total FROM students");
    if ($course_result) {
        $dashboard_stats['courses'] = (int) $course_result->fetch_assoc()['total'];
    }

    $latest_result = $conn->query("SELECT name FROM students ORDER BY id DESC LIMIT 1");
    if ($latest_result && $latest_result->num_rows > 0) {
        $dashboard_stats['latest'] = $latest_result->fetch_assoc()['name'];
    }

    $recent_result = $conn->query("SELECT id, name, course, status, created_at FROM students ORDER BY id DESC LIMIT 3");
    if ($recent_result) {
        while ($row = $recent_result->fetch_assoc()) {
            $recent_students[] = $row;
        }
    }
}
?>

<main class="home-shell">
    <!-- Dashboard hero: quick stats and actions for the admin. -->
    <section class="hero-panel">
        <div class="hero-content">
            <div class="hero-copy">
                <span class="eyebrow">Admin workspace</span>
                <h1>Student Portal</h1>
                <p>Manage student records, course details, and contact information from one clean, focused dashboard.</p>
            </div>

            <div class="hero-stats" aria-label="Portal highlights">
                <?php if(isset($_SESSION['admin_logged_in'])): ?>
                    <div>
                        <strong><?php echo $dashboard_stats['students']; ?></strong>
                        <span>Total students</span>
                    </div>
                    <div>
                        <strong><?php echo $dashboard_stats['courses']; ?></strong>
                        <span>Active courses</span>
                    </div>
                    <div>
                        <strong><?php echo $dashboard_stats['latest']; ?></strong>
                        <span>Latest record</span>
                    </div>
                <?php else: ?>
                    <div>
                        <strong>Fast</strong>
                        <span>Simple record entry</span>
                    </div>
                    <div>
                        <strong>Clear</strong>
                        <span>Readable student table</span>
                    </div>
                    <div>
                        <strong>Secure</strong>
                        <span>Admin-only access</span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="btn-group">
            <?php if(isset($_SESSION['admin_logged_in'])): ?>
                <p class="welcome-note">Hello, <strong><?php echo $_SESSION['admin_username']; ?></strong>. You are logged in.</p>
                <div class="hero-actions">
                    <a href="view_students.php" class="btn btn-primary">View Records</a>
                    <a href="register.php" class="btn btn-success">Register Student</a>
                </div>
            <?php else: ?>
                <p class="welcome-note">Please login to access the management features.</p>
                <a href="login.php" class="btn btn-dark">Login as Admin</a>
            <?php endif; ?>
            </div>
        </div>

        <!-- Activity panel: a simple visual guide to the daily workflow. -->
        <div class="activity-panel" aria-label="Student management workflow">
            <div class="activity-topline">
                <span>Today</span>
                <strong>Records desk</strong>
            </div>
            <div class="activity-list">
                <div class="activity-item is-primary">
                    <span class="activity-dot"></span>
                    <div>
                        <strong>Register</strong>
                        <span>Add verified student data</span>
                    </div>
                </div>
                <div class="activity-item">
                    <span class="activity-dot"></span>
                    <div>
                        <strong>Search</strong>
                        <span>Find records instantly</span>
                    </div>
                </div>
                <div class="activity-item">
                    <span class="activity-dot"></span>
                    <div>
                        <strong>Review</strong>
                        <span>Open complete student profiles</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php if(isset($_SESSION['admin_logged_in'])): ?>
        <!-- Recent students: quick access to the newest three records. -->
        <section class="recent-panel">
            <div class="page-header">
                <div>
                    <span class="eyebrow">Recently added</span>
                    <h2>Latest Students</h2>
                    <p>The newest records added to the system.</p>
                </div>
                <a href="view_students.php" class="btn btn-light">View All</a>
            </div>

            <div class="recent-grid">
                <?php if ($recent_students): ?>
                    <?php foreach ($recent_students as $student): ?>
                        <a class="recent-card" href="student_details.php?id=<?php echo $student['id']; ?>">
                            <span class="profile-badge small"><?php echo strtoupper(substr($student['name'], 0, 1)); ?></span>
                            <div>
                                <strong><?php echo $student['name']; ?></strong>
                                <span><?php echo $student['course']; ?></span>
                                <span class="status-pill status-<?php echo strtolower($student['status']); ?>"><?php echo $student['status']; ?></span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="empty-state">No student records yet.</p>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>
</main>

<?php include 'footer.php'; ?>
