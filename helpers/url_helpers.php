<?php

/**
 * URL helper functions
 * Contains functions for working with URLs in the application
 */

/**
 * Generate a URL for the application
 *
 * @param string $path The path to append to the base URL
 * @return string The complete URL
 */
function url($path = '')
{
    $path = trim($path, '/');
    return BASE_URL . ($path ? "index.php?route={$path}" : '');
}

/**
 * Generate a URL for assets (CSS, JS, images)
 *
 * @param string $path The path to the asset
 * @return string The complete asset URL
 */
function asset($path)
{
    $path = trim($path, '/');
    return BASE_URL . "public/{$path}";
}

/**
 * Redirect to another URL
 *
 * @param string $path The path to redirect to
 * @return void
 */
function redirect($path = '')
{
    header('Location: ' . url($path));
    exit;
}
