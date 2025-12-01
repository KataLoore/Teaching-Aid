<?php
/**
 * The userSql.php file contains functions with SQL to enable CRUD actions in the user table in the database. 
 */

// -- CREATE --
function createUser($pdo, $newUser) {
    $sql = "INSERT INTO user (firstName, lastName, username, email, password, userType) VALUES (:firstName, :lastName, :username, :email, :password, :userType)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':firstName', $newUser['firstName'], PDO::PARAM_STR);
    $stmt->bindParam(':lastName', $newUser['lastName'], PDO::PARAM_STR);
    $stmt->bindParam(':username', $newUser['username'], PDO::PARAM_STR);
    $stmt->bindParam(':email', $newUser['email'], PDO::PARAM_STR);
    $stmt->bindParam(':password', $newUser['password'], PDO::PARAM_STR);
    $stmt->bindParam(':userType', $newUser['userType'], PDO::PARAM_STR);
    
    $result = $stmt->execute();
    
    if (!$result) {
        throw new Exception("Failed to create user");
    }
    return true;
}

// -- RETRIEVE --
function getUserById($pdo, $userId) {
    $sql = "SELECT userId, firstName, lastName, username, email, userType 
            FROM user WHERE userId = :userId";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $result = $stmt->execute();
    
    if(!$result) {
        throw new Exception("Failed to retrieve user by ID");
    }
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}    

// -- UPDATE --
function updateUser($pdo, $userUpdates) {
    $sql = "UPDATE user SET 
                firstName = :firstName,
                lastName = :lastName,
                username = :username,
                email = :email,
                password = :password,
            WHERE userId = :userId";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':firstName', $userUpdates['firstName'], PDO::PARAM_STR);
    $stmt->bindParam(':lastName', $userUpdates['lastName'], PDO::PARAM_STR);
    $stmt->bindParam(':username', $userUpdates['username'], PDO::PARAM_STR);
    $stmt->bindParam(':email', $userUpdates['email'], PDO::PARAM_STR);
    $stmt->bindParam(':password', $userUpdates['password'], PDO::PARAM_STR);
    $stmt->bindParam(':userId', $userUpdates['userId'], PDO::PARAM_INT);
    $result = $stmt->execute();
    
    if(!$result) {
        throw new Exception("Failed to update user");
    }

    return true; 
}

function changeUserRole($pdo, $userId, $newRole) {
    $sql = "UPDATE user SET userType = :userType WHERE userId = :userId";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userType', $newRole, PDO::PARAM_STR);
    $stmt->bindParam(':userId', $userId, PDO::PARAM_STR);
    $result = $stmt->execute();

    if(!$result) {
        throw new Exception("Failed to change user role");
    }
    return true;
}

// -- DELETE --
function deleteUser($pdo, $userId) {
    try {
        // Start transaction for cascading deletions
        $pdo->beginTransaction();
        
        // Delete job applications first (if user is applicant)
        $sql = "DELETE FROM job_application WHERE applicantId = :userId";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        // Delete job posts (if user is employer) - this will cascade to applications for those jobs
        $sql = "DELETE FROM job_application WHERE jobPostId IN (SELECT postId FROM job_post WHERE employerId = :userId)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        $sql = "DELETE FROM job_post WHERE employerId = :userId";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        // Finally delete the user
        $sql = "DELETE FROM user WHERE userId = :userId";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $result = $stmt->execute();
        
        if (!$result) {
            throw new Exception("Failed to delete user");
        }
        
        // Commit transaction
        $pdo->commit();
        return true;
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollback();
        throw new Exception("Failed to delete user and dependencies: " . $e->getMessage());
    }
}
?>
