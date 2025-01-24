<?php
namespace Tests\unit;

use PDO;
use App\Helpers\config;
use PHPUnit\Framework\TestCase;
use App\Database\PDODatabaseConnection;
use App\Contracts\DatabaseConnectionInterface;
use App\Extentions\ConfigFileArrayKeysinvalid;
use App\Extentions\PDODatabaseConnectionException;
use PHPUnit\Framework\Attributes\Depends;

class PDODatabaseConnectionTest extends TestCase
{
   public function testPDODatabaseConnectionImplimentDatabaseConnectionInterface()
   {
      $config = $this->getConfig();
      $PDOConection = new PDODatabaseConnection($config);
      $this->assertInstanceOf(DatabaseConnectionInterface::class,$PDOConection);
   }  
   

   public function testConnectMethodShouldReturnValidInstanse()
   {
      $config = $this->getConfig();
      $PDOConection = new PDODatabaseConnection($config);
      $pdoHolder = $PDOConection->Connect();
      $this->assertInstanceOf(PDODatabaseConnection::class , $pdoHolder);
      return $pdoHolder;
   }


   #[Depends('testConnectMethodShouldReturnValidInstanse')]
   public function testConnectMethodShouldBeConnectToDatabase($pdoHolder)
   {
      $this->assertInstanceOf(PDO::class , $pdoHolder->getConnection());
   }
   

   private function getConfig()
      {
         return config::get('Database','pdo_testing');
      }


   public function testexceptionIsThrowIfConfigIsInvalid()
   {
      $this->expectException(PDODatabaseConnectionException::class);
      $config = $this->getConfig();
      $config['database']='dummy';
      $PDOConection = new PDODatabaseConnection($config);
      $PDOConection->Connect();
   }
   

   public function testReciveConfigHaveRequireKeys(){
      $this->expectException(ConfigFileArrayKeysinvalid::class);
      $config = $this->getConfig();
      unset($config['db_user']);
      $PDOConection = new PDODatabaseConnection($config);
      $PDOConection->Connect();
   }
}