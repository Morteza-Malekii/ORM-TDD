<?php
namespace Tests\unit;

use App\Helpers\config;
use PHPUnit\Framework\TestCase;
use App\Extentions\confingFileNotFoundNewException;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertIsArray;

class ConfigTest extends TestCase
{
    public function testGetFileContentsReturnArray()
    {
        $config = config::getFileContent('Database');
        $this->assertIsArray($config);
    } 


    public function testIfGetExceptionIsTrue(){
        $this->expectException(confingFileNotFoundNewException::class);
        $config = config::getFileContent('dummy');
    }

    
    public function testgetMethodReturnValidData()
    {
        $array = config::get('Database','pdo');
        $expectedArray = [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'database' => 'bug_tracker',
            'db_user' => 'root',
            'db_password' => '12345'        
        ];
        $this->assertEquals($array , $expectedArray);
    }

}

