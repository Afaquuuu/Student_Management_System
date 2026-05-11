<?php
// Only start a session if PHP has not already started one on this request.
if (session_status() === PHP_SESSION_NONE) {
    // Session setup: keep session files inside the project so it works smoothly on local setups.
    $session_path = __DIR__ . '/sessions';
    if (!file_exists($session_path)) {
        mkdir($session_path, 0777, true);
    }
    session_save_path($session_path);
    session_start();
}

// Current page name: used to highlight the right navigation item.
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <div class="logo">
            <a href="index.php" aria-label="Student Portal home">
                <span class="logo-mark">SP</span>
                <span>Student Portal</span>
            </a>
        </div>
        <div class="menu">
            <a href="index.php" class="<?php echo $current_page === 'index.php' ? 'active' : ''; ?>">Home</a>
            <?php if(isset($_SESSION['admin_logged_in'])): ?>
                <a href="view_students.php" class="<?php echo in_array($current_page, ['view_students.php', 'student_details.php', 'edit_student.php']) ? 'active' : ''; ?>">Students</a>
                <a href="register.php" class="<?php echo $current_page === 'register.php' ? 'active' : ''; ?>">Register</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php" class="<?php echo $current_page === 'login.php' ? 'active' : ''; ?>">Admin Login</a>
            <?php endif; ?>
        </div>
    </nav>
