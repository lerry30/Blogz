<?php

namespace App\Controllers;

use App\Models\Post;
use App\Models\Tag;
use App\Models\BlogPostTag;
use App\Helpers\InputValidator;
use InvalidArgumentException;
use Exception;

class TagController extends Controller {
  public function create($postId) {
    try {
      $this->verifyPostId($postId);

      $tagModel = new Tag();
      $tags = $tagModel->all();

      $token = $this->generateCsrfToken();
      $data = [
        'title' => 'Choose Tags',
        'csrf_token' => $token,
        'tags' => $tags,
        'postId' => $postId
      ];

      $this->view('tag/add', $data);
    } catch(Exception $e) {
      $this->redirect('/post/mypost');
    }
  }

  public function store() {
    try {
      if(!$this->verifyCsrf()) {
        die('CSRF validation failed');
      }

      $postId = $this->post('post');
      $sTags = $this->post('s-tags');

      $this->verifyPostId($postId);

      if(!is_array($sTags) || count($sTags) == 0) {
        throw new InvalidArgumentException('Tag selection is required');
      }

      $fTags = [];
      foreach($sTags as $tag) {
        $tTag = trim($tag);
        if(!empty($tTag)) {
          array_push($fTags, $tTag);
        }
      }

      $tagModel = new Tag();
      $tagData = $tagModel->filter('name', $fTags);

      $blogPostTag = new BlogPostTag();

      $blogPost = [];
      foreach($tagData as $data) {
        array_push($blogPost, [
          'blog_post_id' => $postId,
          'tag_id' => $data['id']
        ]);
      }

      $result = $blogPostTag->createMulti($blogPost);
      if($result['success']) {
        $this->redirect('/posts/mypost?success='.urlencode('Successfully saved blog post tags'));
      } else {
        $this->redirect('/posts/mypost?error='.urlencode('Saving blog post tags failed'));
      }
    } catch(InvalidArgumentException $e) {
      $this->redirect('/posts/mypost?error='.$e->getMessage());
    } catch(Exception $e) {
      //error_log($e->getMessage());
      $this->redirect('/posts/mypost?error='.urlencode('Saving post failed'));
    }
  }

  private function verifyPostId($postId) {
    if(!isset($postId) || empty($postId) || !is_numeric($postId)) {
      throw new Exception('Undefined blog post');
    }

    $postModel = new Post();
    $blogPost = $postModel->find($postId);
    if(!$blogPost || count($blogPost) == 0) {
      throw new Exception('Undefined blog post');
    }
  }
}
