<?php

namespace App\Helpers;

class Auth {
  public static function saveUserData($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['firstname'] = $user['firstname'];
    $_SESSION['lastname'] = $user['lastname'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['is_admin'] = $user['is_admin'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['status'] = $user['status'];
  }

  public static function getUser() {
    if(!self::isLoggedIn()) {
      return null;
    }

    return [
      'id' => $_SESSION['user_id'],
      'firstname' => $_SESSION['firstname'],
      'lastname' => $_SESSION['lastname'],
      'email' => $_SESSION['email'],
      'is_admin' => $_SESSION['is_admin'],
      'role' => $_SESSION['role'],
      'status' => $_SESSION['status']
    ];
  }

  public static function isLoggedIn() {
    return isset($_SESSION['user_id']) &&
           !empty($_SESSION['user_id']) &&
           is_numeric($_SESSION['user_id']);
  }

  public static function logout() {
    try {
      // Start session if not already started
      if(session_status() == PHP_SESSION_NONE) {
        session_start();
      }

      // Log the logout event (optional)
      if(isset($_SESSION['user_id'])) {
        error_log("User {$_SESSION['user_id']} logged out");
      }

      // Clear all session data
      session_unset();
      session_destroy();

      // Remove session cookie
      if(isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', 1);
        setcookie(session_name(), '', 1, '/');
      }

      // Start fresh session (prevents session fixation)
      session_start();
      session_regenerate_id(true);

      return true;
    } catch(Exception $e) {
      error_log("Logout error: " . $e->getMessage());
      return false;
    }
  }
}
