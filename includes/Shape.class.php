<?php
# @author Paul Roode

require_once('memcache-config.inc.php');
require_once('ArtDB.class.php');

define('SHAPE_OPTIONS_MEMCACHE_EXPIRATION', 30); # seconds

class Shape {
    
    protected $db;
    protected $data;
    
    public function __construct() {
        $this->db = ArtDB::getInstance();
    }
    
    # returns the shapes record with the given shape id
    public function find($id) {
        try {
            $this->data = $this->db->run('SELECT * FROM shapes WHERE ShapeID = ?', [$id])->fetch();
        } catch (PDOException $e) {
            die($e->getMessage());
        }
        return $this->data;
    }
    
    protected function getAll() {
        $memc = new Memcache();
        $memc->addServer(MEMC_HOST, MEMC_PORT) or die('Could not connect to memcache server');
        if ($shapes = $memc->get('shapes')) {
            echo '<option value="0">Select Shape (from Memcache)</option>';
            return $shapes;
        }
        $sql = 'SELECT ShapeID, ShapeName FROM shapes ORDER BY ShapeName';
        echo '<option value="0">Select Shape</option>';
        $shapes = '';
        try {
            $statement = $this->db->run($sql);
            foreach ($statement->fetchAll() as $row) {
                $shapes .= '<option value="' . $row['ShapeID'] . '">' . $row['ShapeName'] . '</option>';
            }
        } catch (PDOException $e) {
            die($e->getMessage());
        }
        $memc->set('shapes', $shapes, false, SHAPE_OPTIONS_MEMCACHE_EXPIRATION);
        return $shapes;
    }
    
    public function __toString() {
        return $this->getAll();
    }
    
    # returns the name of the shape with the given shape id
    public function getName($id) {
        return $this->find($id)['ShapeName'];
    }
    
}

?>