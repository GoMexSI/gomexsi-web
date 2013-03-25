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
					<div class="entry-content no-left-margin clearfix">
						<?php the_content(); ?>
					</div>
					<?php if(is_user_logged_in()) : ?>
						<hr style="margin: 1em 0 2em;" />
						
						<h2>Query Form</h2>
						
						<form action="" id="data-query">
							<p>
								<label>Predator Name:
									<input type="text" class="query-var" name="predName" value="Scomberomorus cavalla" />
								</label>
							</p>
							
							<p>
								<label>Prey Name:
									<input type="text" class="query-var" name="preyName" />
								</label>
							</p>
							
							<p>
								<label>Service Type:
									<select class="query-var" name="serviceType">
										<option value="rest">REST</option>
										<option value="mock">Mock</option>
										<option value="">Live</option>
									</select>
								</label>
							</p>
							
							<p>
								<label>Request URL:
									<select class="query-var" name="url">
										<option value="http://gomexsi.tamucc.edu/gomexsi/requestHandler/RequestHandler.php">RequestHandler.php</option>
										<option value="http://gomexsi.tamucc.edu/gomexsi/query-test-return.php">query-test-return.php</option>
									</select>
								</label>
							</p>
							
							<p><input type="submit" value="Query" /> <span id="status"></span></p>
						</form>
						
						<hr style="margin: 2em 0;" />
						
						<div style="float: right;"><a href="#" id="clear">Clear Results</a></div>
						
						<h2>Formatted Results:</h2>
						<pre id="results" style="min-height: 100px;"></pre>

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