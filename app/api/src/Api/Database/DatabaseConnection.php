<?php

namespace Api\Database;

use PDO;
use PDOException;
use PDOStatement;

class DatabaseConnection {

    /**
     * @var PDO
     */
    private $pdo;

    /**
     * @var PDOStatement
     */
    private $sQuery;

    private $bConnected = false;

    private $parameters;

    public function __construct() {
        $this->connect();
        $this->parameters = array();
    }

    /**
     * If the SQL query  contains a SELECT or SHOW statement it returns an array containing all of the result set row
     * If the SQL statement is a DELETE, INSERT, or UPDATE statement it returns the number of affected rows
     *
     * @param  string $query
     * @param  array $params
     * @param  int $fetchMode
     * @return mixed
     */
    public function query($query, $params = null, $fetchMode = PDO::FETCH_ASSOC) {
        $query = trim($query);
        $this->init($query, $params);
        $rawStatement = explode(" ", $query);

        $statement = trim(strtolower($rawStatement[0]));

        if ($statement === 'select' || $statement === 'show') {
            return $this->sQuery->fetchAll($fetchMode);
        } else if ($statement === 'insert' || $statement === 'update' || $statement === 'delete') {
            return $this->sQuery->rowCount();
        } else {
            return NULL;
        }
    }

    /**
     * @void
     *
     *    Add the parameter to the parameter array
     * @param string $para
     * @param string $value
     */
    public function bind($para, $value) {
        $this->parameters[sizeof($this->parameters)] = ":" . $para . "\x7F" . utf8_encode($value);
    }

    /**
     * Add more parameters to the parameter array
     *
     * @param array $paraArray
     */
    public function bindMore($paraArray) {
        if (empty($this->parameters) && is_array($paraArray)) {
            $columns = array_keys($paraArray);
            foreach ($columns as $i => &$column) {
                $this->bind($column, $paraArray[$column]);
            }
        }
    }

    /**
     * Returns the last inserted id.
     *
     * @return string
     */
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }

    /**
     * Makes a connection to the database
     */
    private function connect() {
        require_once __DIR__ . '/../../../config/db/config.php';

        try {
            $this->pdo = new PDO(DB_HOST, DB_USER, DB_PASS);

            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            $this->bConnected = true;
        } catch (PDOException $e) {
            echo $e->getMessage();
            die("Unable to select database");
        }
    }

    /**
     * Every method which needs to execute a SQL query uses this method.
     *
     * @param $query string Query string
     * @param $parameters string
     */
    private function init($query, $parameters = "") {
        if (!$this->bConnected) {
            $this->connect();
        }
        try {
            $this->sQuery = $this->pdo->prepare($query);

            $this->bindMore($parameters);

            if (!empty($this->parameters)) {
                foreach ($this->parameters as $param) {
                    $parameters = explode("\x7F", $param);
                    $this->sQuery->bindParam($parameters[0], $parameters[1]);
                }
            }

            $this->sQuery->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
        $this->parameters = array();
    }
}

