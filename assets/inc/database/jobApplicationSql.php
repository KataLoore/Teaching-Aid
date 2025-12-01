<?php
/* 
 * This file contains SQL functions related to job applications.
 * These functions are used to create, update, and retrieve job 
 * application data from the database.
*/

require_once("db.php");
require_once(__DIR__ . "/../functions.php");

// -- CREATE --
// Create a new job application record
function createJobApplication($pdo, $jobApplicationData) {
    $uuid = generateUuid(); // generate UUID for the job application
    
    $sql = "INSERT INTO job_application (uuid, applicantId, jobPostId, coverLetter, status, submitDate) 
            VALUES (:uuid, :applicantId, :jobPostId, :coverLetter, :status, :submitDate)";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':uuid', $uuid, PDO::PARAM_STR); 
    $stmt->bindParam(':applicantId', $jobApplicationData['applicantId'], PDO::PARAM_INT);
    $stmt->bindParam(':jobPostId', $jobApplicationData['jobPostId'], PDO::PARAM_INT);
    $stmt->bindParam(':coverLetter', $jobApplicationData['coverLetter'], PDO::PARAM_STR);
    $stmt->bindParam(':status', $jobApplicationData['status'], PDO::PARAM_STR);
    $stmt->bindParam(':submitDate', $jobApplicationData['submitDate'], PDO::PARAM_STR);
    
    $result = $stmt->execute(); // returns boolean

    if (!$result) {
        throw new Exception("Failed to create job application");
    }
    return true;
}

// -- RETRIEVE --
// Get all job applications submitted by a specific applicant
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

// Get all applications for a specific job post
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

// Get applicants with user details for a specific job post
function getApplicantsForJobPost($pdo, $jobPostId) {
    $sql = "SELECT job_application.*, user.firstName, user.lastName, user.email
            FROM job_application
            JOIN user ON job_application.applicantId = user.userId
            WHERE job_application.jobPostId = :jobPostId
            ORDER BY job_application.submitDate DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':jobPostId', $jobPostId, PDO::PARAM_INT);
    $result = $stmt->execute();

    if (!$result) {
        throw new Exception("Failed to retrieve applicants for job post");
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get single job application by application ID
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

// Get job application by UUID with applicant and employer details
function getApplicationByUuid($pdo, $uuid) {
    $sql = "SELECT ja.*, jp.jobTitle, jp.university, jp.course, jp.deadlineDate, jp.employerId,
                   employer.firstName AS employerFirstName, 
                   employer.lastName AS employerLastName,
                   applicant.firstName AS applicantFirstName, 
                   applicant.lastName AS applicantLastName,
                   applicant.email AS applicantEmail
            FROM job_application ja
            JOIN job_post jp ON ja.jobPostId = jp.postId
            JOIN user employer ON jp.employerId = employer.userId
            JOIN user applicant ON ja.applicantId = applicant.userId
            WHERE ja.uuid = :uuid";
    $query = $pdo->prepare($sql);
    $query->bindParam(':uuid', $uuid, PDO::PARAM_STR);
    $result = $query->execute();
    
    if (!$result) {
        throw new Exception("Failed to retrieve job application by UUID");
    }
    
    return $query->fetch(PDO::FETCH_ASSOC);
}

// Check if user has already applied to a specific job
function hasUserAppliedToJob($pdo, $applicantId, $jobPostId) {
    $sql = "SELECT COUNT(*) as count 
            FROM job_application 
            WHERE applicantId = :applicantId AND jobPostId = :jobPostId";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':applicantId', $applicantId, PDO::PARAM_INT);
    $stmt->bindParam(':jobPostId', $jobPostId, PDO::PARAM_INT);
    $result = $stmt->execute();
    
    if (!$result) {
        throw new Exception("Failed to check if user has applied to job");
    }
    
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    return $data['count'] > 0;
}

// -- UPDATE --
// Update the status of a job application
function updateJobApplicationStatus($pdo, $applicationId, $newStatus) {
    $sql = "UPDATE job_application SET status = :status WHERE applicationId = :applicationId";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':status', $newStatus, PDO::PARAM_STR);
    $stmt->bindParam(':applicationId', $applicationId, PDO::PARAM_INT);
    
    $result = $stmt->execute(); // returns boolean

    if (!$result) {
        throw new Exception("Failed to update job application status");
    }
    return true;
}

// -- DELETE --
// Delete a job application by application ID
function deleteJobApplication($pdo, $applicationId) {
    $sql = "DELETE FROM job_application WHERE applicationId = :applicationId";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':applicationId', $applicationId, PDO::PARAM_INT);

    $result = $stmt->execute(); // returns boolean

    if (!$result) {
        throw new Exception("Failed to delete job application");
    }
    return true;
}