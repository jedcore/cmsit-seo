<?php get_header();?>

<div class="container">
	<div class="row">
		<section class="blog-list">

			<section id="content" role="main">
			<header class="header">
			<h2 class="entry-title"><?php the_title();?></h2>
			</header>
			<hr>
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
				<div class="row">
					<div class="single-wp-thumb-holder">
						<?php
$thumb_id = get_post_thumbnail_id();
$thumb_url = wp_get_attachment_image_src($thumb_id,'thumbnail-size', true);
?><center><img src="<?php echo $thumb_url[0];?>" class="thumbnail img-responsive"></center>
					</div><!--//wp-thumb-holder-->
					<div class="single-wp-post-content">
						<p><?php the_content();?></p>
					</div><!--//wp-post-content-->
				</div><!--//row-->
				
				

			<?php endwhile; endif; ?>
	
			</section>

		</section>
		<section class="sidebar">
			<?php get_sidebar(); ?>


			<div class="other-services-sidebar">
			<span class="widget-title">Our Services</span>

			<?php
			$currentID = get_the_ID();
			$my_query = new WP_Query( array('cat' => '2', 'showposts' => '8', 'post__not_in' => array($currentID)));
			while ( $my_query->have_posts() ) : $my_query->the_post(); ?>
			<div class="sidebar-list">
			<ul class="list-group">
			  <li class="list-group-item"><a href="<?php the_permalink();?>"><span> <?php the_title(); ?></span></a></li>

			</ul>
			</div>

			<?php endwhile; ?>
			</div>
			<hr>
			<span class="widget-title">CMSIT Pty.Ltd</span><br>
			
			<ul class="list-unstyled">
			 <li>Unit 23 110 Bourke Road<br> Alexandria NSW 2015 Sydney</li>
			 <li>Call us: 1300 1 26748 (CMSIT)</li>
			</ul>
			<br>
					<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false"></script><div style="overflow:hidden;height:300px;width:100%;"><div id="gmap_canvas" style="height:300px;width:100%;"></div><style>#gmap_canvas img{max-width:none!important;background:none!important}</style><a class="google-map-code" href="http://www.themecircle.net" id="get-map-data">wordpress themes</a></div><script type="text/javascript"> function init_map(){var myOptions = {zoom:19,center:new google.maps.LatLng(-33.9127104,151.1938434),mapTypeId: google.maps.MapTypeId.ROADMAP};map = new google.maps.Map(document.getElementById("gmap_canvas"), myOptions);marker = new google.maps.Marker({map: map,position: new google.maps.LatLng(-33.9127104, 151.1938434)});infowindow = new google.maps.InfoWindow({content:"<b>CMSIT Pty.Ltd</b><br/>Unit 23 110 Bourke Road  Alexandria NSW 2015<br/> Sydney" });google.maps.event.addListener(marker, "click", function(){infowindow.open(map,marker);});infowindow.open(map,marker);}google.maps.event.addDomListener(window, 'load', init_map);</script>
				
		</section>
	</div><!--//row-->
	
</div><!--//container-->




<?php get_footer();?>