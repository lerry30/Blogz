<?php

namespace App\Database;

/**
 * Database Connection Class
 * Handles database connections using Singleton pattern
 */
class Database {
  /**
   * Database connection instance
   *
   * @var \PDO
   */
  private static $connection = null;

  /**
   * Database configuration
   *
   * @var array
   */
  private static $config = null;

  /**
   * Private constructor to prevent direct instantiation
   */
  private function __construct() {}

  /**
   * Get database connection
   *
   * @return \PDO
   */
  public static function getConnection() {
    if (self::$connection === null) {
      self::connect();
    }

    return self::$connection;
  }

  /**
   * Create database connection
   *
   * @throws \Exception
   */
  private static function connect() {
    try {
      if (self::$config === null) {
        self::$config = require getRoot().'/config/database.php';
      }

      $dsn = "mysql:host=" . self::$config['host'] . ";dbname=" . self::$config['database'] . ";charset=" . self::$config['charset'];
      $options = [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        \PDO::ATTR_EMULATE_PREPARES => false,
      ];

      self::$connection = new \PDO($dsn, self::$config['username'], self::$config['password'], $options);
    } catch (\PDOException $e) {
      throw new \Exception("Database connection failed: " . $e->getMessage());
    }
  }

  /**
   * Close database connection
   */
  public static function closeConnection() {
    self::$connection = null;
  }
}
