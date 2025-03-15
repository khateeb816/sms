<?php

if (!function_exists('getActivityIcon')) {
    /**
     * Get the appropriate icon for an activity based on its description.
     *
     * @param string $description
     * @return string
     */
    function getActivityIcon($description)
    {
        if (stripos($description, 'fee') !== false) {
            return 'zmdi-money';
        } elseif (stripos($description, 'fine') !== false) {
            return 'zmdi-money-off';
        } elseif (stripos($description, 'student') !== false) {
            return 'zmdi-account';
        } elseif (stripos($description, 'teacher') !== false) {
            return 'zmdi-accounts-list';
        } elseif (stripos($description, 'class') !== false) {
            return 'zmdi-graduation-cap';
        } elseif (stripos($description, 'message') !== false) {
            return 'zmdi-email';
        } elseif (stripos($description, 'exam') !== false) {
            return 'zmdi-calendar-check';
        } else {
            return 'zmdi-settings';
        }
    }
}

if (!function_exists('getActivityTitle')) {
    /**
     * Get a title for an activity based on its description.
     *
     * @param string $description
     * @return string
     */
    function getActivityTitle($description)
    {
        if (stripos($description, 'fee') !== false) {
            return 'Fee Activity';
        } elseif (stripos($description, 'fine') !== false) {
            return 'Fine Activity';
        } elseif (stripos($description, 'student') !== false) {
            return 'Student Activity';
        } elseif (stripos($description, 'teacher') !== false) {
            return 'Teacher Activity';
        } elseif (stripos($description, 'class') !== false) {
            return 'Class Activity';
        } elseif (stripos($description, 'message') !== false) {
            return 'Message Activity';
        } elseif (stripos($description, 'exam') !== false) {
            return 'Exam Activity';
        } else {
            return 'System Activity';
        }
    }
}
