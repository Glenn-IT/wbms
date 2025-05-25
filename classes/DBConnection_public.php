<?php
if(!defined('DB_SERVER')){
    require_once(__DIR__ . "/../initialize_public.php");
}

class DBConnection {

    private $host = DB_SERVER;
    private $username = DB_USERNAME;
    private $password = DB_PASSWORD;
    private $database = DB_NAME;
    
    public $conn;
    
    public function __construct() {
        if (!isset($this->conn)) {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);
            
            if ($this->conn->connect_error) {
                echo 'Cannot connect to database server: ' . $this->conn->connect_error;
                exit;
            }            
        }    
    }

    public function __destruct() {
        $this->conn->close();
    }
}
?>
