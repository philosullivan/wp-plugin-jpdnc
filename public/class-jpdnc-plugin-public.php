<?php

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
			'category'   => '', // Category slug(s), comma separated
			'show_title' => 'yes', // Whether to show the staff title/role
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
				$content = get_post_field( 'post_content', $post_id );
				
				$job_title = '';
				if ( 'yes' === $atts['show_title'] ) {
					// Try to get a job title from custom fields or content
					$job_title = get_post_meta( $post_id, 'job_title', true );
					if ( ! $job_title ) {
						$job_title = get_post_meta( $post_id, 'title', true );
					}
					
					// Fallback to SEO title if it contains something specific
					if ( ! $job_title ) {
						$seo_title = get_post_meta( $post_id, '_aioseop_title', true );
						if ( $seo_title && strpos( $seo_title, 'Meet' ) === false ) {
							$job_title = $seo_title;
						}
					}

					// If still empty, try to parse it from the content
					if ( ! $job_title ) {
						// Remove [fusion_title] shortcodes as they can contain the name and confuse the parser
						$content_clean = preg_replace( '/\[fusion_title.*?\](.*?)\[\/fusion_title\]/is', '', $content );
						
						// 1. Try "Title:" or "Position:" or "Board position:" labels
						if ( preg_match( '/(?:Title|Position|Board position)[:\s]+(.*?)(?:<\/p>|<br|\[\/fusion_text\])/is', $content_clean, $matches ) ) {
							$job_title = trim( strip_tags( $matches[1] ) );
						} 
						// 2. Try the first <em> or <strong> tag if it's very early in the content
						elseif ( preg_match( '/(?:<p>|\[fusion_text\])\s*(?:<em>|<strong>)(.*?)(?:<\/em>|<\/strong>)/is', substr($content_clean, 0, 300), $matches ) ) {
							$job_title = trim( strip_tags( $matches[1] ) );
						}
						// 3. Fallback: Take the first line of text if it's short (under 70 chars)
						else {
							$first_line = trim( strip_tags( $content_clean ) );
							$first_line = strtok( $first_line, "\n\r" );
							if ( strlen( $first_line ) > 0 && strlen( $first_line ) < 70 ) {
								$job_title = $first_line;
							}
						}
					}
				}
				
				$thumb_url = get_the_post_thumbnail_url( $post_id, 'full' );
				if ( ! $thumb_url ) {
					$thumb_url = wp_get_attachment_url( 31836 ); // Use the verified placeholder image ID
					if ( ! $thumb_url ) {
						$thumb_url = 'http://localhost:10009/wp-content/uploads/2024/09/Untitled-design.png'; // Hardcoded fallback
					}
				}

				$title_html = ! empty( $job_title ) ? sprintf( '<p class="staff-title">%s</p>', esc_html( $job_title ) ) : '';

				$output .= sprintf(
					'[fusion_builder_column_inner align_self="stretch" spacing="4%%" type="1_3" layout="1_3" class="staff-card-col" link="%s" center_content="no" hover_type="none" background_color="" background_image="" background_position="left top" background_repeat="no-repeat" border_size="0" border_color="" border_style="solid" padding="" margin_top="" margin_bottom="10px" animation_type="" animation_direction="left" animation_speed="0.3" animation_offset="" id="" min_height=""]<img src="%s" alt="%s"><h3>%s</h3>%s[/fusion_builder_column_inner]',
					esc_url( $permalink ),
					esc_url( $thumb_url ),
					esc_attr( $title ),
					esc_html( $title ),
					$title_html
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
		return $this->render_directory_by_parent( 'jpndc-staff', 'yes' );
	}

	/**
	 * Shortcode to render the full people directory by subcategories of JPNDC Clients.
	 *
	 * @return string Rendered HTML.
	 */
	public function render_people_directory() {
		return $this->render_directory_by_parent( 'jpndc-clients', 'no' );
	}

	/**
	 * Helper to render a directory grouped by subcategories of a parent.
	 *
	 * @param string $parent_slug Slug of the parent category.
	 * @param string $show_title  Whether to show staff titles.
	 * @return string Rendered HTML.
	 */
	public function render_directory_by_parent( $parent_slug, $show_title ) {
		$parent = get_term_by( 'slug', $parent_slug, 'category' );
		if ( ! $parent ) {
			return sprintf( 'Category "%s" not found.', esc_html( $parent_slug ) );
		}

		$subcats = get_terms( array(
			'taxonomy' => 'category',
			'parent'   => $parent->term_id,
			'orderby'  => 'description', // Use description field for ordering if numeric
			'order'    => 'ASC',
			'hide_empty' => true,
		) );

		if ( is_wp_error( $subcats ) || empty( $subcats ) ) {
			// If no subcategories, just render the parent category itself
			$grid = $this->render_staff_grid( array( 'category' => $parent_slug, 'show_title' => $show_title ) );
			return do_shortcode( $grid );
		}

		$output = '';
		foreach ( $subcats as $cat ) {
			$grid = $this->render_staff_grid( array( 'category' => $cat->slug, 'show_title' => $show_title ) );
			if ( empty( $grid ) ) continue;

			$output .= sprintf(
				'[fusion_builder_container type="flex" hundred_percent="no" equal_height_columns="no" menu_anchor="" hide_on_mobile="small-visibility,medium-visibility,large-visibility" class="staff-cat-row" id="" background_color="" background_image="" background_position="center center" background_repeat="no-repeat" fade="no" background_parallax="none" parallax_speed="0.3" video_mp4="" video_webm="" video_ogv="" video_url="" video_aspect_ratio="16:9" video_loop="yes" video_mute="yes" overlay_color="" video_preview_image="" border_color="" border_style="solid" padding_top="40px" padding_bottom="20px" padding_left="" padding_right=""][fusion_builder_row][fusion_builder_column type="1_1" layout="1_1" background_position="left top" background_color="" border_color="" border_style="solid" border_position="all" spacing="yes" background_image="" background_repeat="no-repeat" padding_top="" padding_right="" padding_bottom="" padding_left="" margin_top="0px" margin_bottom="0px" class="" id="" animation_type="" animation_speed="0.3" animation_direction="left" hide_on_mobile="small-visibility,medium-visibility,large-visibility" center_content="no" last="true" min_height="" hover_type="none" link="" first="true"][fusion_title size="2" content_align="left" style_type="default" sep_color="" margin_top="" margin_bottom="" class="" id=""]%s[/fusion_title]%s[/fusion_builder_column][/fusion_builder_row][/fusion_builder_container]',
				esc_html( $cat->name ),
				$grid
			);
		}

		return do_shortcode( $output );
	}
	/**
	 * Shortcode to render the childcare provider directory from CSV.
	 *
	 * @return string Rendered HTML.
	 */
	public function render_childcare_directory() {
		$csv_path = WP_CONTENT_DIR . '/Family Childcare Provider Profiles(Sheet1).csv';
		if ( ! file_exists( $csv_path ) ) {
			return 'CSV file not found.';
		}

		$file = fopen($csv_path, 'r');
		$header = fgetcsv($file);
		$data = [];
		while (($row = fgetcsv($file)) !== FALSE) {
			if (count($row) < 7) continue;
			$data[] = array_combine($header, $row);
		}
		fclose($file);

		// Extract unique cities for filtering
		$cities = array_unique(array_column($data, 'NAME OF CITY OR TOWN'));
		asort($cities);

		$output = '<div class="childcare-filter-container" style="margin-bottom: 30px; display: flex; gap: 20px; flex-wrap: wrap;">';
		$output .= '<input id="provider-search" type="text" placeholder="Search by name or language..." style="flex-grow: 1; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" />';
		$output .= '<select id="city-filter" style="padding: 10px; border: 1px solid #ddd; border-radius: 4px;">';
		$output .= '<option value="">All Cities</option>';
		foreach ($cities as $city) {
			if (empty(trim($city))) continue;
			$output .= sprintf('<option value="%s">%s</option>', esc_attr($city), esc_html($city));
		}
		$output .= '</select>';
		$output .= '</div>';

		$output .= '<div id="childcare-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px;">';

		foreach ($data as $row) {
			$name = $row['NAME OF EDUCATOR'];
			$business = $row['NAME OF CHILDCARE'];
			$city = $row['NAME OF CITY OR TOWN'];
			$languages = $row['ALL LANGUAGES YOU SPEAK'];
			$experience = $row['NUMBER OF YEARS OF EXPERIENCE'];
			$children = $row['NUMBER OF CHILDREN'];
			
			$output .= sprintf(
				'<div class="provider-card" data-city="%s" style="background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 20px rgba(0,0,0,0.05); transition: transform 0.3s ease;">
					<div style="padding: 25px;">
						<h3 style="margin: 0 0 10px 0; color: #014153; font-family: Montserrat, sans-serif; font-size: 20px;">%s</h3>
						<p style="margin: 0 0 15px 0; color: #6EB440; font-weight: 700; font-family: Figtree, sans-serif;">%s</p>
						<div style="font-size: 14px; color: #555; font-family: Figtree, sans-serif;">
							<p style="margin: 5px 0;"><strong>City:</strong> %s</p>
							<p style="margin: 5px 0;"><strong>Languages:</strong> %s</p>
							<p style="margin: 5px 0;"><strong>Experience:</strong> %s years</p>
							<p style="margin: 5px 0;"><strong>Capacity:</strong> %s children</p>
						</div>
					</div>
				</div>',
				esc_attr($city),
				esc_html($name),
				esc_html($business),
				esc_html($city),
				esc_html($languages),
				esc_html($experience),
				esc_html($children)
			);
		}

		$output .= '</div>';

		// Filtering Script
		$output .= '
		<script>
		jQuery(document).ready(function($){
			function filterProviders() {
				var searchVal = $("#provider-search").val().toLowerCase().trim();
				var cityVal = $("#city-filter").val().toLowerCase();

				$(".provider-card").each(function(){
					var cardText = $(this).text().toLowerCase();
					var cardCity = $(this).data("city").toLowerCase();
					
					var matchesSearch = cardText.indexOf(searchVal) > -1;
					var matchesCity = cityVal === "" || cardCity === cityVal;
					
					$(this).toggle(matchesSearch && matchesCity);
				});
			}

			$("#provider-search").on("input", filterProviders);
			$("#city-filter").on("change", filterProviders);
		});
		</script>';

		return $output;
	}

}
