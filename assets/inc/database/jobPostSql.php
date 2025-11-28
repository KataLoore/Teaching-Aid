<?php
/**
 * The jobPostSql.php file contains SQL to insert, update and retrieve data 
 * from the job_post table in the database. 
 */

function createJobPost($pdo, $jobPostData) {
    $sql = "INSERT INTO job_post (employerId, jobTitle, jobDescription, university, faculty, course, language, maxWorkload, weeklyWorkload, deadlineDate) 
            VALUES (:employerId, :jobTitle, :jobDescription, :university, :faculty, :course, :language, :maxWorkload, :weeklyWorkload, :deadlineDate)";

    $stmt = $pdo->prepare($sql);
    
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
    
    $stmt->execute();
}

function retrieveEmployerJobs($pdo, $employerId) {
    $jobPosts = [];
    
    $sql = "SELECT * FROM job_post WHERE employerId = :employerId ORDER BY publicationDate DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':employerId', $employerId, PDO::PARAM_INT);
    $stmt->execute();
    $jobPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return $jobPosts;
} 


?>
