<?php

namespace App\Models;

class Category extends Model {
  protected $table = 'categories';
  protected $id = 'id';
  protected $fillable = [
    'name', 'slug', 'description',
    'is_active', 'post_count'
  ];
}
