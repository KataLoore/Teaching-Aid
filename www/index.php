<?php
    // initialize db tables
    require_once('../assets/inc/database/initDb.php');
    require_once('../assets/inc/functions.php');
    session_start();
    $message; 
    
    if(isset($_POST['login'])) {
        $username = cleanFormInput($_POST['username']);

        try { // check if username exists in db
            $sql = "SELECT userId, firstName, lastName, username, email, password, userType
                    FROM user WHERE username = :username";
            $q = $pdo->prepare($sql);
            $q->bindParam(':username', $username, PDO::PARAM_STR);
            $q->execute();
            $user_db = $q->fetch(PDO::FETCH_OBJ);

            // verify user if it exists and provided password matches db hashed pw
            if($user_db && password_verify($_POST['password'], $user_db->password)) {
                // successful login
                $_SESSION['user']['userId'] = $user_db->userId;
                $_SESSION['user']['firstName'] = $user_db->firstName;
                $_SESSION['user']['lastName'] = $user_db->lastName;
                $_SESSION['user']['username'] = $user_db->username;
                $_SESSION['user']['email'] = $user_db->email;
                $_SESSION['user']['userType'] = $user_db->userType;
                $_SESSION['user']['loggedIn'] = True;
                $_SESSION['user']['loginTimestamp'] = time();

                // Redirect to dashboard
                header("Location: views/dashboard.php");
                exit();

            } else {
                global $message;
                $message = "Username or password incorrect.";
            }

        } catch (PDOException $e) {
            error_log($e->getMessage());
            global $message;
            $message='Login failed.';
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teaching Aid - Login</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div>
        <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <div>
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div>
                <label for="password">Password</label>
                <input type="password" name="password" required>
            </div>
            <div>
                <button type="submit" name="login">Login</button>
            </div>
        </form>
        <div>
            <?php if(!empty($message)) { echo htmlspecialchars($message); } ?>
        </div>
        <a href="views/createUser.php">Register User</a>
    </div>
</body>
</html>