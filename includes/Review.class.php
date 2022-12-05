<?php
# @author Paul Roode

require_once('ArtDB.class.php');

class Review {
    
    protected $db;
    
    public function __construct() {
        $this->db = ArtDB::getInstance();
    }
    
    # displays reviews for the given painting id
    public function displayReviews($paintingID) {
        $sql = 'SELECT ReviewDate, Rating, Comment FROM reviews WHERE PaintingID = ?';
        try {
            $reviews = $this->db->run($sql, [$paintingID])->fetchAll();
            foreach ($reviews as $key => $row) {
                $this->displaySingleReview($row);
                if (!($key === array_key_last($reviews))) echo '<div class="ui divider"></div>';
            }
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }
    
    # displays a single review; auxiliary to displayReviews()
    public function displaySingleReview($row) {
        echo '<div class="event">';
		echo     '<div class="content">';
		echo	     '<div class="date">' . date('n/j/Y', strtotime($row['ReviewDate'])) . '</div>';
		echo	     '<div class="meta">';
		echo	         '<a class="like">'; 
        for ($i = 0; $i < $row['Rating']; ++$i)
            echo             '<i class="star icon"></i>';	 
        for ($i = $row['Rating']; $i < 5; ++$i)
            echo             '<i class="empty star icon"></i>';   
		echo	         '</a>';
		echo	     '</div>';            
		echo         '<div class="summary">' . $row['Comment'] . '</div>';       
		echo     '</div>';
        echo '</div>';
    }
    
    # displays the average rating for the given painting id, rounded to the nearest integer
    public function displayAverageRating($paintingID) {
        try {
            if ($avg = round($this->db->run('SELECT AVG(Rating) FROM reviews WHERE PaintingID = ?', [$paintingID])->fetch()['AVG(Rating)'])) {
                for ($i = 0; $i < $avg; ++$i) echo '<i class="orange star icon"></i>';
                for ($i = $avg; $i < 5; ++$i) echo '<i class="empty star icon"></i>';
            } else {
                for ($i = 0; $i < 5; ++$i) echo '<i class="empty star icon"></i>';
            }
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }
    
}

?>