<?php
# @author Paul Roode

require_once('memcache-config.inc.php');
require_once('ArtDB.class.php');

define('MUSEUM_OPTIONS_MEMCACHE_EXPIRATION', 30); # seconds

class Gallery {
    
    protected $db;
    protected $data;
    
    public function __construct() {
        $this->db = ArtDB::getInstance();
    }
    
    # returns the galleries record with the given gallery id
    public function find($id) {
        try {
            $this->data = $this->db->run('SELECT * FROM galleries WHERE GalleryID = ?', [$id])->fetch();
        } catch (PDOException $e) {
            die($e->getMessage());
        }
        return $this->data;
    }
    
    protected function getAll() {
        $memc = new Memcache();
        $memc->addServer(MEMC_HOST, MEMC_PORT) or die('Could not connect to memcache server');
        if ($museums = $memc->get('museums')) {
            echo '<option value="0">Select Museum (from Memcache)</option>';
            return $museums;
        }
        $sql = 'SELECT GalleryID, GalleryName FROM galleries ORDER BY GalleryName';
        echo '<option value="0">Select Museum</option>';
        $museums = '';
        try {
            $statement = $this->db->run($sql);
            foreach ($statement->fetchAll() as $row) {
                $museums .= '<option value="' . $row['GalleryID'] . '">' . $row['GalleryName'] . '</option>';
            }
        } catch (PDOException $e) {
            die($e->getMessage());
        }
        $memc->set('museums', $museums, false, MUSEUM_OPTIONS_MEMCACHE_EXPIRATION);
        return $museums;
    }
    
    public function __toString() {
        return $this->getAll();
    }
    
    # returns the name of the gallery with the given gallery id
    public function getName($id) {
        return $this->find($id)['GalleryName'];
    }
    
}

?>