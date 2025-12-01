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

$messages = [];
$formData = $_POST;

// Pre-fill jobPostId if coming from a specific job listing
if (isset($_GET['jobId'])) {
    $prefilledJobId = (int)$_GET['jobId'];
} else {
    $prefilledJobId = '';
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['createApplication'])) {
    
    $validator = new Validator();
    
    // Validate raw input
    $validator->validateJobPostId($_POST['jobPostId']);
    $validator->validateCoverLetter($_POST['coverLetter']);
    
     // Security validation
    if (!$validator->hasErrors()) {
        $jobPostId = (int)cleanFormInput($_POST['jobPostId']);
        $validator->validateNotOwnJob($pdo, $jobPostId, $_SESSION['user']['userId']);
        $validator->validateNotDuplicateApplication($pdo, $_SESSION['user']['userId'], $jobPostId);
    }
    // If no validation errors, proceed to create application
    if (!$validator->hasErrors()) {
        try {
            $applicationData = [ // sanitize data
                'applicantId' => $_SESSION['user']['userId'],
                'jobPostId' => cleanFormInput($_POST['jobPostId']),
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
    <div class="form-container">
        <h1 style="text-align: center;">Submit Job Application</h1>
        
        <form method="POST" action="">
            <div>
                Job Post ID <abbr title="The ID of the job you're applying for">?</abbr> <input type="number" name="jobPostId" value="<?= preserveFormValue($formData, 'jobPostId') ?: htmlspecialchars($prefilledJobId) ?>" required>
            </div>

            <div>
                Cover Letter <abbr title="Explain why you're qualified for this position">?</abbr><br>
                <textarea name="coverLetter" rows="8" placeholder="Explain why you're qualified for this position" required><?= preserveFormValue($formData, 'coverLetter') ?></textarea>
            </div>

            <div>
                <button type="submit" name="createApplication">Submit Application</button>
            </div>
        </form>
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