<?php
namespace App\Models;

class User extends Model {
  protected $table = 'users';
  protected $primaryKey = 'id';

  /**
   * The attributes that are mass assignable
   * @var array
   */
  protected $fillable = [
    'firstname', 'lastname', 'email', 'password',
    'is_admin', 'role', 'status'
  ];

  /**
   * Find a user by email
   *
   * @param string $email
   * @return array|false
   */
  public function findByEmail($email) {
    $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email LIMIT 1");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    return $stmt->fetch();
  }

  /**
   * Create a new user with hashed password
   *
   * @param array $data
   * @return int|false The last insert ID or false on failure
   */
  public function createUser($data) {
    // Hash the password before storing
    if(isset($data['password'])) {
      $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
    }

    return $this->create($data);
  }

  /**
   * Verify a user's credentials
   *
   * @param string $email
   * @param string $password
   * @return array|false User data or false if invalid
   */
  public function verifyCredentials($email, $password) {
    $user = $this->findByEmail($email);

    if($user && password_verify($password, $user['password'])) {
      // Remove sensitive data before returning
      unset($user['password']);
      return $user;
    }

    return false;
  }

  /**
   * Update a user's password
   *
   * @param int $id
   * @param string $newPassword
   * @return bool
   */
  public function updatePassword($id, $newPassword) {
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);

    $stmt = $this->db->prepare("UPDATE {$this->table} SET password = :password WHERE {$this->primaryKey} = :id");
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':id', $id);

    return $stmt->execute();
  }
}
