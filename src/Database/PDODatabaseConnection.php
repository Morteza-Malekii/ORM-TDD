<?php
namespace App\Database;

use App\Contracts\DatabaseConnectionInterface;
use App\Extentions\PDODatabaseConnectionException;
use App\Extentions\ConfigFileArrayKeysinvalid;
use App\Helpers\config;
use Exception;
use PDO;
use PDOException;

class PDODatabaseConnection implements DatabaseConnectionInterface
{
    protected $connection;
    protected $config;
    const REQUIRE_CONFIG_KEYS = [
        'driver',
        'host',
        'database',
        'db_user',
        'db_password'
    ];


    public function __construct(array $config) {
        if(!$this->isValidConfigKeys($config))
            throw new ConfigFileArrayKeysinvalid();
       $this->config = $config;
    }


    public function Connect()
    {
        $dsn = $this->generateDsn($this->config);
        try {
            $this->connection = new PDO(...$dsn);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        } catch(PDOException $e) {
            throw new PDODatabaseConnectionException($e->getMessage());
        }
        return $this;
    } 


  public function getConnection()
  {
    return $this->connection;
  }


  protected function generateDsn($config)
  {
    $dsn = "{$config['driver']}:host={$config['host']};dbname={$config['database']}";
    return [$dsn,$config['db_user'],$config['db_password']];
  }

  
  private function isValidConfigKeys($config)
  {
    $matches =  array_intersect(self::REQUIRE_CONFIG_KEYS , array_keys($config));
    return count($matches) === count(self::REQUIRE_CONFIG_KEYS);
  }
}