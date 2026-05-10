<?php
/**
 * Student Registration Page
 * This page allows the admin to add new student records to the database.
 */
include 'db_config.php';
include 'header.php';

// Page guard: only logged-in admins can add student records.
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Form options: these lists keep course and status values consistent in the database.
$course_options = ['Computer Science', 'Software Engineering', 'Web Development', 'Data Science', 'Business'];
$status_options = ['Active', 'Inactive', 'Graduated'];

// Page state: messages, validation errors, and old input values for failed submissions.
$message = "";
$errors = [];
$old = [
    'name' => '',
    'email' => '',
    'course' => '',
    'phone' => '',
    'status' => 'Active'
];

// Validation helper: the browser helps, but PHP must protect the database too.
function validate_student_input($name, $email, $course, $phone, $status, $course_options, $status_options) {
    $errors = [];

    if (strlen($name) < 3 || strlen($name) > 100) {
        $errors[] = "Full name must be between 3 and 100 characters.";
    }

    if (!preg_match("/^[a-zA-Z .'-]+$/", $name)) {
        $errors[] = "Full name can only contain letters, spaces, dots, apostrophes, and hyphens.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
        $errors[] = "Please enter a valid email address.";
    }

    if (!in_array($course, $course_options, true)) {
        $errors[] = "Please choose a valid course.";
    }

    if (!preg_match('/^[0-9+\-\s()]{10,20}$/', $phone)) {
        $errors[] = "Phone number must be 10 to 20 characters and may contain digits, spaces, +, -, or parentheses.";
    }

    if (!in_array($status, $status_options, true)) {
        $errors[] = "Please choose a valid student status.";
    }

    return $errors;
}

// Form submission: trim input, validate it, then insert a clean student record.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $course = trim($_POST['course'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $status = trim($_POST['status'] ?? 'Active');
    $old = compact('name', 'email', 'course', 'phone', 'status');

    $errors = validate_student_input($name, $email, $course, $phone, $status, $course_options, $status_options);

    // Duplicate check: using raw query
    if (!$errors) {
        $check_sql = "SELECT id FROM students WHERE email = '$email' LIMIT 1";
        $existing = $conn->query($check_sql);

        if ($existing && $existing->num_rows > 0) {
            $errors[] = "A student with this email already exists.";
        }
    }

    // Insert record: using raw query without prepared statements
    if (!$errors) {
        $sql = "INSERT INTO students (name, email, course, phone, status) VALUES ('$name', '$email', '$course', '$phone', '$status')";

        if ($conn->query($sql)) {
            $message = "Student registered successfully!";
            $old = ['name' => '', 'email' => '', 'course' => '', 'phone' => '', 'status' => 'Active'];
        } else {
            $message = "Error: " . $conn->error;
        }
    } else {
        $message = "Please fix the highlighted validation issues.";
    }
}
?>

<div class="container">
    <div class="page-header compact">
        <div>
            <span class="eyebrow">New record</span>
            <h2>Register New Student</h2>
            <p>Add accurate contact and course details for the student directory.</p>
        </div>
    </div>
    <?php if($message): ?>
        <p class="alert <?php echo ($errors || strpos($message, 'Error') === 0) ? 'alert-error' : 'alert-success'; ?>"><?php echo $message; ?></p>
    <?php endif; ?>
    <?php if($errors): ?>
        <ul class="alert-list">
            <?php foreach($errors as $error): ?>
                <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <!-- Student form: dropdowns keep course and status values consistent. -->
    <form action="register.php" method="POST" id="registrationForm" class="form-card">
        <label for="name">Full Name:</label>
        <input type="text" name="name" id="name" value="<?php echo $old['name']; ?>" minlength="3" maxlength="100" required>
        
        <label for="email">Email Address:</label>
        <input type="email" name="email" id="email" value="<?php echo $old['email']; ?>" maxlength="100" required>
        
        <label for="course">Course:</label>
        <select name="course" id="course" required>
            <option value="">Select a course</option>
            <?php foreach ($course_options as $course_option): ?>
                <option value="<?php echo $course_option; ?>" <?php echo $old['course'] === $course_option ? 'selected' : ''; ?>>
                    <?php echo $course_option; ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <label for="phone">Phone Number:</label>
        <input type="text" name="phone" id="phone" value="<?php echo $old['phone']; ?>" minlength="10" maxlength="20" required>

        <label for="status">Student Status:</label>
        <select name="status" id="status" required>
            <?php foreach ($status_options as $status_option): ?>
                <option value="<?php echo $status_option; ?>" <?php echo $old['status'] === $status_option ? 'selected' : ''; ?>>
                    <?php echo $status_option; ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <button type="submit" class="btn btn-success btn-full">Register Student</button>
    </form>
    <p class="form-link"><a href="view_students.php">View All Students</a></p>
</div>

<?php include 'footer.php'; ?>
