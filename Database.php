<?php

namespace App\core;
use PDO;
use PDOException;
use App\core\Singleton;

class Database extends Singleton{
    public PDO $pdo;

    private function __construct(PDO $pdo){
        try{
            $this->pdo = $pdo;
            // echo "Connected to the database";
        }catch(PDOException $e){
            echo "Error: " . $e->getMessage();
        }
    }
}