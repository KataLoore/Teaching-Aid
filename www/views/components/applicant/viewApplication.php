<?php
/**
 * The viewApplication  displays full details of a single job application
 * Works for both applicants (viewing their own application) and employer (viewing applications to choose candidate for a job) 
 */
if(!isset($_SESSION['user']['loggedIn']) || $_SESSION['user']['loggedIn']!==True)  {
    echo "<script>
            alert('Please log in to access this content.');
            window.location.href = '../../logIn.php';
        </script>";
    exit();
}

require_once('../../assets/inc/database/db.php');
require_once('../../assets/inc/database/jobApplicationSql.php');
require_once('../../assets/inc/database/jobPostSql.php');
require_once('../../assets/inc/functions.php');
require_once('../../assets/inc/database/jobPostSql.php');


$message = "";
$application = null;
$isOwner = false;
$userType = $_SESSION['user']['userType'];


// Check if application UUID is provided
if (!isset($_GET['uuid']) || empty($_GET['uuid'])) {
    $redirectPage = ($userType === 'employer') ? 'myJobs' : 'myApplications';
    header("Location: ?page=$redirectPage");
    exit();
}

$uuid = $_GET['uuid'];

// Validate UUID format
if (!isValidUuid($uuid)) {
    $message = "Invalid application identifier.";
} else {
    try {
        $application = getApplicationByUuid($pdo, $uuid);
        
        if (!$application) {
            $message = "Application not found.";
        } else {
            // Security check: Check access permission based on User Type
            if($userType === 'applicant') {

                if ($application['applicantId'] !== $_SESSION['user']['userId']) {
                    $message = "You do not have permission to view this application.";
                    $application = null;
                } else {
                    $isOwner = true;
                }

            } elseif ($userType === 'employer') {
                // Employers can only view applications for their own job posts
                if ($application['employerId'] !== $_SESSION['user']['userId']) {
                    $message = "You do not have permission to view this application.";
                    $application = null;
                }
            }
        }
    } catch (Exception $e) {
        error_log("Error retrieving application: " . $e->getMessage());
        $message = "Unable to load application at this time.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>View Application</title>
    <link rel="stylesheet" href="../../../assets/css/style.css">
</head>
<body>
    <div>
        <?php if ($userType === 'applicant'): ?>
            <a href="?page=myApplications">← Back to My Applications</a>
        <?php else: ?>
            <a href="?page=myJobs">← Back to My Jobs</a>
        <?php endif; ?>
        
        <?php if (!empty($message)): ?>
            <p><?= htmlspecialchars($message) ?></p>

        <?php elseif ($application): ?>
            <?php if ($userType === 'applicant'): ?>
                <!-- Applicant viewing their own application -->
                <h1>Your Application for <?= htmlspecialchars($application['jobTitle']) ?></h1>
                <div>
                    <h2>Job Details</h2>
                    <p><strong>Employer:</strong> <?= htmlspecialchars($application['employerFirstName'] . ' ' . $application['employerLastName']) ?></p>
                    <p><strong>University:</strong> <?= htmlspecialchars($application['university']) ?></p>
                    <p><strong>Course:</strong> <?= htmlspecialchars($application['course']) ?></p>
                    <p><strong>Status:</strong> <?= htmlspecialchars($application['status']) ?></p>
                    <p><strong>Applied On:</strong> <?= htmlspecialchars(date('F j, Y', strtotime($application['submitDate']))) ?></p>
                    
                    <h2>Your Cover Letter</h2>
                    <p><?= nl2br(htmlspecialchars($application['coverLetter'])) ?></p>
                </div>

            <?php else: ?>
                <!-- Employer viewing an application to their job -->
                <h1>Application for <?= htmlspecialchars($application['jobTitle']) ?></h1>
                <div>
                    <h2>Applicant Information</h2>
                    <p><strong>Name:</strong> <?= htmlspecialchars($application['applicantFirstName'] . ' ' . $application['applicantLastName']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($application['applicantEmail']) ?></p>
                    <p><strong>Status:</strong> <?= htmlspecialchars($application['status']) ?></p>
                    <p><strong>Applied On:</strong> <?= htmlspecialchars(date('F j, Y', strtotime($application['submitDate']))) ?></p>
                    
                    <h2>Cover Letter</h2>
                    <p><?= nl2br(htmlspecialchars($application['coverLetter'])) ?></p>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>