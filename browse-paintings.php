<?php
# @author Paul Roode

require_once('includes/Artist.class.php');
require_once('includes/Gallery.class.php');
require_once('includes/Painting.class.php');
require_once('includes/Shape.class.php');

# passed into displayPaintingsForBrowsing()
$paintingFilterValues = $_SERVER['REQUEST_METHOD'] == 'GET' 
    ? ['artistFilterValue' => isset($_GET['artist']) && !empty($_GET['artist']) ? $_GET['artist'] : 0,
        'museumFilterValue' => isset($_GET['museum']) && !empty($_GET['museum']) ? $_GET['museum'] : 0,
        'shapeFilterValue' => isset($_GET['shape']) && !empty($_GET['shape']) ? $_GET['shape'] : 0] 
    : [0, 0, 0];
    
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
    
    <main class="ui segment doubling stackable grid container">
        <section class="five wide column">
            <form class="ui form">
                <h4 class="ui dividing header">Filters</h4>
                <div class="field">
                    <label>Artist</label>
                    <select name="artist" class="ui fluid dropdown">
                        <?php echo new Artist(); ?>
                    </select>
                </div>
                <div class="field">
                    <label>Museum</label>
                    <select name="museum" class="ui fluid dropdown">
                        <?php echo new Gallery(); ?>
                    </select>
                </div>   
                <div class="field">
                    <label>Shape</label>
                    <select name="shape" class="ui fluid dropdown">
                        <?php echo new Shape(); ?>
                    </select>
                </div>   
                <button class="small ui orange button" type="submit">
                    <i class="filter icon"></i> Filter 
                </button>    
            </form>
        </section>
        
        <section class="eleven wide column">
            <h1 class="ui header">Paintings</h1>
            <?php (new Painting())->displayPaintingsForBrowsing($paintingFilterValues); ?>          
        </section>  
    </main>    
    
    <?php include 'includes/footer.inc.php'; ?>
</body>
</html>