<?php
/**
 * The editJobPost view processes form data for job post updates.
 * Only accessible by the employer who created the job post.
 */

if(!isset($_SESSION['user']['loggedIn']) || $_SESSION['user']['loggedIn']!==True)  {
        echo "<script>
                alert('Please log in to access this content.');
                window.location.href = 'index.php';
              </script>";
        exit();
} elseif ($_SESSION['user']['userType'] !== 'employer') {
    header("Location: views/dashboard.php");
    exit();
}

require_once('../../assets/inc/functions.php');
require_once('../../assets/lib/validator.php');
require_once('../../assets/inc/database/db.php');
require_once('../../assets/inc/database/jobPostSql.php');
    
$messages = [];
$job = null;
$isOwner = false;

// Check if job ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ?page=myJobs");
    exit();
}

$uuid = $_GET['id'];

// Validate UUID format and get job data
if (!isValidUuid($uuid)) {
    $messages[] = "Invalid job identifier.";
} else {
    try {
        // Use secure function that verifies ownership
        $job = getJobPostByUuidForEmployer($pdo, $uuid, $_SESSION['user']['userId']);
        
        if (!$job) {
            $messages[] = "Job post not found or you don't have permission to edit it.";
        } else {
            $isOwner = true;
        }
        
    } catch (Exception $e) {
        error_log("Error retrieving job post for edit: " . $e->getMessage());
        $messages[] = "Unable to load job post at this time.";
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['updateJobPost']) && $isOwner) {
    
    $validator = new Validator();
    
    // Validate raw input
    $validator->validateJobTitle($_POST['jobTitle']);
    $validator->validateJobDescription($_POST['jobDescription']);
    $validator->validateWorkload($_POST['maxWorkload'], 'maxWorkload');
    $validator->validateWorkload($_POST['weeklyWorkload'], 'weeklyWorkload');
    $validator->validateDeadlineDate($_POST['deadlineDate']);
    $validator->validateName($_POST['university'], 'university');
    $validator->validateName($_POST['faculty'], 'faculty');
    $validator->validateCourseCode($_POST['course']);
    
    if (!$validator->hasErrors()) { 
        try {
            $jobData = [ // sanitize data
                'postId' => $job['postId'],
                'employerId' => $_SESSION['user']['userId'],
                'jobTitle' => cleanFormInput($_POST['jobTitle']),
                'jobDescription' => cleanFormInput($_POST['jobDescription']),
                'university' => cleanFormInput($_POST['university']),
                'faculty' => cleanFormInput($_POST['faculty']),
                'course' => cleanFormInput($_POST['course']),
                'language' => cleanFormInput($_POST['language']),
                'maxWorkload' => cleanFormInput($_POST['maxWorkload']),
                'weeklyWorkload' => cleanFormInput($_POST['weeklyWorkload']),
                'deadlineDate' => cleanFormInput($_POST['deadlineDate'])
            ];
            
            $result = updateJobPost($pdo, $jobData); 
            if($result === True) {
                $messages[] = "Job post updated successfully!";
                // Reload job data to show updated values
                $job = getJobPostByUuidForEmployer($pdo, $uuid, $_SESSION['user']['userId']);
            }
            
        } catch (Exception $e) {
            $messages[] = "An error occurred while updating the job post"; 
            error_log("Job post update error: " . $e->getMessage());
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
    <title>Edit Job Post</title>
    <link rel="stylesheet" href="../../../assets/css/style.css">
</head>
<body>
    <div>
        <a href="?page=myJobs">← Back to Job List</a>
        
        <?php if (!empty($messages)): ?>
            <div>
                <?php foreach ($messages as $msg): ?>
                    <p><?= htmlspecialchars($msg) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($job && $isOwner): ?>
            <h1>Edit Job Post</h1>
            
            <form method="POST" action="">
                <div>
                    <label for="jobTitle">
                        Job Title <abbr title="The title of the job posting.">?</abbr>
                    </label><br>
                    <input type="text" name="jobTitle" value="<?= htmlspecialchars($job['jobTitle']) ?>" required>
                </div>

                <div>
                    <label for="jobDescription">
                        Job Description <abbr title="A short summary of what the job involves.">?</abbr>
                    </label><br>
                    <textarea name="jobDescription" required><?= htmlspecialchars($job['jobDescription']) ?></textarea>
                </div>

                <div>
                    <label for="university">
                        University <abbr title="Select the university associated with this job.">?</abbr>
                    </label><br>
                    <select name="university" required>
                        <option value="uia" <?= $job['university'] === 'uia' ? 'selected' : '' ?>>University of Agder (UiA)</option>
                    </select>
                </div>

                <div>
                    <label for="faculty">
                        Faculty <abbr title="The faculty or department offering the job.">?</abbr>
                    </label><br>
                    <input type="text" name="faculty" value="<?= htmlspecialchars($job['faculty']) ?>" required>
                </div>

                <div>
                    <label for="course">
                        Course <abbr title="The relevant course for this position.">?</abbr>
                    </label><br>
                    <input type="text" name="course" value="<?= htmlspecialchars($job['course']) ?>" required>
                </div>

                <div>
                    <label for="language">
                        Language <abbr title="The required working language for this job.">?</abbr>
                    </label><br>
                    <select name="language" required>
                        <option value="en" <?= $job['language'] === 'en' ? 'selected' : '' ?>>English</option>
                        <option value="no" <?= $job['language'] === 'no' ? 'selected' : '' ?>>Norwegian</option>
                    </select>
                </div>

                <div>
                    <label for="maxWorkload">
                        Maximum Workload <abbr title="Total number of hours allowed for this job.">?</abbr>
                    </label><br>
                    <input type="text" name="maxWorkload" value="<?= htmlspecialchars($job['maxWorkload']) ?>" required>
                </div>

                <div>
                    <label for="weeklyWorkload">
                        Weekly Workload <abbr title="Expected number of working hours per week.">?</abbr>
                    </label><br>
                    <input type="text" name="weeklyWorkload" value="<?= htmlspecialchars($job['weeklyWorkload']) ?>" required>
                </div>

                <div>
                    <label for="deadlineDate">
                        Deadline <abbr title="Final date for applications.">?</abbr>
                    </label><br>
                    <input type="date" name="deadlineDate" value="<?= htmlspecialchars(date('Y-m-d', strtotime($job['deadlineDate']))) ?>" required>
                </div>

                <div>
                    <button type="button" onclick="window.location.href='?page=myJobs'">Cancel</button>
                    <button type="submit" name="updateJobPost">Update Job Post</button>
                </div>
            </form>
        <?php elseif (!$job): ?>
            <p>Job post not found or access denied.</p>
            <a href="?page=myJobs">Back to Job List</a>
        <?php endif; ?>
    </div>
</body>
</html>