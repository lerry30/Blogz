<?php
namespace App\Controllers;

/**
 * Base Controller Class
 * All controllers should extend this class
 */
class Controller {
  /**
   * Loads and renders a view
   * @param string $view The view file to load
   * @param array $data Data to pass to the view
   * @return void
   */
  protected function view($view, $data = []) {
    // Extract data to individual variables
    extract($data);

    // Check if view exists
    $viewFile = getRoot().'/app/Views/'.$view.'.php';
    if(file_exists($viewFile)) {
      // Start output buffering
      ob_start();

      // Include the view file
      require_once $viewFile;

      // Get the buffer content and clean the buffer
      $content = ob_get_clean();

      // Output the content
      echo $content;
    } else {
      throw new \Exception("View {$view} not found");
    }
  }

  /**
   * Returns data as JSON
   * @param mixed $data Data to convert to JSON
   * @return void
   */
  protected function json($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
  }

  /**
   * Redirect to another URL
   *
   * @param string $url URL to redirect to
   * @return void
   */
  protected function redirect($url) {
    header("Location: {$url}");
    exit;
  }

  /**
   * Get POST data
   *
   * @param string $key The POST key to retrieve
   * @param mixed $default Default value if key doesn't exist
   * @return mixed
   */
  protected function post($key=null, $default=null) {
    if($key === null) {
      return $_POST;
    }
    return isset($_POST[$key]) ? $this->sanitizeInput($_POST[$key]) : $default;
  }

  /**
   * Get GET data
   *
   * @param string $key The GET key to retrieve
   * @param mixed $default Default value if key doesn't exist
   * @return mixed
   */
  protected function get($key = null, $default = null) {
    if($key === null) {
      return $_GET;
    }
    return isset($_GET[$key]) ? $this->sanitizeInput($_GET[$key]) : $default;
  }

  /**
   * Sanitize input data to prevent XSS
   *
   * @param mixed $input Input to sanitize
   * @return mixed Sanitized input
   */
  private function sanitizeInput($input) {
    if(is_array($input)) {
      foreach($input as $key => $value) {
        $input[$key] = $this->sanitizeInput($value);
      }
      return $input;
    }
    // Sanitize the input
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
  }

  /**
   * Verify CSRF token
   * @return bool
   */
  protected function verifyCsrf() {
    if(!isset($_POST['_token']) || !isset($_SESSION['_token'])) {
      return false;
    }
    $token = $_POST['_token'];
    if(hash_equals($_SESSION['_token'], $token)) {
      return true;
    }
    return false;
  }

  /**
   * Generate CSRF token
   *
   * @return string
   */
  protected function generateCsrfToken() {
    if(!isset($_SESSION['_token'])) {
      $_SESSION['_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_token'];
  }

  /**
    * Get file data from $_FILES superglobal
    * @param string|null $key - The file input name
    * @param mixed $default - Default value if key doesn't exist
    * @return mixed - File data or default value
    */
  protected function files($key = null, $default = null) {
    if($key === null) {
      return $_FILES;
    }
    return isset($_FILES[$key]) ? $_FILES[$key] : $default;
  }
}
