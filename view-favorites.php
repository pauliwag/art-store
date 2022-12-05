<?php
# @author Paul Roode

session_start();

function getFavorites() {
    if (isset($_SESSION['favorites'])) {
        echo '<table class="ui striped very padded collapsing table">';
        echo     '<thead>';
        echo         '<tr>';
        echo             '<th>Image</th>';
        echo             '<th>Title</th>';
        echo             '<th>Action</th>';
        echo         '<tr>';
        echo     '</thead>';
        echo     '<tbody>'; 
        foreach ($_SESSION['favorites'] as $fav) {
            echo     '<tr>';
            echo         '<td>';
            echo             '<a href="single-painting.php?id=' . $fav['id'] . '">'; 
            echo                 '<img src="images/art/works/square-medium/' . $fav['imgFilename'] . '.jpg" height="75px" width="75px" />';
            echo             '</a>';
            echo         '</td>';
            echo         '<td>'; 
            echo             '<a href="single-painting.php?id=' . $fav['id'] . '">' . $fav['title'] . '</a>';
            echo         '</td>';
            echo         '<td>';
            echo             '<a href="remove-favorites.php?id=' . $fav['id'] . '">';
            echo                 '<button class="ui button">Remove</button>';
            echo             '</a>';
            echo         '</td>';
            echo     '</tr>';
        }
        echo     '</tbody>';
        echo     '<tfoot>';
        echo         '<tr>';
        echo             '<th colspan="3">';
        echo                 '<a href="remove-favorites.php">';
        echo                     '<button class="ui labeled icon teal button">';
        echo                         '<i class="trash icon"></i>Remove All Favorites';
        echo                     '</button>';
        echo                 '</a>';
        echo             '</th>';
        echo         '<tr>';
        echo     '</tfoot>';
        echo '</table>';
    } else {
        echo '<p>You don\'t have any favorites yet!</p>';
    }
}

?>
<!DOCTYPE html>
<html lang=en>
<head> 
    <meta charset=utf-8>
    <link href="http://fonts.googleapis.com/css?family=Merriweather" rel="stylesheet" type="text/css">
    <link href="http://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" type="text/css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <script src="css/semantic.js"></script>
    <script src="js/misc.js"></script>

    <link href="css/semantic.css" rel="stylesheet" >
    <link href="css/icon.css" rel="stylesheet" >
    <link href="css/styles.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.inc.php'; ?>
    <main>
        <div class="ui container">
            <p></p>
            <h2><i class="heart icon"></i> Favorites</h2>
            <p></p>
            <?php getFavorites(); ?>
        </div>
    </main>
    <?php include 'includes/footer.inc.php'; ?>
</body>
</html>