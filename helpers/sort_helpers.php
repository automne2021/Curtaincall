<?php

/**
 * Sort Helper Functions
 * Contains functions for handling sorting in list views
 */

/**
 * Generate URL for sort links
 * 
 * @param string $field The field to sort by
 * @param string $current_sort_field Currently active sort field
 * @param string $current_sort_dir Current sort direction
 * @param string $theater_id Optional theater ID filter
 * @param string $route The route name
 * @return string The generated URL
 */
function getSortUrl($field, $current_sort_field = null, $current_sort_dir = null, $theater_id = null, $route = 'play')
{
    // Use provided values or get from globals
    $current_sort_field = $current_sort_field ?? $GLOBALS['sort_field'] ?? '';
    $current_sort_dir = $current_sort_dir ?? $GLOBALS['sort_dir'] ?? '';
    $theater_id = $theater_id ?? $GLOBALS['theater_id'] ?? '';

    // If clicking the same field, toggle direction
    $direction = ($field == $current_sort_field)
        ? ($current_sort_dir == 'asc' ? 'desc' : 'asc')
        : 'asc';

    // Build URL with the correct parameters
    $url = "index.php?route={$route}&sort={$field}&dir={$direction}";
    if (!empty($theater_id)) {
        $url .= "&theater_id=" . $theater_id;
    }
    return $url;
}

/**
 * Check if a sort field is currently active
 * 
 * @param string $field The field to check
 * @param string $current_sort_field Currently active sort field
 * @return string CSS class name if active, empty string otherwise
 */
function isSortActive($field, $current_sort_field = null)
{
    $current_sort_field = $current_sort_field ?? $GLOBALS['sort_field'] ?? '';
    return $current_sort_field === $field ? 'active' : '';
}

/**
 * Generate HTML for sort icons
 * 
 * @param string $field The field to get an icon for
 * @param string $current_sort_field Currently active sort field
 * @param string $current_sort_dir Current sort direction
 * @return string HTML for the appropriate icon
 */
function getSortIcon($field, $current_sort_field = null, $current_sort_dir = null)
{
    $current_sort_field = $current_sort_field ?? $GLOBALS['sort_field'] ?? '';
    $current_sort_dir = $current_sort_dir ?? $GLOBALS['sort_dir'] ?? '';

    if ($field != $current_sort_field) {
        // Default icons for inactive sort options
        switch ($field) {
            case 'name':
                return '<i class="bi bi-sort-alpha-down"></i>';
            case 'price':
                return '<i class="bi bi-sort-numeric-down"></i>';
            case 'date':
                return '<i class="bi bi-calendar"></i>';
            default:
                return '<i class="bi bi-sort"></i>';
        }
    }

    // Icons for active sort options
    switch ($field) {
        case 'name':
            return $current_sort_dir == 'asc' ?
                '<i class="bi bi-sort-alpha-down"></i>' :
                '<i class="bi bi-sort-alpha-up"></i>';
        case 'price':
            return $current_sort_dir == 'asc' ?
                '<i class="bi bi-sort-numeric-down"></i>' :
                '<i class="bi bi-sort-numeric-up"></i>';
        case 'date':
            return $current_sort_dir == 'asc' ?
                '<i class="bi bi-calendar-date"></i> (Earliest)' :
                '<i class="bi bi-calendar-date"></i> (Latest)';
        default:
            return $current_sort_dir == 'asc' ?
                '<i class="bi bi-sort-down"></i>' :
                '<i class="bi bi-sort-up"></i>';
    }
}
