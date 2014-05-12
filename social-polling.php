<?php
/**
 * The WordPress Plugin Boilerplate.
 *
 * A foundation off of which to build well-documented WordPress plugins that
 * also follow WordPress Coding Standards and PHP best practices.
 *
 * @package   Social_Polling
 * @author    Your Name <email@example.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2014 Your Name or Company Name
 *
 * @wordpress-plugin
 * Plugin Name:       @TODO
 * Plugin URI:        @TODO
 * Description:       @TODO
 * Version:           1.0.0
 * Author:            @TODO
 * Author URI:        @TODO
 * Text Domain:       social-polling-locale
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/<owner>/<repo>
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

/*
 * @TODO:
 *
 * - replace `class-social-polling.php` with the name of the plugin's class file
 *
 */
require_once( plugin_dir_path( __FILE__ ) . 'public/class-social-polling.php' );
require_once( plugin_dir_path( __FILE__ ) . 'public/includes/class-sp_comments.php' );
/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 *
 * @TODO:
 *
 * - replace Social_Polling with the name of the class defined in
 *   `class-social-polling.php`
 */
register_activation_hook( __FILE__, array( 'Social_Polling', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Social_Polling', 'deactivate' ) );


//COMEBACKVERB
register_activation_hook( __FILE__, array( 'spComments', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'spComments', 'deactivate' ) );


/*
 * @TODO:
 *
 * - replace Social_Polling with the name of the class defined in
 *   `class-social-polling.php`
 */
add_action( 'plugins_loaded', array( 'Social_Polling', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * @TODO:
 *
 * - replace `class-social-polling-admin.php` with the name of the plugin's admin file
 * - replace Social_Polling_Admin with the name of the class defined in
 *   `class-social-polling-admin.php`
 *
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-social-polling-admin.php' );
	add_action( 'plugins_loaded', array( 'Social_Polling_Admin', 'get_instance' ) );

}
