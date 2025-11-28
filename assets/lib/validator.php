<?php
/**
 * Validator class for form input validation.
 * Provides validation methods for form data.
 * Collects and stores validation errors with 
 * descriptive error messages for display to users.
 */

class Validator {
    private $errors = [];

    public function hasErrors() {
        return !empty($this->errors);
    }

    public function getErrors() {
        return $this->errors;
    }

    // ----- USER VALIDATION -----
    public function validateName($value, $fieldName) {
        if (empty($value)) {
            $this->errors[$fieldName] = ucfirst($fieldName) . " is required.";
            return false;
        }
        if (preg_match('/\d/', $value)) {
            $this->errors[$fieldName] = ucfirst($fieldName) . " cannot include numbers.";
            return false;
        }
        return true;
    }
    
    public function validateUsername($value) {
        if (empty($value)) {
            $this->errors['username'] = "Username is required";
            return false;
        }
        if (preg_match('/\s/', $value)) {
            $this->errors['username'] = "Username cannot contain spaces.";
            return false;
        }
        return true;
    }
    
    public function validateEmail($value) {
        if (empty($value)) {
            $this->errors['email'] = "E-mail is required";
            return false;
        }
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = "Invalid email format";
            return false;
        }
        return true;
    }
    
    public function validatePassword($value) {
        if (empty($value)) {
            $this->errors['password'] = "Password is required";
            return false;
        }
        if (strlen($value) < 8) {
            $this->errors['password'] = "Password must consist of at least 8 characters.";
            return false;
        }
        if (!preg_match('/^(?=.*[A-Z])(?=.*\d).+$/', $value)) {
            $this->errors['password'] = "Password must contain at least one uppercase letter and one number";
            return false;
        }
        return true;
    }

    public function validateUserType($value) {
        if (empty($value)) {
            $this->errors['userType'] = "User type is required";
            return false;
        }
        if (!in_array($value, ['applicant', 'employer'], true)) {
            $this->errors['userType'] = "Invalid user type selected";
            return false;
        }
        return true;
    }
    
    // ----- JOB POST VALIDATION -----
    public function validateJobTitle($value) {
        if (empty($value)) {
            $this->errors['jobTitle'] = "Job title is required";
            return false;
        }
        if (strlen($value) > 100) {
            $this->errors['jobTitle'] = "Job title cannot exceed 100 characters";
            return false;
        }
        return true;
    }
    
    public function validateJobDescription($value) {
        if (empty($value)) {
            $this->errors['jobDescription'] = "Job description is required";
            return false;
        }
        return true;
    }
    
    public function validateWorkload($value, $fieldName) {
        if (empty($value) || !is_numeric($value)) {
            $this->errors[$fieldName] = ucfirst($fieldName) . " is required and must be a number";
            return false;
        }
        
        $intValue = (int)$value;
        if ($fieldName === 'weeklyWorkload') {
            if ($intValue < 1 || $intValue > 40) {
                $this->errors[$fieldName] = "Weekly workload must be between 1 and 40 hours";
                return false;
            }
        } else if ($fieldName === 'maxWorkload') {
            if ($intValue < 1 || $intValue > 999) {
                $this->errors[$fieldName] = "Maximum workload must be between 1 and 999 hours";
                return false;
            }
        }
        return true;
    }
    
    public function validateDeadlineDate($value) {
        if (empty($value)) {
            $this->errors['deadlineDate'] = "Application deadline is required";
            return false;
        }
        
        $deadlineTimestamp = strtotime($value);
        if ($deadlineTimestamp === false) {
            $this->errors['deadlineDate'] = "Invalid deadline date format";
            return false;
        }
        
        if ($deadlineTimestamp <= time()) {
            $this->errors['deadlineDate'] = "Deadline must be in the future";
            return false;
        }
        
        return true;
    }
    
    public function validateCourseCode($value) {
        if (empty($value)) {
            $this->errors['course'] = "Course is required";
            return false;
        }
        
        if (!preg_match('/^[A-Za-z0-9\s\-]+$/', $value)) {
            $this->errors['course'] = "Course code can only contain letters, numbers, hyphens, and spaces";
            return false;
        }
        
        if (strlen($value) > 50) {
            $this->errors['course'] = "Course code cannot exceed 50 characters";
            return false;
        }
        
        return true;
    }
}
?>