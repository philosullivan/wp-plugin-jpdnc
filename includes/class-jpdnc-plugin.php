<?php

/**
 * The file that defines the core plugin class
 *
 * @link       https://jpdnc.org
 * @since      1.0.0
 *
 * @package    Jpdnc_Plugin
 * @subpackage Jpdnc_Plugin/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Jpdnc_Plugin
 * @subpackage Jpdnc_Plugin/includes
 * @author     JPDNC <https://jpdnc.org>
 */
class Jpdnc_Plugin {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Jpdnc_Plugin_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, set the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'JPDNC_PLUGIN_VERSION' ) ) {
			$this->version = JPDNC_PLUGIN_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'jpdnc-plugin';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

		// Add this block here:
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			$this->define_cli_hooks();
		}

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Jpdnc_Plugin_Loader. Orchestrates the hooks of the plugin.
	 * - Jpdnc_Plugin_i18n. Defines internationalization functionality.
	 * - Jpdnc_Plugin_Admin. Defines all hooks for the admin area.
	 * - Jpdnc_Plugin_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the hooks with the core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-jpdnc-plugin-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-jpdnc-plugin-i18n.php';

		/**
		 * The class responsible for defining all hooks that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-jpdnc-plugin-admin.php';

		/**
		 * The class responsible for defining all hooks that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-jpdnc-plugin-public.php';

		$this->loader = new Jpdnc_Plugin_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Jpdnc_Plugin_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Jpdnc_Plugin_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Jpdnc_Plugin_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// Disable comments in admin.
		$this->loader->add_action( 'admin_init', $plugin_admin, 'disable_comments_post_types_support' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'disable_comments_admin_menu' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'disable_comments_admin_menu_redirect' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'disable_comments_admin_bar' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Jpdnc_Plugin_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// custom fix.
		$this->loader->add_action( 'init', $this, 'fix_ninja_forms_loading' );

		// Disable comments in public/frontend/REST.
		$this->loader->add_filter( 'comments_open', $plugin_public, 'filter_comments_closed', 20 );
		$this->loader->add_filter( 'pings_open', $plugin_public, 'filter_comments_closed', 20 );
		$this->loader->add_filter( 'comments_array', $plugin_public, 'filter_empty_comments_array', 10 );
		$this->loader->add_filter( 'rest_endpoints', $plugin_public, 'disable_comments_rest_api' );

		// Show hero on password protected pages.
		$this->loader->add_filter( 'the_content', $plugin_public, 'show_hero_on_password_protected_page', 1 );
		$this->loader->add_filter( 'awb_should_render_page_title_bar', $plugin_public, 'show_page_title_bar_on_password_protected_page', 10, 2 );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the core plugin.
	 *
	 * @since     1.0.0
	 * @return    Jpdnc_Plugin_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

/**
	 * Delays Ninja Forms translation loading.
	 */
	public function fix_ninja_forms_loading() {
		if ( function_exists( 'Ninja_Forms' ) ) {
			load_plugin_textdomain( 'ninja-forms' );
		}
	}

	/**
	 * Registers custom JPNDC commands with WP-CLI.
	 *
	 * @return void
	 */
	public function register_cli_commands() {
		WP_CLI::add_command( 'jpndc fix-forms', [ $this, 'cli_fix_forms' ] );
		WP_CLI::add_command( 'jpndc purge-comments', [ $this, 'cli_purge_comments' ] );
	}

	/**
	 * Manually triggers the Ninja Forms textdomain load via CLI.
	 *
	 * @param array $args       Command arguments.
	 * @param array $assoc_args Command flags.
	 * @return void
	 */
	public function cli_fix_forms( $args, $assoc_args ) {
		if ( function_exists( 'Ninja_Forms' ) ) {
			load_plugin_textdomain( 'ninja-forms' );
			WP_CLI::success( 'Ninja Forms textdomain loaded manually.' );
		} else {
			WP_CLI::error( 'Ninja Forms is not active on this site.' );
		}
	}

	/**
	 * Purges all comments via CLI.
	 *
	 * @param array $args       Command arguments.
	 * @param array $assoc_args Command flags.
	 * @return void
	 */
	public function cli_purge_comments( $args, $assoc_args ) {
		require_once plugin_dir_path( __FILE__ ) . 'class-jpdnc-plugin-activator.php';
		Jpdnc_Plugin_Activator::activate();
		WP_CLI::success( 'All comments purged and comments disabled on all posts.' );
	}

	/**
	 * Register the WP-CLI commands.
	 */
	private function define_cli_hooks() {
		// Register the command directly.
		\WP_CLI::add_command( 'jpndc fix-forms', [ $this, 'cli_fix_forms' ] );
		\WP_CLI::add_command( 'jpndc purge-comments', [ $this, 'cli_purge_comments' ] );
	}
}
