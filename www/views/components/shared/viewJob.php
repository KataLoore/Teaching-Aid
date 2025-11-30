<?php
/**
 * The viewJob view displays full details of a single job post
 * Works for both employers (viewing their own posts) and applicants (viewing jobs to apply for)
 */
    if(!isset($_SESSION['user']['loggedIn']) || $_SESSION['user']['loggedIn']!==True)  {
            echo "<script>
                    alert('Please log in to access this content.');
                    window.location.href = 'index.php';
                </script>";
            exit();
    }

    require_once('../../assets/inc/database/db.php');
    require_once('../../assets/inc/database/jobPostSql.php');
    require_once('../../assets/inc/functions.php');
        
    $message = "";
    $job = null;
    $isOwner = false;
    $userType = $_SESSION['user']['userType'];

    // Check if job ID is provided
    if (!isset($_GET['uuid']) || empty($_GET['uuid'])) {
        $redirectPage = ($userType === 'employer') ? 'myJobs' : 'availableJobs';
        header("Location: ?page=$redirectPage");
        exit();
    }

    $uuid = $_GET['uuid'];
    
    // Validate UUID format
    if (!isValidUuid($uuid)) {
        $message = "Invalid job identifier.";
    } else {
        try {
            $job = getJobPostByUuid($pdo, $uuid);
            
            if (!$job) {
                $message = "Job post not found.";
            } else {
                // SECURITY: Check access permissions based on user type
                if ($userType === 'employer') {
                    // Employers can only view their own job posts
                    if ($job['employerId'] !== $_SESSION['user']['userId']) {
                        $message = "You do not have permission to view this job post.";
                        $job = null;
                    } else {
                        $isOwner = true;
                    }
                } elseif ($userType === 'applicant') {
                    // Applicants can only view open jobs
                    if ($job['status'] !== 'open') {
                        $message = "This job posting is no longer accepting applications.";
                        $job = null;
                    }
                }
            }
            
        } catch (Exception $e) {
            error_log("Error retrieving job post: " . $e->getMessage());
            $message = "Unable to load job post at this time.";
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>View Job Post</title>
    <link rel="stylesheet" href="../../../assets/css/style.css">
</head>
<body>
    <div>
        <?php if ($userType === 'employer'): ?>
            <a href="?page=myJobs">← Back to Job List</a>
        <?php else: ?>
            <a href="?page=availableJobs">← Back to Browse Jobs</a>
        <?php endif; ?>
        
        <?php if (!empty($message)): ?>
            <p><?= htmlspecialchars($message) ?></p>
        <?php elseif ($job): ?>
            <h1><?= htmlspecialchars($job['jobTitle']) ?></h1>
            
            <div>
                <h2>Job Details</h2>
                
                <p>
                    <strong>Job Description:</strong><br>
                    <?= nl2br(htmlspecialchars($job['jobDescription'])) ?>
                </p>
                
                <p>
                    <strong>University:</strong> 
                    <?= htmlspecialchars($job['university']) ?>
                </p>
                
                <p>
                    <strong>Faculty:</strong> 
                    <?= htmlspecialchars($job['faculty']) ?>
                </p>
                
                <p>
                    <strong>Course:</strong> 
                    <?= htmlspecialchars($job['course']) ?>
                </p>
                
                <p>
                    <strong>Language:</strong> 
                    <?= htmlspecialchars($job['language'] === 'en' ? 'English' : 'Norwegian') ?>
                </p>
                
                <p>
                    <strong>Maximum Workload:</strong> 
                    <?= htmlspecialchars($job['maxWorkload']) ?> hours
                </p>
                
                <p>
                    <strong>Weekly Workload:</strong> 
                    <?= htmlspecialchars($job['weeklyWorkload']) ?> hours/week
                </p>
                
                <p>
                    <strong>Status:</strong> 
                    <?= htmlspecialchars($job['status']) ?>
                </p>
                
                <p>
                    <strong>Publication Date:</strong> 
                    <?= htmlspecialchars(date('F j, Y', strtotime($job['publicationDate']))) ?>
                </p>
                
                <p>
                    <strong>Application Deadline:</strong> 
                    <?= htmlspecialchars(date('F j, Y', strtotime($job['deadlineDate']))) ?>
                </p>
            </div>
            
            <div>
                <?php if ($userType === 'employer' && $isOwner): ?>
                    <!-- Employer actions -->
                    <a href="?page=editJob&id=<?= htmlspecialchars($job['uuid']) ?>">Edit Job Post</a>
                    <a href="?page=myJobs">Back to List</a>
                <?php elseif ($userType === 'applicant' && $job['status'] === 'open'): ?>
                    <!-- Applicant actions -->
                    <a href="?page=applyForJob&id=<?= htmlspecialchars($job['uuid']) ?>">Apply for This Job</a>
                    <a href="?page=availableJobs">Back to Browse</a>
                <?php else: ?>
                    <!-- Fallback -->
                    <a href="?page=overview">Back to Dashboard</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>