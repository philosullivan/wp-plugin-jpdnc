<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://jpdnc.org
 * @since      1.0.0
 *
 * @package    Jpdnc_Plugin
 * @subpackage Jpdnc_Plugin/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Jpdnc_Plugin
 * @subpackage Jpdnc_Plugin/public
 * @author     JPDNC <https://jpdnc.org>
 */
class Jpdnc_Plugin_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string    $plugin_name       The name of this plugin.
	 * @param    string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Jpdnc_Plugin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Jpdnc_Plugin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/jpdnc-plugin-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Jpdnc_Plugin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Jpdnc_Plugin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/jpdnc-plugin-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Filter to close comments.
	 *
	 * @since    1.0.0
	 * @return   boolean   False to close comments.
	 */
	public function filter_comments_closed() {
		return false;
	}

	/**
	 * Filter to return an empty array of comments.
	 *
	 * @since    1.0.0
	 * @param    array     $comments   Existing comments.
	 * @return   array     Empty array.
	 */
	public function filter_empty_comments_array( $comments ) {
		return array();
	}

	/**
	 * Disable the comments REST API endpoint.
	 *
	 * @since    1.0.0
	 * @param    array     $endpoints  REST API endpoints.
	 * @return   array     Filtered endpoints.
	 */
	public function disable_comments_rest_api( $endpoints ) {
		if ( isset( $endpoints['/wp/v2/comments'] ) ) {
			unset( $endpoints['/wp/v2/comments'] );
		}
		if ( isset( $endpoints['/wp/v2/comments/(?P<id>[\d]+)'] ) ) {
			unset( $endpoints['/wp/v2/comments/(?P<id>[\d]+)'] );
		}
		return $endpoints;
	}

	/**
	 * Show the hero section even if the post is password protected.
	 *
	 * @since    1.0.0
	 * @param    string    $content    The post content.
	 * @return   string    Filtered content.
	 */
	public function show_hero_on_password_protected_page( $content ) {
		if ( is_singular() && post_password_required() ) {
			$post = get_post();
			if ( strpos( $post->post_content, 'fusion_builder_container' ) !== false ) {
				// Extract the hero section from the original content
				$original_content = $post->post_content;

				// Try to find the container with admin_label="Hero Section"
				if ( preg_match( '/\[fusion_builder_container admin_label="Hero Section".*?\[\/fusion_builder_container\]/s', $original_content, $matches ) ) {
					$hero = $matches[0];
					// Render the hero shortcode
					$rendered_hero = do_shortcode( $hero );
					// Prepend it to the password form (which is what $content currently is)
					return $rendered_hero . $content;
				}
			}
		}
		return $content;
	}

	/**
	 * Ensure page title bar is visible on password protected pages.
	 *
	 * @since    1.0.0
	 * @param    bool      $render     Whether to render or not.
	 * @param    int       $post_id    The post ID.
	 * @return   bool      Filtered render value.
	 */
	public function show_page_title_bar_on_password_protected_page( $render, $post_id ) {
		if ( post_password_required( $post_id ) ) {
			return true;
		}
		return $render;
	}

}
