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
								
								<div id="form-section-find" class="form-section clearfix">
									<div class="section-label"><?php _qe('Find', 'Buscar'); ?></div>
									<div class="section-input clearfix">
										<div class="clearfix row">
											<label><input type="checkbox" class="master-checkbox" name="allInteractions" /> <?php _qe('All Interaction Types', 'Todos los tipos de interacción'); ?></label>
										</div>
										<div class="clearfix row">
											<div class="spacer">
												<label><input type="checkbox" class="switch" name="findPrey" data-switch="filterPrey" <?php check_if($_GET['findPrey']); ?> /> <?php _qe('Prey', 'Presa'); ?></label>
											</div>
											<div class="conditional" data-switch="filterPrey">
												<label>
													<span class="visuallyhidden"><?php _qe('Limit results by name', 'Delimitar los resultados por nombre'); ?></span>
													<div class="tax-wrapper">
														<?php $filterPrey = (isset($_GET['filterPrey']) ? $_GET['filterPrey'] : ''); ?>
														<input type="text" class="taxonomic filter" name="filterPrey" placeholder="<?php _qe('Limit results by name', 'Delimitar los resultados por nombre'); ?>" autocomplete="off" value="<?php echo $filterPrey; ?>" />
													</div>
												</label>
											</div>
										</div>
										<div class="clearfix row">
											<div class="spacer">
												<label><input type="checkbox" class="switch" name="findPredators" data-switch="filterPredators" <?php check_if($_GET['findPredators']); ?> /> <?php _qe('Predator', 'Predador'); ?></label>
											</div>
											<div class="conditional" data-switch="filterPredators">
												<label>
													<span class="visuallyhidden"><?php _qe('Limit results by name', 'Delimitar los resultados por nombre'); ?></span>
													<div class="tax-wrapper">
														<?php $filterPredators = (isset($_GET['filterPredators']) ? $_GET['filterPredators'] : ''); ?>
														<input type="text" class="taxonomic filter" name="filterPredators" placeholder="<?php _qe('Limit results by name', 'Delimitar los resultados por nombre'); ?>" autocomplete="off" value="<?php echo $filterPrey; ?>" />
													</div>
												</label>
											</div>
										</div>
									</div>
								</div>
								
								<div class="form-section clearfix">
									<div class="query-instructions"><?php _qe('Resize the yellow box on the map to limit your results.', 'Ajuste el tamaño del recuadro en el mapa para limitar los resultados.'); ?></div>
								</div>
							</div>
							
							<?php $boundNorth = (isset($_GET['boundNorth']) ? $_GET['boundNorth'] : '30'); ?>
							<input type="hidden" name="boundNorth" value="<?php echo $boundNorth; ?>" />
							<?php $boundEast = (isset($_GET['boundEast']) ? $_GET['boundEast'] : '-80'); ?>
							<input type="hidden" name="boundEast" value="<?php echo $boundEast; ?>" />
							<?php $boundSouth = (isset($_GET['boundSouth']) ? $_GET['boundSouth'] : '20'); ?>
							<input type="hidden" name="boundSouth" value="<?php echo $boundSouth; ?>" />
							<?php $boundWest = (isset($_GET['boundWest']) ? $_GET['boundWest'] : '-100'); ?>
							<input type="hidden" name="boundWest" value="<?php echo $boundWest; ?>" />
							
							<input type="hidden" name="serviceType" value="rest" />
							<input type="hidden" name="action" value="rhm_data_query" />
							<input type="submit" id="form-submit" class="gradient" value="<?php _qe('Submit Query', 'Consultar'); ?>" />
							<div id="status"></div>
						</form>
						
						<div id="query-map-top"></div>
						<div id="query-map"></div>
						
						<div id="query-results-header" class="clearfix">
							<a id="query-results-download" href="#" class="visuallyhidden"><?php _qe('Download the raw data', 'Descargar los datos crudos'); ?></a>
							<span id="query-results-info"></span>
							<div id="nametip-instructions" class="visuallyhidden"><?php _qe('Click on species names for links to additional resources.', 'Dar clic en los nombres de las especies para más información.'); ?></div>
						</div>
						
						<div id="results-area"></div>
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