<?php
/*
* The createUser view processes form data for user registration.
* Input data is sanitized, validated and tested for errors. 
* Additional functionality has been added to improve UX ($registrationData 
* ensures only invalid fields are cleared in case of field validation errors)
*
* The user-creation process utilises two-layer error handling:
*   - Layer 1: Handles expected user errors through validator objects
*   - Layer 2: Handles unexpected system exceptions
*
* In both cases, the user gets feedback through $errorMessages, ensuring 
* that technical details stay hidden in case of system errors. 
*/

require_once '../../assets/inc/functions.php';
require_once '../../assets/lib/validator.php';
require_once '../../assets/inc/database/db.php';
require_once '../../assets/inc/database/userSql.php';

$errorMessages = [];
$successMessage = '';
$registrationData = []; // Store sanitized form data for repopulation in case of validation errors

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    
    // Store sanitized form data
    $registrationData = [
        'firstName' => cleanFormInput($_POST['firstName']),
        'lastName' => cleanFormInput($_POST['lastName']),
        'username' => cleanFormInput($_POST['username']),
        'email' => cleanFormInput($_POST['email']),
        'userType' => cleanFormInput($_POST['userType']),
        'password' => $_POST['password']
    ];

    $validator = new Validator();
    
    // Layer 1: Validate sanitized data
    $validator->validateName($registrationData['firstName'], 'firstName');
    $validator->validateName($registrationData['lastName'], 'lastName');
    $validator->validateUsername($registrationData['username']);
    $validator->validateEmail($registrationData['email']);
    $validator->validateUserType($registrationData['userType']);
    $validator->validatePassword($registrationData['password']);
    
    $errorMessages = $validator->getErrors();
    
    if (!$validator->hasErrors()) {
        try {
            // Hash password and add to form data
            $registrationData['password'] = password_hash($registrationData['password'], PASSWORD_DEFAULT);
            
            createUser($pdo, $registrationData); // Save user to database
            
            // Only show success if no database errors occurred
            if (empty($errorMessages)) {
                $successMessage = "Registration successful!";
                $registrationData = []; // Clear form data on success
            }
            
        // Layer 2: handle and log unexpected system/db exceptions
        } catch (Exception $e) {
            $errorMessages['registration'] = "An error occurred during registration"; 
            error_log("User registration error: " . $e->getMessage());
            displayErrorMessage($errorMessages, 'registration');
        }
        // If validation fails, $registrationData remains populated
    }   
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teaching Aid - Sign Up</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body class="register-body">

    <!-- Success message display -->
        <?php if (!empty($successMessage)): ?>
            <div style="text-align: center;"><?= htmlspecialchars($successMessage) ?></div>
        <?php endif; ?>

    <div class="form-container">
        <h1 style="text-align: center;">Create an Account</h1>

        <!-- Error messages display -->
        <?php foreach (['registration', 'database'] as $errorType): ?>
            <?php if (isset($errorMessages[$errorType])): ?>
                <div><?= htmlspecialchars($errorMessages[$errorType]) ?></div>
            <?php endif; ?>
        <?php endforeach; ?>
        
        <!-- Registration Form -->
        <form method="post" action="<?=htmlspecialchars($_SERVER['PHP_SELF']);?>" autocomplete="off">
            
            <div>
                <label for="firstName">First Name</label>
                <input required type="text" id="firstName" name="firstName" placeholder="Johnson" autocomplete="off" value="<?= preserveFormValue($registrationData, 'firstName') ?>">
                <?= displayErrorMessage($errorMessages, 'firstName') ?>
            </div>

            <div>
                <label for="lastName">Last Name</label>
                <input required type="text" id="lastName" name="lastName" placeholder="Doe" autocomplete="off" value="<?= preserveFormValue($registrationData, 'lastName') ?>">
                <?= displayErrorMessage($errorMessages, 'lastName') ?>
            </div>

            <div>
                <label for="email">Email</label>
                <input required type="email" id="email" name="email" placeholder="johnsondoe@gmail.com" autocomplete="off" value="<?= preserveFormValue($registrationData, 'email') ?>">
                <?= displayErrorMessage($errorMessages, 'email') ?>
            </div>

            <div>
                <label for="username">Username</label>
                <input required type="text" id="username" name="username" placeholder="johnsondoe" autocomplete="off" value="<?= preserveFormValue($registrationData, 'username') ?>">
                <?= displayErrorMessage($errorMessages, 'username') ?>
            </div>

            <div>
                <label for="password">Password</label>
                <input required type="password" id="password" name="password" placeholder="••••••••••••" autocomplete="off">
                <?= displayErrorMessage($errorMessages, 'password') ?>
            </div>

            <div>
                <label for="userType">Register as</label>
                <select required id="userType" name="userType">
                    <option value=""></option>
                    <option value="applicant" <?= isSelected($registrationData, 'userType', 'applicant') ?>>Student</option>
                    <option value="employer" <?= isSelected($registrationData, 'userType', 'employer') ?>>Employer</option>
                </select>
                <?= displayErrorMessage($errorMessages, 'userType') ?>
            </div>

            <button type="submit" name="register">Register</button>
        </form>
        <div><br>
            Already have an account? <a href="../logIn.php">Login here!</a>
        </div>
    </div>
</body>
</html>