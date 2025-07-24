<?php

namespace App\core\abstract;
use App\core\App;
use App\core\Database;
use App\core\Singleton;

class AbstractRepository extends Singleton
{
    protected Database $db;

    public function __construct(Database $db){
        $this->db = $db;
    }
}