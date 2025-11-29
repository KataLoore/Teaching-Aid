<?php
/**
 * List all available job posts for applicants to browse
 * 
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
$jobPosts = [];

try {
    require_once('../../assets/inc/database/jobPostSql.php');
    // Get all available job posts
     
} catch (Exception $e) {
    error_log("Error retrieving job posts: " . $e->getMessage());
    $message = "Unable to load job posts at this time.";
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Browse Available Jobs</title>
    <link rel="stylesheet" href="../../../assets/css/style.css">
</head>
<body>
    <h1>Available Job Positions</h1>
    
    <?php if (!empty($message)): ?>
        <p class="error-message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    
    <?php if (empty($jobPosts)): ?>
        <p>No job posts available at the moment. Check back later!</p>
    <?php else: ?>
        <p>Found <?= count($jobPosts) ?> open position(s)</p>
        
        <table>
            <thead>
                <tr>
                    <th>Job Title</th>
                    <th>University</th>
                    <th>Course</th>
                    <th>Posted By</th>
                    <th>Workload</th>
                    <th>Deadline</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($jobPosts as $job): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($job['jobTitle']) ?></strong></td>
                        <td><?= htmlspecialchars($job['university']) ?></td>
                        <td><?= htmlspecialchars($job['course']) ?></td>
                        <td><?= htmlspecialchars($job['firstName'] . ' ' . $job['lastName']) ?></td>
                        <td><?= htmlspecialchars($job['weeklyWorkload']) ?>h/week (max <?= htmlspecialchars($job['maxWorkload']) ?>h)</td>
                        <td><?= htmlspecialchars(date('Y-m-d', strtotime($job['deadlineDate']))) ?></td>
                        <td>
                            <a href="?page=viewJob&id=<?= $job['postId'] ?>">View Details</a> |
                            <a href="?page=createApplication&jobId=<?= $job['postId'] ?>">Apply</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>