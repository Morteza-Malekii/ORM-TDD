<?php

require_once __DIR__ . "/../../vendor/autoload.php";

use App\Database\PDODatabaseConnection;
use App\Database\PDOQueryBuilder;
use App\Helpers\config;

$config = config::get('Database','pdo_testing');
$connection = new PDODatabaseConnection($config);
$queryBuilder = new PDOQueryBuilder($connection->connect());
$queryBuilder->truncateAllTable();