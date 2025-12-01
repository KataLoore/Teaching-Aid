<?php
/**
 * List all job applications submitted by the current applicant
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

require_once('../../assets/inc/database/db.php');
$message = "";
$applications = [];

try {
    require_once('../../assets/inc/database/jobApplicationSql.php');
// Fetch applications for the logged-in applicant
    $applications = getJobApplicationsByApplicant($pdo, $_SESSION['user']['userId']);
    
} catch (Exception $e) {
    error_log("Error retrieving applications: " . $e->getMessage());
    $message = "Unable to load your applications at this time.";
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>My Applications</title>
    <link rel="stylesheet" href="../../../assets/css/style.css">
</head>
<body>
    <h1>My Job Applications</h1>
    
    <?php if (!empty($message)): ?>
        <p class="error-message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    
    <?php if (empty($applications)): ?>
        <p>You haven't applied to any positions yet.</p>
        <a href="?page=listAvailableJobs">Browse Available Jobs</a>
    <?php else: ?>
        <p>You have submitted <?= count($applications) ?> application(s)</p>
        
        <table>
            <thead>
                <tr>
                    <th>Job Title</th>
                    <th>University</th>
                    <th>Course</th>
                    <th>Employer</th>
                    <th>Applied On</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($applications as $application): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($application['jobTitle']) ?></strong></td>
                        <td><?= htmlspecialchars($application['university']) ?></td>
                        <td><?= htmlspecialchars($application['course']) ?></td>
                        <td><?= htmlspecialchars($application['firstName'] . ' ' . $application['lastName']) ?></td>
                        <td><?= htmlspecialchars(date('Y-m-d', strtotime($application['submitDate']))) ?></td>
                        <td>
                            <span class="status-<?= strtolower(str_replace(' ', '-', $application['status'])) ?>">
                                <?= htmlspecialchars(ucfirst($application['status'])) ?>
                            </span>
                        </td>
                        <td>
                            <a href="?page=viewApplication&uuid=<?= $application['uuid'] ?>">View Details</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>