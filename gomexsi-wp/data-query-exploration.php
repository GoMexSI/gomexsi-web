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
								
								<div class="form-section clearfix">
									<label>Service Type:
										<select name="serviceType">
											<option value="rest">REST</option>
											<option value="mock">Mock</option>
											<option value="">Live</option>
										</select>
									</label>
									
									<label>Request URL:
										<select name="url">
											<option value="http://gomexsi.tamucc.edu/gomexsi/query-full-mock.php">query-full-mock.php</option>
											<option value="http://gomexsi.tamucc.edu/gomexsi/requestHandler/RequestHandler.php">RequestHandler.php</option>
											<option value="http://gomexsi.tamucc.edu/gomexsi/query-test-return.php">query-test-return.php</option>
										</select>
									</label>
									
									<br />
									
									Status: <span id="status"></span>
								</div>
								
								<div class="form-section clearfix">
									<div class="query-instructions">Search for any species name to load a food web. Then click on any name in the web to explore.</div>
								</div>
							</div>
							
							<input type="hidden" name="findPrey" value="on" />
							<input type="hidden" name="findPredators" value="on" />
							
							<input type="hidden" name="action" value="rhm_data_query" />
							<input type="submit" id="form-submit" class="gradient" value="Submit Query" />
						</form>
						
						<hr />
						
						<div id="ex-area"></div>
						
						<pre><h4>Raw Results:</h4></pre>
						<pre id="raw-results"></pre>

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