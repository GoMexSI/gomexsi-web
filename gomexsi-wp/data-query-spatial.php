<?php
/**
 * Template Name: Data Query - Spatial
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
												<input type="text" class="taxonomic" name="subjectName" placeholder="Any taxonomic level, scientific or common name" value="<?php echo $subjectName; ?>" autocomplete="off" />
											</div>
										</div>
									</label>
								</div>
								
								<div id="form-section-find" class="form-section clearfix">
									<div class="section-label">Find</div>
									<div class="section-input clearfix">
										<div class="clearfix row">
											<label><input type="checkbox" class="master-checkbox" name="allInteractions" /> All Interaction Types</label>
										</div>
										<div class="clearfix row">
											<div class="spacer">
												<label><input type="checkbox" class="switch" name="findPrey" data-switch="filterPrey" /> Prey</label>
											</div>
											<div class="conditional" data-switch="filterPrey">
												<label>
													<span class="visuallyhidden">Limit prey results by name</span>
													<div class="tax-wrapper">
														<input type="text" class="taxonomic filter" name="filterPrey" placeholder="Limit results by name" autocomplete="off" />
													</div>
												</label>
											</div>
										</div>
										<div class="clearfix row">
											<div class="spacer">
												<label><input type="checkbox" class="switch" name="findPredators" data-switch="filterPredators" /> Predators</label>
											</div>
											<div class="conditional" data-switch="filterPredators">
												<label>
													<span class="visuallyhidden">Limit predator results by name</span>
													<div class="tax-wrapper">
														<input type="text" class="taxonomic filter" name="filterPredators" placeholder="Limit results by name" autocomplete="off" />
													</div>
												</label>
											</div>
										</div>
									</div>
								</div>
								
								<div class="form-section clearfix">
									<div class="query-instructions">Resize the box on the map to limit your results.</div>
								</div>
							</div>
							
							<input type="hidden" name="boundNorth" value="" />
							<input type="hidden" name="boundEast" value="" />
							<input type="hidden" name="boundSouth" value="" />
							<input type="hidden" name="boundWest" value="" />
							
							<input type="hidden" name="serviceType" value="rest" />
							<input type="hidden" name="action" value="rhm_data_query" />
							<input type="submit" id="form-submit" class="gradient" value="Submit Query" />
							<div id="status"></div>
						</form>
						
						<div id="query-map-top"></div>
						<div id="query-map"></div>
						
						<div id="query-results-header" class="clearfix">
							<a id="query-results-download" href="#query-results-download-options" class="visuallyhidden">Download the raw data</a>
							<span id="query-results-info"></span>
						</div>
						
						<div id="results-area"></div>
						
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

<div id="hideaway" class="visuallyhidden">
	<div id="map-canvas"></div>
	<div id="query-results-download-options">Raw data download will be available soon.</div>
</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>