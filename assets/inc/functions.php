<?php

/**
 * Simple helper functions for form processing and error handling
 */

// Clean and sanitize form input data
function cleanFormInput($data) {
    $data = trim($data);
    $data = strip_tags($data);
    $data = htmlentities($data);
    return $data;
}

// Display error message for a specific field
function displayErrorMessage($errorMessages, $key) {
    if (isset($errorMessages[$key])) {
        return '<span class="error">' . htmlspecialchars($errorMessages[$key]) . '</span>';
    }
}

// Preserve form value after submission (for repopulating forms)
function preserveFormValue($formData, $key) {
    if (isset($formData[$key])) {
        return htmlspecialchars($formData[$key]);
    }
}

// Check if option should be selected in dropdown
function isSelected($formData, $key, $value) {
    if (isset($formData[$key]) && $formData[$key] === $value) {
        return 'selected';
    }
}

// Generate a UUID v4
function generateUuid() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

// Validate UUID format
function isValidUuid($uuid) {
    return preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-4[a-f0-9]{3}-[89ab][a-f0-9]{3}-[a-f0-9]{12}$/i', $uuid) === 1;
}

?>