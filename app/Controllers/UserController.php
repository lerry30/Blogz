<?php

namespace App\Controllers;

use App\Models\User;
use App\Helpers\InputValidator;
use App\Helpers\Auth;
use Exception;
use InvalidArgumentException;

class UserController extends Controller {
  public function create() {
    $token = $this->generateCsrfToken();
    $data = [
      'title' => 'Signup',
      'csrf_token' => $token
    ];

    $this->view('user/signup', $data);
  }

  public function store() {
    try {
      if(!$this->verifyCsrf()) {
        die('CSRF token validation failed');
      }

      $rawFname = trim($this->post('fname'));
      $rawLname = trim($this->post('lname'));
      $rawEmail = trim($this->post('email'));
      $rawPassword = trim($this->post('password'));

      $fname = InputValidator::validateString(
        $rawFname, [
          'required' => true,  // Changed from 'require' to 'required'
          'min_length' => 2,
          'max_length' => 50,
          'pattern' => '/^[a-zA-Z\s]+$/'
        ]
      );

      $lname = InputValidator::validateString(
        $rawLname, [
          'required' => true,  // Changed from 'require' to 'required'
          'min_length' => 2,
          'max_length' => 50,
          'pattern' => '/^[a-zA-Z\s]+$/'
        ]
      );

      $email = InputValidator::validateEmail($rawEmail);

      $password = InputValidator::validateString(
        $rawPassword, [
          'required' => true,  // Changed from 'require' to 'required'
          'min_length' => 6,
          'max_length' => 50,
          'pattern' => '/^[a-zA-Z0-9!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]+$/'
        ]
      );

      $model = new User();
      if(isset($model)) {
        $id = $model->createUser([
          'firstname' => ucwords($fname),
          'lastname' => ucwords($lname),
          'email' => $email,
          'password' => $password,
          'is_admin' => 0,
          'role' => 'user',
          'status' => 'active'
        ]);

        $user = $model->find($id);
        Auth::saveUserData($data);
      }
      $this->redirect('/?success='.urlencode('You\'re now successfully registered'));
    } catch(InvalidArgumentException $e) {
      $this->redirect('/users/create?error=' . urlencode($e->getMessage()));
    } catch(Exception $e) {
      $this->redirect('/users/create?error=' . urlencode($e->getMessage()));
    }
  }

  public function login() {
    $token = $this->generateCsrfToken();
    $data = [
      'title' => 'Signin',
      'csrf_token' => $token
    ];

    $this->view('/user/login', $data);
  }

  public function auth() {
    try {
      if(!$this->verifyCsrf()) {
        die('CSRF token validation failed');
      }

      $rawEmail = trim($this->post('email'));
      $rawPassword = trim($this->post('password'));

      $email = InputValidator::validateEmail($rawEmail);
      $password = InputValidator::validateString(
        $rawPassword, [
          'required' => true,
          'min_length' => 6,
          'max_length' => 50,
          'pattern' => '/^[a-zA-Z0-9!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]+$/'
        ]
      );

      $model = new User();
      $user = $model->verifyCredentials($email,$password);
      if(!$user) {
        throw new Exception('User not found');
      }

      if(isset($_POST['password'])) {
        unset($_POST['password']);
      }

      Auth::saveUserData($user);

      $this->redirect('/dashboards/user');
    } catch(InvalidArgumentException $e) {
      $this->redirect('/users/login?error='.urlencode('Wrong username and password'));
    } catch(Exception $e) {
      $this->redirect('/users/login?error='.urlencode('Wrong username and password'));
    }
  }

  public function logout() {
    if(Auth::logout()) {
      redirect('/users/login');
    }
  }
}
