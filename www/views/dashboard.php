<?php
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
        'overview' => 'Dashboard Overview',
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
    $requestedPage = $_GET['page'] ?? 'overview';
    
    // Define valid pages (including hidden ones like viewJob)
    $validPages = array_merge(array_keys($pages), ['viewJob', 'editJob', 'viewApplication', 'viewPublicProfile']);
    $currentPage = in_array($requestedPage, $validPages) ? $requestedPage : 'overview';    // Set page title (use a default for hidden pages)
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
    <div class="sidebar"> 
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
                case 'overview':
                    include 'components/shared/overview.php';
                    break;

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

                // -- sub-view for Public Profile (accessed through views) --
                case 'viewPublicProfile':
                    include 'components/shared/viewPublicProfile.php';
                    break;

              
                // -- fallback -- ****NEEDED ? 
                default:
                    ?>
                    <div>
                        <h2>Page Not Found</h2>
                        <p>The requested page could not be found.</p>
                        <a href="?page=overview">Return to Dashboard</a>
                    </div>
                    <?php
                    break;
            }
        ?>
    </div>
</body>
</html>