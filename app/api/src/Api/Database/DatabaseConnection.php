<?php

namespace Api\Database;

use PDO;
use PDOException;

class DatabaseConnection {

    private $pdo;

    public function __construct() {
        include_once '../../../config/db/config.php';

        try {
            $this->pdo = new PDO(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        } catch(PDOException $e) {
            die( "Unable to select database");
        }
    }
}

