<?php
namespace App\Middleware;

/**
 * Authentication Middleware
 * Checks if user is logged in
 */
class AuthMiddleware implements MiddlewareInterface {
  /**
   * Handle the incoming request
   * @param mixed $request
   * @return mixed
   */
  public function handle($request) {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
      // Redirect to login page
      header('Location: /login');
      exit;
    }

    // Continue with the request
    return $request;
  }
}
