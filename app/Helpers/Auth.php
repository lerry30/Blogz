<?php

namespace App\Helpers;

class Auth {
  public static function isLoggedIn() {
    return isset($_SESSION['user_id']) &&
           !empty($_SESSION['user_id']) &&
           is_numeric($_SESSION['user_id']);
  }

  public static function logout() {
    try {
      // Start session if not already started
      if (session_status() == PHP_SESSION_NONE) {
        session_start();
      }

      // Log the logout event (optional)
      if (isset($_SESSION['user_id'])) {
        error_log("User {$_SESSION['user_id']} logged out");
      }

      // Clear all session data
      session_unset();
      session_destroy();

      // Remove session cookie
      if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', 1);
        setcookie(session_name(), '', 1, '/');
      }

      // Start fresh session (prevents session fixation)
      session_start();
      session_regenerate_id(true);

      return true;
    } catch (Exception $e) {
      error_log("Logout error: " . $e->getMessage());
      return false;
    }
  }
}
