<?php
/**
 * The jobPostSql.php file contains functions with SQL to enable CRUD actions
 * in the job_post table in the database. 
 */

// -- CREATE --
// Create a new jobpost record
function createJobPost($pdo, $jobPostData) {
    $uuid = generateUuid();// generate UUID for the job post
    
    $sql = "INSERT INTO job_post (uuid, employerId, jobTitle, jobDescription, university, faculty, course, language, maxWorkload, weeklyWorkload, deadlineDate) 
            VALUES (:uuid, :employerId, :jobTitle, :jobDescription, :university, :faculty, :course, :language, :maxWorkload, :weeklyWorkload, :deadlineDate)";

    $stmt = $pdo->prepare($sql);
    
    $stmt->bindParam(':uuid', $uuid, PDO::PARAM_STR);
    $stmt->bindParam(':employerId', $jobPostData['employerId'], PDO::PARAM_INT);
    $stmt->bindParam(':jobTitle', $jobPostData['jobTitle'], PDO::PARAM_STR);
    $stmt->bindParam(':jobDescription', $jobPostData['jobDescription'], PDO::PARAM_STR);
    $stmt->bindParam(':university', $jobPostData['university'], PDO::PARAM_STR);
    $stmt->bindParam(':faculty', $jobPostData['faculty'], PDO::PARAM_STR);
    $stmt->bindParam(':course', $jobPostData['course'], PDO::PARAM_STR);
    $stmt->bindParam(':language', $jobPostData['language'], PDO::PARAM_STR);
    $stmt->bindParam(':maxWorkload', $jobPostData['maxWorkload'], PDO::PARAM_INT);
    $stmt->bindParam(':weeklyWorkload', $jobPostData['weeklyWorkload'], PDO::PARAM_INT);
    $stmt->bindParam(':deadlineDate', $jobPostData['deadlineDate'], PDO::PARAM_STR);
    
    $result = $stmt->execute(); // returns boolean

    if (!$result) {
        throw new Exception("Failed to create job post");
    }
    return true;
}

// -- RETRIEVE --
// Get all jobpost records by specified employer
function getEmployerJobs($pdo, $employerId) {
    $jobPosts = [];
    
    $sql = "SELECT * FROM job_post 
            WHERE employerId = :employerId 
            ORDER BY publicationDate DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':employerId', $employerId, PDO::PARAM_INT);
    $result = $stmt->execute();

    if (!$result) {
        throw new Exception("Failed to retrieve employer jobs");
    }

    $jobPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $jobPosts; // Returns the job array or false if not found
} 

// Get job post by post id
function getJobPostById($pdo, $postId) {
    $sql = "SELECT * FROM job_post WHERE postId = :postId";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':postId', $postId, PDO::PARAM_INT);
    $result = $stmt->execute();
    if (!$result) {
        throw new Exception("Failed to retrieve job post");
    }
    
    $job = $stmt->fetch(PDO::FETCH_ASSOC);
    return $job; // Returns the job array or false if not found
}

function getJobPostByUuid($pdo, $uuid) {
    // Validate UUID format first
    if (!isValidUuid($uuid)) {
        return false;
    }
    
    $sql = "SELECT * FROM job_post WHERE uuid = :uuid";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':uuid', $uuid, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// -- UPDATE --
// Update an existing job post by post id and employer id
function updateJobPost($pdo, $jobPostData) {
    $sql = "UPDATE job_post 
            SET jobTitle = :jobTitle,
                jobDescription = :jobDescription,
                university = :university,
                faculty = :faculty,
                course = :course,
                language = :language,
                maxWorkload = :maxWorkload,
                weeklyWorkload = :weeklyWorkload,
                deadlineDate = :deadlineDate
            WHERE postId = :postId AND employerId = :employerId";

    $stmt = $pdo->prepare($sql);
    
    $stmt->bindParam(':postId', $jobPostData['postId'], PDO::PARAM_INT);
    $stmt->bindParam(':employerId', $jobPostData['employerId'], PDO::PARAM_INT);
    $stmt->bindParam(':jobTitle', $jobPostData['jobTitle'], PDO::PARAM_STR);
    $stmt->bindParam(':jobDescription', $jobPostData['jobDescription'], PDO::PARAM_STR);
    $stmt->bindParam(':university', $jobPostData['university'], PDO::PARAM_STR);
    $stmt->bindParam(':faculty', $jobPostData['faculty'], PDO::PARAM_STR);
    $stmt->bindParam(':course', $jobPostData['course'], PDO::PARAM_STR);
    $stmt->bindParam(':language', $jobPostData['language'], PDO::PARAM_STR);
    $stmt->bindParam(':maxWorkload', $jobPostData['maxWorkload'], PDO::PARAM_INT);
    $stmt->bindParam(':weeklyWorkload', $jobPostData['weeklyWorkload'], PDO::PARAM_INT);
    $stmt->bindParam(':deadlineDate', $jobPostData['deadlineDate'], PDO::PARAM_STR);
    
    $result = $stmt->execute(); // returns boolean

    if (!$result) {
        throw new Exception("Failed to update job post");
    }
    
    return true;
}

// -- DELETE --

?>
