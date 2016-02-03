<?php /*Template Name: Services Landing Page*/ ?>
<?php get_header();?>

	<section class="landing-services">
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		<?php
$thumb_id = get_post_thumbnail_id();
$thumb_url = wp_get_attachment_image_src($thumb_id,'thumbnail-size', true);
?>
		<div class="landing-banner hidden-sm hidden-xs" style="background-image:url('<?php echo $thumb_url[0];?>');">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<h1 class="entry-title"><?php the_title(); ?></h1> 
					</div>
					<div class="col-md-8 col-md-push-2">
						<?php $f1 = get_post_meta($post->ID, 'blurb', false); ?>
						<?php foreach($f1 as $f1) {
							echo '<p class="blurb text-center">'.$f1.'</p>';
						} ?>
					</div>
				</div><!--//container-->
				</div><!--//row-->
		</div><!--//landing-banner-->
		<div class="container">
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<section class="entry-content">
					<div class="row">
						<div class="left">

						<div class="hidden-md hidden-lg"><h1 class="mobile entry-title"><?php the_title(); ?></h1> 
				
					
						<?php $f1 = get_post_meta($post->ID, 'blurb', false); ?>
						<?php foreach($f1 as $f1) {
							echo '<p class="blurb">'.$f1.'</p><br>';
						} ?>

						</div>
						<?php the_content(); ?>
							
						
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