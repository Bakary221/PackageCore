<?php

namespace App\core;
use PDO;
use PDOException;
use App\core\Singleton;

class Database extends Singleton{
    public PDO $pdo;

    private function __construct(){
        try{
            $this->pdo = new PDO(DSN, USER, PASS);
            // echo "Connected to the database";
        }catch(PDOException $e){
            echo "Error: " . $e->getMessage();
        }
    }
}