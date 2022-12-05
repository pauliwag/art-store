<?php
# @author Paul Roode

require_once('includes/Artist.class.php');
require_once('includes/Frame.class.php');
require_once('includes/Gallery.class.php');
require_once('includes/Genre.class.php');
require_once('includes/Glass.class.php');
require_once('includes/Matt.class.php');
require_once('includes/Painting.class.php');
require_once('includes/Review.class.php');
require_once('includes/Subject.class.php');

# if id query param is absent, noninteger, or not in the database, then display a random painting
$painting = $_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id']) && !empty($_GET['id']) 
    && is_numeric($_GET['id']) && (new Painting())->find($_GET['id'])
    ? (new Painting())->find($_GET['id']) 
    : (new Painting())->getRandom();

$artist = (new Artist())->find($painting['ArtistID']);
$gallery = (new Gallery())->find($painting['GalleryID']);
    
?>
<!DOCTYPE html>
<html lang=en>
<head>
<meta charset=utf-8>
    <link href='http://fonts.googleapis.com/css?family=Merriweather' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
    
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
    <!-- Main section about painting -->
    <section class="ui segment grey100">
        <div class="ui doubling stackable grid container">
		
            <div class="nine wide column">
              <img src="images/art/works/medium/<?php echo $painting['ImageFileName']; ?>.jpg" alt="<?php echo $painting['Title']; ?>" class="ui big image" id="artwork">
                
                <div class="ui fullscreen modal">
                  <div class="image content">
                      <img src="images/art/works/large/<?php echo $painting['ImageFileName']; ?>.jpg" alt="<?php echo $painting['Title']; ?>" class="image">
                      <div class="description">
                      <p></p>
                    </div>
                  </div>
                </div>                
                
            </div>	<!-- END LEFT Picture Column --> 
			
            <div class="seven wide column">
                
                <!-- Main Info -->
                <div class="item">
					<h2 class="header"><?php echo $painting['Title']; ?></h2>
					<h3><?php echo $artist['FirstName'] . ' ' . $artist['LastName']; ?></h3>
					<div class="meta">
						<p>
						  <?php (new Review())->displayAverageRating($painting['PaintingID']); ?>
						</p>
						<p><?php echo $painting['Excerpt']; ?></p>
					</div>  
                </div>                          
                  
                <!-- Tabs For Details, Museum, Genre, Subjects -->
                <div class="ui top attached tabular menu ">
                    <a class="active item" data-tab="details"><i class="image icon"></i>Details</a>
                    <a class="item" data-tab="museum"><i class="university icon"></i>Museum</a>
                    <a class="item" data-tab="genres"><i class="theme icon"></i>Genres</a>
                    <a class="item" data-tab="subjects"><i class="cube icon"></i>Subjects</a>    
                </div>
                
                <div class="ui bottom attached active tab segment" data-tab="details">
                    <table class="ui definition very basic collapsing celled table">
					  <tbody>
						  <tr>
						 <td>
							  Artist
						  </td>
						  <td>
							<a href="browse-paintings.php?artist=<?php echo $painting['ArtistID']; ?>"><?php echo $artist['FirstName'] . ' ' . $artist['LastName']; ?></a>
						  </td>                       
						  </tr>
						<tr>                       
						  <td>
							  Year
						  </td>
						  <td>
							<?php echo $painting['YearOfWork']; ?>
						  </td>
						</tr>       
						<tr>
						  <td>
							  Medium
						  </td>
						  <td>
							<?php echo $painting['Medium']; ?>
						  </td>
						</tr>  
						<tr>
						  <td>
							  Dimensions
						  </td>
						  <td>
							<?php echo $painting['Width'] . 'cm x ' . $painting['Height'] . 'cm'; ?>
						  </td>
						</tr>        
					  </tbody>
					</table>
                </div>
				
                <div class="ui bottom attached tab segment" data-tab="museum">
                    <table class="ui definition very basic collapsing celled table">
                      <tbody>
                        <tr>
                          <td>
                              Museum
                          </td>
                          <td>
                            <?php echo $gallery['GalleryName']; ?>
                          </td>
                        </tr>       
                        <tr>
                          <td>
                              Accession #
                          </td>
                          <td>
                            <?php echo $painting['AccessionNumber']; ?>
                          </td>
                        </tr>  
                        <tr>
                          <td>
                              Copyright
                          </td>
                          <td>
                            <?php echo $painting['CopyrightText']; ?>
                          </td>
                        </tr>       
                        <tr>
                          <td>
                              URL
                          </td>
                          <td>
                            <?php if ($painting['MuseumLink']) echo '<a href="' . $painting['MuseumLink'] . '">View painting at museum site</a>'; ?>
                          </td>
                        </tr>        
                      </tbody>
                    </table>    
                </div>     
                <div class="ui bottom attached tab segment" data-tab="genres">
 
                        <ul class="ui list">
                          <?php (new Genre())->displayGenres($painting['PaintingID']); ?>
                        </ul>

                </div>  
                <div class="ui bottom attached tab segment" data-tab="subjects">
                    <ul class="ui list">
                        <?php (new Subject())->displaySubjects($painting['PaintingID']); ?>
                    </ul>
                </div>  
                
                <!-- Cart and Price -->
                <div class="ui segment">
                    <div class="ui form">
                        <div class="ui tiny statistic">
                          <div class="value">
                            <?php echo '$' . number_format($painting['MSRP']); ?>
                          </div>
                        </div>
                        <div class="four fields">
                            <div class="three wide field">
                                <label>Quantity</label>
                                <input type="number" min="0" value="1">
                            </div>                               
                            <div class="four wide field">
                                <label>Frame</label>
                                <select id="frame" class="ui search dropdown">
                                    <?php echo new Frame(); ?>
                                </select>
                            </div>  
                            <div class="four wide field">
                                <label>Glass</label>
                                <select id="glass" class="ui search dropdown">
                                    <?php echo new Glass(); ?>
                                </select>
                            </div>  
                            <div class="four wide field">
                                <label>Matt</label>
                                <select id="matt" class="ui search dropdown">
                                    <?php echo new Matt(); ?>
                                </select>
                            </div>           
                        </div>                     
                    </div>

                    <div class="ui divider"></div>

                    <button class="ui labeled icon orange button">
                      <i class="add to cart icon"></i>
                      Add to Cart
                    </button>
                    <a href="addToFavorites.php?id=<?php echo $painting['PaintingID'] . '&imgFilename=' . $painting['ImageFileName'] . '&title=' . $painting['Title'] ?>">
                        <button class="ui right labeled icon button">
                            <i class="heart icon"></i>
                            Add to Favorites
                        </button>
                    </a>
                </div>     <!-- END Cart -->                      
                          
            </div>	<!-- END RIGHT data Column --> 
        </div>		<!-- END Grid --> 
    </section>		<!-- END Main Section --> 
    
    <!-- Tabs for Description, On the Web, Reviews -->
    <section class="ui doubling stackable grid container">
        <div class="sixteen wide column">
        
            <div class="ui top attached tabular menu ">
              <a class="active item" data-tab="first">Description</a>
              <a class="item" data-tab="second">On the Web</a>
              <a class="item" data-tab="third">Reviews</a>
            </div>
			
            <div class="ui bottom attached active tab segment" data-tab="first">
              <?php echo $painting['Description']; ?>
            </div>	<!-- END DescriptionTab --> 
			
            <div class="ui bottom attached tab segment" data-tab="second">
				<?php (new Painting())->displayWebInfo($painting['PaintingID']); ?>
            </div>   <!-- END On the Web Tab --> 
			
            <div class="ui bottom attached tab segment" data-tab="third">                
				<div class="ui feed">    
                  <?php (new Review())->displayReviews($painting['PaintingID']); ?>	
				</div>                                
            </div>   <!-- END Reviews Tab -->
            <p></p>
        
        </div>        
    </section> <!-- END Description, On the Web, Reviews Tabs --> 
    
    <!-- Related Images -->    
    <section class="ui container"> 
        <?php (new Painting())->displayRelatedWorks($painting['PaintingID']); ?>
	</section>  	
</main>    
    
    <?php include 'includes/footer.inc.php'; ?>
</body>
</html>