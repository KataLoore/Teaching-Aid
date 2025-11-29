<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
</head>
<body>
    <h1><?php echo htmlspecialchars($_SESSION['user']['firstName'] . ' ' . $_SESSION['user']['lastName']); ?></h1>
    <p>@<?php echo htmlspecialchars($_SESSION['user']['username']); ?></p>
    
    <div>
        <p><strong>First Name:</strong> <?php echo htmlspecialchars($_SESSION['user']['firstName']); ?></p>
        <p><strong>Last Name:</strong> <?php echo htmlspecialchars($_SESSION['user']['lastName']); ?></p>
        <p><strong>Username:</strong> <?php echo htmlspecialchars($_SESSION['user']['username']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['user']['email']); ?></p>
        <p><strong>User Type:</strong> <?php echo htmlspecialchars($_SESSION['user']['userType']); ?></p>
    </div>
</body>
</html>