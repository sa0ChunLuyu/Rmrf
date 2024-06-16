<?php
require_once __DIR__ . '/Tool.php';

class Db
{
  public $db;
  private static $instance = array();

  public function __construct($data)
  {
    $dsn = "mysql:dbname=" . $data['dbname'] . ";host=" . $data['dbhost'];
    $this->db = new \PDO($dsn, $data['dbuser'], $data['dbpassword']);
    $this->db->query('set character set utf8mb4;');
    $this->db->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
  }

  public static function get($key = ''): Db
  {
    $database['dbname'] = Tool::ini($key . 'DB_DATABASE');
    $database['dbuser'] = Tool::ini($key . 'DB_USERNAME');
    $database['dbpassword'] = Tool::ini($key . 'DB_PASSWORD');
    $database['dbhost'] = Tool::ini($key . 'DB_HOST');
    if (!isset(self::$instance[$database['dbname']]) || !self::$instance[$database['dbname']] instanceof self) {
      self::$instance[$database['dbname']] = new Db($database);
    } else {
      try {
        //断线重连
        self::$instance[$database['dbname']]->db->getAttribute(\PDO::ATTR_SERVER_INFO);
      } catch (\PDOException $e) {
        if (strpos($e->getMessage(), 'MySQL server has gone away') !== false) {
          self::$instance[$database['dbname']] = new Db($database);
        }
      }
    }

    return self::$instance[$database['dbname']];
  }


  /*********PDO**********/
  public function count($sql, $parameters = null): int
  {
    return $this->exeupdate($sql, $parameters);
  }

  public function querysql($sql, $parameters = null)
  {
    return $this->exeupdate($sql, $parameters);
  }


  public function querysqlinsertid($sql, $parameters = null)
  {
    if ($this->exeupdate($sql, $parameters)) {
      return $this->db->lastInsertId();
    } else {
      return 0;
    }
  }


  public function getRow($sql, $parameters = null)
  {
    $res = $this->exequery($sql, $parameters);
    if (count($res) > 0) {

      return $res[0];
    } else {
      return array();
    }
  }

  public function getAll($sql, $parameters = null)
  {
    $res = $this->exequery($sql, $parameters);
    if (count($res) > 0) {
      return $res;
    } else {
      return array();
    }
  }

  public function beginTransaction()
  {
    $this->db->beginTransaction();
  }

  public function rollback()
  {
    $this->db->rollback();
  }

  public function commit()
  {
    $this->db->commit();
  }

  public function exequery($sql, $parameters = null)
  {
    $conn = $this->db;
    $stmt = $conn->prepare($sql);
    $stmt->execute($parameters);
    $rs = $stmt->fetchall(\PDO::FETCH_ASSOC);
    $stmt = null;
    $conn = null;
    return $rs;
  }

  public function exeupdate($sql, $parameters = null)
  {
    $stmt = $this->db->prepare($sql);
    $stmt->execute($parameters);
    $affectedrows = $stmt->rowcount();
    $stmt = null;
    $conn = null;
    return $affectedrows;
  }

  public function checklink()
  {
    $res = $this->db->getAttribute(\PDO::ATTR_SERVER_INFO);
    if (strpos($res, 'server has gone away') !== false) {
      $this->db = null;
      return false;
    } else {
      return true;
    }
  }

  public function getinsertid()
  {
    return $this->db->lastInsertId();
  }

  public function close()
  {
    return $this->db = null;
  }
}
