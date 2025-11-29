<?php
/* 
 * This file contains SQL functions related to job applications.
 * These functions are used to create, update, and retrieve job application data from the database.
*/

require_once("db.php");

function createJobApplication($pdo, $jobApplicationData) {
    $sql = "INSERT INTO job_application (applicantId, jobPostId, coverLetter, cv_path, status, submitDate) 
            VALUES (:applicantId, :jobPostId, :coverLetter, :cv_path, :status, :submitDate)";

    $query = $pdo->prepare($sql);
    $query->bindParam(':applicantId', $jobApplicationData['applicantId'], PDO::PARAM_INT);
    $query->bindParam(':jobPostId', $jobApplicationData['jobPostId'], PDO::PARAM_INT);
    $query->bindParam(':coverLetter', $jobApplicationData['coverLetter'], PDO::PARAM_STR);        
    $query->bindParam(':cv_path', $jobApplicationData['cv_Path'], PDO::PARAM_STR);
    $query->bindParam(':status', $jobApplicationData['status'], PDO::PARAM_STR);
    $query->bindParam(':submitDate', $jobApplicationData['submitDate'], PDO::PARAM_STR);
    
    $query->execute();
}


function updateJobApplicationStatus($pdo, $applicationId, $newStatus) {
    $sql = "UPDATE job_application SET status = :status WHERE applicationId = :applicationId";
    $query = $pdo->prepare($sql);
    $query->bindParam(':status', $newStatus, PDO::PARAM_STR);
    $query->bindParam(':applicationId', $applicationId, PDO::PARAM_INT);
    
    return $query->execute();
}

function getJobApplicationsByApplicant($pdo, $applicantId) {    
    $sql = "SELECT * FROM job_application WHERE applicantId = :applicantId";
    $query = $pdo->prepare($sql);
    $query->bindParam(':applicantId', $applicantId, PDO::PARAM_INT);
    $query->execute();
    
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

function getApplicationsSpecificJobPost($pdo, $jobPostId) {
    $sql = "SELECT * FROM job_application WHERE jobPostId = :jobPostId";
    $query = $pdo->prepare($sql);
    $query->bindParam(':jobPostId', $jobPostId, PDO::PARAM_INT);
    $query->execute();
    
    return $query->fetchAll(PDO::FETCH_ASSOC);
}   

function getSingleJobApplication($pdo, $applicationId) {
        $sql = "SELECT * FROM job_application WHERE applicationId = :applicationId";
        $query = $pdo->prepare($sql);
        $query->bindParam(':applicationId', $applicationId, PDO::PARAM_INT);
        $query->execute();
    
        return $query->fetch(PDO::FETCH_ASSOC);
}


function deleteJobApplication($pdo, $applicationId) {

        $sql = "DELETE FROM job_application WHERE applicationId = :applicationId";
        $query = $pdo->prepare($sql);
        $query->bindParam(':applicationId', $applicationId, PDO::PARAM_INT);

        return $query->execute(); 
  
}   