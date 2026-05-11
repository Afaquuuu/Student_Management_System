<?php
/**
 * Admin Login Page
 * This page handles the authentication for the system administrator.
 */
include 'db_config.php';

// Only start a session if PHP has not already started one on this request.
if (session_status() === PHP_SESSION_NONE) {
    // IMPORTANT: Set session path before starting session
    $session_path = __DIR__ . '/sessions';
    if (!file_exists($session_path)) {
        mkdir($session_path, 0777, true);
    }
    session_save_path($session_path);
    session_start();
}

$error = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Search for the admin in the database using raw query
    $sql = "SELECT * FROM admin WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows == 1) {
        $row = $result->fetch_assoc();
        
        // Check if the password matches the hash
        if (password_verify($password, $row['password'])) {
            
            // Set session variables to keep the admin logged in
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $username;
            
            // Redirect to home page
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "Admin not found!";
    }
}

// Now include the header (which contains HTML)
include 'header.php';
?>

<div class="container">
    <div class="page-header compact">
        <div>
            <span class="eyebrow">Secure access</span>
            <h2>Admin Login</h2>
            <p>Sign in to manage student records.</p>
        </div>
    </div>
    <?php if($error): ?>
        <p class="alert alert-error"><?php echo $error; ?></p>
    <?php endif; ?>
    <form action="login.php" method="POST" class="form-card">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required>
        
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>
        
        <button type="submit" class="btn btn-primary btn-full">Login</button>
    </form>
    <p class="credential-note">Default Credentials: <b>admin / admin123</b></p>
</div>

<?php include 'footer.php'; ?>
