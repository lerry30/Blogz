<?php
namespace App\Models;

/**
 * Base Model Class
 * All models should extend this class
 */
class Model {
  /**
   * Database connection
   *
   * @var \PDO
   */
  protected $db;

  /**
   * Table name for the model
   *
   * @var string
   */
  protected $table;

  /**
   * Primary key for the table
   *
   * @var string
   */
  protected $primaryKey = 'id';

  /**
   * Columns that can be filled with data
   *
   * @var array
   */
  protected $fillable = [];

  /**
   * Constructor - Initialize database connection
   */
  public function __construct() {
    try {
      $config = require_once __DIR__ . '/../../config/database.php';

      $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']}";
      $options = [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        \PDO::ATTR_EMULATE_PREPARES => false,
      ];

      $this->db = new \PDO($dsn, $config['username'], $config['password'], $options);
    } catch (\PDOException $e) {
      throw new \Exception("Database connection failed: " . $e->getMessage());
    }
  }

  /**
   * Get all records from the table
   *
   * @return array
   */
  public function all()
  {
    $stmt = $this->db->prepare("SELECT * FROM {$this->table}");
    $stmt->execute();

    return $stmt->fetchAll();
  }

  /**
   * Find a record by its primary key
   *
   * @param mixed $id
   * @return array|false
   */
  public function find($id)
  {
    $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    return $stmt->fetch();
  }

  /**
   * Create a new record
   *
   * @param array $data
   * @return int|false The last insert ID or false on failure
   */
  public function create($data)
  {
    // Filter out non-fillable fields
    $filteredData = array_intersect_key($data, array_flip($this->fillable));

    $columns = implode(', ', array_keys($filteredData));
    $placeholders = ':' . implode(', :', array_keys($filteredData));

    $stmt = $this->db->prepare("INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})");

    foreach ($filteredData as $key => $value) {
        $stmt->bindValue(":{$key}", $value);
    }

    if ($stmt->execute()) {
        return $this->db->lastInsertId();
    }

    return false;
  }

  /**
   * Update a record
   *
   * @param mixed $id
   * @param array $data
   * @return bool
   */
  public function update($id, $data)
  {
    // Filter out non-fillable fields
    $filteredData = array_intersect_key($data, array_flip($this->fillable));

    $setClause = [];
    foreach (array_keys($filteredData) as $key) {
        $setClause[] = "{$key} = :{$key}";
    }

    $setClause = implode(', ', $setClause);

    $stmt = $this->db->prepare("UPDATE {$this->table} SET {$setClause} WHERE {$this->primaryKey} = :id");
    $stmt->bindParam(':id', $id);

    foreach ($filteredData as $key => $value) {
        $stmt->bindValue(":{$key}", $value);
    }

    return $stmt->execute();
  }

  /**
   * Delete a record
   *
   * @param mixed $id
   * @return bool
   */
  public function delete($id)
  {
    $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id");
    $stmt->bindParam(':id', $id);
    return $stmt->execute();
  }

  /**
   * Find records by a field and value
   *
   * @param string $field
   * @param mixed $value
   * @return array
   */
  public function where($field, $value) {
    $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$field} = :value");
    $stmt->bindParam(':value', $value);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  /**
   * Execute a raw SQL query
   *
   * @param string $sql
   * @param array $params
   * @return \PDOStatement
   */
  protected function query($sql, $params = []) {
    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    return $stmt;
  }
}
