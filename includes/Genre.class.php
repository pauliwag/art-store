<?php
# @author Paul Roode

require_once('ArtDB.class.php');

class Genre {
    
    protected $db;
    
    public function __construct() {
        $this->db = ArtDB::getInstance();
    }
    
    # echoes a list of genres for the given painting id
    public function displayGenres($paintingID) {
        $sql = 'SELECT GenreName FROM genres INNER JOIN paintinggenres ON genres.GenreID = paintinggenres.GenreID
            INNER JOIN paintings ON paintinggenres.PaintingID = paintings.PaintingID
            WHERE paintings.PaintingID = ?';
        try {
            $statement = $this->db->run($sql, [$paintingID]);
            foreach ($statement->fetchAll() as $row) {
                echo '<li class="item"><a href="#">' . $row['GenreName'] . '</a></li>';
            }
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }
    
}

?>