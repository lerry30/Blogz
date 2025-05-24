<?php

namespace App\Controllers;

class DashboardController extends Controller {
  public function user() {
    $data = [
      'title' => 'Dashboard'
    ];

    $this->view('dashboard/user', $data);
  }
}
