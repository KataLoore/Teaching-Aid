<?php
/**
 * The userSql.php file contains SQL to insert, update and retrieve data 
 * from the user table in the database. The functions are utilised in the user class.
 * 
 * @see user.php 
 */

function insertUser($pdo, $newUser) {
    $sql = "INSERT INTO user (firstName, lastName, username, email, password, userType) VALUES (:firstName, :lastName, :username, :email, :password, :userType)";
    
    $query = $pdo->prepare($sql);
    $query->bindParam(':firstName', $newUser['firstName'], PDO::PARAM_STR);
    $query->bindParam(':lastName', $newUser['lastName'], PDO::PARAM_STR);
    $query->bindParam(':username', $newUser['username'], PDO::PARAM_STR);
    $query->bindParam(':email', $newUser['email'], PDO::PARAM_STR);
    $query->bindParam(':password', $newUser['password'], PDO::PARAM_STR);
    $query->bindParam(':userType', $newUser['userType'], PDO::PARAM_STR);
    
    return $query->execute();
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
    
    $query = $pdo->prepare($sql);
    $query->bindParam(':firstName', $userUpdates['firstName'], PDO::PARAM_STR);
    $query->bindParam(':lastName', $userUpdates['lastName'], PDO::PARAM_STR);
    $query->bindParam(':username', $userUpdates['username'], PDO::PARAM_STR);
    $query->bindParam(':email', $userUpdates['email'], PDO::PARAM_STR);
    $query->bindParam(':password', $userUpdates['password'], PDO::PARAM_STR);
    $query->bindParam(':userType', $userUpdates['userType'], PDO::PARAM_STR);
    $query->bindParam(':userId', $userUpdates['userId'], PDO::PARAM_INT);
    $query->execute();
   
}

function getUserById($pdo, $user) {
    $sql = "SELECT userId, firstName, lastName, username, email, userType 
            FROM user WHERE userId = :userId";
    
    $query = $pdo->prepare($sql);
    $query->bindParam(':userId', $user['userId'], PDO::PARAM_INT);
    $query->execute();
    
    return $query->fetch(PDO::FETCH_ASSOC);
}


function deleteUser($pdo, $userId) {
    $sql = "DELETE FROM user WHERE userId = :userId";
    $query = $pdo->prepare($sql);
    $query->bindParam(':userId', $userId, PDO::PARAM_INT);
    $query->execute();
}


 /*

// Change user role (requirement!)
$sql = "UPDATE users SET role = :role WHERE user_id = :user_id";
$query = $pdo->prepare($sql);
$query->bindParam(':role', $newRole, PDO::PARAM_STR);
$query->bindParam(':user_id', $userId, PDO::PARAM_INT);
$query->execute();}
}

 */

?>
