<?php

namespace App\Models;

class Tag extends Model {
  protected $table = 'tags';
  protected $primaryKey = 'id';
  protected $fillable = [
    'name',
    'slug',
    'usage_count'
  ];
}
