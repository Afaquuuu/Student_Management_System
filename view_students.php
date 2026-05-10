<?php
/**
 * Student Records Display Page
 * This page fetches all student records from the database and displays them in a table.
 */
include 'db_config.php';
include 'header.php';

// Page guard: student records should only be visible after admin login.
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit(); // Always use exit after header redirect
}

// Filter options: these match the register/edit forms.
$course_options = ['Computer Science', 'Software Engineering', 'Web Development', 'Data Science', 'Business'];
$status_options = ['Active', 'Inactive', 'Graduated'];

// Read filters from the URL so searches can be bookmarked and exported.
$search = trim($_GET['q'] ?? '');
$course_filter = trim($_GET['course'] ?? '');
$status_filter = trim($_GET['status'] ?? '');
$total_result = $conn->query("SELECT COUNT(*) AS total FROM students");
$total_students = $total_result ? (int) $total_result->fetch_assoc()['total'] : 0;

// Build a simple filtered query using raw SQL.
$where = [];

if ($search !== '') {
    $where[] = "(name LIKE '%$search%' OR email LIKE '%$search%' OR course LIKE '%$search%' OR phone LIKE '%$search%' OR status LIKE '%$search%')";
}

if (in_array($course_filter, $course_options, true)) {
    $where[] = "course = '$course_filter'";
}

if (in_array($status_filter, $status_options, true)) {
    $where[] = "status = '$status_filter'";
}

$sql = "SELECT * FROM students";
if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY id DESC";

$result = $conn->query($sql);

$shown_students = $result ? $result->num_rows : 0;
?>

<div class="container">
    <div class="page-header">
        <div>
            <span class="eyebrow">Records</span>
            <h2>Student Records</h2>
            <p>
                <?php echo $total_students; ?> student<?php echo $total_students === 1 ? '' : 's'; ?> currently registered.
                <?php if ($search !== '' || $course_filter !== '' || $status_filter !== ''): ?>
                    Showing <?php echo $shown_students; ?> matching result<?php echo $shown_students === 1 ? '' : 's'; ?>.
                <?php endif; ?>
            </p>
        </div>
        <div class="hero-actions">
            <a href="export_students.php?q=<?php echo $search; ?>&course=<?php echo $course_filter; ?>&status=<?php echo $status_filter; ?>" class="btn btn-light">Export CSV</a>
            <a href="register.php" class="btn btn-success">+ Add New Student</a>
        </div>
    </div>

    <!-- Flash message: small feedback after update or delete actions. -->
    <?php if(isset($_GET['msg'])): ?>
        <p class="alert <?php echo $_GET['msg'] === 'invalid' ? 'alert-error' : 'alert-success'; ?>">
            <?php
                if ($_GET['msg'] === 'deleted') {
                    echo 'Student record deleted successfully.';
                } elseif ($_GET['msg'] === 'invalid') {
                    echo 'Delete request was blocked for safety. Please try again from the records table.';
                } else {
                    echo 'Student record updated successfully.';
                }
            ?>
        </p>
    <?php endif; ?>

    <div class="summary-strip" aria-label="Student records summary">
        <div>
            <span>Total records</span>
            <strong><?php echo $total_students; ?></strong>
        </div>
        <div>
            <span>Visible records</span>
            <strong><?php echo $shown_students; ?></strong>
        </div>
        <div>
            <span>Search status</span>
            <strong><?php echo ($search !== '' || $course_filter !== '' || $status_filter !== '') ? 'Filtered' : 'All records'; ?></strong>
        </div>
    </div>

    <!-- Search and filters: all values are sent through GET so export uses the same view. -->
    <form class="table-toolbar" action="view_students.php" method="GET">
        <label for="studentSearch">Search students</label>
        <div class="search-row">
            <input type="search" id="studentSearch" name="q" value="<?php echo $search; ?>" placeholder="Search by name, email, course, or phone">
            <select name="course" aria-label="Filter by course">
                <option value="">All courses</option>
                <?php foreach ($course_options as $course_option): ?>
                    <option value="<?php echo $course_option; ?>" <?php echo $course_filter === $course_option ? 'selected' : ''; ?>>
                        <?php echo $course_option; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select name="status" aria-label="Filter by status">
                <option value="">All statuses</option>
                <?php foreach ($status_options as $status_option): ?>
                    <option value="<?php echo $status_option; ?>" <?php echo $status_filter === $status_option ? 'selected' : ''; ?>>
                        <?php echo $status_option; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary">Apply</button>
            <a href="view_students.php" class="btn btn-light">Clear</a>
        </div>
    </form>

    <!-- Student table: action buttons stay close to each record. -->
    <div class="table-wrap">
    <table id="studentsTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Course</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Updated</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><strong><?php echo $row['name']; ?></strong></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><span class="course-pill"><?php echo $row['course']; ?></span></td>
                        <td><?php echo $row['phone']; ?></td>
                        <td><span class="status-pill status-<?php echo strtolower($row['status']); ?>"><?php echo $row['status']; ?></span></td>
                        <td><?php echo date("M d, Y", strtotime($row['updated_at'])); ?></td>
                        <td class="actions-cell">
                            <a href="student_details.php?id=<?php echo $row['id']; ?>" class="btn-view">View</a>
                            <a href="edit_student.php?id=<?php echo $row['id']; ?>" class="btn-edit">Edit</a>
                            <form action="delete_student.php" method="POST" class="inline-form">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn-delete" data-confirm="Are you sure you want to delete this record?">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="empty-state">No students found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <p class="empty-state is-hidden" id="noSearchResults">No matching students found.</p>
    </div>
</div>

<?php include 'footer.php'; ?>
