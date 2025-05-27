<?php
/**
 * Global helper functions
 */

if (!function_exists('env')) {
  /**
   * Get the value of an environment variable
   * @param string $key
   * @param mixed $default
   * @return mixed
   */
  function env($key, $default = null) {
    $value = $_ENV[$key];
    if ($value === false) {
      return $default;
    }
    switch (strtolower($value)) {
      case 'true':
      case '(true)':
        return true;
      case 'false':
      case '(false)':
        return false;
      case 'null':
      case '(null)':
        return null;
      case 'empty':
      case '(empty)':
        return '';
    }
    return $value;
  }
}

if(!function_exists('config')) {
  /**
   * Get a configuration value
   * @param string $key
   * @param mixed $default
   * @return mixed
   */
  function config($key, $default = null) {
    $keys = explode('.', $key);
    $file = array_shift($keys);

    $configFile = __DIR__ . '/../../config/' . $file . '.php';

    if(!file_exists($configFile)) {
      return $default;
    }

    $config = require_once $configFile;

    foreach($keys as $segment) {
      if(!is_array($config) || !array_key_exists($segment, $config)) {
        return $default;
      }

      $config = $config[$segment];
    }
    return $config;
  }
}

if(!function_exists('asset')) {
  /**
   * Generate an asset URL
   * @param string $path
   * @return string
   */
  function asset($path) {
    return env('APP_URL', '').'/assets/'.ltrim($path, '/');
  }
}

if(!function_exists('url')) {
  /**
   * Generate a URL
   * @param string $path
   * @return string
   */
  function url($path = '') {
    return env('APP_URL', '').'/'.ltrim($path, '/');
  }
}

if(!function_exists('redirect')) {
  /**
   * Redirect to a URL
   * @param string $url
   * @return void
   */
  function redirect($url) {
    header("Location: {$url}");
    exit;
  }
}

if(!function_exists('csrf_token')) {
  /**
   * Generate CSRF token
   * @return string
   */
  function csrf_token() {
    if(!isset($_SESSION['_token'])) {
      $_SESSION['_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_token'];
  }
}

if(!function_exists('csrf_field')) {
  /**
   * Generate CSRF field
   * @return string
   */
  function csrf_field() {
    return '<input type="hidden" name="_token" value="'.csrf_token().'">';
  }
}

if(!function_exists('old')) {
  /**
   * Get old input value
   * @param string $key
   * @param mixed $default
   * @return mixed
   */
  function old($key, $default='') {
    if(isset($_SESSION['_old_input']) && array_key_exists($key, $_SESSION['_old_input'])) {
      return $_SESSION['_old_input'][$key];
    }
    return $default;
  }
}

if(!function_exists('logger')) {
  /**
   * Log a message
   * @param string $message
   * @param string $level
   * @return void
   */
  function logger($message, $level = 'info') {
    $logFile = __DIR__.'/../../storage/logs/app.log';
    $timestamp = date('Y-m-d H:i:s');

    $logMessage = "[{$timestamp}] ".strtoupper($level).": {$message}".PHP_EOL;

    file_put_contents($logFile, $logMessage, FILE_APPEND);
  }
}

if(!function_exists('isLoggedIn')) {
  function isLoggedIn() {
    return isset($_SESSION['user_id']) &&
           !empty($_SESSION['user_id']) &&
           is_numeric($_SESSION['user_id']);
  }
}

if(!function_exists('pr')) {
  function pr($arr) {
    echo '<pre>';
    print_r($arr);
    echo '</pre>';
  }
}
