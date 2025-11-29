<?php
if(!isset($_SESSION['user']['loggedIn']) || $_SESSION['user']['loggedIn']!==True)  {
    echo "<script>
            alert('Please log in to access this content.');
            window.location.href = '../../index.php';
        </script>";
    exit();
} elseif ($_SESSION['user']['userType'] !== 'applicant') {
    header("Location: ../dashboard.php");
    exit();
}

require_once('../../assets/inc/database/db.php');
require_once('../../assets/inc/database/jobApplicationSql.php');

$applicationId = $_GET['id'] ?? null;
$message = "";
$application = null;

if (!$applicationId) {
    header("Location: ?page=myApplications");
    exit();
}

try {
    $application = getJobApplicationsByApplicant($pdo, $applicantId);
    if (!$application) {
        $message = "Application not found.";
    }
} catch (Exception $e) {
    error_log("Error retrieving application: " . $e->getMessage());
    $message = "Unable to load application at this time.";
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
            <p><?= htmlspecialchars($message) ?></p>
        <?php elseif ($app): ?>
            <h1>Application for <?= htmlspecialchars($app['jobTitle']) ?></h1>
            <div>
                <h2>Application Details</h2>
                <p><strong>Employer:</strong> <?= htmlspecialchars($app['firstName'] . ' ' . $app['lastName']) ?></p>
                <p><strong>University:</strong> <?= htmlspecialchars($app['university']) ?></p>
                <p><strong>Course:</strong> <?= htmlspecialchars($app['course']) ?></p>
                <p><strong>Status:</strong> <?= htmlspecialchars($app['status']) ?></p>
                <p><strong>Motivation:</strong><br><?= nl2br(htmlspecialchars($app['motivation'])) ?></p>
                <p><strong>Applied On:</strong> <?= htmlspecialchars(date('F j, Y', strtotime($app['submitDate']))) ?></p>
            </div>
            <div>
                <a href="?page=editApplication&id=<?= htmlspecialchars($app['applicationId']) ?>">Edit Application</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>