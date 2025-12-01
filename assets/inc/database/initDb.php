<?php
/**
 * The initDb.php file contains sql statements to create all of the tables in the db.
 */
require_once("db.php");
        
    // ---- Create user table if it does not exist ----
    try {
        $sql = "CREATE TABLE IF NOT EXISTS user (
            userId INT AUTO_INCREMENT PRIMARY KEY,
            firstName VARCHAR(50) NOT NULL,
            lastName VARCHAR(50) NOT NULL,
            username VARCHAR(50) NOT NULL UNIQUE,
            email VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            userType ENUM('employer', 'applicant') NOT NULL
        )";

        $pdo->exec($sql);

    } catch (PDOException $e) {
        error_log("Error creating user table: " . $e->getMessage());
        echo "A system error has occured. <br>"; 
    }

    // ---- Create job_post table if it does not exist ----
    try {
        $sql = "CREATE TABLE IF NOT EXISTS job_post (
            postId INT AUTO_INCREMENT PRIMARY KEY,
            uuid CHAR(36) NOT NULL UNIQUE,
            employerId INT NOT NULL,
            jobTitle VARCHAR(100) NOT NULL,
            jobDescription TEXT NOT NULL, 
            university VARCHAR(100),
            faculty VARCHAR(100),
            course VARCHAR(100),
            language VARCHAR(20), 
            maxWorkload SMALLINT,
            weeklyWorkload TINYINT,
            status ENUM('open', 'closed') DEFAULT 'open',
            publicationDate DATETIME DEFAULT CURRENT_TIMESTAMP,
            deadlineDate DATETIME,
            FOREIGN KEY (employerId) REFERENCES user(userId)
        )";

        $pdo->exec($sql);

    } catch (PDOException $e) {
        error_log("Error creating job_post table: " . $e->getMessage());
        echo "A system error has occured. <br>"; 
    }

    // ---- Create job_application table if it does not exist ----
    try {
        $sql = "CREATE TABLE IF NOT EXISTS job_application (
            applicationId INT AUTO_INCREMENT PRIMARY KEY,
            uuid CHAR(36) NOT NULL UNIQUE,
            applicantId INT NOT NULL,
            jobPostId INT NOT NULL,
            coverLetter TEXT,
            status ENUM('submitted', 'under review', 'rejected', 'accepted') NOT NULL DEFAULT 'submitted',
            submitDate DATE NOT NULL,
            FOREIGN KEY (applicantId) REFERENCES user(userId),
            FOREIGN KEY (jobPostId) REFERENCES job_post(postId)
        )";

        $pdo->exec($sql);
    
    } catch (PDOException $e) {
        error_log("Error creating job_application table: " . $e->getMessage());
        echo "A system error has occured. <br>";
    }
?>