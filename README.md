# Teaching-Aid

A web-based platform for managing teaching assistant job postings and applications at universities. The system connects employers (faculty/departments) with potential teaching assistants through a streamlined job posting and application process.

## Features

- **User Management**: Separate registration and login for employers and applicants
- **Job Posting**: Employers can create, edit, and manage teaching assistant positions
- **Job Applications**: Students can browse available positions and submit applications
- **Application Tracking**: View application status and manage submitted applications
- **Secure Authentication**: Session-based user authentication with role-based access control

## Setup Instructions

### Prerequisites

1. **Download and Install XAMPP**
   - Download XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
   - Install XAMPP with Apache and MySQL components
   - Start Apache and MySQL services from the XAMPP Control Panel

### Installation

1. **Clone the Repository**

   ```bash
   git clone https://github.com/KataLoore/Teaching-Aid.git
   ```

2. **Move Project to XAMPP**

   - Copy the `Teaching-Aid` folder to `C:\xampp\htdocs\` (Windows) or `/Applications/XAMPP/htdocs/` (Mac)

3. **Database Setup**

   - Open your web browser and go to `http://localhost/phpmyadmin`
   - Create a new database named `teaching_aid`
   - The database tables will be automatically created when you first access the application

4. **Access the Application**
   - Open your web browser and navigate to `http://localhost/Teaching-Aid/www/logIn.php`
   - The database will be initialized automatically on first run
   - Sample data (users, job posts, and applications) will be populated automatically for testing

### Test Accounts

The system includes pre-populated test accounts for development and testing:

**Employers:**

- Username: `j.anderson`, `s.johnson`, `m.chen`

**Applicants:**

- Username: `e.wilson`, `j.brown`, `l.davis`, `a.garcia`

**Password for all accounts:** `password123`

### Default Database Configuration

The application uses the following default database settings (configured in `assets/inc/database/db.php`):

- **Host**: localhost
- **Database Name**: teaching_aid
- **Username**: root
- **Password**: (empty)

Modify `assets/inc/database/db.php` if you need different database credentials.

## File Structure

```
Teaching-Aid/
├── README.md                          # Project documentation
├── assets/                            # Static assets and core functionality
│   ├── css/
│   │   └── style.css                  # Main stylesheet with form-container styling
│   ├── img/                           # Image assets (currently empty)
│   ├── inc/
│   │   ├── functions.php              # Utility functions (form sanitization, etc.)
│   │   └── database/
│   │       ├── db.php                 # Database connection configuration
│   │       ├── exampleData.php        # Sample data insertion for testing/development
│   │       ├── initDb.php             # Database initialization and table creation
│   │       ├── jobApplicationSql.php   # Job application database operations
│   │       ├── jobPostSql.php         # Job posting database operations
│   │       └── userSql.php            # User management database operations
│   └── lib/
│       └── validator.php              # Form validation classes and methods
└── www/                               # Web-accessible files
    ├── logIn.php                      # Main login page
    └── views/
        ├── createUser.php             # User registration page
        ├── dashboard.php              # Main dashboard after login
        └── components/
            ├── applicant/             # Student/applicant functionality
            │   ├── createApplication.php    # Submit job applications
            │   ├── listAppliedJobs.php     # View submitted applications
            │   ├── listAvailableJobs.php   # Browse available positions
            │   └── viewApplication.php     # View application details
            ├── employer/              # Faculty/employer functionality
            │   ├── createJobPost.php       # Create new job postings
            │   ├── editJobPost.php         # Edit existing job postings
            │   └── listPostedJobs.php      # Manage posted positions
            └── shared/                # Common functionality for both user types
                ├── logout.php              # User logout
                ├── settings.php            # Account settings
                ├── viewJob.php            # View job posting details
                └── viewProfile.php        # View user profile
```

## User Types

### Employers (Faculty/Departments)

- Create and manage teaching assistant job postings
- Edit job details and requirements
- View applications submitted for their positions

### Applicants (Students)

- Browse available teaching assistant positions
- Submit applications with required information
- Track application status and view submitted applications

## Technology Stack

- **Backend**: PHP with PDO for database operations
- **Database**: MySQL
- **Frontend**: HTML, CSS, JavaScript
- **Server**: Apache (via XAMPP)
- **Authentication**: Session-based with role-based access control
