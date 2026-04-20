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

	/**
	 * Shortcode to render a dynamic staff grid based on category.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string Rendered HTML/Shortcodes.
	 */
	public function render_staff_grid( $atts ) {
		$atts = shortcode_atts( array(
			'category' => '', // Category slug(s), comma separated
		), $atts, 'jpndc_staff_grid' );

		if ( empty( $atts['category'] ) ) {
			return '';
		}

		$categories = array_map('trim', explode(',', $atts['category']));

		$args = array(
			'post_type'      => 'post',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
			'post_status'    => 'publish',
			'tax_query'      => array(
				array(
					'taxonomy' => 'category',
					'field'    => 'slug',
					'terms'    => $categories,
				),
			),
		);

		$query = new WP_Query( $args );
		$output = '';

		if ( $query->have_posts() ) {
			$output .= '[fusion_builder_row_inner equal_height_columns="yes" column_spacing="4%" class="" hide_on_mobile="small-visibility,medium-visibility,large-visibility" id=""]';
			
			while ( $query->have_posts() ) {
				$query->the_post();
				$post_id = get_the_ID();
				$title = get_the_title();
				$permalink = get_permalink();
				
				// Try to get a job title from custom fields or content
				$job_title = get_post_meta( $post_id, 'job_title', true );
				if ( ! $job_title ) {
					$job_title = get_post_meta( $post_id, 'title', true );
				}
				
				$thumb_url = get_the_post_thumbnail_url( $post_id, 'full' );
				if ( ! $thumb_url ) {
					$thumb_url = wp_get_attachment_url( 31836 ); // Use the verified placeholder image ID
					if ( ! $thumb_url ) {
						$thumb_url = 'http://localhost:10009/wp-content/uploads/2024/09/Untitled-design.png'; // Hardcoded fallback
					}
				}

				$output .= sprintf(
					'[fusion_builder_column_inner align_self="stretch" spacing="4%%" type="1_3" layout="1_3" class="staff-card-col" link="%s" center_content="no" hover_type="none" background_color="" background_image="" background_position="left top" background_repeat="no-repeat" border_size="0" border_color="" border_style="solid" padding="" margin_top="" margin_bottom="10px" animation_type="" animation_direction="left" animation_speed="0.3" animation_offset="" id="" min_height=""]<img src="%s" alt="%s"><h3>%s</h3><p>%s</p>[/fusion_builder_column_inner]',
					esc_url( $permalink ),
					esc_url( $thumb_url ),
					esc_attr( $title ),
					esc_html( $title ),
					esc_html( $job_title )
				);
			}
			
			$output .= '[/fusion_builder_row_inner]';
			wp_reset_postdata();
		}

		return $output; // Return raw shortcodes to be processed by parent or do_shortcode
	}

	/**
	 * Shortcode to render the full staff directory by subcategories of JPNDC Staff.
	 *
	 * @return string Rendered HTML.
	 */
	public function render_full_staff_directory() {
		$parent_cat = get_category_by_slug( 'jpndc-staff' );
		if ( ! $parent_cat ) {
			return 'Parent category "JPNDC Staff" not found.';
		}

		$sub_categories = get_categories( array(
			'parent'     => $parent_cat->term_id,
			'hide_empty' => 1,
			'orderby'    => 'name',
			'order'      => 'ASC',
		) );

		$output = '';

		foreach ( $sub_categories as $sub_cat ) {
			$grid = $this->render_staff_grid( array( 'category' => $sub_cat->slug ) );
			if ( empty( $grid ) ) {
				continue;
			}

			$output .= sprintf(
				'[fusion_builder_container class="staff-cat-row" hundred_percent="no" hide_on_mobile="no" background_position="left top" background_repeat="no-repeat" fade="no" background_parallax="none" enable_mobile="no" parallax_speed="0.3" video_aspect_ratio="16:9" video_loop="yes" video_mute="yes" border_size="0px" border_style="solid" padding_top="20" padding_bottom="20"]
				  [fusion_builder_row]
				    [fusion_builder_column type="1_1" layout="1_1" background_position="left top" background_color="" border_size="" border_color="" border_style="solid" background_image="" background_repeat="no-repeat" padding="" margin_top="" margin_bottom="10px" animation_type="" animation_direction="left" animation_speed="0.3" animation_offset="" class="" id="" min_height=""]
				      [fusion_title size="2" content_align="left" style_type="single solid" sep_color="" margin_top="" margin_bottom="10px" class="" id=""]%s[/fusion_title]
				      %s
				    [/fusion_builder_column]
				  [/fusion_builder_row]
				[/fusion_builder_container]',
				esc_html( $sub_cat->name ),
				$grid
			);
		}

		return do_shortcode( $output );
	}

}
