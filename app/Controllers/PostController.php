<?php

namespace App\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Helpers\InputValidator;
use App\Helpers\Auth;
use App\Helpers\FileUpload;
use InvalidArgumentException;
use Exception;

class PostController extends Controller {
  public function create($categoryId) {
    try {
      if(!isset($categoryId) || empty($categoryId) || !is_numeric($categoryId)) {
        throw new Exception('No category selected.');
      }

      $categoryId = InputValidator::validateInteger($categoryId);
      $catModel = new Category();
      $category = $catModel->find($categoryId);

      if(!$category || count($category) == 0) {
        throw new Exception('Selected category is not exist');
      }

      $token = $this->generateCsrfToken();
      $data = [
        'title' => 'New Post',
        'csrf_token' => $token,
        'category' => $category,
      ];

      $this->view('post/newpost', $data);
    } catch(InvalidArgumentException $e) {
      $this->redirect('/categories/create?error='.urlencode($e->getMessage()));
    } catch(Exception $e) {
      $this->redirect('/categories/create');
    }
  }

  public function store() {
    $categoryId = $this->post('category');
    if(empty($categoryId) || !is_numeric($categoryId)) {
      $this->redirect('/categories/create?error='.urlencode('Category not found.'));
      exit();
    }

    try {
      if(!$this->verifyCsrf()) {
        die('Csrf token validation failed');
      }

      $rawTitle = trim($this->post('title'));
      $rawShortDesc = trim($this->post('short-desc'));
      $rawContent = trim($this->post('content'));
      $rawFtrImg = $this->files('ftr-img');
      $rawStatus = trim($this->post('status'));

      $strDataList = [
        'title' => $rawTitle,
        'shortDesc' => $rawShortDesc,
        'content' => $rawContent,
        'status' => $rawStatus
      ];
      $nList = [];

      foreach($strDataList as $key => $str) {
        $maxChar = $key == 'shortDesc' ? 255 : 100;
        $maxChar = $key == 'content' ? 5000 : $maxChar;

        $input = InputValidator::validateString($str, [
          'required' => true,
          'min_length' => 2,
          'max_length' => $maxChar,
          'pattern' => '/^[a-zA-Z0-9\s.,!?;:\'"\-_()&@#$%^*+=\[\]{}|\\<>\/]+$/u'
        ]);

        $nList[$key] = $input;
      }

      $categoryId = InputValidator::validateInteger($categoryId);

      $folder = 'featured_images';
      $fImage = FileUpload::image($rawFtrImg, $folder);

      $postModel = new Post();
      $postId = $postModel->createBlogPost([
        'category_id' => $categoryId,
        'title' => $nList['title'],
        'excerpt' => $nList['shortDesc'],
        'content' => $nList['content'],
        'featured_image' => $fImage,
        'status' => $nList['status']
      ]);

      if(!$postId) {
        deleteUploadedImage($fImage);
        throw new InvalidArgumentException('Can\'t save the post.');
      }

      $this->redirect('/tags/create/'.$postId);
    } catch(InvalidArgumentException $e) {
      $this->redirect('/posts/create/'.urlencode($categoryId)).'?error='.urlencode($e->getMessage());
    } catch(Exception $e) {
      $this->redirect('/posts/create/'.urlencode($categoryId).'?error='.urlencode('Creating post failed'));
    }
  }

  public function myPost() {
    try {
      // query all user posts
      $postModel = new Post();
      $blogPosts = $postModel->all();

      $token = $this->generateCsrfToken();
      $data = [
        'title' => 'My Posts',
        'csrf_token' => $token,
        'blog_posts' => $blogPosts
      ];

      $this->view('post/mypost', $data);
    } catch(Exception $e) {
      error_log($e->getMessage());
      $this->redirect('/dashboards/user?error='.urlencode('Loading blog post failed'));
    }
  }

  public function destroy() {
    try {
      if(!$this->verifyCsrf()) {
        die('Csrf validation failed');
      }

      $postId = trim($this->post('post'));
      $postId = InputValidator::validateInteger($postId);

      $postModel = new Post();
      $postData = $postModel->find($postId);
      $fImage = $postData['featured_image'];
      $result = $postModel->removeBlogPost($postId);
      if($result) {
        deleteUploadedImage($fImage);
        $this->redirect('/posts/mypost?success='.urlencode('Successfully deleted post'));
        exit();
      }
      throw new Exception('Blog post deletion failed');
    } catch(Exception $e) {
      $this->redirect('/posts/mypost?error='.urlencode('Deleting blog post failed'));
    }
  }
}
