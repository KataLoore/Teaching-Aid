<?php
/**
 * The userSql.php file contains SQL to insert, update and retrieve data 
 * from the user table in the database. The functions are utilised in the user class.
 * 
 * @see user.php 
 */

function createUser($pdo, $newUser, &$errorMessages) {

        // --- Add user to users table ---
        try {
            // Check if username already exists 
            $usernameSql = "SELECT EXISTS(SELECT 1 FROM user where username = :username)";

            $usernameQuery = $pdo->prepare($usernameSql);
            $usernameQuery->bindParam(':username', $newUser['username'], PDO::PARAM_STR);
            $usernameQuery->execute();

            // Check if email already exists 
            $emailSql = "SELECT EXISTS(SELECT 1 FROM user where email = :email)";

            $emailQuery = $pdo->prepare($emailSql);
            $emailQuery->bindParam(':email', $newUser['email'], PDO::PARAM_STR);
            $emailQuery->execute();

            // Store value (0|1) from sql queries
            $usernameExists = $usernameQuery->fetchColumn();
            $emailExists = $emailQuery->fetchColumn();

            // Error handling
            if ($usernameExists && $emailExists) {
                $errorMessages['database'] = "User with this username and email already exists";
            } elseif ($usernameExists) {
                $errorMessages['database'] = "User with this username already exists";
            } elseif ($emailExists) {
                $errorMessages['database'] = "User with this email already exists";
            } else {
                // Insert user if neither username or email already exists in db
                $sql = "INSERT INTO user (firstName, lastName, username, email, password, userType) VALUES (:firstName, :lastName, :username, :email, :password, :userType)";

                $st = $pdo->prepare($sql);

                $st->bindParam(':firstName', $newUser['firstName'], PDO::PARAM_STR);
                $st->bindParam(':lastName', $newUser['lastName'], PDO::PARAM_STR);
                $st->bindParam(':username', $newUser['username'], PDO::PARAM_STR);
                $st->bindParam(':email', $newUser['email'], PDO::PARAM_STR);
                $st->bindParam(':password', $newUser['password'], PDO::PARAM_STR);
                $st->bindParam(':userType', $newUser['userType'], PDO::PARAM_STR);

                $st->execute();
            } 
        }
            catch (PDOException $e) {
                error_log("INSERT user error: " . $e->getMessage());
                $errorMessages['database'] = "An error occured during user registration. <br>";
            }
    }

    
?>