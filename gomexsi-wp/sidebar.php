<div id="sidebar" class="gradient">
	<div class="container gradient">
		<div id="login-box" class="gradient">
			<?php if(is_user_logged_in()) : ?>
				<?php
					global $current_user;
					get_currentuserinfo();
				?>
				<p class="welcome"><?php _qe('Hi', 'Hola'); ?>, <?php echo $current_user->display_name; ?>.</p>
				<p class="user-links"><a href="<?php echo get_edit_user_link(); ?>"><?php _qe('My Account', 'Mi Cuenta'); ?></a> | <a href="<?php echo wp_logout_url( get_permalink() ); ?>"><?php _qe('Logout', 'Salir'); ?></a></p>
			<?php else : ?>
				<p class="welcome"><?php _qe('Welcome!', '¡Bienvenido!'); ?></p>
				<p class="user-links"><a href="<?php echo wp_login_url( get_permalink() ); ?>" id="login-link" title="<?php _qe('Login', 'Ingresar'); ?>"><?php _qe('Login', 'Ingresar'); ?></a> | <a href="/registration/" id="registration-link"><?php _qe('Register', 'Registrar'); ?></a></p>
				<p class="for-db-access"><?php _qe('For Database Access', 'Para Acceder a las<br />Bases de Datos'); ?></p>
				<?php wp_login_form(); ?>
<?php /*?>
				<form action="" method="post" id="registrationform">
					<p class="registration-username">
						<label for="user_login">Choose a Username</label>
						<input type="text" name="user_login" id="user_login" class="input" />
					</p>
					<p class="registration-email">
						<label for="user_email">Your Email Address</label>
						<input type="text" name="user_email" id="user_email" class="input" />
					</p>
					<input type="hidden" name="redirect_to" value="<?php echo get_permalink(); ?>" />
					<p id="reg_passmail">Password will be emailed.</p>
					<?php do_action('register_form'); ?>
					<input type="submit" value="Register" id="register" /> 
				</form>
<?php */?>
			<?php endif; ?>
		</div>
		<nav id="nav-main">
			<h3><?php _qe('Menu', 'Menú'); ?></h3>
			<?php wp_nav_menu(array('theme_location' => 'primary_navigation', 'container_class' => 'main-menu')); ?>
		</nav>
		<aside id="sidebar-widgets" role="complementary">
			<div class="container">
				<?php dynamic_sidebar('Sidebar'); ?>
			</div>
		</aside>
	</div>
</div>