<?php
/**
 * Job application submission form for applicants to apply to available teaching assistant positions.
 * Validates application data, prevents duplicate applications, and ensures applicants cannot apply to their own job posts.
 * Dependencies: validator.php, jobApplicationSql.php, and functions.php for form validation and database operations.
 */

if(!isset($_SESSION['user']['loggedIn']) || $_SESSION['user']['loggedIn']!==True) {
    echo "<script>
            alert('Please log in to access this content.');
            window.location.href = '../../logIn.php';
          </script>";
    exit();
} elseif ($_SESSION['user']['userType'] !== 'applicant') {
    header("Location: ../dashboard.php");
    exit();
}

require_once '../../assets/inc/functions.php';
require_once '../../assets/lib/validator.php';
require_once '../../assets/inc/database/db.php';
require_once '../../assets/inc/database/jobApplicationSql.php';
require_once '../../assets/inc/database/jobPostSql.php';

$messages = [];
$formData = $_POST;
$jobPost = null;

// Get job UUID from URL parameter
if (isset($_GET['uuid'])) {
    $uuid = $_GET['uuid'];
    
    // Validate UUID format
    if (!isValidUuid($uuid)) {
        $messages[] = "Invalid job identifier.";
    } else {
        try {
            $jobPost = getJobPostByUuid($pdo, $uuid);
            if (!$jobPost) {
                $messages[] = "Job post not found.";
            } elseif ($jobPost['status'] !== 'open') {
                $messages[] = "This job posting is no longer accepting applications.";
                $jobPost = null;
            }
        } catch (Exception $e) {
            $messages[] = "Error retrieving job information.";
            error_log("Error fetching job post: " . $e->getMessage());
        }
    }
} else {
    $messages[] = "No job specified for application.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['createApplication']) && $jobPost) {
    
    $validator = new Validator();
    
    // Validate cover letter input
    $validator->validateCoverLetter($_POST['coverLetter']);
    
    // Security validation
    if (!$validator->hasErrors()) {
        $validator->validateNotOwnJob($pdo, $jobPost['postId'], $_SESSION['user']['userId']);
        $validator->validateNotDuplicateApplication($pdo, $_SESSION['user']['userId'], $jobPost['postId']);
    }
    
    // If no validation errors, proceed to create application
    if (!$validator->hasErrors()) {
        try {
            $applicationData = [ // sanitize data
                'applicantId' => $_SESSION['user']['userId'],
                'jobPostId' => $jobPost['postId'],
                'coverLetter' => cleanFormInput($_POST['coverLetter']),
                'cv_Path' => null, // TODO: Implement file upload later
                'status' => 'submitted',
                'submitDate' => date("Y-m-d H:i:s")
            ];
            
            createJobApplication($pdo, $applicationData);
            $messages[] = "Application submitted successfully!";
            $formData = []; // clear form after success
            
        } catch (Exception $e) {
            $messages[] = "An error occurred while submitting your application"; 
            error_log("Job application creation error: " . $e->getMessage());
        }
    } else {
        $messages = $validator->getErrors();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Create Job Application</title>
    <link rel="stylesheet" href="../../../../assets/css/style.css">
</head>
<body>

    <p><br><a href="?page=availableJobs">← Back to Available Jobs</a></p>
    
    <div class="form-container-wide">
        <h1 style="text-align: center;">Submit Job Application</h1>
        
        <?php if ($jobPost): ?>
            <!-- Job Information Section -->
            <details>
                <summary> Details</summary>
                <div style="margin-left: 20px; margin-top: 10px;">
                    <p><strong>Job Title:</strong> <?= htmlspecialchars($jobPost['jobTitle']) ?></p>
                    <p><strong>Course:</strong> <?= htmlspecialchars($jobPost['course']) ?></p>
                    <p><strong>University:</strong> <?= htmlspecialchars($jobPost['university']) ?></p>
                    <p><strong>Faculty:</strong> <?= htmlspecialchars($jobPost['faculty']) ?></p>
                    <p><strong>Language:</strong> <?= htmlspecialchars($jobPost['language']) ?></p>
                    <p><strong>Workload:</strong> <?= htmlspecialchars($jobPost['weeklyWorkload']) ?>h/week (max <?= htmlspecialchars($jobPost['maxWorkload']) ?>h)</p>
                    <p><strong>Application Deadline:</strong> <?= htmlspecialchars(date('Y-m-d', strtotime($jobPost['deadlineDate']))) ?></p>
                </div>
            </details>

            <!-- Applicant Information Section -->
            <details>
                <summary>Your Information</summary>
                <div style="margin-left: 20px; margin-top: 10px;">
                    <p><strong>Name:</strong> <?= htmlspecialchars($_SESSION['user']['firstName'] . ' ' . $_SESSION['user']['lastName']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($_SESSION['user']['email']) ?></p>
                    <p><em>This is the information the employer will see when reviewing your application.</em></p>
                </div>
            </details>

            <!-- Application Form -->
            <form method="POST" action=""><br>
                <div>
                    <label for="coverLetter">Cover Letter <abbr title="Explain why you're qualified for this position">?</abbr></label>
                    <textarea name="coverLetter" id="coverLetter" rows="8" placeholder="Explain why you're qualified for this position and why you're interested in this role..." required><?= preserveFormValue($formData, 'coverLetter') ?></textarea>
                </div>

                <div>
                    <button type="submit" name="createApplication">Submit Application</button>
                </div>
            </form>
        <?php endif; ?>
        
        <div>
            <?php 
            if (!empty($messages)) {
                foreach ($messages as $msg) {
                    echo htmlspecialchars($msg) . '<br>';
                }
            }
            ?>
        </div>
        
    </div>
</body>
</html>