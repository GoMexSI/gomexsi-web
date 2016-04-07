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
						<p><strong><?php _qe('Note: Some features are not yet finalized. If you run into any issues, <a href="/feedback/">please give us feedback</a>.', 'Nota: algunas aplicaciones están aun en construcción. Si se te presenta algún problema, <a href="/feedback/">por favor notifícanos</a>.'); ?></strong></p>
						<form action="" method="post" id="data-query" class="clearfix">
							<div class="query-inputs">
								<div id="form-section-name" class="form-section clearfix">
									<label>
										<div class="section-label"><?php _qe('Name', 'Nombre'); ?></div>
										<div class="section-input clearfix">
											<div class="tax-wrapper">
												<?php $subjectName = (isset($_GET['subjectName']) ? $_GET['subjectName'] : ''); ?>
												<input type="text" class="taxonomic" name="subjectName" placeholder="<?php _qe('Any taxonomic level, scientific or common name', 'Cualquier nivel taxonómico, nombre científico o común'); ?>" value="<?php echo $subjectName; ?>" autocomplete="off" />
											</div>
										</div>
									</label>
								</div>
								
								<div class="form-section clearfix">
									<div class="query-instructions"><?php _qe('Search for any species name to load a food web. Then click on the "Explore This" link below any name in the web to center the web on that species. (Right-click or control-click to open in a new window.)', 'Para cargar la cadena alimentaria realice la búsqueda para cualquier especie. A continuación, haga clic en cualquier nombre en la cadena para explorar.'); ?></div>
								</div>
							</div>
							
							<input type="hidden" name="findPrey" value="on" />
							<input type="hidden" name="findPredators" value="on" />
							
							<input type="hidden" name="serviceType" value="rest" />
							<input type="hidden" name="action" value="rhm_data_query" />
							<input type="submit" id="form-submit" class="gradient" value="<?php _qe('Submit Query', 'Consultar'); ?>" />
							<div id="status"></div>
						</form>
						
						<hr />
						
						<div id="ex-area"></div>
						
					<?php else : ?>
						<hr style="margin: 1em 0 2em;" />

						<h3><?php _qe('You must be logged in to query data.', 'Debes ingresar para consultar las bases de datos.'); ?></h3>

						<p><?php _qe('Existing users can <a href="#" class="login-link">log in</a> to continue. New users can <a href="/registration/" class="registration-link">register now</a>&mdash;it\'s fast, easy, and free!', 'Usarios registrados pueden <a href="#" class="login-link">ingresar</a> para continuar. Nuevos usarios pueden <a href="/registration/" class="registration-link">registrarse ahora</a> &ndash; es rápido, fácil, y gratis.'); ?></p>
					<?php endif; ?>
				</article>
			<?php endwhile; ?>
			<?php get_template_part('copyright'); ?>
		</div>
	</div>
</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>