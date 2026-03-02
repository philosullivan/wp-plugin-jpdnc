<?php

/**
 * Register all actions and filters for the plugin
 *
 * @link       https://jpdnc.org
 * @since      1.0.0
 *
 * @package    Jpdnc_Plugin
 * @subpackage Jpdnc_Plugin/includes
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    Jpdnc_Plugin
 * @subpackage Jpdnc_Plugin/includes
 * @author     JPDNC <https://jpdnc.org>
 */
/**
 * Register all actions and filters for the plugin.
 */
/**
 * Register all actions and filters for the plugin.
 */
class Jpdnc_Plugin_Loader {

	/**
	 * The array of actions registered with WordPress.
	 *
	 * @var array $actions The actions registered with WordPress to run when the loader is run.
	 */
	protected $actions = [];

	/**
	 * The array of filters registered with WordPress.
	 *
	 * @var array $filters The filters registered with WordPress to run when the loader is run.
	 */
	protected $filters = [];

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 */
	public function __construct() {
		// Collections initialized as empty arrays via protected properties.
	}

	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @access public
	 */
	public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @access public
	 */
	public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * A utility function that is used to register the actions and filters into a single collection.
	 *
	 * @access public
	 */
	public function add( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {
		$hooks[] = [
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		];

		return $hooks;
	}

	/**
	 * Execute the registration of all of the hooks with WordPress.
	 *
	 * @access public
	 */
	public function run() {
		foreach ( $this->filters as $hook ) {
			add_filter( $hook['hook'], [ $hook['component'], $hook['callback'] ], $hook['priority'], $hook['accepted_args'] );
		}

		foreach ( $this->actions as $hook ) {
			add_action( $hook['hook'], [ $hook['component'], $hook['callback'] ], $hook['priority'], $hook['accepted_args'] );
		}
	}
}
