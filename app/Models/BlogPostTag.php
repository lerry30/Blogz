<?php

namespace App\Models;

class BlogPostTag extends Model {
  protected $table = 'blog_post_tags';
  protected $primaryKey = 'id';
  protected $fillable = [
    'blog_post_id',
    'tag_id'
  ];
}
