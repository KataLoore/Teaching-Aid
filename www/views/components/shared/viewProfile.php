<?php
/**
 * View Profile Component
 * 
 */
require_once('../../assets/inc/database/db.php');
require_once('../../assets/inc/database/userSql.php');

// check to see if user is logged in
if(!isset($_SESSION['user']['loggedIn']) || $_SESSION['user']['loggedIn']!==True) {
    echo "<script>
            alert('Please log in to access this content.');
            window.location.href = '../../logIn.php';
        </script>";
    exit();
}

$user = getUserById($pdo, $_SESSION['user']['userId']);

//Check to see if correct user
if (!$user) {
    echo "<script>
            alert('User authentication failed. Please log in again.');
            window.location.href = '../../logIn.php';
          </script>";
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="../../../assets/css/style.css">
</head>
<body>
    <h1><?= htmlspecialchars('Name: ' . ucfirst($_SESSION['user']['firstName']) . ' ' . ucfirst($_SESSION['user']['lastName'])) ?></h1>
    <div>
        <h2>Profile Information</h2>
        <p><strong>Username:</strong> <?= htmlspecialchars($_SESSION['user']['username']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($_SESSION['user']['email']) ?></p>
        <p><strong>Current Role:</strong> <?= htmlspecialchars(ucfirst($_SESSION['user']['userType'])) ?></p>
    </div>
</body>
</html>