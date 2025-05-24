<?php
namespace App\Middleware;

/**
 * CSRF Protection Middleware
 * Verifies CSRF token for POST, PUT, DELETE requests
 */
class CsrfMiddleware implements MiddlewareInterface {
  /**
   * Handle the incoming request
   *
   * @param mixed $request
   * @return mixed
   */
  public function handle($request) {
    // Only check POST, PUT, DELETE requests
    if ($_SERVER['REQUEST_METHOD'] === 'POST' || 
      $_SERVER['REQUEST_METHOD'] === 'PUT' || 
      $_SERVER['REQUEST_METHOD'] === 'DELETE') {

      // Check if CSRF token exists and is valid
      if (!isset($_POST['_token']) || !isset($_SESSION['_token'])) {
        // Token missing
        header('HTTP/1.1 403 Forbidden');
        echo 'CSRF token validation failed';
        exit;
      }

      // Verify token
      if (!hash_equals($_SESSION['_token'], $_POST['_token'])) {
        // Token invalid
        header('HTTP/1.1 403 Forbidden');
        echo 'CSRF token validation failed';
        exit;
      }
    }

    // Generate new token for next request
    if (!isset($_SESSION['_token'])) {
      $_SESSION['_token'] = bin2hex(random_bytes(32));
    }

    // Continue with the request
    return $request;
  }
}
