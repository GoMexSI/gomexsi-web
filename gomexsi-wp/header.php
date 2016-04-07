<!doctype html>
<html <?php language_attributes(); ?>>
	<head>
		<title><?php wp_title('|', true, 'right'); bloginfo('name'); ?></title>
		
		<!-- Meta -->
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta name="description" content="<?php bloginfo('description'); ?>" />
		<meta name="author" content="<?php bloginfo('name'); ?>" />
		<meta name="viewport" content="width=device-width" />
		
		<!-- Style -->
		<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/style.css" />
		<!--[if gte IE 9]> <style type="text/css"> .gradient { filter: none; } </style> <![endif]-->
		
		<!-- Feed -->
		<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> Feed" href="<?php echo home_url(); ?>/feed/" />
		
		<!-- Icons -->
		<link rel="apple-touch-icon" sizes="144x144" href="<?php echo get_stylesheet_directory_uri(); ?>/img/favicon/apple-touch-icon-144x144.png" />
		<link rel="apple-touch-icon" sizes="114x114" href="<?php echo get_stylesheet_directory_uri(); ?>/img/favicon/apple-touch-icon-114x114.png" />
		<link rel="apple-touch-icon" sizes="72x72" href="<?php echo get_stylesheet_directory_uri(); ?>/img/favicon/apple-touch-icon-72x72.png" />
		<link rel="apple-touch-icon" href="<?php echo get_stylesheet_directory_uri(); ?>/img/favicon/apple-touch-icon-57x57.png" />
		<link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri(); ?>/img/favicon/apple-touch-icon-57x57.png" />
		<link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri(); ?>/img/favicon/favicon.ico" />
		
		<!-- Scripts -->
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="<?php echo get_template_directory_uri(); ?>/js/jquery-1.9.1.min.js"><\/script>')</script>
		<!--[if lt IE 9]><script src="<?php echo get_template_directory_uri(); ?>/js/html5-3.6-respond-1.1.0.min.js"></script><![endif]-->
		<!--[if lt IE 8]><script src="<?php echo get_template_directory_uri(); ?>/js/imgsizer.js"></script><![endif]-->
		
		<?php wp_head(); ?>
	</head>
	
	<body <?php body_class($class); ?>>
		<header id="page-header">
			<div class="container">
				<a id="tamucc-logo" href="http://www.tamucc.edu/" target="_blank"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/tamucc-logo.png" /></a>
				<a id="gomexsi-logo" href="<?php echo home_url(); ?>/"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/gomexsi-logo.png" /></a>
				<a id="gomexsi-logo-2" href="<?php echo home_url(); ?>/"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/gomexsi-logo-2.png" /></a>
			</div>
		</header>
		<section id="content-wrapper">
			<div class="container">