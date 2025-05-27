<?php

namespace App\Controllers;

use App\Models\Category;
use Exception;

class CategoryController extends Controller {
  public function create() {
    try {
      $model = new Category();
      $categories = $model->all();
//      pr($categories); die;
      if(!$categories || count($categories) <= 0) {
        throw new Exception('Error empty list of categories.');
      }

      $data = [
        'title' => 'Choose a Category',
        'categories' => $categories
      ];

      $this->view('categories/category', $data);
    } catch(Exception $e) {
      redirect('/?error='.urlencode('Sorry, but this feature is not ready for now.'));
    }
  }
}
