<?php get_header(); ?>

<div id="content" class="gradient">
	<div class="mid-layer gradient">
		<div class="container">
			<?php get_template_part('loop', 'single'); ?>
			<?php get_template_part('copyright'); ?>
		</div>
	</div>
</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>