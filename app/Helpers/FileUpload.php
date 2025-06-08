<?php

namespace App\Helpers;

use InvalidArgumentException;

class FileUpload {
  public static function image($image, $subDir=null) {
    // Accept one level sub directory
    $cSubDir = !$subDir ? '' : str_replace('/', '', $subDir).'/';
    $target_dir = getRoot().'/public/assets/img/uploads/'.$cSubDir;
    $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
    $max_size = 5 * 1024 * 1024; // 5MB max file size

    // Create uploads directory if it doesn't exist
    if(!file_exists($target_dir)) {
      mkdir($target_dir, 0777, true);
    }

    // Get file information
    $file_name = $image['name'];
    $file_tmp = $image['tmp_name'];
    $file_size = $image['size'];
    $file_error = $image['error'];

    // Get file extension
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // Generate unique filename to prevent conflicts
    $new_filename = uniqid().'.'.$file_ext;
    $target_file = $target_dir.$new_filename;

    // Check if file was uploaded without errors
    if($file_error !== UPLOAD_ERR_OK) {
      throw new InvalidArgumentException('Upload error occurred.');
    }

    // Check file size
    if($file_size > $max_size) {
      throw new InvalidArgumentException('File is too large. Maximum size is 5MB.');
    }

    // Check file type
    if(!in_array($file_ext, $allowed_types)) {
      throw new InvalidArgumentException('Only JPG, JPEG, PNG & GIF files are allowed.');
    }

    // Verify it's actually an image
    $check = getimagesize($file_tmp);
    if($check === false) {
      throw new InvalidArgumentException('File is not a valid image.');
    }

    // Upload file if everything is ok
    if(move_uploaded_file($file_tmp, $target_file)) {
      return $cSubDir.$new_filename;
    }

    return null;
  }
}
