<?php

namespace App\Models;

use App\Database\Database;

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
    $this->db = Database::getConnection();
  }

  /**
   * Get all records from the table
   *
   * @return array
   */
  public function all($isDesc=false) {
    $orderBy = $isDesc ? 'DESC' : 'ASC';
    $stmt = $this->db->prepare("SELECT * FROM {$this->table} ORDER BY {$this->primaryKey} {$orderBy}");
    $stmt->execute();

    return $stmt->fetchAll();
  }

  /**
   * Find a record by its primary key
   *
   * @param mixed $id
   * @return array|false
   */
  public function find($id) {
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
  public function create($data) {
    // Filter out non-fillable fields
    $filteredData = array_intersect_key($data, array_flip($this->fillable));

    $columns = implode(', ', array_keys($filteredData));
    $placeholders = ':'.implode(', :', array_keys($filteredData));

    $stmt = $this->db->prepare("INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})");

    foreach($filteredData as $key => $value) {
      $stmt->bindValue(":{$key}", $value);
    }

    if($stmt->execute()) {
      $lastId1 = $this->db->lastInsertId();
//      $lastId2 = $this->db->lastInsertId($this->table); // Try with table name
//      $affected = $stmt->rowCount();
      return $lastId1;
    }
//    error_log("Execute failed: " . print_r($stmt->errorInfo(), true));
    return false;
  }

  /**
   * Create multi records
   *
   * @param array $data
   * @return int|false The last insert ID or false on failure
   */
  public function createMulti($data) {
    $filteredData = [];
    foreach($data as $arr) {
      // Filter out non-fillable fields
      array_push(
        $filteredData,
        array_intersect_key($arr, array_flip($this->fillable))
      );
    }

    $pivotCols = array_keys($filteredData[0]);
    $columns = implode(', ', $pivotCols);
    $prepCols = [];

    for($i = 0; $i < count($filteredData); $i++) {
      $rowPlaceholder = [];
      foreach($pivotCols as $col) {
        $rowPlaceholder[] = ":{$col}_{$i}";
      }
      array_push(
        $prepCols,
        implode(', ', $rowPlaceholder)
      );
    }

    $placeholders = '('.implode('), (', $prepCols).')';
    $stmt = $this->db->prepare("INSERT INTO {$this->table} ({$columns}) VALUES {$placeholders}");

    foreach($filteredData as $index => $fData) {
      foreach($pivotCols as $col) {
        $stmt->bindValue(
          ":{$col}_{$index}",
          $fData[$col]
        );
      }
    }

    $insertedIds = [];

    if($stmt->execute()) {
      // Note: lastInsertId() only returns the ID of the first inserted row
      // For multiple rows, you might need a different approach to get all IDs
      $firstId = $this->db->lastInsertId();

      // If you need all inserted IDs and your table has an auto-increment primary key
      // you can calculate them (assuming no gaps in sequence):
      for($i = 0; $i < count($filteredData); $i++) {
        array_push($insertedIds, $firstId + $i);
      }

      return [
        'success' => true,
        'inserted_ids' => $insertedIds,
        'count' => count($filteredData)
      ];
    }

    return [
      'success' => false,
      'error' => 'Failed to execute statement'
    ];
  }

  /**
   * Update a record
   *
   * @param mixed $id
   * @param array $data
   * @return bool
   */
  public function update($id, $data) {
    // Filter out non-fillable fields
    $filteredData = array_intersect_key($data, array_flip($this->fillable));

    $setClause = [];
    foreach(array_keys($filteredData) as $key) {
        $setClause[] = "{$key} = :{$key}";
    }

    $setClause = implode(', ', $setClause);

    $stmt = $this->db->prepare("UPDATE {$this->table} SET {$setClause} WHERE {$this->primaryKey} = :id");
    $stmt->bindParam(':id', $id);

    foreach($filteredData as $key => $value) {
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
  public function delete($id) {
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

  /**
   * Query existing data base on given input
   */
  public function filter($field, $data, $isDesc = false) {
    if(empty($data))
      return [];

    $placeholders = [];
    $params = [];

    foreach($data as $i => $item) {
      $key = ":param_{$i}";
      $placeholders[] = $key;
      $params[$key] = $item;
    }

    $orderBy = $isDesc ? 'DESC' : 'ASC';
    $sql = "SELECT * FROM {$this->table} WHERE {$field} IN (" . 
           implode(',', $placeholders) . ") ORDER BY {$this->primaryKey} {$orderBy}";

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
  }
}
