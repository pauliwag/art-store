<?php
# @author Paul Roode

require_once('ArtDB.class.php');

class Subject {
    
    protected $db;
    
    public function __construct() {
        $this->db = ArtDB::getInstance();
    }
    
    # echoes a list of subjects for the given painting id
    public function displaySubjects($paintingID) {
        $sql = 'SELECT SubjectName FROM subjects INNER JOIN paintingsubjects ON subjects.SubjectID = paintingsubjects.SubjectID
            INNER JOIN paintings ON paintingsubjects.PaintingID = paintings.PaintingID
            WHERE paintings.PaintingID = ?';
        try {
            $statement = $this->db->run($sql, [$paintingID]);
            foreach ($statement->fetchAll() as $row) {
                echo '<li class="item"><a href="#">' . $row['SubjectName'] . '</a></li>';
            }
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }
    
}

?>