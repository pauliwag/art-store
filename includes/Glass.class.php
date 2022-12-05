<?php
# @author Paul Roode

require_once('ArtDB.class.php');

class Glass {
    
    protected $db;
    
    public function __construct() {
        $this->db = ArtDB::getInstance();
    }
    
    protected function getAll() {
        $sql = 'SELECT * FROM typesglass ORDER BY Title';
        $str = '';
        try {
            $statement = $this->db->run($sql);
            foreach ($statement->fetchAll() as $row) {
                $str .= '<option value="' . $row['GlassID'] . '">';
                $str .= $row['Title'] != '[None]'
                    ? $row['Title'] . ' [$' . number_format($row['Price']) . ', ' . $row['Description'] . ']'
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