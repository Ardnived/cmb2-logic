<?php
/**
 * @wordpress-plugin
 * Plugin Name:       CMB2 Field Logic
 * Plugin URI:        https://github.com/Ardnived/cmb2-logic
 * Description:       A simple plugin which adds display logic for fields created by CMB2
 * Version:           1.0
 * Author:            Devindra Payment
 * Text Domain:       cmb2l
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/Ardnived/cmb2-logic
 */

class CMB2_Logic {
	public static $directory_path = '';
	public static $directory_url = '';
	
	public static function init() {
		self::$directory_path = plugin_dir_path( __FILE__ );
		self::$directory_url = plugin_dir_url( __FILE__ );
		
		add_action( 'admin_notices', array( __CLASS__, 'check_requirements' ) );
		add_action( 'plugins_loaded', array( __CLASS__, 'load' ), 11 );
	}

	/**
	 * Load the plugin, if we meet requirements.
	 * @filter plugins_loaded
	 */
	public static function load() {
		if ( self::meets_requirements() ) {
			require_once( self::$directory_path . '/includes/class-cmb2l-main.php' );
		}
	}

	/**
	 * Generate a custom error message and deactivates the plugin if we don't meet requirements
	 * @filter admin_notices
	 */
	public static function check_requirements() {
		if ( ! self::meets_requirements() ) {
			?>
			<div id="message" class="error">
				<p>
					<?php printf( __( 'CMB2 Field Logic requires CMB2 to run, and has thus been <a href="%s">deactivated</a>. Please install and activate CMB2 and then reactivate this plugin.', 'ubcreg' ), admin_url( 'plugins.php' ) ); ?>
				</p>
			</div>
			<?php

			// Deactivate our plugin
			deactivate_plugins( plugin_basename( __FILE__ ) );
		}
	}

	/**
	 * Checks if the required plugin is installed.
	 */
	public static function meets_requirements() {
		return true; //defined( 'CMB2_VERSION' );
	}
}

CMB2_Logic::init();
