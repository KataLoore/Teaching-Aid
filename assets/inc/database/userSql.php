<?php
/**
 * The userSql.php file contains functions with SQL to enable CRUD actions
 * in the user table in the database. 
 * 
 * @see user.php 
 */

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
    
function updateUser($pdo, $userUpdates) {
    $sql = "UPDATE user SET 
                firstName = :firstName,
                lastName = :lastName,
                username = :username,
                email = :email,
                password = :password,
                userType = :userType
            WHERE userId = :userId";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':firstName', $userUpdates['firstName'], PDO::PARAM_STR);
    $stmt->bindParam(':lastName', $userUpdates['lastName'], PDO::PARAM_STR);
    $stmt->bindParam(':username', $userUpdates['username'], PDO::PARAM_STR);
    $stmt->bindParam(':email', $userUpdates['email'], PDO::PARAM_STR);
    $stmt->bindParam(':password', $userUpdates['password'], PDO::PARAM_STR);
    $stmt->bindParam(':userType', $userUpdates['userType'], PDO::PARAM_STR);
    $stmt->bindParam(':userId', $userUpdates['userId'], PDO::PARAM_INT);
    $stmt->execute();
   
}

function getUserById($pdo, $user) {
    $sql = "SELECT userId, firstName, lastName, username, email, userType 
            FROM user WHERE userId = :userId";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userId', $user['userId'], PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


function deleteUser($pdo, $userId) {
    $sql = "DELETE FROM user WHERE userId = :userId";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();
}

?>
