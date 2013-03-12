<?php
/**
 * Template Name: Data Query
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
							<label>Species Name:
								<input type="text" id="species" />
							</label>
							<input type="submit" value="Query" /> <span id="status"></span>
						</form>
						
						<hr style="margin: 2em 0;" />
						
						<div style="float: right;"><a href="#" id="clear">Clear Results</a></div>
						
						<h2>Results:</h2>
						<pre id="results" style="min-height: 100px;"></pre>

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