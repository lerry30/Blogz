<?php

namespace App\Controllers;

//use App\Models\Post;
//use Exception;

class PostController extends Controller {
  public function create() {
    $data = [
      'title' => 'New Post',
    ];

    $this->view('posts/newpost', $data);
  }
}
