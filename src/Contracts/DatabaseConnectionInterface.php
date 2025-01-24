<?php
namespace App\Contracts;

interface DatabaseConnectionInterface 
{
    public function Connect();
    public function getConnection();
}