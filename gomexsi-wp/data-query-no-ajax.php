<?php
/**
 * Template Name: Data Query No Ajax
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
						
						<form action="http://gomexsi.tamucc.edu/gomexsi/requestHandler/RequestHandler.php" method="post" id="data-query">
<!-- 						<form action="http://gomexsi.tamucc.edu/gomexsi/query-test-return.php" method="post" id="data-query"> -->
							<p>
								<label>Predator Name:
									<input type="text" class="query-var" name="predName" />
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
										<option value="mock">Mock</option>
										<option value="rest">REST</option>
										<option value="">Live</option>
									</select>
								</label>
							</p>
							
							<p><input type="submit" value="Query" /> <span id="status"></span></p>
						</form>
						
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