<?php

namespace App\Controllers;

class PostController extends Controller {
  public function create() {
    $data = [
      'title' => 'New Post',
      
    ];

    $this->view('posts/newpost', $data);
  }
}
