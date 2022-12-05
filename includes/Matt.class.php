<?php
# @author Paul Roode

require_once('ArtDB.class.php');

class Matt {
    
    protected $db;
    
    public function __construct() {
        $this->db = ArtDB::getInstance();
    }
    
    protected function getAll() {
        $sql = 'SELECT * FROM typesmatt ORDER BY Title';
        $str = '';
        try {
            $statement = $this->db->run($sql);
            foreach ($statement->fetchAll() as $row) {
                $str .= '<option value="' . $row['MattID'] . '">';
                $str .= $row['Title'] != '[None]'
                    ? $row['Title'] . ' [Hex code: ' . $row['ColorCode'] . ']'
                    : 'None';
                $str .= '</option>';
            }
        } catch (PDOException $e) {
            die($e->getMessage());
        }
        return $str;
    }
    
    public function __toString() {
        return $this->getAll();
    }
    
}

?>