<?php
/**
 * The viewApplication  displays full details of a single job application
 * Works for both applicants (viewing their own application) and employer (viewing applications to choose candidate for a job) 
 */
if(!isset($_SESSION['user']['loggedIn']) || $_SESSION['user']['loggedIn']!==True)  {
    echo "<script>
            alert('Please log in to access this content.');
            window.location.href = '../../index.php';
        </script>";
    exit();
}

require_once('../../assets/inc/database/db.php');
require_once('../../assets/inc/database/jobApplicationSql.php');
require_once('../../assets/inc/database/jobPostSql.php');
require_once('../../assets/inc/functions.php');

$message = "";
$application = null;
$isOwner = false;
$userType = $_SESSION['user']['userType'];


// Check if application UUID is provided
if (!isset($_GET['uuid']) || empty($_GET['uuid'])) {
    $redirectPage = ($userType === 'employer') ? 'myJobs' : 'myApplications';
    header("Location: ?page$redirectPage");
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
            // Security check: Chech acess permission based on User Type
            if($userType === 'applicant') {
                if ($application['applicantId'] !== $_SESSION['user']['userId']) {
                $message = "You do not have permission to view this application.";
                $application = null; // Clear application data
                } else {
                    $isOwner = true;
                }
            } elseif ($userType === 'employer') {
                // Employers can only view applications for their own job posts
                // Need to check if this application is for one of their job posts
                $jobPost = getJobPostById($pdo, $application['jobPostId']);
                
                if (!$jobPost || $jobPost['employerId'] !== $_SESSION['user']['userId']) {
                    $message = "You do not have permission to view this application.";
                    $application = null; // Clear application data 
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
        <a href="?page=myApplications">← Back to My Applications</a>
        <?php if (!empty($message)): ?>
            <p  ?>><?= htmlspecialchars($message) ?></p>
        <?php elseif ($application): ?>
            <h1>Application for  <?= htmlspecialchars($application['jobTitle']) ?></h1>
            <div>
                <h2>Application Details</h2>
                <p><strong>Employer:</strong> <?= htmlspecialchars($application['firstName'] . ' ' . $application['lastName']) ?></p>
                <p><strong>University:</strong> <?= htmlspecialchars($application['university']) ?></p>
                <p><strong>Course:</strong> <?= htmlspecialchars($application['course']) ?></p>
                <p><strong>Status:</strong> <?= htmlspecialchars($application['status']) ?></p>
                <p><strong>Cover Letter:</strong> <?= nl2br(htmlspecialchars($application['coverLetter'])) ?></p>
                <p><strong>Applied On:</strong> <?= htmlspecialchars(date('F j, Y', strtotime($application['submitDate']))) ?></p>
            </div>
            <?php endif; ?>
    </div>
</body>
</html>