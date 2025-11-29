<?php
/**
 * List all job applications submitted by the current applicant
 */

if(!isset($_SESSION['user']['loggedIn']) || $_SESSION['user']['loggedIn']!==True) {
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

$message = "";
$applications = [];

try {
// Fetch applications for the logged-in applicant
  
    
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
        <a href="?page=availableJobs">Browse Available Jobs</a>
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
                <?php foreach ($applications as $app): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($app['jobTitle']) ?></strong></td>
                        <td><?= htmlspecialchars($app['university']) ?></td>
                        <td><?= htmlspecialchars($app['course']) ?></td>
                        <td><?= htmlspecialchars($app['firstName'] . ' ' . $app['lastName']) ?></td>
                        <td><?= htmlspecialchars(date('Y-m-d', strtotime($app['submitDate']))) ?></td>
                        <td>
                            <span class="status-<?= strtolower(str_replace(' ', '-', $app['status'])) ?>">
                                <?= htmlspecialchars(ucfirst($app['status'])) ?>
                            </span>
                        </td>
                        <td>
                            <a href="?page=viewApplication&id=<?= $app['applicationId'] ?>">View Details</a>
                            <?php if ($app['status'] === 'submitted'): ?>
                                | <a href="?page=editApplication&id=<?= $app['applicationId'] ?>">Edit</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>