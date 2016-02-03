<?php /*Template Name: Contact*/ ?>
<?php get_header();?>

	<section class="landing-services">
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		<?php
$thumb_id = get_post_thumbnail_id();
$thumb_url = wp_get_attachment_image_src($thumb_id,'thumbnail-size', true);
?>
	
		<div class="container">
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<section class="entry-content">
					<div class="row">
						<div class="left">

				
					
					
						
						<?php the_content(); ?>
						<span class="site-title">CMS Information Technology Pty.Ltd</span><br>
			
						<ul class="list-unstyled">
						 <li>Unit 23<br> 110 Bourke Road<br> Alexandria NSW 2015</li>
						 <li><i class="fa fa-phone"></i> 1300 1 26748 (CMSIT)</li>
						</ul>
			<br>
						<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false"></script><div style="overflow:hidden;height:280px;width:100%;"><div id="gmap_canvas" style="height:280px;width:100%;"></div><style>#gmap_canvas img{max-width:none!important;background:none!important}</style><a class="google-map-code" href="http://www.themecircle.net" id="get-map-data">wordpress themes</a></div><script type="text/javascript"> function init_map(){var myOptions = {zoom:19,center:new google.maps.LatLng(-33.9127104,151.1938434),mapTypeId: google.maps.MapTypeId.ROADMAP};map = new google.maps.Map(document.getElementById("gmap_canvas"), myOptions);marker = new google.maps.Marker({map: map,position: new google.maps.LatLng(-33.9127104, 151.1938434)});infowindow = new google.maps.InfoWindow({content:"<b>CMSIT Pty.Ltd</b><br/>Unit 23 110 Bourke Road  Alexandria NSW 2015<br/> Sydney" });google.maps.event.addListener(marker, "click", function(){infowindow.open(map,marker);});infowindow.open(map,marker);}google.maps.event.addDomListener(window, 'load', init_map);</script>

							
						
						</div><!--//left-->
						<div class="right">
							
							<div class="lead-form">
							
								 <h3>Contact us</h3>
							 
							      <?php echo do_shortcode('[contact-form-7 id="49" title="Contact form 1"]');?>
							       
							 
							</div><!--//lead-form-->
						</div><!--//left-->
					</div><!--//row-->
				
				<div class="entry-links"><?php wp_link_pages(); ?></div>
				</section>
			</article>
		</div><!--//container-->
		<?php endwhile; endif; ?>

		<section class="other-services">
			<div class="container">
			<hr>
				<h3>Other services offered</h3>
				<div class="row">


<?php
$currentID = get_the_ID();
$my_query = new WP_Query( array('cat' => '2', 'showposts' => '7', 'post__not_in' => array($currentID)));
while ( $my_query->have_posts() ) : $my_query->the_post(); ?>




	<div class="oso-item">
						<div class="box">
								<a href="<?php the_permalink();?>"><span> <?php the_title(); ?></span></a>
						</div><!--//box-->
					</div><!--//oso-item-->

<?php endwhile; ?>
			
				
				</div><!--//row-->
			</div><!--//container-->
		</section>

	</section>

<?php get_footer();?>