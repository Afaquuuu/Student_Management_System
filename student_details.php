<?php
/**
 * Student Details Page
 * Shows one student record in a readable profile-style layout.
 */
include 'db_config.php';
include 'header.php';

// Page guard: only logged-in admins can view full student profiles.
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Load one record based on the id in the URL.
$student = null;
$id = (int) ($_GET['id'] ?? 0);

if ($id > 0) {
    $sql = "SELECT * FROM students WHERE id = $id";
    $result = $conn->query($sql);
    $student = $result->fetch_assoc();
}

// Friendly fallback: show a helpful message if the record is missing.
if (!$student) {
    echo "<div class='container'><p class='alert alert-error'>Student not found.</p><p><a href='view_students.php'>Back to student records</a></p></div>";
    include 'footer.php';
    exit();
}
?>

<div class="container">
    <div class="page-header">
        <div>
            <span class="eyebrow">Student profile</span>
            <h2><?php echo $student['name']; ?></h2>
            <p>Complete record and quick actions for this student.</p>
        </div>
        <div class="hero-actions">
            <a href="edit_student.php?id=<?php echo $student['id']; ?>" class="btn btn-primary">Edit Student</a>
            <a href="view_students.php" class="btn btn-light">Back to List</a>
        </div>
    </div>

    <!-- Student profile card: one clean place to review a complete record. -->
    <section class="details-card">
        <div class="profile-badge" aria-hidden="true">
            <?php echo strtoupper(substr($student['name'], 0, 1)); ?>
        </div>

        <div class="details-grid">
            <div class="detail-item">
                <span>Student ID</span>
                <strong><?php echo $student['id']; ?></strong>
            </div>
            <div class="detail-item">
                <span>Full Name</span>
                <strong><?php echo $student['name']; ?></strong>
            </div>
            <div class="detail-item">
                <span>Email Address</span>
                <strong><a href="mailto:<?php echo $student['email']; ?>"><?php echo $student['email']; ?></a></strong>
            </div>
            <div class="detail-item">
                <span>Course</span>
                <strong><span class="course-pill"><?php echo $student['course']; ?></span></strong>
            </div>
            <div class="detail-item">
                <span>Phone Number</span>
                <strong><a href="tel:<?php echo $student['phone']; ?>"><?php echo $student['phone']; ?></a></strong>
            </div>
            <div class="detail-item">
                <span>Status</span>
                <strong><span class="status-pill status-<?php echo strtolower($student['status']); ?>"><?php echo $student['status']; ?></span></strong>
            </div>
            <div class="detail-item">
                <span>Registered On</span>
                <strong><?php echo date("M d, Y", strtotime($student['created_at'])); ?></strong>
            </div>
            <div class="detail-item">
                <span>Last Updated</span>
                <strong><?php echo date("M d, Y", strtotime($student['updated_at'])); ?></strong>
            </div>
        </div>
    </section>
</div>

<?php include 'footer.php'; ?>
