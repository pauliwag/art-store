<?php
# @author Paul Roode

require_once('ArtDB.class.php');

class Frame {
    
    protected $db;
    
    public function __construct() {
        $this->db = ArtDB::getInstance();
    }
    
    protected function getAll() {
        $sql = 'SELECT * FROM typesframes ORDER BY Title';
        $str = '';
        try {
            $statement = $this->db->run($sql);
            foreach ($statement->fetchAll() as $row) {
                $str .= '<option value="' . $row['FrameID'] . '">';
                $str .= $row['Title'] != '[None]'
                    ? $row['Title'] . ' [$' . number_format($row['Price']) . ', ' . $row['Color'] . ', ' . $row['Syle'] . ']'
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