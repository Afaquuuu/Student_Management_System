<?php
if (session_status() === PHP_SESSION_NONE) {
    $session_path = __DIR__ . '/sessions';
    if (!file_exists($session_path)) {
        mkdir($session_path, 0777, true);
    }
    session_save_path($session_path);
    session_start();
}
session_unset();
session_destroy();
header("Location: index.php");
exit();
?>
