<?php
namespace App\Controllers;

class HomeController extends Controller {
  /**
   * Display the home page
   */
  public function index() {
    $data = [
      'title' => 'Home Page',
      'content' => 'Welcome to Blogz!'
    ];
    $this->view('home/index', $data);
  }
  /**
   * Display the about page
   */
  public function about() {
    $data = [
      'title' => 'About Us',
      'content' => 'This is a simple MVC framework built with PHP.'
    ];
    $this->view('home/about', $data);
  }
  /**
   * Display the contact page
   */
  public function contact() {
    // Generate CSRF token for the contact form
    $token = $this->generateCsrfToken(); 
    $data = [
      'title' => 'Contact Us',
      'csrf_token' => $token
    ];

    $this->view('home/contact', $data);
  }
  /**
   * Process contact form submission
   */
  public function submitContact() {
    // Verify CSRF token
    if(!$this->verifyCsrf()) {
      die('CSRF token validation failed');
    }
    // Get form data
    $name = $this->post('name');
    $email = $this->post('email');
    $message = $this->post('message');

    // Validate data
    if (empty($name) || empty($email) || empty($message)) {
      // Redirect back with error
      // In a full implementation, you would use a flash messaging system
      $this->redirect('/contact?error=Please fill all fields');
    }

    // Process the form (e.g., send email, save to database)

    // Redirect with success message
    $this->redirect('/contact?success=Message sent successfully');
  }
}
