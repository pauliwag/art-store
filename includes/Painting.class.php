<?php
# @author Paul Roode

require_once('memcache-config.inc.php');
require_once('ArtDB.class.php');
require_once('Artist.class.php');
require_once('Gallery.class.php');
require_once('Shape.class.php');

define('BROWSE_LIMIT', 20);
define('DISPLAYED_PAINTINGS_MEMCACHE_EXPIRATION', 30); # seconds

class Painting {
    
    protected $db;
    protected $data;
    
    public function __construct() {
        $this->db = ArtDB::getInstance();
    }
    
    # returns the paintings record with the given painting id
    public function find($id) {
        try {
            $this->data = $this->db->run('SELECT * FROM paintings WHERE PaintingID = ?', [$id])->fetch();
        } catch (PDOException $e) {
            die($e->getMessage());
        }
        return $this->data;
    }
    
    # displays paintings and their info for browsing, as per filters; memcaches query results
    public function displayPaintingsForBrowsing($filterValues) {
        $memc = new Memcache();
        $memc->addServer(MEMC_HOST, MEMC_PORT) or die('Could not connect to memcache server');
        $filterValuesKey = implode(',', $filterValues);
        $paintings = $memc->get($filterValuesKey);
        
        # don't echo memcached paintings yet, as we should first display the filter details for the user
        $sql = 'SELECT artists.ArtistID, Excerpt, FirstName, GalleryID, ImageFileName, LastName, MSRP, PaintingID, ShapeID, Title 
            FROM artists INNER JOIN paintings ON artists.ArtistID = paintings.ArtistID ';
        if ($isFilterSelected = !empty(array_filter($filterValues))) {
            extract($filterValues);
            $isFilterInDB = [
                'artist' => (new Artist())->find($artistFilterValue),
                'museum' => (new Gallery())->find($museumFilterValue),
                'shape' => (new Shape())->find($shapeFilterValue)
            ];
            if (!empty(array_filter($isFilterInDB))) {
                $filteredBy = '<strong>FILTERED BY';
                if ($isFilterInDB['artist']) $filteredBy .= ' ARTIST="' . (new Artist())->getName($artistFilterValue) . '"';
                if ($isFilterInDB['museum']) $filteredBy .= ' MUSEUM="' . (new Gallery())->getName($museumFilterValue) . '"';
                if ($isFilterInDB['shape']) $filteredBy .= ' SHAPE="' . (new Shape())->getName($shapeFilterValue) . '"';
                if ($paintings) {
                    echo $filteredBy . ' [PULLED FROM MEMCACHE]</strong>';
                    echo $paintings;
                    return;
                }
                echo $filteredBy . '</strong>';
                $sql .= 'WHERE artists.ArtistID = :artistFilterValue OR GalleryID = :museumFilterValue OR ShapeID = :shapeFilterValue 
                    ORDER BY Title LIMIT ' . BROWSE_LIMIT;
            } else {
                echo '<strong>NO PAINTINGS MATCH YOUR FILTER CRITERIA</strong>';
                return;
            }
        } else {
            $showing = '<strong>ALL PAINTINGS [TOP ' . BROWSE_LIMIT; 
            if ($paintings) {
                echo $showing . ' PULLED FROM MEMCACHE]</strong>';
                echo $paintings;
                return;
            }
            echo $showing . ']</strong>';
            $sql .= 'LIMIT ' . BROWSE_LIMIT;
        }
        
        # query results weren't memcached, so fetch a new result set and store it in memcache
        $paintings = '<ul class="ui divided items" id="paintingsList">';
        try {
            $statement = $isFilterSelected ? $this->db->run($sql, $filterValues) : $this->db->run($sql);
            foreach ($statement->fetchAll() as $row) {
                $paintings .= $this->getSinglePaintingForBrowsing($row);
            }
        } catch (PDOException $e) {
            die($e->getMessage());
        } 
        $paintings .= '</ul>';
        $memc->set($filterValuesKey, $paintings, false, DISPLAYED_PAINTINGS_MEMCACHE_EXPIRATION);
        echo $paintings;
    }
    
