<?php
namespace Tests\Functional;

use App\Database\PDODatabaseConnection;
use App\Database\PDOQueryBuilder;
use App\Helpers\config;
use App\Helpers\HttpClient;
use LDAP\Result;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Depends;

class CrudTest extends TestCase
{
    private $queryBuilder;
    private $httpClient;
    public function setup():void
    {
        $pdoConnection = new PDODatabaseConnection($this->getConfig());
        $this->queryBuilder = new PDOQueryBuilder($pdoConnection->Connect());
        $this->httpClient = new HttpClient();
        parent::setup();
    }


    public function tearDown():void
    {
        $this->httpClient = null;
        parent::tearDown();
    }

    
    private function getConfig()
    {
        return config::get('Database', 'pdo_testing');
    }


    public function testItCanCreateDataWithApi()
    {
        $data = [
            'json'=>[
                'name'=>'sevil',
                'user'=>'sevil20',
                'link'=>'www.sevil.com',
                'email'=>'hasanzadeh@gmail.com'
            ]
        ];
        $response = $this->httpClient->post('index.php',$data);
        $this->assertEquals(200 , $response->getStatusCode());
        $result = $this->queryBuilder
                    ->table('bugs')
                    ->where('name','sevil')
                    ->where('user', 'sevil20')
                    ->getFirstRecord();
        $this->assertNotNull($result);
        return $result;
    }


    #[Depends('testItCanCreateDataWithApi')]
    public function testItCanUpdateDataWithApi($result)
    {
        $data = [
            'json'=>[
                'id' => $result->id,
                'name' => 'farhan'
            ]
            ];
        $response = $this->httpClient->put('index.php',$data);
        $this->assertEquals(200,$response->getStatusCode());
        $result = $this->queryBuilder
                    ->table('bugs')
                    ->find($result->id);
        $this->assertNotNull($result);
        $this->assertEquals('farhan', $result->name);
      }


    #[Depends('testItCanCreateDataWithApi')]
    public function testItCanFetchData($result)
    {
        $data = [
            'json'=>[
                'id'=>$result->id
            ]
            ];
        
        $response = $this->httpClient->get('index.php',$data);
        $this->assertEquals(200 , $response->getStatusCode());
        $this->assertArrayHasKey('id',json_decode($response->getBody(), true));
    }


    #[Depends('testItCanCreateDataWithApi')]
    Public function testItCanDelete($result)
    {
        $data = [
            'json'=>[
                'id'=>$result->id
            ]
            ];
        $response = $this->httpClient->delete('index.php',$data);
        $this->assertEquals(204 , $response->getStatusCode());
        $result = $this->queryBuilder->table('bugs')
                        ->find($result->id);
        $this->assertNull($result);
    }

}