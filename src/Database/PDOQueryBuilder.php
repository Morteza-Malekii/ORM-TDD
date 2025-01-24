<?php

namespace App\Database;

use App\Contracts\DatabaseConnectionInterface;
use PDO;

class PDOQueryBuilder 
{
    protected $table;
    protected $connection;
    protected $conditions;
    protected $value;
    protected $statment;
    
    public function __construct(DatabaseConnectionInterface $connection )
    {
        $this->connection = $connection->getConnection();        
    }


    public function table(string $table)
    {
        $this->table = $table;
        return $this;
    }


    public function create(array $data)
    {
        $placeholder = [];
        foreach($data as $key=>$value)
        {
            $placeholder[] = '?';
        }
        $placeholder = implode(',',array_values($placeholder));
        $fiels = implode(',',array_keys($data)) ;
        $this->value = array_values($data);
        $sql = "INSERT INTO {$this->table} ({$fiels}) VALUES ({$placeholder})";
        $this->execute($sql);
        return (int)$this->connection->lastInsertId();
    }


    public function where(string $column , string $value)
    {
        if(is_null($this->conditions))
        {
            $this->conditions = "{$column} = ?";
        }else{
            $this->conditions .= " and {$column} = ?";
        }
        $this->value[] = $value;
        return $this;
    }

    
    public function  update(array $data)  
    {
        $fields = [];
        foreach($data as $column=>$value)
        {
            $fields[]="{$column} = '{$value}'";
        }
        $fields = implode(',', $fields);
        $sql = "UPDATE {$this->table} SET {$fields} WHERE {$this->conditions}";
        $this->execute($sql);
        return $this->statment->rowCount();
    }


   public function truncateAllTable()
   {
     $query = $this->connection->prepare("SHOW TABLES");
     $query->execute();
     foreach($query->fetchAll(PDO::FETCH_COLUMN) as $table)
     {
        $this->connection->prepare("TRUNCATE TABLE `{$table}`")->execute();
     }
   }


   public function deleteData()
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->conditions}";
        $this->execute($sql);
        return $this->statment->rowCount();
    }


    public function beginTransaction()
    {
        $this->connection->beginTransaction();
    }


    public function rollBack()
    {
        $this->connection->rollBack();
    }


    public function get(array $column = ['*'])
    {
        $column = implode(',',$column);
        $sql = "SELECT {$column} FROM {$this->table} WHERE {$this->conditions}";
        $this->execute($sql);
        return $this->statment->fetchAll();
    }

    
    public function getFirstRecord(array $column = ['*'])
    {
        $data = $this->get($column);
        return  empty($data) ? null : $data[0];
    }


    public function find(int $id)
    {
        return $this->where('id',$id)->getFirstRecord();
    }


    public function findBy(string $column , $value)
    {
        return $this->where($column , $value)->getFirstRecord();
    }


    private function execute(string $sql)
    {
        $this->statment = $this->connection->prepare($sql);
        $this->statment->execute($this->value);
        $this->value = [];
        return $this;
    }

}