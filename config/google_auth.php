<?php
// Simple function to load environment variables from .env file
function loadEnv($path)
{
    if (!file_exists($path)) {
        return false;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        // Don't overwrite existing environment variables
        if (!array_key_exists($name, $_ENV)) {
            $_ENV[$name] = $value;
        }
    }

    return true;
}

// Load environment variables
$envPath = __DIR__ . '/../.env';
loadEnv($envPath);

// Define constants from environment variables with fallbacks
define('GOOGLE_CLIENT_ID', $_ENV['GOOGLE_CLIENT_ID'] ?? '');
define('GOOGLE_CLIENT_SECRET', $_ENV['GOOGLE_CLIENT_SECRET'] ?? '');
define('GOOGLE_REDIRECT_URI', $_ENV['GOOGLE_REDIRECT_URI'] ?? 'http://localhost/Curtaincall/index.php?route=user/google-callback');

// Simple validation to ensure required values are set
if (empty(GOOGLE_CLIENT_ID) || empty(GOOGLE_CLIENT_SECRET)) {
    error_log('Missing required Google OAuth configuration. Please check your .env file.');
}
