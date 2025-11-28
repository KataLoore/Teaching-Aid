<?php
/* 
 * This file contains SQL functions related to job applications.
 * These functions are used to create, update, and retrieve job application data from the database.
*/

require_once("db.php");

function updateJobApplication($pdo, $formdata, &$errorMessages) {
    // --- Add job application to job_application table ---
    try {
        $sql = "INSERT INTO job_application (applicantId, jobPostId, coverLetter, status, submitDate) 
                VALUES (:applicantId, :jobPostId, :coverLetter, :status, :submitDate)";

        $query = $pdo->prepare($sql);
        $query->bindParam(':applicantId', $formdata['applicantId'], PDO::PARAM_INT);
        $query->bindParam(':jobPostId', $formdata['jobPostId'], PDO::PARAM_INT);
        $query->bindParam(':coverLetter', $formdata['coverLetter'], PDO::PARAM_STR);
        $query->bindParam(':status', $formdata['status'], PDO::PARAM_STR);
        $query->bindParam(':submitDate', $formdata['submitDate'], PDO::PARAM_STR);

        $query->execute();

    } catch (PDOException $e) {
        error_log("Error inserting job application: " . $e->getMessage());
        $errorMessages['database'] = "Error creating job application. Please try again later.";
    }

}


function updateJobApplicationStatus($pdo, $applicationId, $newStatus, &$errorMessages) {
    try {
        $sql = "UPDATE job_application SET status = :status WHERE applicationId = :applicationId";
        $query = $pdo->prepare($sql);
        $query->bindParam(':status', $newStatus, PDO::PARAM_STR);
        $query->bindParam(':applicationId', $applicationId, PDO::PARAM_INT);
        $query->execute();
    } catch (PDOException $e) {
        error_log("Error updating job application status: " . $e->getMessage());
        $errorMessages['database'] = "Error updating job application status. Please try again later.";
    }
}

function getJobApplicationsByApplicant($pdo, $applicantId, &$errorMessages) {
    $applications = [];

    try {
        $sql = "SELECT * FROM job_application WHERE applicantId = :applicantId";
        $query = $pdo->prepare($sql);
        $query->bindParam(':applicantId', $applicantId, PDO::PARAM_INT);
        $query->execute();

        $applications = $query->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("Error retrieving job applications: " . $e->getMessage());
        $errorMessages['database'] = "Error retrieving job applications. Please try again later.";
    }

    return $applications;

}


?>