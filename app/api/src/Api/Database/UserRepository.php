<?php

namespace Api\Database;

class UserRepository {
    private $databaseConnection;


    /**
     * UserRepository constructor.
     * @param $databaseConnection
     */
    public function __construct($databaseConnection) {
        $this->databaseConnection = $databaseConnection;
    }
}
