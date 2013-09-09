<?php while (have_posts()) : the_post(); ?>
	<?php
		$post_ID = get_the_ID();
		$subtitle = get_post_meta($post_ID, 'wpcf-subtitle', true);
		$subtitulo = get_post_meta($post_ID, 'wpcf-subtitulo', true);
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<header class="entry-header">
			<?php if(($subtitle && qtrans_getLanguage() == 'en') || ($subtitulo && qtrans_getLanguage() == 'es')) : ?>
				<h1 class="page-title no-underline"><?php the_title(); ?></h1>
				<div class="subtitle">
					<?php
						if(qtrans_getLanguage() == 'en'){
							echo $subtitle;
						elseif(qtrans_getLanguage() == 'es'){
							echo $subtitulo;
						}
					?>
				</div>
			<?php else : ?>
				<h1 class="page-title"><?php the_title(); ?></h1>
			<?php endif; ?>
			<?php wp_link_pages(array('before' => '<nav id="page-nav"><p>' . 'Pages:', 'after' => '</p></nav>' )); ?>
		</header>
		<div class="entry-content clearfix">
			<?php the_content(); ?>
		</div>
		<?php
			$tags = get_the_tags();
			$cats = get_the_category();
			if($tags || $cats) :
		?>
			<footer class="entry-footer">
				<?php
					if($tags){
						echo '<p class="tags">';
						the_tags();
						echo '</p>';
					}
					
					if($cats){
						echo '<p class="categories">';
						echo (count($cats) > 1 ? 'Categories: ' : 'Category: ');
						the_category(', ');
						echo '</p>';
					}
				?>
			</footer>
		<?php endif; ?>
	</article>
	<?php comments_template(); ?>
<?php endwhile; ?>