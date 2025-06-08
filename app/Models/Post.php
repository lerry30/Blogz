<?php

namespace App\Models;

use App\Helpers\Auth;
use Exception;

class Post extends Model {
  protected $table = 'blog_posts';
  protected $primaryKey = 'id';
  protected $fillable = [
    'user_id',
    'category_id',
    'title',
    'slug',
    'excerpt',
    'content',
    'featured_image',
    'status',
    'admin_status',
    'approved_by',
    'approved_at',
    'is_featured',
    'view_count',
    'like_count',
    'published_at'
  ];

  public function createBlogPost($data) {
    $userId = Auth::getUser()['id'];
    $slug = str_replace(' ', '-', strtolower($data['title']));

    $data = [
      ...$data,
      'user_id' => $userId,
      'slug' => $slug,
      'admin_status' => 'pending',
      'approved_by' => null,
      'approved_at' => null,
      'is_featured' => 0,
      'view_count' => 0,
      'like_count' => 0,
      'published_at' => null
    ];

    return $this->create($data);
  }

  public function removeBlogPost($postId) {
    $userId = Auth::getUser()['id'];
    $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :postId AND user_id = :userId");
    $stmt->bindParam(':postId', $postId);
    $stmt->bindParam(':userId', $userId);
    return $stmt->execute();
  }
}
