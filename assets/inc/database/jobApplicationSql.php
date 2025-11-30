<?php
/* 
 * This file contains SQL functions related to job applications.
 * These functions are used to create, update, and retrieve job application data from the database.
*/

require_once("db.php");

function createJobApplication($pdo, $jobApplicationData) {
    $uuid = generateUuid();
    
    $sql = "INSERT INTO job_application (uuid, applicantId, jobPostId, coverLetter, status, submitDate) 
            VALUES (:uuid, :applicantId, :jobPostId, :coverLetter, :status, :submitDate)";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':uuid', $uuid, PDO::PARAM_STR); // <-- Bind UUID
    $stmt->bindParam(':applicantId', $jobApplicationData['applicantId'], PDO::PARAM_INT);
    $stmt->bindParam(':jobPostId', $jobApplicationData['jobPostId'], PDO::PARAM_INT);
    $stmt->bindParam(':coverLetter', $jobApplicationData['coverLetter'], PDO::PARAM_STR);
    $stmt->bindParam(':status', $jobApplicationData['status'], PDO::PARAM_STR);
    $stmt->bindParam(':submitDate', $jobApplicationData['submitDate'], PDO::PARAM_STR);
    
    $result = $stmt->execute();

    if (!$result) {
        throw new Exception("Failed to create job post");
    }
    return true;
}


function updateJobApplicationStatus($pdo, $applicationId, $newStatus) {
    $sql = "UPDATE job_application SET status = :status WHERE applicationId = :applicationId";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':status', $newStatus, PDO::PARAM_STR);
    $stmt->bindParam(':applicationId', $applicationId, PDO::PARAM_INT);
    
    $result = $stmt->execute();

    if (!$result) {
        throw new Exception("Failed to update job application status");
    }
    return true;
}

/*
function updateJobApplicationCoverLetter($pdo, $applicationId, $newCoverLetter) {
    $sql = "UPDATE job_application SET coverLetter = :coverLetter WHERE applicationId = :applicationId";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':coverLetter', $newCoverLetter, PDO::PARAM_STR);
    $stmt->bindParam(':applicationId', $applicationId, PDO::PARAM_INT);
    
    return $stmt->execute();
}
*/

function getJobApplicationsByApplicant($pdo, $applicantId) {    
    $sql = "SELECT ja.*, jp.jobTitle, jp.university, jp.course, u.firstName, u.lastName
            FROM job_application ja
            JOIN job_post jp ON ja.jobPostId = jp.postId
            JOIN user u ON jp.employerId = u.userId
            WHERE ja.applicantId = :applicantId
            ORDER BY ja.submitDate DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':applicantId', $applicantId, PDO::PARAM_INT);
    $result = $stmt->execute();

    if (!$result) {
        throw new Exception("Failed to retrieve job applications by applicant");
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getApplicationsSpecificJobPost($pdo, $jobPostId) {
    $sql = "SELECT * FROM job_application WHERE jobPostId = :jobPostId";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':jobPostId', $jobPostId, PDO::PARAM_INT);
    $result = $stmt->execute();

    if (!$result) {
        throw new Exception("Failed to retrieve job applications for the specific job post");
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}   

function getSingleJobApplication($pdo, $applicationId) {
        $sql = "SELECT * FROM job_application WHERE applicationId = :applicationId";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':applicationId', $applicationId, PDO::PARAM_INT);
        $result = $stmt->execute();
    
        if (!$result) {
            throw new Exception("Failed to retrieve the job application");
        }

        return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getApplicationByUuid($pdo, $uuid) {
    if (!isValidUuid($uuid)) {
        return false;
    }
    
    $sql = "SELECT ja.*, jp.jobTitle, jp.university, jp.course, jp.deadlineDate,
                   u.firstName, u.lastName
            FROM job_application ja
            JOIN job_post jp ON ja.jobPostId = jp.postId
            JOIN user u ON jp.employerId = u.userId
            WHERE ja.uuid = :uuid";
    $query = $pdo->prepare($sql);
    $query->bindParam(':uuid', $uuid, PDO::PARAM_STR);
    $result = $query->execute();
    
    if (!$result) {
        throw new Exception("Failed to retrieve job application by UUID");
    }
    
    return $query->fetch(PDO::FETCH_ASSOC);
}

function deleteJobApplication($pdo, $applicationId) {
    $sql = "DELETE FROM job_application WHERE applicationId = :applicationId";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':applicationId', $applicationId, PDO::PARAM_INT);

    $result = $stmt->execute();

    if (!$result) {
        throw new Exception("Failed to delete job application");
    }
    return true;
}