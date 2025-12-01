<?php
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'teaching_aid');
    $dsn = 'mysql:dbname=' . DB_NAME . ';host=' . DB_HOST; 

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        if (!$pdo) {
            throw new Exception("Failed to connect to database");
        }
    } catch (PDOException $e) {
        error_log("Database connection error: " . $e->getMessage());
        echo "System error";
    }
?>