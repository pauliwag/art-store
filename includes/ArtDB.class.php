<?php
# @author Paul Roode

require_once('art-config.inc.php');

# singleton
class ArtDB {
    
    private static $instance;
    private $pdo;
    
    private function __construct() {
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION    
        ];
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHAR;
        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }
    
    # returns an instance of self
    public static function getInstance() {
        if (!self::$instance instanceof self) {
            self::$instance = new self;
        }
        return self::$instance;
    }
    
    # clone() and wakeup() prevent external instantation of copies of self 
    public function __clone() { trigger_error('Clone is not allowed', E_USER_ERROR); }
    public function __wakeup() { trigger_error('Deserializing is not allowed', E_USER_ERROR); }
    
    # executes the given SQL with the given params
    public function run($sql, $params=[]) {
        try {
            $statement = $this->pdo->prepare($sql);
            $statement->execute($params);
            return $statement;
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }
    
}

?>