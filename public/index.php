<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Import the App class from its namespace
//use App\Core\App;

// Load environment variables
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Error reporting based on environment
if (getenv('APP_ENV') === 'production') {
  error_reporting(0);
  ini_set('display_errors', 0);
} else {
  error_reporting(E_ALL);
  ini_set('display_errors', 1);
}

// Security headers
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("Content-Security-Policy: default-src 'self'");
header("Referrer-Policy: strict-origin-when-cross-origin");

// Start the session with secure settings
session_start([
  'cookie_httponly' => true,
  'cookie_secure' => isset($_SERVER['HTTPS']),
  'cookie_samesite' => 'Lax',
  'use_strict_mode' => true
]);

// Load the core framework
//require_once __DIR__ . '/../app/Core/App.php';
//require_once __DIR__ . '/../app/Middleware/AuthMiddleware.php';
//require_once __DIR__ . '/../app/Middleware/CsrfMiddleware.php';

// Initialize the application
$app = new \App\Core\App();

// middlewares
$authMid = new \App\Middleware\AuthMiddleware();
$csrfMid = new \App\Middleware\CsrfMiddleware();

$app->registerMiddleware('auth', $authMid);
$app->registerMiddleware('csrf', $csrfMid);
