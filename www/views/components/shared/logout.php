<?php
/**
 * User logout functionality that destroys the current session and redirects to login page.
 * Clears all session data to ensure complete logout and prevents unauthorized access to protected pages.
 * Dependencies: PHP session management - no external files required for basic logout functionality.
 */ // -- Logout script --
    session_start();
    unset($_SESSION['user']); // Clear all session data
    session_destroy();
    header('Location: /Teaching-Aid/www/logIn.php');
    exit();
?>