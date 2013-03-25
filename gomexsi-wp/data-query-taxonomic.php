<?php
/**
 * Template Name: Data Query - Taxonomic
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
							<div class="comment">The subject about which the results are centered. I.e., Scomberomorus cavalla</div>
							<div id="form-section-name" class="form-section clearfix">
								<label>
									<div class="section-label">Name</div>
									<div class="section-input clearfix">
										<div class="tax-wrapper">
											<input type="text" class="query-var taxonomic" name="subjectName" placeholder="Any taxonomic level, scientific or common name" />
										</div>
									</div>
								</label>
							</div>
							
							<div class="comment">The types of interactions to look for. One search may return both prey and predators for the subject. When a box is checked, another field appears that allows a keyword filter for that interaction.</div>
							<div id="form-section-find" class="form-section clearfix">
								<div class="section-label">Find</div>
								<div class="section-input clearfix">
									<div class="clearfix row">
										<div class="spacer">
											<label><input type="checkbox" class="switch" name="findRel" data-switch="filterPrey" value="filterPreyCheck" /> Prey</label>
										</div>
										<div class="conditional" data-switch="filterPrey">
											<label>
												<span class="visuallyhidden">Limit prey results by name</span>
												<div class="tax-wrapper">
													<input type="text" class="query-var taxonomic filter" name="filterPrey" placeholder="Limit results by name" />
												</div>
											</label>
										</div>
									</div>
									<div class="clearfix row">
										<div class="spacer">
											<label><input type="checkbox" class="switch" name="findRel" data-switch="filterPredators" /> Predators</label>
										</div>
										<div class="conditional" data-switch="filterPredators">
											<label>
												<span class="visuallyhidden">Limit predator results by name</span>
												<div class="tax-wrapper">
													<input type="text" class="query-var taxonomic filter" name="filterPredators" placeholder="Limit results by name" />
												</div>
											</label>
										</div>
									</div>
									<div class="clearfix row">
										<div class="spacer">
											<label><input type="checkbox" class="switch" name="findRel" data-switch="filterParasites" /> Parasites</label>
										</div>
										<div class="conditional" data-switch="filterParasites">
											<label>
												<span class="visuallyhidden">Limit parasites results by name</span>
												<div class="tax-wrapper">
													<input type="text" class="query-var taxonomic filter" name="filterParasites" placeholder="Limit results by name" />
												</div>
											</label>
										</div>
									</div>
									<div class="clearfix row">
										<div class="spacer">
											<label><input type="checkbox" class="switch" name="findRel" data-switch="filterMutalists" /> Mutalists</label>
										</div>
										<div class="conditional" data-switch="filterMutalists">
											<label>
												<span class="visuallyhidden">Limit mutalists results by name</span>
												<div class="tax-wrapper">
													<input type="text" class="query-var taxonomic filter" name="filterMutalists" placeholder="Limit results by name" />
												</div>
											</label>
										</div>
									</div>
									<div class="clearfix row">
										<div class="spacer">
											<label><input type="checkbox" class="switch" name="findRel" data-switch="filterCommonsals" /> Commonsals</label>
										</div>
										<div class="conditional" data-switch="filterCommonsals">
											<label>
												<span class="visuallyhidden">Limit commonsals results by name</span>
												<div class="tax-wrapper">
													<input type="text" class="query-var taxonomic filter" name="filterCommonsals" placeholder="Limit results by name" />
												</div>
											</label>
										</div>
									</div>
									<div class="clearfix row">
										<div class="spacer">
											<label><input type="checkbox" class="switch" name="findRel" data-switch="filterAmensals" /> Amensals</label>
										</div>
										<div class="conditional" data-switch="filterAmensals">
											<label>
												<span class="visuallyhidden">Limit amensals results by name</span>
												<div class="tax-wrapper">
													<input type="text" class="query-var taxonomic filter" name="filterAmensals" placeholder="Limit results by name" />
												</div>
											</label>
										</div>
									</div>
									<div class="clearfix row">
										<div class="spacer">
											<label><input type="checkbox" class="switch" name="findRel" data-switch="filterPrimaryHosts" /> Primary Hosts</label>
										</div>
										<div class="conditional" data-switch="filterPrimaryHosts">
											<label>
												<span class="visuallyhidden">Limit primary hosts results by name</span>
												<div class="tax-wrapper">
													<input type="text" class="query-var taxonomic filter" name="filterPrimaryHosts" placeholder="Limit results by name" />
												</div>
											</label>
										</div>
									</div>
									<div class="clearfix row">
										<div class="spacer">
											<label><input type="checkbox" class="switch" name="findRel" data-switch="filterSecondaryHosts" /> Secondary Hosts</label>
										</div>
										<div class="conditional" data-switch="filterSecondaryHosts">
											<label>
												<span class="visuallyhidden">Limit secondary hosts results by name</span>
												<div class="tax-wrapper">
													<input type="text" class="query-var taxonomic filter" name="filterSecondaryHosts" placeholder="Limit results by name" />
												</div>
											</label>
										</div>
									</div>
								</div>
							</div>
							
							<div class="comment">Filter by various location parameters. (Additive? Subtractive?)</div>
							<div id="form-section-location" class="form-section clearfix">
								<div class="section-label">Location</div>
								<div class="section-input clearfix">
									<label><input type="checkbox" class="switch" name="byRegion" data-switch="byRegion" /> Search by region</label>
									<div class="conditional" data-switch="byRegion">
										<label><div class="spacer">Octant</div>
											<select class="query-var" name="octant">
												<option value=""></option>
												<option value="nne">North Northeast</option>
												<option value="ene">East Northeast</option>
												<option value="ese">East Southeast</option>
												<option value="sse">South Southeast</option>
												<option value="ssw">South Southwest</option>
												<option value="wsw">West Southwest</option>
												<option value="wnw">West Northwest</option>
												<option value="nnw">North Northwest</option>
											</select>
										</label>
										
										<label><div class="spacer">Major locale</div>
											<select class="query-var" name="majorLocale">
												<option value=""></option>
												<option value="locale1">Locale 1</option>
												<option value="locale2">Locale 2</option>
												<option value="locale3">Locale 3</option>
												<option value="locale4">Locale 4</option>
											</select>
										</label>
										
										<label><div class="spacer">Locale type</div>
											<select class="query-var" name="localeType">
												<option value=""></option>
												<option value="type1">Type 1</option>
												<option value="type2">Type 2</option>
												<option value="type3">Type 3</option>
												<option value="type4">Type 4</option>
												<option value="type5">Type 5</option>
												<option value="type6">Type 6</option>
												<option value="type7">Type 7</option>
												<option value="type8">Type 8</option>
												<option value="type9">Type 9</option>
												<option value="type10">Type 10</option>
												<option value="type11">Type 11</option>
												<option value="type12">Type 12</option>
											</select>
										</label>
										
										<label><div class="spacer">Major bay name</div>
											<select class="query-var" name="majorBayName">
												<option value=""></option>
												<option value="bay1">Bay 1</option>
												<option value="bay2">Bay 2</option>
												<option value="bay3">Bay 3</option>
												<option value="bay4">Bay 4</option>
											</select>
										</label>
										
										<label><div class="spacer">Major riverine name</div>
											<select class="query-var" name="majorRiverineName">
												<option value=""></option>
												<option value="river1">River 1</option>
												<option value="river2">River 2</option>
												<option value="river3">River 3</option>
												<option value="river4">River 4</option>
											</select>
										</label>
									</div>
								</div>
							</div>
							
							<div class="comment">Development testing options. The submitted query object (POST data) is logged to your browser's console. If you set Request URL to query-test-return.php, the POST array will show under Raw Results.</div>
							<div class="form-section clearfix">
								<label>Service Type:
									<select class="query-var" name="serviceType">
										<option value="rest">REST</option>
										<option value="mock">Mock</option>
										<option value="">Live</option>
									</select>
								</label>
								
								<label>Request URL:
									<select class="query-var" name="url">
										<option value="http://gomexsi.tamucc.edu/gomexsi/requestHandler/RequestHandler.php">RequestHandler.php</option>
										<option value="http://gomexsi.tamucc.edu/gomexsi/query-full-mock.php">query-full-mock.php</option>
										<option value="http://gomexsi.tamucc.edu/gomexsi/query-test-return.php">query-test-return.php</option>
									</select>
								</label>
								
								<br />
								
								Status: <span id="status"></span>
							</div>
						
							<input type="submit" id="form-submit" class="gradient" value="Submit Query" />
						</form>
						
						<hr style="margin: 2em 0;" />
						
						<div style="float: right;"><a href="#" id="clear">Clear Results</a></div>
						
						<div id="query-results"></div>

						<hr style="margin: 2em 0;" />
						
						<h2>Raw Results:</h2>
						<pre id="raw-results" style="min-height: 100px;"></pre>

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