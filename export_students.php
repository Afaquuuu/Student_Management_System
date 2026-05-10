<?php
/**
 * Export Students Page
 * Downloads the current student list as a CSV file for Excel or Google Sheets.
 */
include 'db_config.php';

// Session setup: exports happen before HTML is printed, so we start the session here.
$session_path = __DIR__ . '/sessions';
if (!file_exists($session_path)) {
    mkdir($session_path, 0777, true);
}
session_save_path($session_path);
session_start();

// Page guard: only logged-in admins can export student data.
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Filter options: these keep export filters aligned with the records page.
$course_options = ['Computer Science', 'Software Engineering', 'Web Development', 'Data Science', 'Business'];
$status_options = ['Active', 'Inactive', 'Graduated'];

// Read the same filters used on view_students.php.
$search = trim($_GET['q'] ?? '');
$course_filter = trim($_GET['course'] ?? '');
$status_filter = trim($_GET['status'] ?? '');

// Build the export query using raw SQL.
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

$sql = "SELECT id, name, email, course, phone, status, created_at, updated_at FROM students";
if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY id DESC";

$result = $conn->query($sql);

// CSV response: the browser will download this as a spreadsheet-friendly file.
$filename = "students_export_" . date("Y-m-d") . ".csv";
header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename=\"$filename\"");

$output = fopen("php://output", "w");
fputcsv($output, ['ID', 'Name', 'Email', 'Course', 'Phone', 'Status', 'Created At', 'Updated At']);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['id'],
        $row['name'],
        $row['email'],
        $row['course'],
        $row['phone'],
        $row['status'],
        $row['created_at'],
        $row['updated_at']
    ]);
}

fclose($output);
exit();
?>
