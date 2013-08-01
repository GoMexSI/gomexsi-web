<?php
/**
 * Template Name: Data Query - Exploration
 */
?>

<?php get_header(); ?>

<div id="content" class="gradient">
	<div class="mid-layer gradient">
		<div class="container">
			<?php while (have_posts()) : the_post(); ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<header class="entry-header">
						<h1 class="page-title"><?php the_title(); ?></h1>
						<?php wp_link_pages(array('before' => '<nav id="page-nav"><p>' . 'Pages:', 'after' => '</p></nav>' )); ?>
					</header>
					<?php if(is_user_logged_in()) : ?>
						<p><strong>Note: Some features are not yet finalized. If you run into any issues, <a href="/feedback/">please give us feedback</a>.</strong></p>
						<form action="" id="data-query" class="clearfix">
							<div class="query-inputs">
								<div id="form-section-name" class="form-section clearfix">
									<label>
										<div class="section-label">Name</div>
										<div class="section-input clearfix">
											<div class="tax-wrapper">
												<?php $subjectName = (isset($_POST['subjectName']) ? $_POST['subjectName'] : ''); ?>
												<input type="text" class="taxonomic" name="subjectName" placeholder="Please enter a scientific name" value="<?php echo $subjectName; ?>" autocomplete="off" />
											</div>
										</div>
									</label>
								</div>
								
								<div class="form-section clearfix">
									<div class="query-instructions">Search for any species name to load a food web. Then click on any name in the web to explore.</div>
								</div>
							</div>
							
							<input type="hidden" name="findPrey" value="on" />
							<input type="hidden" name="findPredators" value="on" />
							
							<input type="hidden" name="serviceType" value="rest" />
							<input type="hidden" name="action" value="rhm_data_query" />
							<input type="submit" id="form-submit" class="gradient" value="Submit Query" />
							<div id="status"></div>
						</form>
						
						<hr />
						
						<div id="ex-area"></div>
						
					<?php else : ?>
						<hr style="margin: 1em 0 2em;" />

						<h3>You must be logged in to query data.</h3>

						<p>Existing users can <a href="#" class="login-link">log in</a> to continue. New users can <a href="#" class="registration-link">register now</a>&mdash;it's fast, easy, and free!</p>
					<?php endif; ?>
				</article>
			<?php endwhile; ?>
			<?php get_template_part('copyright'); ?>
		</div>
	</div>
</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>