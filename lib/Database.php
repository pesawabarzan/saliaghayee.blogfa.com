<?php
/** lib/Database.php
 * کانکشن PDO و helper ها
 */
class Database {
  public PDO $pdo;
  public function __construct(array $config) {
    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $config['db_host'], $config['db_name']);
    $options = [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false
    ];
    $this->pdo = new PDO($dsn, $config['db_user'], $config['db_pass'], $options);
  }
}
