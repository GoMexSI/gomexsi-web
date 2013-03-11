<?php get_header(); ?>

<div id="content" class="gradient">
	<div class="mid-layer gradient">
		<div class="container">
			<h1 class="page-title">
				<?php
					switch(true){
						// "Fall through" time, day, month, and year conditions.
						case is_time():
						case is_day():
						case is_month():
						case is_year():
							echo 'Archives: ' . wp_title('', false);
							break;
						case is_author():
							echo 'Post by: ' . wp_title('', false);
							break;
						case is_category():
							echo 'Category: ' . wp_title('', false);
							break;
						case is_tag();
							echo 'Tag: ' . wp_title('', false);
							break;
						default:
							wp_title('', true);
							break;
					}
				?>
			</h1>
			<?php get_template_part('loop'); ?>
			<?php get_template_part('copyright'); ?>
		</div>
	</div>
</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>