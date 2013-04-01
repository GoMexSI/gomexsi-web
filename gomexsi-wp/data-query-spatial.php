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
						<form action="" id="data-query" class="clearfix">
							<div class="query-inputs">
								<div id="form-section-name" class="form-section clearfix">
									<label>
										<div class="section-label">Name</div>
										<div class="section-input clearfix">
											<div class="tax-wrapper">
												<?php $subjectName = (isset($_POST['subjectName']) ? $_POST['subjectName'] : ''); ?>
												<input type="text" class="taxonomic" name="subjectName" placeholder="Any taxonomic level, scientific or common name" value="<?php echo $subjectName; ?>" />
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
														<input type="text" class="taxonomic filter" name="filterPrey" placeholder="Limit results by name" />
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
														<input type="text" class="taxonomic filter" name="filterPredators" placeholder="Limit results by name" />
													</div>
												</label>
											</div>
										</div>
										<div class="clearfix row">
											<div class="spacer">
												<label><input type="checkbox" class="switch" name="findParasites" data-switch="filterParasites" /> Parasites</label>
											</div>
											<div class="conditional" data-switch="filterParasites">
												<label>
													<span class="visuallyhidden">Limit parasites results by name</span>
													<div class="tax-wrapper">
														<input type="text" class="taxonomic filter" name="filterParasites" placeholder="Limit results by name" />
													</div>
												</label>
											</div>
										</div>
										<div class="clearfix row">
											<div class="spacer">
												<label><input type="checkbox" class="switch" name="findMutualists" data-switch="filterMutalists" /> Mutalists</label>
											</div>
											<div class="conditional" data-switch="filterMutalists">
												<label>
													<span class="visuallyhidden">Limit mutalists results by name</span>
													<div class="tax-wrapper">
														<input type="text" class="taxonomic filter" name="filterMutalists" placeholder="Limit results by name" />
													</div>
												</label>
											</div>
										</div>
										<div class="clearfix row">
											<div class="spacer">
												<label><input type="checkbox" class="switch" name="findCommonsals" data-switch="filterCommonsals" /> Commonsals</label>
											</div>
											<div class="conditional" data-switch="filterCommonsals">
												<label>
													<span class="visuallyhidden">Limit commonsals results by name</span>
													<div class="tax-wrapper">
														<input type="text" class="taxonomic filter" name="filterCommonsals" placeholder="Limit results by name" />
													</div>
												</label>
											</div>
										</div>
										<div class="clearfix row">
											<div class="spacer">
												<label><input type="checkbox" class="switch" name="findAmensals" data-switch="filterAmensals" /> Amensals</label>
											</div>
											<div class="conditional" data-switch="filterAmensals">
												<label>
													<span class="visuallyhidden">Limit amensals results by name</span>
													<div class="tax-wrapper">
														<input type="text" class="taxonomic filter" name="filterAmensals" placeholder="Limit results by name" />
													</div>
												</label>
											</div>
										</div>
										<div class="clearfix row">
											<div class="spacer">
												<label><input type="checkbox" class="switch" name="findPrimaryHosts" data-switch="filterPrimaryHosts" /> Primary Hosts</label>
											</div>
											<div class="conditional" data-switch="filterPrimaryHosts">
												<label>
													<span class="visuallyhidden">Limit primary hosts results by name</span>
													<div class="tax-wrapper">
														<input type="text" class="taxonomic filter" name="filterPrimaryHosts" placeholder="Limit results by name" />
													</div>
												</label>
											</div>
										</div>
										<div class="clearfix row">
											<div class="spacer">
												<label><input type="checkbox" class="switch" name="findSecondaryHosts" data-switch="filterSecondaryHosts" /> Secondary Hosts</label>
											</div>
											<div class="conditional" data-switch="filterSecondaryHosts">
												<label>
													<span class="visuallyhidden">Limit secondary hosts results by name</span>
													<div class="tax-wrapper">
														<input type="text" class="taxonomic filter" name="filterSecondaryHosts" placeholder="Limit results by name" />
													</div>
												</label>
											</div>
										</div>
									</div>
								</div>
								
								<div class="form-section clearfix">
									<label>Service Type:
										<select name="serviceType">
											<option value="rest">Live</option>
											<option value="mock">Mock</option>
										</select>
									</label>
									
									<label>Request URL:
										<select name="url">
											<option value="http://gomexsi.tamucc.edu/gomexsi/requestHandler/RequestHandler.php">RequestHandler.php</option>
											<option value="http://gomexsi.tamucc.edu/gomexsi/query-full-mock.php">Full Mock Data</option>
										</select>
									</label>
									
									<br />
									
									Status: <span id="status"></span>
								</div>
								
								<div class="form-section clearfix">
									<div class="query-instructions">Resize the box on the map to limit your results.</div>
								</div>
							</div>
							
							<input type="hidden" name="boundNorth" value="" />
							<input type="hidden" name="boundEast" value="" />
							<input type="hidden" name="boundSouth" value="" />
							<input type="hidden" name="boundWest" value="" />
							
							<input type="hidden" name="action" value="rhm_data_query" />
							
							<input type="submit" id="form-submit" class="gradient" value="Submit Query" />
						</form>
						
						<div id="query-map-top"></div>
						<div id="query-map"></div>
												
						<div id="query-results-header" class="clearfix">
							<!-- <a id="query-results-download" href="#">Download the raw data</a> -->
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
</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>