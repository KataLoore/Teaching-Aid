<?php

if (!isset($_SESSION['user']['loggedIn']) || $_SESSION['user']['loggedIn']!==True)  {
    echo "<script>
            alert('Please log in to access this content.');
            window.location.href = '../../index.php';
         </script>";
    exit();
} elseif ($_SESSION['user']['userType'] !== 'employer') {
    header("Location: ../dashboard.php");
    exit();
}

require_once('../../assets/inc/database/db.php');
require_once('../../assets/inc/database/jobApplicationSql.php');


// change status of application
$applicationId = $_GET['id'] ?? null;
$message = "";
if ($applicationId) {
    $newStatus = $_POST['status'] ?? null;

    if ($newStatus) {
        try {
            $updated = updateJobApplicationStatus($pdo, $applicationId, $newStatus);
            if ($updated) {
                $message = "Application status updated successfully.";
            } else {
                $message = "Failed to update application status.";
            }
        } catch (Exception $e) {
            error_log("Error updating application status: " . $e->getMessage());
            $message = "Unable to update application status at this time.";
        }
    }
} else {
    $message = "Invalid application ID.";
}