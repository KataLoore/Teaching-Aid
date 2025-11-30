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
require_once('../../assets/inc/functions.php');

$uuid = $_GET['uuid'] ?? null;
$message = "";
$application = null;

if (!$uuid) {
    header("Location: ?page=myApplications");
    exit();
}

// Validate UUID format
if (!isValidUuid($uuid)) {
    $message = "Invalid application identifier.";
} else {
    try {
        $application = getApplicationByUuid($pdo, $uuid);
        
        if (!$application) {
            $message = "Application not found.";
        } else {
            // Security check: Only show if user owns this application
            if ($application['applicantId'] !== $_SESSION['user']['userId']) {
                $message = "You do not have permission to view this application.";
                $application = null;
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
            <div>
                    <a href="?page=editApplication&uuid=<?= htmlspecialchars($application['uuid']) ?>">Edit Application</a>
                </div>
            <?php endif; ?>
    </div>
</body>
</html>