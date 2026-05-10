<?php
/**
 * Delete Student Page
 * This file only accepts POST requests so records are not deleted by opening a link.
 */
include 'db_config.php';

// Session setup: delete runs before the shared header, so we start the session here.
$session_path = __DIR__ . '/sessions';
if (!file_exists($session_path)) {
    mkdir($session_path, 0777, true);
}
session_save_path($session_path);
session_start();

// Delete record: using raw query without prepared statements.
$id = (int) ($_REQUEST['id'] ?? 0);
if ($id > 0) {
    $sql = "DELETE FROM students WHERE id = $id";
    if ($conn->query($sql)) {
        header("Location: view_students.php?msg=deleted");
        exit();
    }
}

// Fallback: return to the list if something was missing or invalid.
header("Location: view_students.php");
exit();
?>