    # returns a single painting and its info for browsing; auxiliary to displayPaintingsForBrowsing()
    protected function getSinglePaintingForBrowsing($row) {
        $painting =  '<li class="item">';
        $painting .=     '<a class="ui small image" href="single-painting.php?id=' . $row['PaintingID'] . '">';
        $painting .=         '<img src="images/art/works/square-medium/' . $row['ImageFileName'] . '.jpg" alt="' . $row['Title'] . '">';
        $painting .=     '</a>';
        $painting .=     '<div class="content">';
        $painting .=         '<a class="header" href="single-painting.php?id=' . $row['PaintingID'] . '">' . $row['Title'] . '</a>';
        $painting .=         '<div class="meta"><span class="cinema">' . $row['FirstName'] . ' ' . $row['LastName'] . '</span></div>';   
        $painting .=         '<div class="description">';
        $painting .=             '<p>' . $row['Excerpt'] . '</p>';
        $painting .=         '</div>';
        $painting .=         '<div class="meta">'; 
        $painting .=             '<strong>$' . number_format($row['MSRP']) . '</strong>';  
        $painting .=         '</div>';        
        $painting .=         '<div class="extra">';
        $painting .=             '<a class="ui icon orange button" href="#"><i class="add to cart icon"></i></a>';
        $painting .=             '<a class="ui icon button" href="addToFavorites.php?id=' . $row['PaintingID'] . '&imgFilename=' 
                                     . $row['ImageFileName'] . '&title=' . $row['Title'] . '"><i class="heart icon"></i></a>';          
        $painting .=         '</div>';        
        $painting .=     '</div>';      
        $painting .= '</li>';
        return $painting;
    }
    
    # displays paintings having the same artist or shape of the given painting id, in random order
    function displayRelatedWorks($id) {
        $painting = $this->find($id);
        echo '<div class="ui segment">'; 
        echo     '<h3 class="ui dividing header">Related Works</h3>';
        echo     '<div class="ui six doubling cards">'; 
        foreach ($this->db->run('SELECT * FROM paintings WHERE (ArtistID = ? OR ShapeID = ?) AND PaintingID <> ? ORDER BY RAND() LIMIT ' . BROWSE_LIMIT, 
                                [$painting['ArtistID'], $painting['ShapeID'], $id])->fetchAll() as $row) {
            echo     '<div class="ui fluid card">';
            echo         '<div class="ui fluid image">';
            echo             '<a href="single-painting.php?id=' . $row['PaintingID'] . '">';
            echo                 '<img src="images/art/works/square-medium/' . $row['ImageFileName'] . '.jpg">';
            echo             '</a>';
            echo         '</div>';
            echo         '<div class="extra">';
            echo             '<h4>';
            echo                 '<a href="single-painting.php?id=' . $row['PaintingID'] . '">';
            echo                     $row['Title'];
            echo                 '</a>';
            echo             '</h4>';
            echo         '</div>'; # end class=extra
            echo     '</div>'; # end class=card
        }
        echo     '</div>';
        echo '</div>';
    }
    
    # returns a random paintings record
    public function getRandom() {
        try {
            return $this->db->run('SELECT * FROM paintings ORDER BY RAND() LIMIT 1')->fetch();
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }
    
    # displays a table of web info for the painting with the given painting id
    public function displayWebInfo($id) {
        $painting = $this->find($id);
        if ($painting['WikiLink'] || $painting['GoogleLink'] || $painting['GoogleDescription']) {
            echo '<table class="ui definition very basic collapsing celled table">';
            echo     '<tbody>';
            echo         '<tr>';
            echo             '<td>Wikipedia Link</td>';
            echo             '<td>';
            if ($painting['WikiLink'])
                echo             '<a href="' . $painting['WikiLink'] . '">View painting on Wikipedia</a>';
            echo             '</td>';
            echo         '</tr>';
            echo         '<tr>';
            echo             '<td>Google Link</td>';
            echo             '<td>';
            if ($painting['GoogleLink'])
                echo             '<a href="' . $painting['GoogleLink'] . '">View painting on Google Art Project</a>';
            echo             '</td>';
            echo         '</tr>';
            echo         '<tr>';
            echo             '<td>Google Text</td>';
            echo             '<td>';
            if ($painting['GoogleDescription'])
                echo             '<p>' . $painting['GoogleDescription'] . '</p>';
            echo             '</td>';
            echo         '</tr>';
            echo     '</tbody>';
            echo '</table>';
        }
    }
    
}

?>