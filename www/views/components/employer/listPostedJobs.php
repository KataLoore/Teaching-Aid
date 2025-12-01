<?php
/**
 * The listPostedJobs view shows all the jobs posted by the current user
 */
    if(!isset($_SESSION['user']['loggedIn']) || $_SESSION['user']['loggedIn']!==True)  {
            echo "<script>
                    alert('Please log in to access this content.');
                    window.location.href = 'logIn.php';
                </script>";
            exit();
    } elseif ($_SESSION['user']['userType'] !== 'employer') {
        header("Location: views/dashboard.php");
        exit();
    }

    require_once('../../assets/inc/database/db.php');
    require_once('../../assets/inc/database/jobPostSql.php');
    require_once('../../assets/inc/database/jobApplicationSql.php');
        
    $message = "";

    // Handle delete request
    if (isset($_POST['delete_job']) && isset($_POST['job_uuid'])) {
        try {
            $deleted = deleteJobPost($pdo, $_POST['job_uuid'], $_SESSION['user']['userId']);
            if ($deleted) {
                $message = "Job post deleted successfully.";
            } else {
                $message = "Job post not found or you don't have permission to delete it.";
            }
        } catch (Exception $e) {
            error_log("Error deleting job post: " . $e->getMessage());
            $message = "Unable to delete job post at this time.";
        }
    }

    // Handle application status update
    if (isset($_POST['update_status']) && isset($_POST['application_id']) && isset($_POST['new_status'])) {
        try {
            $updated = updateJobApplicationStatus($pdo, $_POST['application_id'], $_POST['new_status']);
            if ($updated) {
                $message = "Application status updated successfully.";
            }
        } catch (Exception $e) {
            error_log("Error updating application status: " . $e->getMessage());
            $message = "Unable to update application status at this time.";
        }
    }

    try {

        $jobPosts = getEmployerJobs($pdo, $_SESSION['user']['userId']);
        
        // Get applicants for each job post
        foreach ($jobPosts as &$job) {
            try {
                $job['applicants'] = getApplicantsForJobPost($pdo, $job['postId']);
                $job['applicantCount'] = count($job['applicants']);
            } catch (Exception $e) {
                error_log("Error retrieving applicants for job {$job['postId']}: " . $e->getMessage());
                $job['applicants'] = [];
                $job['applicantCount'] = 0;
            }
        }
        unset($job); // Important: unset the reference to prevent issues
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
    <h1>My Posted Jobs</h1>
    
    <?php if (!empty($message)): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <?php if (empty($jobPosts)): ?>
        <p>You haven't posted any jobs yet.</p>
        <a href="?page=postJob">Create your first job post</a>
    <?php else: ?>

    <table>
        <thead>
            <tr>
                <th>University</th>
                <th>Course Code</th>
                <th>Job Post</th>
                <th>Posted</th>
                <th>Application Deadline</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($jobPosts as $job): ?>
                <tr>
                    <td><?= htmlspecialchars($job['university']) ?></td>
                    <td><?= htmlspecialchars($job['course']) ?></td>
                    <td><a href="?page=viewJob&uuid=<?= htmlspecialchars($job['uuid']) ?>"><?= htmlspecialchars($job['jobTitle']) ?></a></td>
                    <td><?= htmlspecialchars(date('F j, Y', strtotime($job['publicationDate']))) ?></td>
                    <td><?= htmlspecialchars(date('F j, Y', strtotime($job['deadlineDate']))) ?></td>
                    <td><?= htmlspecialchars($job['status']) ?></td>
                    <td>
                        <a href="?page=viewJob&uuid=<?= htmlspecialchars($job['uuid']) ?>">View</a>
                        <a href="?page=editJob&id=<?= htmlspecialchars($job['uuid']) ?>">Edit</a>
                        <form method="POST" class="delete-form">
                            <input type="hidden" name="job_uuid" value="<?= htmlspecialchars($job['uuid']) ?>">
                            <input type="submit" name="delete_job" value="Delete" class="delete-btn">
                        </form>
                    </td>
                </tr>
                <tr>
                    <td colspan="7">
                        <details>
                            <summary>
                                <?= $job['applicantCount'] ?> Applicant<?= $job['applicantCount'] !== 1 ? 's' : '' ?>
                            </summary>
                            <div>
                                <?php if (empty($job['applicants'])): ?>
                                    <p>No applications received for this job post yet.</p>
                                <?php else: ?>
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Applicant Name</th>
                                                <th>Email</th>
                                                <th>Submit Date</th>
                                                <th>Actions</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($job['applicants'] as $applicant): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($applicant['firstName'] . ' ' . $applicant['lastName']) ?></td>
                                                    <td><?= htmlspecialchars($applicant['email']) ?></td>
                                                    <td><?= htmlspecialchars(date('F j, Y', strtotime($applicant['submitDate']))) ?></td>
                                                    <td>
                                                        <a href="?page=viewApplication&uuid=<?= htmlspecialchars($applicant['uuid']) ?>">View Application</a>
                                                    </td>
                                                    <td>
                                                        <form method="POST" style="display: inline;">
                                                            <input type="hidden" name="application_id" value="<?= htmlspecialchars($applicant['applicationId']) ?>">
                                                            <select name="new_status">
                                                                <option value="submitted" <?= $applicant['status'] === 'submitted' ? 'selected' : '' ?>>Submitted</option>
                                                                <option value="under review" <?= $applicant['status'] === 'under review' ? 'selected' : '' ?>>Under Review</option>
                                                                <option value="accepted" <?= $applicant['status'] === 'accepted' ? 'selected' : '' ?>>Accepted</option>
                                                                <option value="rejected" <?= $applicant['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                                            </select>
                                                            <input type="hidden" name="update_status" value="1">
                                                            <button type="submit">Update</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php endif; ?>
                            </div>
                        </details>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

</body>
</html>