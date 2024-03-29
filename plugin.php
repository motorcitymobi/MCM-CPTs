<?php
/*
	Plugin Name: MCM CPTs
	Plugin URI: http://motorcitymobility.com/
	Description: MCM CPTs is a plugin which adds a CPTs custom post type for client projects.
	Author: Motor City Mobility
	Author URI: http://motorcitymobility.com/

	Version: 1.2.0

	License: GNU General Public License v2.0 (or later)
	License URI: http://www.opensource.org/licenses/gpl-license.php
*/

register_activation_hook( __FILE__, 'mcm_cpts_activation' );
/**
 * This function runs on plugin activation. It checks to make sure the required
 * minimum Genesis version is installed. If not, it deactivates itself.
 *
 * @since 0.1.0
 */
function mcm_cpts_activation() {

		$latest = '2.0.2';

		if ( 'genesis' != get_option( 'template' ) ) {

			//* Deactivate ourself
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( sprintf( __( 'Sorry, you can\'t activate unless you have installed <a href="%s">Genesis</a>', 'mcm-cpts' ), 'http://my.studiopress.com/themes/genesis/' ) );

		}

		if ( version_compare( wp_get_theme( 'genesis' )->get( 'Version' ), $latest, '<' ) ) {

			//* Deactivate ourself
			deactivate_plugins( plugin_basename( __FILE__ ) ); /** Deactivate ourself */
			wp_die( sprintf( __( 'Sorry, you cannot activate without <a href="%s">Genesis %s</a> or greater', 'mcm-cpts' ), 'http://www.studiopress.com/support/showthread.php?t=19576', $latest ) );

		}
		
		/** Flush rewrite rules */
		if ( ! post_type_exists( 'cpt' ) ) {

			mcm_cpts_init();
			global $_mcm_cpts, $_mcm_taxonomies;
			$_mcm_cpts->create_post_type();
			$_mcm_taxonomies->register_taxonomies();

		}

		flush_rewrite_rules();

}

add_action( 'after_setup_theme', 'mcm_cpts_init' );
/**
 * Initialize MCM CPTs.
 *
 * Include the libraries, define global variables, instantiate the classes.
 *
 * @since 0.1.0
 */
function mcm_cpts_init() {
	
	/** Do nothing if a Genesis child theme isn't active */
	if ( ! function_exists( 'genesis_get_option' ) )
		return;

	global $_mcm_cpts, $_mcm_taxonomies;

	define( 'APL_URL', plugin_dir_url( __FILE__ ) );
	define( 'APL_VERSION', '1.0.0' );

	/** Load textdomain for translation */
	load_plugin_textdomain( 'mcm-cpts', false, basename( dirname( __FILE__ ) ) . '/languages/' );

	/** Includes */
	require_once( dirname( __FILE__ ) . '/includes/functions.php' );
	require_once( dirname( __FILE__ ) . '/includes/class-cpts.php' );
	require_once( dirname( __FILE__ ) . '/includes/class-taxonomies.php' );
	require_once( dirname( __FILE__ ) . '/includes/class-featured-cpts-widget.php' );
	require_once( dirname( __FILE__ ) . '/includes/class-custompost-search-widget.php' );

	/** Instantiate */
	$_mcm_cpts = new MCM_CPTs;
	$_mcm_taxonomies = new MCM_Taxonomies;

	add_action( 'widgets_init', 'mcm_register_widgets' );

}

/**
 * Register Widgets that will be used in the MCM CPTs plugin
 *
 * @since 0.1.0
 */
function mcm_register_widgets() {

	$widgets = array( 'MCM_Featured_CPTs_Widget', 'MCM_CPTs_Search_Widget' );

	foreach ( (array) $widgets as $widget ) {
		register_widget( $widget );
	}

}

