<?php
/**
 * The listPostedJobs view shows all the jobs posted by the current user
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

    require_once('../../assets/inc/database/db.php');
    require_once('../../assets/inc/database/jobPostSql.php');
        
    $message = "";

    try {
        $jobPosts = getEmployerJobs($pdo, $_SESSION['user']['userId']);
    } catch (Exception $e) {
        error_log("Error retrieving job posts: " . $e->getMessage());
        $jobPosts = [];
        $message = "Unable to load job posts at this time.";
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Your Posted Jobs</title>
    <link rel="stylesheet" href="../../../assets/css/style.css">
</head>
<body>
    <h1>Posted Jobs</h1>
    <?php if (!empty($message)): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <?php if (empty($jobPosts)): ?>
        <p>You haven't posted any jobs yet.</p>
        <a href="?page=createJobPost">Create your first job post</a>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Job Title</th>
                    <th>University</th>
                    <th>Course</th>
                    <th>Status</th>
                    <th>Posted</th>
                    <th>Deadline</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($jobPosts as $job): ?>
                    <tr>
                        <td><?= htmlspecialchars($job['jobTitle']) ?></td>
                        <td><?= htmlspecialchars($job['university']) ?></td>
                        <td><?= htmlspecialchars($job['course']) ?></td>
                        <td><?= htmlspecialchars($job['status']) ?></td>
                        <td><?= htmlspecialchars(date('Y-m-d', strtotime($job['publicationDate']))) ?></td>
                        <td><?= htmlspecialchars(date('Y-m-d', strtotime($job['deadlineDate']))) ?></td>
                        <td>
                            <a href="?page=viewJob&uuid=<?= htmlspecialchars($job['uuid']) ?>">View</a>
                            <a href="?page=editJob&id=<?= htmlspecialchars($job['uuid']) ?>">Edit</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>