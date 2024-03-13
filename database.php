<?php

class Database
{
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "cashierdb";

    public $conn;

    public function __construct(){
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);
    }
    
}