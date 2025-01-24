<?php
namespace Tests\unit;

use App\Database\PDODatabaseConnection;
use App\Helpers\config;
use PHPUnit\Framework\TestCase;
use App\Database\PDOQueryBuilder;
use PhpParser\ErrorHandler\Throwing;

class PDOQueryBuilderTest extends TestCase
{
    private $queryBuilder;
    public function setUp() :void
    {
        $pdoConnection = new PDODatabaseConnection($this->getConfig());
        $this->queryBuilder = new PDOQueryBuilder($pdoConnection->Connect());
        $this->queryBuilder->beginTransaction();
        parent::setUp();
    }
    

    public function tearDown():void
    {
        $this->queryBuilder->rollBack();
        parent::tearDown();
    }


    public function testItCanCreateData()
    {
        $result = $this->InsertToDb();
        $this->assertIsInt($result);
        $this->assertGreaterThan(0,$result);
    }


    public function testItCanUpdateData()
    {
        $this->InsertToDb();
        $result = $this->queryBuilder
             ->table('bugs')
             ->where('user','morteza167')
             ->update(['email'=>'mrt@gmail.com','name'=>'data after update']);
        $this->assertEquals(1,$result);
    } 


    public function testItCanUpdateMultipleWhere()
    {
        $this->InsertToDb();
        $this->InsertToDb(['user'=>'mahsa']);
        $result = $this->queryBuilder
                        ->table('bugs')
                        ->where('user','morteza167')
                        ->where('link','mrt.com/bug_report')
                        ->update(['email'=>'mormah@gmail.com']);
        $this->assertEquals(1,$result);
    }


    public function InsertToDb($option = [])
    {
        $data = array_merge([
            'name'=>'Morteza',
            'link'=>'mrt.com/bug_report',
            'user'=>'Morteza167',
            'email'=>'Morteza167@gmail.com'
        ],$option);
        return $this->queryBuilder->table('bugs')->create($data);
    }


    private function getConfig()
    {
        return config::get('Database', 'pdo_testing');
    }


    public function testDeleteData()  
    {
        $this->InsertToDb();
        $this->InsertToDb();
        $this->InsertToDb();
        $this->InsertToDb();
        $result = $this->queryBuilder
             ->table('bugs')
             ->where('user','morteza167')
             ->deleteData();
        $this->assertEquals(4,$result); 
    }


    public function testItCanFetchData()
    {
        $this->insertMultipleData(10);
        $this->insertMultipleData(10, ['user' => 'mahsa']);
        $result = $this->queryBuilder
                ->table('bugs')
                ->where('user','mahsa')
                ->get();
        $this->assertIsArray($result);
        $this->assertCount(10 , $result);
    }


    private function insertMultipleData($count , $option = [])
    {
        for($i=1; $i <= $count; $i++)
        {
            $this->InsertToDb($option);
        }
    }


    public function testItCanFetchSpecificColumns()
    {
        $this->insertMultipleData(10);
        $this->insertMultipleData(10,['user'=>'mahsa']);
        $result = $this->queryBuilder
                ->table('bugs')
                ->where('user','mahsa')
                ->get(['user','email']);
        $this->assertIsArray($result);
        $result = json_decode(json_encode($result[0]),true);
        $this->assertArrayHasKey('user',$result);
        $this->assertArrayHasKey('email',$result);
        $this->assertEquals(['user','email'] , array_keys($result));
    }


    public function testItCanGetFirstRow()
    {
        $this->insertMultipleData(10,['user'=>'ali']);
        $result = $this->queryBuilder
                    ->table('bugs')
                    ->where('user','ali')
                    ->getFirstRecord();
        $this->assertIsObject($result);
        $this->assertObjectHasProperty('name',$result);
        $this->assertObjectHasProperty('user',$result);
        $this->assertObjectHasProperty('email',$result);
        $this->assertObjectHasProperty('link',$result);
    }


    public function testFindWithId()
    {
        $id = $this->InsertToDb();
        $id = $this->InsertToDb(['user'=>'morteza']);
        $result = $this->queryBuilder
                ->table('bugs')
                ->find($id);
        $this->assertIsObject($result);
        $this->assertEquals('morteza',$result->user);
    }


    public function testItCanFindBy()
    {
        $this->InsertToDb();
        $id = $this->InsertToDb(['name'=>'mahsa']);
        $result = $this->queryBuilder
             ->table('bugs')
             ->findBy('name','mahsa');
        $this->assertIsObject($result);
        $this->assertEquals($id,$result->id);
    }


    public function testReturnEmptyArrayWhenDataIsNotFound()
    {
        $this->insertMultipleData(10,['user'=>'zahra']);
        $result = $this->queryBuilder
                ->table('bugs ')
                ->where('name','jimi')
                ->get();
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }
    

    public function testItCanReturnNullWhenUpdateIsFailed()
    {
        $this->insertMultipleData(4);
        $result = $this->queryBuilder
                ->table('bugs')
                ->where('name','vahid')
                ->update(['name'=>'alis']);
        $this->assertEquals(0,$result);
    }


    public function testReturnZeroWhenFirstMethodNotFound()
    {
        $this->insertMultipleData(5);
        $result = $this->queryBuilder
                ->table('bugs')
                ->where('name','afshin')
                ->getFirstRecord();
        $this->assertNull($result);
    }
}