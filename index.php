<?php
require_once "./vendor/autoload.php";

use App\Database\PDODatabaseConnection;
use App\Database\PDOQueryBuilder;
use App\Helpers\config;

$config = config::get('Database' ,'pdo_testing');
$pdoconnection = new PDODatabaseConnection($config);
$pdoquerybuilder = new PDOQueryBuilder($pdoconnection->Connect());


function request()
{
    return json_decode(file_get_contents('php://input'),true);
}


if($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $pdoquerybuilder->table('bugs')->create(request());
    json_response(null,200);
}


function json_response($data = null , $statusCode = 200)
{
    header_remove();
    header('content-type: application/json');
    http_response_code($statusCode);
    echo json_encode($data);
    exit();
}


if($_SERVER['REQUEST_METHOD'] === 'PUT')
{
    $pdoquerybuilder->table('bugs')
            ->where('id', request()['id'])
            ->update(request());
    json_response(null,200);
}


if($_SERVER['REQUEST_METHOD'] === 'GET')
{
    $response = $pdoquerybuilder->table('bugs')
                ->find(request()['id']);
    json_response($response , 200  );
}


if($_SERVER['REQUEST_METHOD'] === 'DELETE')
{
    $response = $pdoquerybuilder->table('bugs')
                    ->where('id',request()['id'])
                    ->deleteData();
    json_response(null , 204);                      
}

