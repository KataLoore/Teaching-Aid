<?php 
/**
 * In settings.php, users can update their account settings, delete their account, and change user roles.
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

$message = "";

// Handle role change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['changeRole'])) {
    $newRole = $_POST['newRole'];
    
    if (in_array($newRole, ['applicant', 'employer'])) {
        try {
            $changeUserRole = changeUserRole($pdo, $_SESSION['user']['userId'], $newRole);
            $_SESSION['user']['userType'] = $newRole;


            $message = "Role changed successfully! Please refresh page, or click on any options in the navbar to the left to activate the change of roles, to see your new options";
        } catch (Exception $e) {
            error_log("Error changing role: " . $e->getMessage());
            $message = "Error changing role.";
        }
    }
}

// Delete user account
/* if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteUser'])){
    $removeUser = $_POST['deleteUser'];

    if(!in_array($removeUser, ['applicant','employer'])) {
        try {
            $deleteUser = deleteUser($pdo, $_SESSION[][], $deleteUser); 
        } catch  {  

        }
    }
}

*/
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
    <h1><?= htmlspecialchars('Name: ' . $_SESSION['user']['firstName'] . ' ' . $_SESSION['user']['lastName']) ?></h1>
    <p><?= htmlspecialchars('Username: ' . $_SESSION['user']['username']) ?></p>
    <div>
        <h2>Profile Information</h2>
        <p><strong>First Name:</strong> <?= htmlspecialchars($_SESSION['user']['firstName']) ?></p>
        <p><strong>Last Name:</strong> <?= htmlspecialchars($_SESSION['user']['lastName']) ?></p>
        <p><strong>Username:</strong> <?= htmlspecialchars($_SESSION['user']['username']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($_SESSION['user']['email']) ?></p>
        <p><strong>Current Role:</strong> <?= htmlspecialchars(ucfirst($_SESSION['user']['userType'])) ?></p>
    </div>
    
    <div>
        <h2>Change Role</h2>
        <form method="POST">
            <label for="newRole">New Role:</label>
            <select name="newRole" id="newRole" required>
                <option value="applicant" <?= $_SESSION['user']['userType'] === 'applicant' ? 'selected' : '' ?>>Applicant (Student)</option>
                <option value="employer" <?= $_SESSION['user']['userType'] === 'employer' ? 'selected' : '' ?>>Employer</option>
            </select>
            <button type="submit" name="changeRole">Change Role</button>
        </form>

    <div>
        <h2>Delete User</h2>
        <form method="$_POST">
            <label for=""></label>
            <select name="" id="" required>
                <option value="applicant"> </option>
                <option value="employer"> </option>
        </select>
        <button type="submit" name="deleteUser">Delete User</button>
        </form>
    </div>

    <?php if (!empty($message)): ?>
        <p><strong><?= htmlspecialchars($message) ?></strong></p>
    <?php endif; ?>
    </div>
</body>
</html>
