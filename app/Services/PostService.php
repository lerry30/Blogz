<?php

namespace App\Services;

use App\Database\Database;
use App\Helpers\Auth;
use Exception;

class PostService {
  private $db = null;

  public function __construct() {
    $this->db = Database::getConnection();
  }

  public function archive($postId) {
    try {
      $this->db->beginTransaction();

      $userId = Auth::getUser()['id'];
      $archived = 'archived';

      $stsStmt = $this->db->prepare("SELECT status FROM blog_posts WHERE id = :id AND user_id = :user_id;");
      $stsStmt->bindParam(':id', $postId);
      $stsStmt->bindParam(':user_id', $userId);
      $stsStmt->execute();
      $status = $stsStmt->fetch()['status'];

      $postStmt = $this->db->prepare("UPDATE blog_posts SET status = :status WHERE id = :id AND user_id = :user_id;");
      $postStmt->bindParam(':status', $archived);
      $postStmt->bindParam(':id', $postId);
      $postStmt->bindParam(':user_id', $userId);
      $postStmt->execute();

      $prevStmt = $this->db->prepare("INSERT INTO previous_blog_posts (blog_post_id, status) VALUE (:blog_post_id, :status);");
      $prevStmt->bindParam(':blog_post_id', $postId);
      $prevStmt->bindParam(':status', $status);
      $prevStmt->execute();

      $this->db->commit();
      return true;
    } catch(Exception $e) {
      $this->db->rollback();
      throw $e;
    }
  }

  public function unarchive($postId) {
    try {
      $this->db->beginTransaction();

      $prevStmt = $this->db->prepare("SELECT * FROM previous_blog_posts WHERE blog_post_id = :blog_post_id;");
      $prevStmt->bindParam(':blog_post_id', $postId);
      $prevStmt->execute();
      $prevStatus = $prevStmt->fetch();

      if(!is_array($prevStatus) || empty($prevStatus))
        $prevStatus = ['status' => 'draft'];

      $postStmt = $this->db->prepare("UPDATE blog_posts SET status = :status WHERE id = :id;");
      $postStmt->bindParam(':status', $prevStatus['status']);
      $postStmt->bindParam(':id', $postId);
      $postStmt->execute();

      $prevDelStmt = $this->db->prepare("DELETE FROM previous_blog_posts WHERE blog_post_id = :blog_post_id;");
      $prevDelStmt->bindParam(':blog_post_id', $postId);
      $prevDelStmt->execute();

      $this->db->commit();
      return true;
    } catch(Exception $e) {
      $this->db->rollback();
      throw $e;
    }
  }

  public function removeBlogPost($postId) {
    try {
      $this->db->beginTransaction();

      $userId = Auth::getUser()['id'];

      $imgStmt = $this->db->prepare("SELECT featured_image FROM blog_posts WHERE id = :id AND user_id = :user_id;");
      $imgStmt->bindParam(':id', $postId);
      $imgStmt->bindParam(':user_id', $userId);
      $imgStmt->execute();
      $fImgData = $imgStmt->fetch();

      $delStmt = $this->db->prepare("DELETE FROM blog_posts WHERE id = :id AND user_id = :user_id;");
      $delStmt->bindParam(':id', $postId);
      $delStmt->bindParam(':user_id', $userId);
      $delStmt->execute();

      $prevStmt = $this->db->prepare("DELETE FROM previous_blog_posts WHERE blog_post_id = :blog_post_id;");
      $prevStmt->bindParam(':blog_post_id', $postId);
      $prevStmt->execute();

      $this->db->commit();

      if(!is_array($fImgData) || empty($fImgData))
        return null;
      return $fImgData['featured_image'];
    } catch(Exception $e) {
      $this->db->rollback();
      throw $e;
    }
  }
}
