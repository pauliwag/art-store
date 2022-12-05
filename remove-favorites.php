<?php
# @author Paul Roode

session_start();

function unsetFavorites() {
    unset($_SESSION['favorites']);
    unset($_SESSION['numFavs']);
}

if (isset($_SESSION['favorites'])) {
    if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
        if (array_key_exists($_GET['id'], $_SESSION['favorites'])) {
            unset($_SESSION['favorites'][$_GET['id']]);
            if (!--$_SESSION['numFavs']) {
                unsetFavorites();
            }
        }
    } else {
        unsetFavorites();
    } 
}

header('Location: view-favorites.php');
        
?>