<?php // -- Logout script --
    session_start();
    unset($_SESSION['user']); // Clear all session data
    session_destroy();
    header('Location: index.php');
    exit();
?>