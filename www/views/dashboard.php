<?php
/**
 * Main dashboard interface that dynamically loads different components based on user type and navigation.
 * Provides role-based access control and serves as the central hub for employer and applicant functionality.
 * Dependencies: Session management and various component files in the applicant/, employer/, and shared/ directories.
 */

    // --- Login Session Check ---
    session_start();
    if(!isset($_SESSION['user']['loggedIn']) || $_SESSION['user']['loggedIn']!==True) {
        echo "<script>
                alert('Please log in to access this content.');
                window.location.href = 'logIn.php';
              </script>";
        exit();
    }

    // --- Dashboard Functionality ---
    // Define available pages and their titles: pageKey => pageTitle

    $pages = [ // shared pages
        'profile' => 'My Profile',
    ];

    if($_SESSION['user']['userType'] === 'employer') { 
        $pages = array_merge($pages, [ // add employer pages
            'postJob' => 'Create Job Post',
            'myJobs' => 'My Posted Jobs',
        ]);
    } elseif($_SESSION['user']['userType'] === 'applicant') { 
        $pages = array_merge($pages, [ // add applicant pages
            'availableJobs' => 'Browse Jobs',
            'createApplication' => 'Create Application',
            'myApplications' => 'My Applications',
        ]);
    }

    $pages = array_merge($pages, [ // shared pages
            'settings' => 'Settings',
            'logout' => 'Log Out',
        ]);

    // --- Main Content Display ---
    // Get and validate the requested page
    $requestedPage = $_GET['page'] ?? 'profile';
    
    // Define valid pages (including hidden ones like viewJob)
    $validPages = array_merge(array_keys($pages), ['viewJob', 'editJob', 'viewApplication']);
    $currentPage = in_array($requestedPage, $validPages) ? $requestedPage : 'profile';    // Set page title (use a default for hidden pages)
    $currentPageTitle = $pages[$currentPage] ?? 'View Details';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teaching Aid - <?= htmlspecialchars($currentPageTitle) ?></title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="sidebar <?= htmlspecialchars($_SESSION['user']['userType']) ?>"> 
        <div class="logo-container">
            <a href="?page=profile">
                <img src="../../assets/img/teaching-aid-high-resolution-logo-transparent.png" 
                alt="Teaching Aid Logo" class="sidebar-logo">
            </a>
        </div>
        <?php 
            foreach($pages as $pageKey => $pageTitle) { 
                echo "<a href=\"?page=" . htmlspecialchars($pageKey) . "\">$pageTitle</a>";
            } 
        ?>
    </div>
    <div class="content">
        <?php
            switch ($currentPage) {
                // -- sidebar links --
                case 'profile':
                    include 'components/shared/viewProfile.php';
                    break;
                    
                case 'postJob':
                    include 'components/employer/createJobPost.php';
                    break;
                    
                case 'myJobs':
                    include 'components/employer/listPostedJobs.php';
                    break;
                    
                case 'myApplications':
                    include 'components/applicant/listAppliedJobs.php';
                    break;
                    
                case 'availableJobs':
                    include 'components/applicant/listAvailableJobs.php';
                    break;
                    
                case 'createApplication':
                    include 'components/applicant/createApplication.php';
                    break;
                    
                case 'settings':
                    include 'components/shared/settings.php';
                    break;

                case 'logout':
                    include 'components/shared/logout.php';
                    break;

                // -- sub-views for JobPost (accessed through views) --
                case 'viewJob':
                    include 'components/shared/viewJob.php';
                    break;

                case 'editJob':
                    include 'components/employer/editJobPost.php';
                    break;
                
                    // -- sub-view for JobApplication (accessed through views) --
                case 'viewApplication':
                    include __DIR__ . '/components/applicant/viewApplication.php';
                    break;

                // -- fallback -- ****NEEDED ? 
                default:
                    ?>
                    <div>
                        <h2>Page Not Found</h2>
                        <p>The requested page could not be found.</p>
                        <a href="?page=profile">Return to Dashboard</a>
                    </div>
                    <?php
                    break;
            }
        ?>
    </div>
</body>
</html>