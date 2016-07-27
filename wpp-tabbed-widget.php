<?php
/*
Plugin Name: Wordpress Popular Posts - Tabbed Widget
Description: Adds a widget with three tabs able to display day/week/month/all time of popular posts.
Plugin URI:  http://www.radgh.com/
Version:     1.1.0
Author:      Radley Sustaire
Author URI:  mailto:radleygh@gmail.com
License:     GPL2
*/

/*
GNU GENERAL PUBLIC LICENSE

Adds a widget with three tabs able to display day/week/month/all time of popular posts.
Copyright (C) 2015 Radley Sustaire

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>
*/

if( !defined( 'ABSPATH' ) ) exit;

define( 'WPPTW_URL', untrailingslashit(plugin_dir_url( __FILE__ )) );
define( 'WPPTW_PATH', dirname(__FILE__) );
define( 'WPPTW_VERSION', '1.0.2' );

add_action( 'plugins_loaded', 'wpptw_initialize', 15 );


function wpptw_initialize() {
	if ( !class_exists('WordpressPopularPosts') ) {
		add_action( 'admin_notices', 'wpptw_dependency_warning' );
		include( WPPTW_PATH . '/widgets/wpp-disabled-placeholder.php' );
	}else{
		include( WPPTW_PATH . '/widgets/wpp-tabbed.php' );
		add_action( 'wp_enqueue_scripts', 'wpptw_enqueue_scripts' );
	}

	add_action( 'widgets_init', 'wpptw_register_widget' );
}


function wpptw_dependency_warning() {
	?>
	<div class="error">
		<p><strong>Wordpress Popular Posts - Tabbed Widget: Warning</strong></p>
		<p>The plugin <a href="https://wordpress.org/plugins/wordpress-popular-posts/" target="_blank" rel="external">WordPress Popular Posts</a> is not installed or inactive. The tabbed widget will not appear until this dependency is activated.</p>
	</div>
	<?php
}


function wpptw_register_widget() {
	register_widget( 'wppTabbedWidget' );
}


function wpptw_enqueue_scripts() {
	wp_enqueue_style( 'wpptw', WPPTW_URL . '/assets/wpptw.css', array(), WPPTW_VERSION );
	wp_enqueue_script( 'wpptw', WPPTW_URL . '/assets/wpptw.js', array('jquery'), WPPTW_VERSION );
}