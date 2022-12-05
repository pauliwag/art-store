<?php
# @author Paul Roode

require_once('memcache-config.inc.php');
require_once('ArtDB.class.php');

define('ARTIST_OPTIONS_MEMCACHE_EXPIRATION', 30); # seconds

class Artist {
    
    protected $db;
    protected $data;
    
    public function __construct() {
        $this->db = ArtDB::getInstance();
    }
    
    # returns the artists record with the given artist id
    public function find($id) {
        try {
            $this->data = $this->db->run('SELECT * FROM artists WHERE ArtistID = ?', [$id])->fetch();
        } catch (PDOException $e) {
            die($e->getMessage());
        }
        return $this->data;
    }
    
    protected function getAll() {
        $memc = new Memcache();
        $memc->addServer(MEMC_HOST, MEMC_PORT) or die('Could not connect to memcache server');
        if ($artists = $memc->get('artists')) {
            echo '<option value="0">Select Artist (from Memcache)</option>';
            return $artists;
        }
        $sql = 'SELECT ArtistID, FirstName, LastName FROM artists ORDER BY LastName';
        echo '<option value="0">Select Artist</option>';
        $artists = '';
        try {
            $statement = $this->db->run($sql);
            foreach ($statement->fetchAll() as $row) {
                $artists .= '<option value="' . $row['ArtistID'] . '">' . $row['FirstName'] . ' ' . $row['LastName'] . '</option>';
            }
        } catch (PDOException $e) {
            die($e->getMessage());
        }
        $memc->set('artists', $artists, false, ARTIST_OPTIONS_MEMCACHE_EXPIRATION);
        return $artists;
    }
    
    public function __toString() {
        return $this->getAll();
    }
    
    # returns the full name of the artist with the given artist id
    public function getName($id) {
        $artist = $this->find($id);
        return $artist['FirstName'] . ' ' . $artist['LastName'];
    }
    
}

?>