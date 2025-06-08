<?php

namespace App\Helpers;

use InvalidArgumentException;
use Exception;

class InputValidator {
  public static function validateString($input, $options = []) {
    $options = array_merge([
      'required' => true,
      'min_length' => 0,
      'max_length' => 255,
      'allow_html' => false,
      'pattern' => null
    ], $options);

    if($options['required'] && empty(trim($input))) {
      throw new InvalidArgumentException("Field is required");
    }

    $input = trim($input);
    $length = strlen($input);

    if($length < $options['min_length'] || $length > $options['max_length']) {
      throw new InvalidArgumentException("String length must be between {$options['min_length']} and {$options['max_length']}");
    }

    if(!$options['allow_html']) {
      $input = strip_tags($input);
    }

    if ($options['pattern'] && !preg_match($options['pattern'], $input)) {
      throw new InvalidArgumentException("Invalid format");
    }

    return $input;
  }

  public static function validateInteger($input, $min = null, $max = null) {
    if(!filter_var($input, FILTER_VALIDATE_INT)) {
      throw new InvalidArgumentException("Invalid integer");
    }

    $int = (int)$input;

    if($min !== null && $int < $min) {
      throw new InvalidArgumentException("Value must be at least $min");
    }

    if($max !== null && $int > $max) {
      throw new InvalidArgumentException("Value must be at most $max");
    }

    return $int;
  }

  public static function validateArray($input, $allowedKeys = null) {
    if(!is_array($input)) {
      throw new InvalidArgumentException("Expected array");
    }

    if($allowedKeys) {
      foreach(array_keys($input) as $key) {
        if (!in_array($key, $allowedKeys)) {
          throw new InvalidArgumentException("Invalid array key: $key");
        }
      }
    }

    return $input;
  }

  public static function validateEmail($email) {
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      throw new InvalidArgumentException("Invalid email format");
    }
    return $email;
  }

  public static function validatePhone($phone) {
    if(!preg_match('/^[\d\s\-\+\(\)]+$/', $phone)) {
      throw new InvalidArgumentException("Invalid phone format");
    }
    return $phone;
  }
}
