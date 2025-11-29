<?php
/*
* The createJobPost view processes form data for job post creation.
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

require_once '../../assets/inc/functions.php';
require_once '../../assets/lib/validator.php';
require_once '../../assets/inc/database/db.php';
require_once '../../assets/inc/database/jobPostSql.php';
    
$messages = []; 
$formData = $_POST;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['createJobPost'])) {
    
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
            
            $result = createJobPost($pdo, $jobData); 
            if($result === True) {
                $messages[] = "Job post created successfully!";
                $formData = []; // clear form on success
            }
            
            
        } catch (Exception $e) {
            $messages[] = "An error occurred while creating the job post"; 
            error_log("Job post creation error: " . $e->getMessage());
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
    <title>Create Job Post</title>
    <link rel="stylesheet" href="../../../../assets/css/style.css">
</head>
<body>
    <div>
        <form method="POST" action="">
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