<?php
# @author Paul Roode

require_once('includes/Painting.class.php');

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'GET' 
    && isset($_GET['id']) && !empty($_GET['id']) 
    && isset($_GET['imgFilename']) && !empty($_GET['imgFilename']) 
    && isset($_GET['title']) && !empty($_GET['title'])) {
    
    # verify the database contains a painting with the given id
    if ($paintingToVerify = (new Painting())->find($_GET['id'])) {
        
        # verify query param validity
        if ($paintingToVerify['PaintingID'] === $_GET['id']
            && $paintingToVerify['ImageFileName'] === $_GET['imgFilename']
            && $paintingToVerify['Title'] === $_GET['title']) {
            
            if (!isset($_SESSION['favorites'])) {
                $_SESSION['favorites'] = [];
                $_SESSION['numFavs'] = 0;
            }
            if (!array_key_exists($_GET['id'], $_SESSION['favorites'])) {
                $_SESSION['favorites'][$_GET['id']] = [
                    'id' => $_GET['id'],
                    'imgFilename' => $_GET['imgFilename'],
                    'title' => $_GET['title']                    
                ];
                ++$_SESSION['numFavs'];
            }
            header('Location: view-favorites.php');
        }
    }
} else {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
}

?>