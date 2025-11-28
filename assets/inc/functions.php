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

?>