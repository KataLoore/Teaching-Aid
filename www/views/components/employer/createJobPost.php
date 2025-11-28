<?php
/*
* The createJobPost view processes form data for job post creation.
* Input data is sanitized, validated and tested for errors. 
* Additional functionality has been added to improve UX ($jobPostData 
* ensures only invalid fields are cleared in case of field validation errors)
*
* The job post creation process utilises two-layer error handling:
*   - Layer 1: Handles expected user errors through validator objects
*   - Layer 2: Handles unexpected system exceptions
*
* In both cases, the user gets feedback through $errorMessages, ensuring 
* that technical details stay hidden in case of system errors. 
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


$errorMessages = [];
$successMessage = '';
$jobPostData = []; // Store sanitized form data for repopulation in case of validation errors

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['createJobPost'])) {
    
    // Store sanitized form data
    $jobPostData = [
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

    $validator = new Validator();
    
    // Layer 1: Validate sanitized data
    $validator->validateJobTitle($jobPostData['jobTitle']);
    $validator->validateJobDescription($jobPostData['jobDescription']);
    $validator->validateWorkload($jobPostData['maxWorkload'], 'maxWorkload');
    $validator->validateWorkload($jobPostData['weeklyWorkload'], 'weeklyWorkload');
    $validator->validateDeadlineDate($jobPostData['deadlineDate']);
    
    // Optional field validations (only if provided)
    if (!empty($jobPostData['university'])) {
        $validator->validateName($jobPostData['university'], 'university');
    }
    if (!empty($jobPostData['faculty'])) {
        $validator->validateName($jobPostData['faculty'], 'faculty');
    }
    if (!empty($jobPostData['course'])) {
        $validator->validateCourseCode($jobPostData['course']);
    }
    
    $errorMessages = $validator->getErrors();
    
    if (!$validator->hasErrors()) {
        try {
            createJobPost($pdo, $jobPostData, $errorMessages); // Save job post to database
            
            // Only show success if no database errors occurred
            if (empty($errorMessages)) {
                $successMessage = "Job post created successfully!";
                $jobPostData = []; // Clear form data on success
            }
            
        // Layer 2: handle and log unexpected system/db exceptions
        } catch (Exception $e) {
            $errorMessages['registration'] = "An error occurred while creating the job post"; 
            error_log("Job post creation error: " . $e->getMessage());
        }
        // If validation fails, $jobPostData remains populated
    }   
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Create Job Post</title>
    <link rel="stylesheet" href="../../../../assets/css/style.css">
</head>
<body>
    <div>
        <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <div>
        <label for="jobTitle">
            Job Title <abbr title="The title of the job posting.">?</abbr>
        </label><br>
        <input type="text" name="jobTitle" required>
    </div>

    <div>
        <label for="jobDescription">
            Job Description <abbr title="A short summary of what the job involves.">?</abbr>
        </label><br>
        <input type="text" name="jobDescription" required>
    </div>

    <div>
        <label for="university">
            University <abbr title="Select the university associated with this job.">?</abbr>
        </label><br>
        <select name="university" required>
            <option value="uia">University of Agder (UiA)</option>
        </select>
    </div>

    <div>
        <label for="faculty">
            Faculty <abbr title="The faculty or department offering the job.">?</abbr>
        </label><br>
        <input type="text" name="faculty" required>
    </div>

    <div>
        <label for="course">
            Course <abbr title="The relevant course for this position.">?</abbr>
        </label><br>
        <input type="text" name="course" required>
    </div>

    <div>
        <label for="language">
            Language <abbr title="The required working language for this job.">?</abbr>
        </label><br>
        <select name="language" required>
            <option value="en">English</option>
            <option value="no">Norwegian</option>
        </select>
    </div>

    <div>
        <label for="maxWorkload">
            Maximum Workload <abbr title="Total number of hours allowed for this job.">?</abbr>
        </label><br>
        <input type="text" name="maxWorkload" required>
    </div>

    <div>
        <label for="weeklyWorkload">
            Weekly Workload <abbr title="Expected number of working hours per week.">?</abbr>
        </label><br>
        <input type="text" name="weeklyWorkload" required>
    </div>

    <div>
        <label for="deadlineDate">
            Deadline <abbr title="Final date for applications.">?</abbr>
        </label><br>
        <input type="date" name="deadlineDate" required>
    </div>

    <div>
        <button type="reset">Clear</button>
        <button type="submit" name="createJobPost">Post Job</button>
    </div>
</form>

        <div>
            <?php if(!empty($message)) { echo htmlspecialchars($message); } ?>
        </div>
    </div>
</body>
</html>