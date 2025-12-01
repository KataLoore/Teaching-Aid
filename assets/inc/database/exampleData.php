<?php
/**
 * Sample data insertion script that populates database tables with realistic test data for development and testing.
 * Creates sample users (employers and applicants), job postings, and applications with proper relationships and realistic content.
 * Dependencies: db.php for database connection and existing database tables created by initDb.php.
 */

require_once("db.php");

try {
    // Start transaction to ensure all data is inserted successfully
    $pdo->beginTransaction();

    // ---- Insert sample users ----
    
    // Employers
    $employers = [
        [
            'firstName' => 'John',
            'lastName' => 'Anderson',
            'username' => 'j.anderson',
            'email' => 'john.anderson@uia.no',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'userType' => 'employer'
        ],
        [
            'firstName' => 'Sarah',
            'lastName' => 'Johnson',
            'username' => 's.johnson',
            'email' => 'sarah.johnson@uia.no',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'userType' => 'employer'
        ],
        [
            'firstName' => 'Michael',
            'lastName' => 'Chen',
            'username' => 'm.chen',
            'email' => 'michael.chen@uia.no',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'userType' => 'employer'
        ]
    ];

    // Applicants
    $applicants = [
        [
            'firstName' => 'Emma',
            'lastName' => 'Wilson',
            'username' => 'e.wilson',
            'email' => 'emma.wilson@student.uia.no',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'userType' => 'applicant'
        ],
        [
            'firstName' => 'James',
            'lastName' => 'Brown',
            'username' => 'j.brown',
            'email' => 'james.brown@student.uia.no',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'userType' => 'applicant'
        ],
        [
            'firstName' => 'Lisa',
            'lastName' => 'Davis',
            'username' => 'l.davis',
            'email' => 'lisa.davis@student.uia.no',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'userType' => 'applicant'
        ],
        [
            'firstName' => 'Alex',
            'lastName' => 'Garcia',
            'username' => 'a.garcia',
            'email' => 'alex.garcia@student.uia.no',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'userType' => 'applicant'
        ]
    ];

    // Insert employers and applicants
    $allUsers = array_merge($employers, $applicants);
    $userSql = "INSERT INTO user (firstName, lastName, username, email, password, userType) VALUES (?, ?, ?, ?, ?, ?)";
    $userStmt = $pdo->prepare($userSql);

    foreach ($allUsers as $user) {
        $userStmt->execute([
            $user['firstName'],
            $user['lastName'],
            $user['username'],
            $user['email'],
            $user['password'],
            $user['userType']
        ]);
    }

    // ---- Insert sample job posts ----
    
    $jobPosts = [
        [
            'uuid' => '550e8400-e29b-41d4-a716-446655440001',
            'employerId' => 1, // John Anderson
            'jobTitle' => 'Teaching Assistant - Introduction to Programming',
            'jobDescription' => 'Assist students with Python programming basics, grade assignments, and conduct lab sessions.',
            'university' => 'UiA',
            'faculty' => 'Faculty of Engineering and Science',
            'course' => 'ITE1806',
            'language' => 'en',
            'maxWorkload' => 120,
            'weeklyWorkload' => 8,
            'status' => 'open',
            'deadlineDate' => date('Y-m-d H:i:s', strtotime('+30 days'))
        ],
        [
            'uuid' => '550e8400-e29b-41d4-a716-446655440002',
            'employerId' => 2, // Sarah Johnson
            'jobTitle' => 'Teaching Assistant - Database Systems',
            'jobDescription' => 'Help students understand SQL, database design principles, and assist with practical exercises.',
            'university' => 'UiA',
            'faculty' => 'Faculty of Engineering and Science',
            'course' => 'IS-105',
            'language' => 'en',
            'maxWorkload' => 100,
            'weeklyWorkload' => 6,
            'status' => 'open',
            'deadlineDate' => date('Y-m-d H:i:s', strtotime('+25 days'))
        ],
        [
            'uuid' => '550e8400-e29b-41d4-a716-446655440003',
            'employerId' => 3, // Michael Chen
            'jobTitle' => 'Teaching Assistant - Mathematics for Computer Science',
            'jobDescription' => 'Support students with discrete mathematics, linear algebra, and problem-solving sessions.',
            'university' => 'UiA',
            'faculty' => 'Faculty of Engineering and Science',
            'course' => 'MA-181',
            'language' => 'no',
            'maxWorkload' => 80,
            'weeklyWorkload' => 5,
            'status' => 'open',
            'deadlineDate' => date('Y-m-d H:i:s', strtotime('+20 days'))
        ],
        [
            'uuid' => '550e8400-e29b-41d4-a716-446655440004',
            'employerId' => 1, // John Anderson
            'jobTitle' => 'Teaching Assistant - Web Development',
            'jobDescription' => 'Guide students through HTML, CSS, JavaScript, and PHP development projects.',
            'university' => 'UiA',
            'faculty' => 'Faculty of Engineering and Science',
            'course' => 'IS-114',
            'language' => 'en',
            'maxWorkload' => 150,
            'weeklyWorkload' => 10,
            'status' => 'open',
            'deadlineDate' => date('Y-m-d H:i:s', strtotime('+35 days'))
        ],
        [
            'uuid' => '550e8400-e29b-41d4-a716-446655440005',
            'employerId' => 2, // Sarah Johnson
            'jobTitle' => 'Teaching Assistant - Data Structures and Algorithms',
            'jobDescription' => 'Assist with algorithm implementation, code reviews, and help students understand complexity analysis.',
            'university' => 'UiA',
            'faculty' => 'Faculty of Engineering and Science',
            'course' => 'IS-202',
            'language' => 'en',
            'maxWorkload' => 110,
            'weeklyWorkload' => 7,
            'status' => 'closed',
            'deadlineDate' => date('Y-m-d H:i:s', strtotime('-5 days'))
        ]
    ];

    $jobSql = "INSERT INTO job_post (uuid, employerId, jobTitle, jobDescription, university, faculty, course, language, maxWorkload, weeklyWorkload, status, deadlineDate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $jobStmt = $pdo->prepare($jobSql);

    foreach ($jobPosts as $job) {
        $jobStmt->execute([
            $job['uuid'],
            $job['employerId'],
            $job['jobTitle'],
            $job['jobDescription'],
            $job['university'],
            $job['faculty'],
            $job['course'],
            $job['language'],
            $job['maxWorkload'],
            $job['weeklyWorkload'],
            $job['status'],
            $job['deadlineDate']
        ]);
    }

    // ---- Insert sample job applications ----
    
    $applications = [
        [
            'uuid' => '660e8400-e29b-41d4-a716-446655440001',
            'applicantId' => 4, // Emma Wilson
            'jobPostId' => 1, // Introduction to Programming
            'coverLetter' => 'Dear Hiring Manager,

I am very interested in the Teaching Assistant position for Introduction to Programming. As a third-year Computer Science student, I have strong knowledge of Python and experience helping fellow students with programming concepts.

I have completed advanced programming courses with excellent grades and have been tutoring students informally for the past year. I am patient, communicative, and passionate about helping others learn to code.

I would love the opportunity to contribute to the learning experience of new programming students.

Best regards,
Emma Wilson',
            'status' => 'submitted',
            'submitDate' => date('Y-m-d', strtotime('-3 days'))
        ],
        [
            'uuid' => '660e8400-e29b-41d4-a716-446655440002',
            'applicantId' => 5, // James Brown
            'jobPostId' => 2, // Database Systems
            'coverLetter' => 'Hello,

I am applying for the Teaching Assistant position in Database Systems. I have excellent experience with SQL, database design, and have worked with both MySQL and PostgreSQL in various projects.

My academic performance in database-related courses has been outstanding, and I have practical experience from an internship where I designed and implemented database solutions.

I am excited about the opportunity to help students understand the fundamentals of database systems.

Sincerely,
James Brown',
            'status' => 'under review',
            'submitDate' => date('Y-m-d', strtotime('-5 days'))
        ],
        [
            'uuid' => '660e8400-e29b-41d4-a716-446655440003',
            'applicantId' => 6, // Lisa Davis
            'jobPostId' => 1, // Introduction to Programming
            'coverLetter' => 'Dear Professor Anderson,

I would like to apply for the Teaching Assistant position for Introduction to Programming. I have strong programming skills in multiple languages including Python, Java, and C++.

I have been actively involved in peer tutoring and have received positive feedback from students I have helped. My approach focuses on breaking down complex concepts into understandable steps.

I am available for the required weekly hours and am committed to supporting student success.

Thank you for your consideration,
Lisa Davis',
            'status' => 'accepted',
            'submitDate' => date('Y-m-d', strtotime('-7 days'))
        ],
        [
            'uuid' => '660e8400-e29b-41d4-a716-446655440004',
            'applicantId' => 7, // Alex Garcia
            'jobPostId' => 4, // Web Development
            'coverLetter' => 'Dear Hiring Committee,

I am writing to express my interest in the Web Development Teaching Assistant position. I have extensive experience with HTML, CSS, JavaScript, and PHP from both coursework and personal projects.

I have built several web applications and have a portfolio showcasing my skills. I enjoy explaining technical concepts and have experience mentoring junior students in web development.

I am particularly excited about this opportunity as web development is my area of specialization and passion.

Best regards,
Alex Garcia',
            'status' => 'submitted',
            'submitDate' => date('Y-m-d', strtotime('-1 day'))
        ],
        [
            'uuid' => '660e8400-e29b-41d4-a716-446655440005',
            'applicantId' => 4, // Emma Wilson
            'jobPostId' => 3, // Mathematics for Computer Science
            'coverLetter' => 'Dear Professor Chen,

I am interested in the Teaching Assistant position for Mathematics for Computer Science. I have a strong foundation in discrete mathematics and linear algebra, having completed these courses with top grades.

My mathematical background combined with my programming experience gives me a unique perspective on how to explain mathematical concepts to computer science students.

I am fluent in Norwegian and would be comfortable conducting sessions in Norwegian as required.

Thank you for your time,
Emma Wilson',
            'status' => 'rejected',
            'submitDate' => date('Y-m-d', strtotime('-10 days'))
        ]
    ];

    $appSql = "INSERT INTO job_application (uuid, applicantId, jobPostId, coverLetter, status, submitDate) VALUES (?, ?, ?, ?, ?, ?)";
    $appStmt = $pdo->prepare($appSql);

    foreach ($applications as $app) {
        $appStmt->execute([
            $app['uuid'],
            $app['applicantId'],
            $app['jobPostId'],
            $app['coverLetter'],
            $app['status'],
            $app['submitDate']
        ]);
    }

    // Commit all changes
    $pdo->commit();

} catch (PDOException $e) {
    // Rollback on error
    $pdo->rollback();
    error_log("Error inserting sample data: " . $e->getMessage());
}
?>
