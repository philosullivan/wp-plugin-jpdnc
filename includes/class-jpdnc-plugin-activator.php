<?php

/**
 * Fired during plugin activation
 *
 * @link       https://jpdnc.org
 * @since      1.0.0
 *
 * @package    Jpdnc_Plugin
 * @subpackage Jpdnc_Plugin/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Jpdnc_Plugin
 * @subpackage Jpdnc_Plugin/includes
 * @author     JPDNC <https://jpdnc.org>
 */
class Jpdnc_Plugin_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;

		// 1. Disable comments and pings on future posts.
		update_option( 'default_comment_status', 'closed' );
		update_option( 'default_ping_status', 'closed' );

		// 2. Close comments on all existing posts.
		$wpdb->query( "UPDATE {$wpdb->posts} SET comment_status = 'closed', ping_status = 'closed'" );

		// 3. Delete all existing comments and comment meta.
		$wpdb->query( "DELETE FROM {$wpdb->comments}" );
		$wpdb->query( "DELETE FROM {$wpdb->commentmeta}" );

		// 4. Reset comment counts on all posts.
		$wpdb->query( "UPDATE {$wpdb->posts} SET comment_count = 0" );
	}

}
