<?php
/**
 * CC American Heart Association Extras
 *
 * @package   CC American Heart Association Extras
 * @author    CARES staff
 * @license   GPL-2.0+
 * @copyright 2014 CommmunityCommons.org
 */

class CC_AHA_Extras {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.0';

	/**
	 *
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'cc-aha-extras';

	/**
	 *
	 * The ID for the AHA group on www.
	 *
	 *
	 *
	 * @since    1.0.0
	 *
	 * @var      int
	 */
	// public static cc_aha_get_group_id();// ( get_home_url() == 'http://commonsdev.local' ) ? 55 : 594 ; //594 on staging and www, 55 on local

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		// add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Add filter to catch removal of a story from a group
		// add_action( 'bp_init', array( $this, 'remove_story_from_group'), 75 );

		// Activate plugin when new blog is added
		// add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );
		
		
		/* Create a custom post types for aha priorities and action steps. */
		add_action( 'init', array( $this, 'register_aha_priorities' ) ); 
		add_action( 'init', array( $this, 'register_aha_community_planning_progress' ) );
		add_action( 'init', array( $this, 'register_aha_action_steps' ) );
		
		// Register taxonomies
		add_action( 'init', array( $this,  'aha_board_taxonomy_register' ) );
		add_action( 'init', array( $this,  'aha_affiliate_taxonomy_register' ) );
		add_action( 'init', array( $this,  'aha_state_taxonomy_register' ) );  
		add_action( 'init', array( $this,  'aha_criteria_taxonomy_register' ) ); 
		add_action( 'init', array( $this,  'aha_benchmark_date_taxonomy_register' ) ); 

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_registration_styles') );

		/* Define custom functionality.
		 * Refer To http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		// add_action( '@TODO', array( $this, 'action_method_name' ) );
		// add_filter( '@TODO', array( $this, 'filter_method_name' ) );
		// add_action( 'bp_before_group_request_membership_content', array( $this, 'print_descriptive_text') );
		// add_action('bp_group_request_membership_content', array( $this, 'print_grantee_list' ) );
		// add_filter( 'groups_member_comments_before_save', array( $this, 'append_grantee_comment' ), 25, 2 );

		// Add "aha" as an interest if the registration originates from an AHA page
		// Filters array provided by registration_form_interest_query_string
		// @returns array with new element (or not)
		add_filter( 'registration_form_interest_query_string', array( $this, 'add_registration_interest_parameter' ), 12, 1 );

		// Registration form additions - These all rely on ?aha=1 being appended to the register url.
		add_action( 'bp_before_account_details_fields', array( $this, 'registration_form_intro_text' ), 60 );
		add_action( 'bp_before_registration_submit_buttons', array( $this, 'registration_section_output' ), 60 );
		add_action( 'bp_core_signup_user', array( $this, 'registration_extras_processing'), 71, 1 );
		// BuddyPress redirects logged-in users away fromm the registration page. Catch that request and redirect requests that include the AHA parameter to the AHA group.
        add_filter( 'bp_loggedin_register_page_redirect_to', array( $this, 'loggedin_register_page_redirect_to' ) );

        // If a user with an @heart email address makes a request, approve it automatically
        add_action( 'groups_membership_requested', array( $this, 'approve_member_requests' ), 12, 4 );


        add_filter( 'group_reports_create_new_label', array( $this, 'change_group_create_report_label' ), 32, 2 );

		// Add filter to catch form submission -- both "metro ID" and questionnaire answers
		add_action( 'bp_init', array( $this, 'save_form_submission'), 75 );

		// Checks existing metro ID cookie value and tries to gracefully set cookie value for Metro ID on page load.
		add_action( 'bp_init', array( $this, 'set_metro_id_cookie_on_load'), 22 );
		
		// Checks requested analysis URL for specified metro_id. Sets cookie if not in agreement.
		add_action( 'bp_init', array( $this, 'check_summary_metro_id_cookie_on_load'), 11 );

		// Adds ajax function for board-approved checkbox on the Health Analysis Rreport page ("interim" piece)
		add_action( 'wp_ajax_save_board_approved_priority' , array( $this, 'save_board_approved_priority' ) );
		add_action( 'wp_ajax_remove_board_approved_priority' , array( $this, 'remove_board_approved_priority' ) );
		add_action( 'wp_ajax_save_board_approved_staff' , array( $this, 'save_board_approved_staff' ) );
		add_action( 'wp_ajax_save_board_potential_priority' , array( $this, 'save_board_potential_priority' ) );
		
		//autocomplete on analysis pages
		
		add_action( 'wp_ajax_get_autocomplete_members' , array( $this, 'get_autocomplete_members' ) );
		
		//action planning ajax
		add_action( 'wp_ajax_get_priority_action_info' , array( $this, 'get_priority_action_info' ) );

		add_action( 'wp_ajax_save_priority_national_plan' , array( $this, 'save_priority_national_plan' ) );
		add_action( 'wp_ajax_get_action_steps' , array( $this, 'get_action_steps' ) );
		add_action( 'wp_ajax_save_action_step' , array( $this, 'save_action_step' ) );
		add_action( 'wp_ajax_delete_action_step' , array( $this, 'delete_action_step' ) );
		add_action( 'wp_ajax_get_national_action_steps' , array( $this, 'get_national_action_steps' ) );
		
		//community planning progress reports 
		add_action( 'wp_ajax_get_action_reports' , array( $this, 'get_action_reports' ) );
		add_action( 'wp_ajax_get_current_action_progress' , array( $this, 'get_current_action_progress' ) );
		add_action( 'wp_ajax_save_action_progress' , array( $this, 'save_action_progress' ) );
		
		//reports ajax
		add_action( 'wp_ajax_get_priority_reports_info' , array( $this, 'get_priority_reports_info' ) );
		add_action( 'wp_ajax_get_all_board_priorities' , array( $this, 'get_all_board_priorities' ) );
		add_action( 'wp_ajax_get_assessment_scores' , array( $this, 'get_assessment_scores' ) );

		//readonly action plan
		add_action( 'wp_ajax_get_action_plan' , array( $this, 'get_action_plan' ) );
	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() {
		// @TODO: Define activation functionality here
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {
		// @TODO: Define deactivation functionality here
	}

	/**
	 * Generate AHA Priority custom post type
	 *
	 * @since    1.0.0
	 */
	public function register_aha_priorities() {

	    $labels = array(
	        'name' => _x( 'AHA Priorities', 'aha-priority' ),
	        'singular_name' => _x( 'AHA Priority', 'aha-priority' ),
	        'add_new' => _x( 'Add New', 'aha-priority' ),
	        'add_new_item' => _x( 'Add New AHA Priority', 'aha-priority' ),
	        'edit_item' => _x( 'Edit AHA Priority', 'aha-priority' ),
	        'new_item' => _x( 'New AHA Priority', 'aha-priority' ),
	        'view_item' => _x( 'View AHA Priority', 'aha-priority' ),
	        'search_items' => _x( 'Search AHA Priorities', 'aha-priority' ),
	        'not_found' => _x( 'No AHA priorities found', 'aha-priority' ),
	        'not_found_in_trash' => _x( 'No aha priorities found in Trash', 'aha-priority' ),
	        'parent_item_colon' => _x( 'Parent AHA Priority:', 'aha-priority' ),
	        'menu_name' => _x( 'AHA Priorities', 'aha-priority' ),
	    );

		//TODO: Make this hidden in wp-admin, once sure it works!
	    $args = array(
	        'labels' => $labels,
	        'hierarchical' => false,
	        'description' => 'This post type is created when AHA boards select priorities ...on..?.',
	        'supports' => array( 'title', 'editor', 'custom-fields', 'page-attributes', 'author', 'excerpt' ),
	        'public' => true,
	        'show_ui' => true,
	        'show_in_menu' => true,
	        //'menu_icon' => '',
	        'show_in_nav_menus' => false,
	        'publicly_queryable' => true,
	        'exclude_from_search' => true,
	        'has_archive' => false,
	        'query_var' => true,
	        'can_export' => true,
	        'rewrite' => false,
			'taxonomies' => array( 'aha-boards' ),
	        'capability_type' => 'post'//,
	        //'map_meta_cap'    => true
	    );

	    register_post_type( 'aha-priority', $args );
	}
	
	/**
	 * Generate AHA Action Steps custom post type
	 *
	 * @since    1.0.0
	 */
	public function register_aha_action_steps() {

	    $labels = array(
	        'name' => _x( 'AHA Action Steps', 'aha-action-step' ),
	        'singular_name' => _x( 'AHA Action Step', 'aha-action-step' ),
	        'add_new' => _x( 'Add New', 'aha-action-step' ),
	        'add_new_item' => _x( 'Add New AHA Action Step', 'aha-action-step' ),
	        'edit_item' => _x( 'Edit AHA Action Step', 'aha-action-step' ),
	        'new_item' => _x( 'New AHA Action Step', 'aha-action-step' ),
	        'view_item' => _x( 'View AHA Action Steps', 'aha-action-step' ),
	        'search_items' => _x( 'Search AHA Action Steps', 'aha-action-step' ),
	        'not_found' => _x( 'No AHA Action Steps found', 'aha-action-step' ),
	        'not_found_in_trash' => _x( 'No AHA Action Steps found in Trash', 'aha-action-step' ),
	        'parent_item_colon' => _x( 'Parent AHA Action Step:', 'aha-action-step' ),
	        'menu_name' => _x( 'AHA Action Steps', 'aha-action-step' ),
	    );

		//TODO: Make this hidden in wp-admin, once sure it works!
	    $args = array(
	        'labels' => $labels,
	        'hierarchical' => true,
			'menu_order' => null,
	        'description' => 'This post type is created when AHA boards select priorities ...on..?.',
	        'supports' => array( 'title', 'editor', 'custom-fields', 'page-attributes', 'author', 'excerpt' ),
	        'public' => true,
	        'show_ui' => true,
	        'show_in_menu' => true,
	        //'menu_icon' => '',
	        'show_in_nav_menus' => false,
	        'publicly_queryable' => true,
	        'exclude_from_search' => true,
	        'has_archive' => false,
	        'query_var' => true,
	        'can_export' => true,
	        'rewrite' => false,
			'taxonomies' => array( 'aha-boards' ),
	        'capability_type' => 'post'//,
	        //'map_meta_cap'    => true
	    );

	    register_post_type( 'aha-action-step', $args );
	}
	
		
	/**
	 * Generate AHA Community Planning Progress Report custom post type
	 *
	 * @since    1.0.0
	 */
	public function register_aha_community_planning_progress() {

	    $labels = array(
	        'name' => _x( 'AHA Community Planning Progress Reports', 'aha-community-planning-progress' ),
	        'singular_name' => _x( 'AHA Community Planning Progress Report', 'aha-community-planning-progress' ),
	        'add_new' => _x( 'Add New', 'aha-community-planning-progress' ),
	        'add_new_item' => _x( 'Add New AHA Community Planning Progress Report', 'aha-community-planning-progress' ),
	        'edit_item' => _x( 'Edit AHA Community Planning Progress Report', 'aha-community-planning-progress' ),
	        'new_item' => _x( 'New AHA Community Planning Progress Report', 'aha-community-planning-progress' ),
	        'view_item' => _x( 'View AHA Community Planning Progress Reports', 'aha-community-planning-progress' ),
	        'search_items' => _x( 'Search AHA Community Planning Progress Reports', 'aha-community-planning-progress' ),
	        'not_found' => _x( 'No AHA Community Planning Progress Reports found', 'aha-community-planning-progress' ),
	        'not_found_in_trash' => _x( 'No AHA Community Planning Progress Reports found in Trash', 'aha-community-planning-progress' ),
	        'parent_item_colon' => _x( 'Parent AHA Community Planning Progress Report:', 'aha-community-planning-progress' ),
	        'menu_name' => _x( 'AHA Action Planning Progress', 'aha-community-planning-progress' ),
	    );

		//TODO: Make this hidden in wp-admin, once sure it works!
	    $args = array(
	        'labels' => $labels,
	        'hierarchical' => true,
			'menu_order' => null,
	        'description' => 'Monthly evaluations by board on viability progress (in Action Progress and Planning). Child of AHA Priority.',
	        'supports' => array( 'title', 'editor', 'custom-fields', 'page-attributes', 'author', 'excerpt' ),
	        'public' => true,
	        'show_ui' => true,
	        'show_in_menu' => true,
	        //'menu_icon' => '',
	        'show_in_nav_menus' => true,
	        'publicly_queryable' => true,
	        'exclude_from_search' => true,
	        'has_archive' => false,
	        'query_var' => true,
	        'can_export' => true,
	        'rewrite' => false,
			'taxonomies' => array( 'aha-boards' ),
	        'capability_type' => 'post'//,
	        //'map_meta_cap'    => true
	    );

	    register_post_type( 'aha-action-progress', $args );
	}
	
		
	/**
	 * Generate AHA Boards, Affiliate, States, Criteria custom taxonomy
	 *
	 * @since    1.0.0
	 */
	public function aha_board_taxonomy_register() {
		$labels = array(
			'name'	=> _x( 'AHA Boards', 'taxonomy general name' ),
			'singular_name'	=> _x( 'AHA Board', 'taxonomy singular name' ),
			'search_items'	=> __( 'Search AHA Boards' ),
			'popular_items'	=> __( 'Popular AHA Boards' ),
			'all_items'	=> __( 'All AHA Boards' ),
			'parent_item' => null,
			'parent_item_colon'	=> null,
			'edit_item' => __( 'Edit AHA Board' ), 
			'update_item' => __( 'Update AHA Board' ),
			'add_new_item' => __( 'Add AHA Board' ),
			'new_item_name' => __( 'New AHA Board' ),
			'separate_items_with_commas' => __( 'Separate AHA Boards with commas' ),
			'add_or_remove_items' => __( 'Add or remove AHA Boards' ),
			'choose_from_most_used' => __( 'Choose from the most used AHA Boards' ),
			'not_found' => __( 'No AHA Boards found.' ),
			'menu_name' => __( '-- Edit AHA Boards' )
		);
		
		$args = array(
			'hierarchical' => true,
			'labels' => $labels,
			'show_ui' => true,
			'show_admin_column' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'aha-board-term' )
		);
		
		register_taxonomy( 'aha-board-term', array( 'aha-action-step', 'aha-priority', 'aha-action-progress' ), $args );
	}
	
	public function aha_affiliate_taxonomy_register() {
		$labels = array(
			'name'	=> _x( 'AHA Affiliates', 'taxonomy general name' ),
			'singular_name'	=> _x( 'AHA Affiliate', 'taxonomy singular name' ),
			'search_items'	=> __( 'Search AHA Affiliates' ),
			'popular_items'	=> __( 'Popular AHA Affiliates' ),
			'all_items'	=> __( 'All AHA Affiliates' ),
			'parent_item' => null,
			'parent_item_colon'	=> null,
			'edit_item' => __( 'Edit AHA Affiliate' ), 
			'update_item' => __( 'Update AHA Affiliate' ),
			'add_new_item' => __( 'Add AHA Affiliate' ),
			'new_item_name' => __( 'New AHA Affiliate' ),
			'separate_items_with_commas' => __( 'Separate AHA Affiliates with commas' ),
			'add_or_remove_items' => __( 'Add or remove AHA Affiliates' ),
			'choose_from_most_used' => __( 'Choose from the most used AHA Affiliates' ),
			'not_found' => __( 'No AHA Affiliates found.' ),
			'menu_name' => __( '-- Edit AHA Affiliates' )
		);
		
		$args = array(
			'hierarchical' => true,
			'labels' => $labels,
			'show_ui' => true,
			'show_admin_column' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'aha-affiliate-term' )
		);
		
		register_taxonomy( 'aha-affiliate-term', array( 'aha-action-step', 'aha-priority', 'aha-action-progress' ), $args );
	}
	
	public function aha_state_taxonomy_register() {
		$labels = array(
			'name'	=> _x( 'AHA States', 'taxonomy general name' ),
			'singular_name'	=> _x( 'AHA State', 'taxonomy singular name' ),
			'search_items'	=> __( 'Search AHA States' ),
			'popular_items'	=> __( 'Popular AHA States' ),
			'all_items'	=> __( 'All AHA States' ),
			'parent_item' => null,
			'parent_item_colon'	=> null,
			'edit_item' => __( 'Edit AHA State' ), 
			'update_item' => __( 'Update AHA State' ),
			'add_new_item' => __( 'Add AHA State' ),
			'new_item_name' => __( 'New AHA State' ),
			'separate_items_with_commas' => __( 'Separate AHA States with commas' ),
			'add_or_remove_items' => __( 'Add or remove AHA States' ),
			'choose_from_most_used' => __( 'Choose from the most used AHA States' ),
			'not_found' => __( 'No AHA States found.' ),
			'menu_name' => __( '-- Edit AHA States' )
		);
		
		$args = array(
			'hierarchical' => true,
			'labels' => $labels,
			'show_ui' => true,
			'show_admin_column' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'aha-state-term' )
		);
		
		register_taxonomy( 'aha-state-term', array( 'aha-action-step', 'aha-priority', 'aha-action-progress' ), $args );
	}
	
	public function aha_criteria_taxonomy_register() {
		$labels = array(
			'name'	=> _x( 'AHA Criteria', 'taxonomy general name' ),
			'singular_name'	=> _x( 'AHA Criterion', 'taxonomy singular name' ),
			'search_items'	=> __( 'Search AHA Criteria' ),
			'popular_items'	=> __( 'Popular AHA Criteria' ),
			'all_items'	=> __( 'All AHA Criteria' ),
			'parent_item' => null,
			'parent_item_colon'	=> null,
			'edit_item' => __( 'Edit AHA Criterion' ), 
			'update_item' => __( 'Update AHA Criterion' ),
			'add_new_item' => __( 'Add AHA Criterion' ),
			'new_item_name' => __( 'New AHA Criterion' ),
			'separate_items_with_commas' => __( 'Separate AHA Criteria with commas' ),
			'add_or_remove_items' => __( 'Add or remove AHA Criteria' ),
			'choose_from_most_used' => __( 'Choose from the most used AHA Criteria' ),
			'not_found' => __( 'No AHA Criteria found.' ),
			'menu_name' => __( '-- Edit AHA Criteria' )
		);
		
		$args = array(
			'hierarchical' => true,
			'labels' => $labels,
			'show_ui' => true,
			'show_admin_column' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'aha-criteria-term' )
		);
		
		register_taxonomy( 'aha-criteria-term', array( 'aha-priority', 'aha-action-step', 'aha-action-progress' ), $args );
	}
	
	public function aha_benchmark_date_taxonomy_register() {
		$labels = array(
			'name'	=> _x( 'AHA Benchmark Date', 'taxonomy general name' ),
			'singular_name'	=> _x( 'AHA Benchmark Date', 'taxonomy singular name' ),
			'search_items'	=> __( 'Search AHA Benchmark Dates' ),
			'popular_items'	=> __( 'Popular Benchmark Dates' ),
			'all_items'	=> __( 'All Benchmark Dates' ),
			'parent_item' => null,
			'parent_item_colon'	=> null,
			'edit_item' => __( 'Edit Benchmark Date' ), 
			'update_item' => __( 'Update Benchmark Date' ),
			'add_new_item' => __( 'Add Benchmark Date' ),
			'new_item_name' => __( 'New Benchmark Date' ),
			'separate_items_with_commas' => __( 'Separate Benchmark Dates with commas' ),
			'add_or_remove_items' => __( 'Add or remove Benchmark Dates' ),
			'choose_from_most_used' => __( 'Choose from the most used Benchmark Dates' ),
			'not_found' => __( 'No Benchmark Dates found.' ),
			'menu_name' => __( '-- Edit Benchmark Dates' )
		);
		
		$args = array(
			'hierarchical' => true,
			'labels' => $labels,
			'show_ui' => true,
			'show_admin_column' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'aha-benchmark-date-term' )
		);
		
		register_taxonomy( 'aha-benchmark-date-term', array( 'aha-action-step', 'aha-priority' ), $args );
	}
	
	
	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		if ( cc_aha_is_component() ) {
			wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'css/aha-extras-tab.css', __FILE__ ), array(), '1.38' );
		}
		
		if ( cc_aha_on_reports_screen() ){
			wp_enqueue_style('jquery-multiselect-css', plugins_url( 'css/jquery.multiselect.css', __FILE__ ), array(), '1.0' );
		}
	}

	public function enqueue_registration_styles() {
	    if( bp_is_register_page() && isset( $_GET['aha'] ) && $_GET['aha'] )
	      wp_enqueue_style( 'aha-section-register-css', plugins_url( 'css/aha_registration_extras.css', __FILE__ ), array(), '0.1', 'screen' );
	}

	/**
	 * Register and enqueue public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		if ( cc_aha_is_component() ) {
			wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'js/aha-group-pane-js.js', __FILE__ ), array( 'jquery' ), 1.7 );
			//wp_enqueue_script( 'autocomplete', plugins_url( 'js/jquery.autocomplete-min.js', __FILE__ ), array( 'jquery' ), self::VERSION );
			wp_enqueue_script( 'autocomplete', plugins_url( 'js/jquery-ui.min.js', __FILE__ ), array( 'jquery' ), self::VERSION );
			wp_localize_script( 
				$this->plugin_slug . '-plugin-script', 
				'aha_ajax',
				array( 
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'ajax_nonce' => wp_create_nonce( 'cc_aha_ajax_nonce' ),
					'group_members' => cc_aha_get_member_array_autocomplete(),
					'group_member_names' => cc_aha_get_member_names_array(),
					//'national_plan_fields' => cc_aha_get_national_plan_fields(),
					'board_report_url' => cc_aha_get_board_level_report_permalink(),
					'national_report_url' => cc_aha_get_nat_level_report_permalink(),
					'action_plan_fields' => cc_aha_get_national_plan_fields()
				)
			);
		}

		if ( cc_aha_on_analysis_screen() ) {
			wp_enqueue_script( 'jquery-knob', plugins_url( 'js/jquery.knob.min.js', __FILE__ ), array( 'jquery' ), self::VERSION );
		}
		
		if ( cc_aha_on_report_card_screen() || cc_aha_on_report_card_sub_screen() ) {
			wp_enqueue_script( 'tablesorter', plugins_url( 'js/jquery.tablesorter.min.js', __FILE__ ), array( 'jquery' ), self::VERSION );
			wp_enqueue_script( 'tablesorter-widgets', plugins_url( 'js/jquery.tablesorter.widgets.js', __FILE__ ), array( 'jquery' ), self::VERSION );
			wp_enqueue_script( 'jquery-metadata', plugins_url( 'js/jquery.metadata.js', __FILE__ ), array( 'jquery' ), self::VERSION );
			wp_enqueue_script( 'reportcard-js', plugins_url( 'js/reportcard.js', __FILE__ ), array( 'jquery' ), '1.2' );
		}
		
		if ( cc_aha_on_revenue_report_card_screen() || cc_aha_on_revenue_report_card_sub_screen() ) {
			wp_enqueue_script( 'tablesorter', plugins_url( 'js/jquery.tablesorter.min.js', __FILE__ ), array( 'jquery' ), self::VERSION );
			wp_enqueue_script( 'tablesorter-widgets', plugins_url( 'js/jquery.tablesorter.widgets.js', __FILE__ ), array( 'jquery' ), self::VERSION );
			wp_enqueue_script( 'jquery-metadata', plugins_url( 'js/jquery.metadata.js', __FILE__ ), array( 'jquery' ), self::VERSION );
			wp_enqueue_script( 'reportcard-js', plugins_url( 'js/revenuereportcard.js', __FILE__ ), array( 'jquery' ), '1.2' );
		}
		
		if (cc_aha_on_action_planning_screen()) {
			wp_enqueue_script( 'actionplanning-js', plugins_url( 'js/actionplanning.js', __FILE__ ), array( 'jquery' ), '1.6' );
		}
		
		if ( cc_aha_on_reports_screen() ){
			wp_enqueue_script( 'reports-js', plugins_url( 'js/reports.js', __FILE__ ), array( 'jquery' ), '1.5' );
			wp_enqueue_script( 'multiselect-js', plugins_url( 'js/jquery.multiselect.min.js', __FILE__ ), array( 'jquery' ), '1.0' );
		}
		
		if (cc_aha_on_action_plan_screen()) {
			wp_enqueue_script( 'actionplan-js', plugins_url( 'js/actionplan-readonly.js', __FILE__ ), array( 'jquery' ), '1.0' );
		}
	}

	/**
	 * Output descriptive text above the request form.
	 *
	 * @since    1.0.0
	 */
	// 
	public function print_descriptive_text() {
		//If this isn't the AHA group or the registration page, don't bother.
		if ( ! cc_aha_is_aha_group() &&
		! ( bp_is_register_page() && ( isset( $_GET['aha'] ) && $_GET['aha'] ) ) )
			return false;

		// echo '<p class="description">The Robert Wood Johnson Foundation is offering access to the Childhood Obesity GIS collaborative group space to all current Childhood Obesity grantees free of charge. Within this space you can create maps, reports and documents collaboratively on the Commons. If you are interested in accessing this collaborative space, select your grant name from the list below. We&rsquo;ll respond with access within 24 hours.</p>';
	}

	// Registration form additions
	function registration_form_intro_text() {
	  if ( isset( $_GET['aha'] ) && $_GET['aha'] ) :
	  ?>
	    <p class="">
		  If you are already a Community Commons member, simply visit the <a href="<?php 
    		echo bp_get_group_permalink( groups_get_group( array( 'group_id' => cc_aha_get_group_id() ) ) );
    	?>">American Heart Association group</a> to get started. 
	    </p>
	    <?php
	    endif;
	}

	function registration_section_output() {
	  if ( isset( $_GET['aha'] ) && $_GET['aha'] ) :
	  ?>
	    <div id="aha-interest-opt-in" class="register-section checkbox">
		    <?php  $avatar = bp_core_fetch_avatar( array(
				'item_id' => cc_aha_get_group_id(),
				'object'  => 'group',
				'type'    => 'thumb',
				'class'   => 'registration-logo',

			) ); 
			echo $avatar; ?>
	      <h4 class="registration-headline">Join the Group: <em>American Heart Association</em></h4>

   	      <?php $this->print_descriptive_text(); ?>
	      
	      <label><input type="checkbox" name="aha_interest_group" id="aha_interest_group" value="agreed" <?php $this->determine_checked_status_default_is_checked( 'aha_interest_group' ); ?> /> Yes, I’d like to request membership in the group.</label>

	      <label for="group-request-membership-comments">Comments for the group admin (optional)</label>
	      <textarea name="group-request-membership-comments" id="group-request-membership-comments"><?php 
	      	if ( isset($_POST['group-request-membership-comments']) )
	      		echo $_POST['group-request-membership-comments'];
	      ?></textarea>

	    </div>
	    <?php
	    endif;
	}

	function loggedin_register_page_redirect_to( $redirect_to ) {
	  	if ( isset( $_GET['aha'] ) && $_GET['aha'] ) {
	  		$redirect_to = bp_get_group_permalink( groups_get_group( array( 'group_id' => cc_aha_get_group_id() ) ) );
	  	}

	  	return $redirect_to;
	}
	/**
	* Accept requests that come from members with @heart.org email addresses
	* @since 0.1
	*/
	function approve_member_requests( $user_id, $admins, $group_id, $membership_id ) {

		// For the AHA group, accept requests that come from members with @heart.org email addresses
		if ( cc_aha_get_group_id() == $group_id ) {

			$requestor = get_userdata( $user_id );
	        $email_parts = explode('@', $requestor->user_email);
	        if ( $email_parts[1] == 'heart.org' ) {
       			groups_accept_membership_request( $membership_id, $user_id, $group_id );
       			// TODO: This message gets overwritten at bp-groups-screens L 522. Not sure if that's beatable.
       			bp_core_add_message( __( 'Your membership request has been approved.', 'cc-aha-extras' ) );
	        }
		}

	}

	/**
	* Update usermeta with custom registration data
	* @since 0.1
	*/
	public function registration_extras_processing( $user_id ) {
	  
	  if ( isset( $_POST['aha_interest_group'] ) ) {
	  	// Create the group request
	  	$request = groups_send_membership_request( $user_id, cc_aha_get_group_id() );
	  }
	  
	  return $user_id;
	}

	public function determine_checked_status_default_is_checked( $field_name ){
		  // In its default state, no $_POST should exist. If this is a resubmit effort, $_POST['signup_submit'] will be set, then we can trust the value of the checkboxes.
		  if ( isset( $_POST['signup_submit'] ) && !isset( $_POST[ $field_name ] ) ) {
		    // If the user specifically unchecked the box, don't make them do it again.
		  } else {
		    // Default state, $_POST['signup_submit'] isn't set. Or, it is set and the checkbox is also set.
		    echo 'checked="checked"';
		  } 
	}
	public function add_registration_interest_parameter( $interests ) {

	    if ( bp_is_groups_component() && cc_aha_is_aha_group() ) {
	    	$interests[] = 'aha';
		}

	    return $interests;
	}

	/**
	 * Changes the label of the "Create New Report" button on the AHA page, since it will go to a different report
	 *
	 * @since    1.0.0
	 */
	public function change_group_create_report_label( $label, $group_id ) {

		if ( cc_aha_is_aha_group( $group_id ) ) {
			$label = 'Create an AHA Report'; 
		}

		return $label;
	}

	/**
	 * Handle form submissions
	 *  
	 * @since   1.0.0
	 * @return  boolean
	 */
	public function save_form_submission() {
		// Fires on bp_init action, so this is a catch-action type of filter.
		// Bail out if this isn't the AHA assessment component.
		if ( ! cc_aha_is_component() )
			return false;

		// Catch-all, handles updating board id user meta or setting the various cookies as needed
		if ( bp_is_action_variable( 'save-board-ids', 0 ) ) {

			// Is the nonce good?
			if ( ! wp_verify_nonce( $_REQUEST['save-aha-boards'], 'cc-aha-save-board-id' ) )
				return false;

			// Filter based on which submit button was used
			// User is trying to save board affiliations
			if ( $_POST['submit_save_usermeta_aha_board'] ){
			    if ( $this->save_metro_ids() ) {
	   				bp_core_add_message( __( 'Your board affiliation has been updated.', $this->plugin_slug ) );
			    } else {
					bp_core_add_message( __( 'Your board affiliation could not be updated.', $this->plugin_slug ), 'error' );
			    }
				$url = wp_get_referer();

			} else if ( $_POST['submit_cookie_aha_active_metro_id'] ){
				// User is setting preference for survey section
				if ( isset( $_POST['cookie_aha_active_metro_id'] ) ) {
					setcookie( 'aha_active_metro_id', $_POST['cookie_aha_active_metro_id'], 0, '/' );
					$url = wp_get_referer();
				}

			} else if ( $_POST['submit_cookie_aha_summary_metro_id'] ) {
				// User is setting preference for survey section
				if ( isset( $_POST['cookie_aha_summary_metro_id'] ) ) {
					setcookie( 'aha_summary_metro_id', $_POST['cookie_aha_summary_metro_id'], 0, '/' );
					$section = isset( $_POST['analysis-section'] ) ? $_POST['analysis-section'] : null ;
					$url = cc_aha_get_analysis_permalink( $section, $_POST['cookie_aha_summary_metro_id'] );
				}
			} else if ( $_POST['submit_cookie_aha_action_planning_metro_id'] ) {
				// User is setting preference for survey section
				if ( isset( $_POST['cookie_aha_action_planning_metro_id'] ) ) {
					setcookie( 'aha_action_planning_metro_id', $_POST['cookie_aha_action_planning_metro_id'], 0, '/' );
					$section = isset( $_POST['action-planning-section'] ) ? $_POST['action-planning-section'] : 'health' ;
					//$url = cc_aha_get_analysis_permalink( $section, $_POST['cookie_aha_summary_metro_id'] );
					$url = cc_aha_get_action_planning_permalink( $_POST['cookie_aha_action_planning_metro_id'] );
				}
			} else if ( $_POST['submit_cookie_aha_action_plan_readonly_metro_id'] ) {
				// User is setting preference for survey section
				if ( isset( $_POST['cookie_aha_action_plan_readonly_metro_id'] ) ) {
					setcookie( 'aha_action_plan_readonly_metro_id', $_POST['cookie_aha_action_plan_readonly_metro_id'], 0, '/' );
					
					//$url = cc_aha_get_analysis_permalink( $section, $_POST['cookie_aha_summary_metro_id'] );
					$url = cc_aha_get_action_plan_permalink( $_POST['cookie_aha_action_plan_readonly_metro_id'] );
				}
			}

			// Redirect and exit
			bp_core_redirect( $url );

			return false;
		}

		// Handle questionnaire form saves
		if ( bp_is_action_variable( 'update-assessment', 0 ) ) {
			// Is the nonce good?
			if ( ! wp_verify_nonce( $_REQUEST['set-aha-assessment-nonce'], 'cc-aha-assessment' ) )
				return false;

			$page = bp_action_variable(1);
			
			// Try to save the form data
		    if ( cc_aha_update_form_data() !== FALSE ) {
   				bp_core_add_message( __( 'Your responses have been recorded.', $this->plugin_slug ) );
		    } else {
				bp_core_add_message( __( 'There was a problem saving your responses.', $this->plugin_slug ), 'error' );
		    }

			// Redirect to the appropriate page of the form
			bp_core_redirect( $this->after_save_get_form_page_url( $page ) );
			
		}

		// Handle summary/analysis response saves
		if ( bp_is_action_variable( 'update-summary', 0 ) ) {
			// Is the nonce good?
			if ( ! wp_verify_nonce( $_REQUEST['set-aha-assessment-nonce'], 'cc-aha-assessment' ) )
				return false;

			$page = isset( $_POST['section-impact-area'] ) ? $_POST['section-impact-area'] : null;
			$summary_section = isset( $_POST['analysis-section'] ) ? $_POST['analysis-section'] : null;
			
			//if no summary section, check for revenue section 
			if ( $summary_section == null ) {
				//$summary_section = isset( $_POST['revenue-section'] ) ? $_POST['revenue-section'] : null;
				$summary_section = isset( $_POST['revenue-section'] ) ? 'revenue' : null;
			}
			
			// Try to save the form data
		    if ( cc_aha_update_form_data( $_COOKIE['aha_summary_metro_id'] ) !== FALSE ) {
   				bp_core_add_message( __( 'Your responses have been recorded.', $this->plugin_slug ) );
		    } else {
				bp_core_add_message( __( 'There was a problem saving your responses.', $this->plugin_slug ), 'error' );
		    }

			// Redirect to the appropriate page of the form
			bp_core_redirect( $this->after_save_get_summary_page_url( $summary_section, $page ) );
			
		}
	}

	/**
	 * Save metro ids as user meta
	 * Saves selection as serialized data in the usermeta table
	 * 
	 * @since   1.0.0
	 * @return  boolean
	 */
	function save_metro_ids(){
	    $selected_metros = $_POST['aha_metro_ids'];
	    $user_metros = get_user_meta( get_current_user_id(), 'aha_board' );

	    if ( empty( $selected_metros ) ) {
	        $success = delete_user_meta( get_current_user_id(), 'aha_board' );
	    } else {
			//TODO: account for non-changing affiliations (do we need to, or is the bug on Mel's local only?): 
			// since will return false if the previous value is the same as $new_value
	        $success = update_user_meta( get_current_user_id(), 'aha_board', $selected_metros );
	    }

	    return $success;
	}

	/**
	 * Determine the correct page to redirect the user to after a form page save
	 *  
	 * @since   1.0.0
	 * @return  string - url
	 */
	public function after_save_get_form_page_url( $page ){
			// From $_POST, we know whether the user clicked "continue" or "return to toc" and the form page number
			if ( isset( $_POST['submit-survey-to-toc'] ) ) {
				$url = cc_aha_get_survey_permalink( 1 );
			} else if ( isset( $_POST['submit-revenue-analysis-to-toc'] ) ) {
				$url = cc_aha_get_analysis_permalink( 'revenue' );
			} else if ( $page == cc_aha_get_max_page_number() ) {
				bp_core_add_message( __( 'Thank you for completing the assessment.', $this->plugin_slug ) );
				$url = cc_aha_get_survey_permalink( 1 );
			} else {
				$url = cc_aha_get_survey_permalink( ++$page );
			}

		return $url;
	}

	/**
	 * Determine the correct page to redirect the user to after a summary response save
	 *  
	 * @since   1.0.0
	 * @return  string - url
	 */
	public function after_save_get_summary_page_url( $summary_section, $page ){
			// From $_POST, we know whether the user clicked "continue" or "return to toc" and the form page number
			// TODO: Maybe add some logic here.
			$url = cc_aha_get_analysis_permalink( $summary_section );
			// if ( isset( $_POST['submit-survey-to-toc'] ) ) {
			// 	$url = cc_aha_get_survey_permalink( 1 );
			// } else if ( $page == cc_aha_get_max_page_number() ) {
			// 	bp_core_add_message( __( 'Thank you for completing the assessment.', $this->plugin_slug ) );
			// 	$url = cc_aha_get_survey_permalink( 1 );
			// } else {
			// 	$url = cc_aha_get_survey_permalink( ++$page );
			// }

		return $url;
	}

	/**
	 * Handle form submissions - checkbox fields & yes/no radios
	 *  
	 * @since   1.0.0
	 * @return  boolean
	 */
	public function save_boolean_fields( $metro_id, $fields ){
		foreach ($fields as $field) {
			// If checked, enter 1 in the field
			if ( isset( $_POST['$field'] ) && !empty( $_POST['$field'] ) ) {
				$success = update_aha_field( $metro_id, $field, 1 );
			} else {
				$success = update_aha_field( $metro_id, $field, 0 ); 
			}
			
		}

	}
	/**
	 * Handle form submissions - radio fields NOT BOOLEAN
	 *  
	 * @since   1.0.0
	 * @return  boolean
	 */
	public function save_radio_fields( $metro_id, $fields ){
		foreach ($fields as $field) {
			// If marked "yes" enter 1 in the field
			if ( isset( $_POST['$field'] ) && !empty( $_POST['$field'] ) ) {
				$success = update_aha_field( $metro_id, $field, 1 );
			} else {
				$success = update_aha_field( $metro_id, $field, 0 ); 
			}
			
		}
	}
	/**
	 * Handle form submissions - text fields
	 *  
	 * @since   1.0.0
	 * @return  boolean
	 */
	public function save_text_fields( $metro_id, $fields ){
		foreach ($fields as $field) {
			if ( isset( $_POST['$field'] ) && !empty( $_POST['$field'] ) ) {
				$input = sanitize_text_field( $_POST[ $field ] );

				// update_aha_field( $metro_id, $field, $input )

			}
			
		}
		
	}

	/**
	 * Checks existing metro ID cookie value and tries to gracefully set cookie value for Metro ID on page load.
	 *  
	 * @since   1.0.0
	 * @return  none, creates cookie
	 * @uses 	setcookie(), reset(), wp_redirect()
	 */
	public function set_metro_id_cookie_on_load() {
		// Only needed on the AHA tab, and only for logged-in users. (User has to be logged in to reach AHA tab, though. So we'll let BP handle that.)
		if ( ! cc_aha_is_component() )
			return;

		$survey_cookie_name = 'aha_active_metro_id';
	    // We need to know the user's affiliations
	    $selected_metro_ids = cc_aha_get_array_user_metro_ids();
	    $redirect = false;

        // Cookie is set, we check that it's a valid value FOR THE SURVEY ONLY, if not, unset it.
        // Most common case for this is user changes affiliations, so "active" metro ID is no longer applicable
	    if ( ! empty( $_COOKIE[ $survey_cookie_name ] ) && ! in_array( $_COOKIE[ $survey_cookie_name ], $selected_metro_ids ) ) {
        	// Cookie path must match the cookie we're trying to unset
            setcookie( $survey_cookie_name, '', time()-3600, '/' );
            // Remove it from the $_COOKIE array, too, so the following action will fire.
            unset( $_COOKIE[ $survey_cookie_name ] );
			$redirect = true;	           
	    }

		$cookies = array( 'aha_active_metro_id', 'aha_summary_metro_id', 'aha_action_planning_metro_id', 'aha_action_plan_readonly_metro_id' );
	    foreach ( $cookies as $cookie_name ) {
		    // If cookie doesn't exist (or we just deleted it above), we try to set it.
		    // If user has only one affiliation, we can set the cookie
		    if ( empty( $_COOKIE[ $cookie_name ] ) && count( $selected_metro_ids ) == 1  ){
	            setcookie( $cookie_name, reset( $selected_metro_ids ), 0, '/' );
				$redirect = true;
	        }
	    }

        if ( $redirect ) {
        	wp_redirect( wp_get_referer() );
        }

	}

	/**
	 * Checks existing metro ID cookie value and tries to gracefully set cookie value for Metro ID on page load - For summary section only
	 *  
	 * @since   1.0.0
	 * @return  none, creates cookie
	 * @uses 	setcookie(), wp_redirect()
	 */
	public function check_summary_metro_id_cookie_on_load() {

		// We only do this on the analysis screen.
		if ( ! cc_aha_on_analysis_screen() )
			return;

		// Only continue if there is a metro id set in the URL.
		if ( bp_action_variable( 1 ) && bp_action_variable( 1 ) != '00000' )
			$url_metro_id = bp_action_variable( 1 );

		if ( ! $url_metro_id )
			return;

		// Is there a cookie set that matches that url?
		if ( $url_metro_id != $_COOKIE['aha_summary_metro_id'] ){
			// Either the cookie isn't set, or the two metros don't match. URL should trump cookie.
            setcookie( 'aha_summary_metro_id', $url_metro_id, 0, '/' );
			$current_url = home_url( $_SERVER['REQUEST_URI'] );
			$towrite = PHP_EOL . 'redirecting to: ' . print_r( $current_url, TRUE);
			$towrite .= PHP_EOL . 'actions_variable: ' . print_r( bp_action_variable( 1 ), TRUE);
			$fp = fopen('aha_summary_setup.txt', 'a');
			fwrite($fp, $towrite);
			fclose($fp);
            wp_redirect( $current_url );
            exit;
		}
	}
	
	/* Saves board-approved priorities via ajax
	 *
	 *
	 *
	 *
	*/
	public function save_board_approved_priority(){
	
		// Is the nonce good?  TODO: this
		//if ( ! wp_verify_nonce( $_REQUEST['set-aha-remove-priority-nonce'], 'set-aha-remove-priority-nonce-' . $criterion ) ){
		if ( ! check_ajax_referer( 'cc_aha_ajax_nonce', 'aha_nonce' ) ){
			return false;
		}

		//just for testing, TODO: remove this.
		//$criterion = isset( $_POST['data']['criteria_name'] ) ? $_POST['data']['criteria_name'] : null;
		$criterion = isset( $_POST['criteria_name'] ) ? $_POST['criteria_name'] : null;
		
		//if no criteria, return
		if ( $criterion == null ) {
			return false;
		}
		
		//add board data to $_POST array, from $_COOKIE
		//$priority_data = $_POST['data'];
		//$priority_data['metro_id'] = $_COOKIE['aha_summary_metro_id'];
		/*
		$metro_id = $priority_data['metro_id'];
		$date = $priority_data['date'];
		$criteria = $priority_data['criteria_name'];
		*/
		$metro_id = $_COOKIE['aha_summary_metro_id'];
		$date = $_POST['date'];
		$criteria = $_POST['criteria_name'];
		$criteria_slug = $_POST['criteria_slug'];
		
		//$update_success = cc_aha_update_priority( $priority_data );
		$update_success = cc_aha_update_priority( $metro_id, $date, $criteria, $criteria_slug );
		
		// Try to save the form data
		if ( $update_success !== FALSE ) {
			bp_core_add_message( __( 'Your responses have been recorded.', $this->plugin_slug ) );
		} else {
			bp_core_add_message( __( 'There was a problem saving your responses.', $this->plugin_slug ), 'error' );
		}
	
	
		//return $update_success;
		die();
	
	}

	/* Removes board-approved priorities via ajax
	 *
	 *
	 *
	 *
	*/
	public function remove_board_approved_priority(){
	
		//
		//$criterion = isset( $_POST['data']['criteria_name'] ) ? $_POST['data']['criteria_name'] : null;
		$criterion = isset( $_POST['criteria_name'] ) ? $_POST['criteria_name'] : null;
		
		//add board data to $_POST array, from $_COOKIE
		//$priority_data = $_POST['data'];
		//$priority_data['metro_id'] = $_COOKIE['aha_summary_metro_id'];
		$_POST['metro_id'] = $_COOKIE['aha_summary_metro_id'];
		
		//if no criteria, return
		if ( $criterion == null ) {
			return false;
		}
		
		// Is the nonce good?  TODO: this
		//if ( ! wp_verify_nonce( $_REQUEST['set-aha-remove-priority-nonce'], 'set-aha-remove-priority-nonce-' . $criterion ) ){
		if ( ! check_ajax_referer( 'cc_aha_ajax_nonce', 'aha_nonce' ) ){
			return false;
		}
		
		//$priority_array = cc_aha_get_priorities_by_board_date_criterion( $priority_data['metro_id'], $priority_data['date'], $priority_data['criteria_name'] );
		$priority_array = cc_aha_get_priorities_by_board_date_criterion( $_POST['metro_id'], $_POST['date'], $_POST['criteria_name'] );
		//var_dump ( $priority_data['metro_id'] );
		//var_dump ( $priority_data['date']);
		//var_dump ( $priority_data['criteria_name']);
		
		$priority_id = current( $priority_array );
		//var_dump ($priority_id );
		if( $priority_id > 0 ){
			$error = wp_delete_post( $priority_id );		
		}
		
		// Try to save the form data
		if ( $error !== FALSE ) {
			bp_core_add_message( __( 'Your responses have been recorded.', $this->plugin_slug ) );
		} else {
			bp_core_add_message( __( 'There was a problem saving your responses.', $this->plugin_slug ), 'error' );
		}
	
	
		echo 0;
		die();
	
	}

	/* Saves the selected staff for priorities on the assessment page (interim)
	 *
	 *
	 *
	 *
	*/
	public function save_board_approved_staff(){
	
		// Is the nonce good?  TODO: this
		//if ( ! wp_verify_nonce( $_REQUEST['set-aha-assessment-nonce'], 'cc-aha-assessment' ) )
		if ( ! check_ajax_referer( 'cc_aha_ajax_nonce', 'aha_nonce' ) ) {
			return false;
		}
		
		$priority_id = isset( $_POST['priority_id'] ) ? $_POST['priority_id'] : null;
		
		//if no priority_id, return
		if ( $priority_id == null ) {
			return false;
		}
		
		$volunteer_name = $_POST['volunteer_name'];
		$volunteer_email = $_POST['volunteer_email'];
		$volunteer_name_2 = $_POST['volunteer_name_2'];
		$volunteer_email_2 = $_POST['volunteer_email_2'];
		
		//$priority_array = cc_aha_set_staff_for_priorities( $priority_id, $priority_data['staff_partner'], $priority_data['volunteer_lead'] ); //OLD WAY, w only 1 each staff, volunteer
		//4 cases: 1. vol1 & vol2 write in; 2. vol1 write in, vol2 commons; 3. vol1 commons, vol2 write-in; 4. vol1 & vol2 commons
		if( ( !empty( $volunteer_name ) || !empty( $volunteer_email ) ) && ( !empty( $volunteer_name_2 ) || !empty( $volunteer_email_2 ) ) ){
			//$priority_array = cc_aha_set_staff_for_priorities( $priority_id, $_POST['staff_partner'], null, $volunteer_name, $volunteer_email );
			$priority_array = cc_aha_set_staff_for_priorities( $priority_id, $_POST['staff_partner'], $_POST['staff_partner_2'], null, null, $volunteer_name, $volunteer_email, $volunteer_name_2, $volunteer_email_2 );
		} else if ( !empty( $volunteer_name ) || !empty( $volunteer_email ) ) {
			$priority_array = cc_aha_set_staff_for_priorities( $priority_id, $_POST['staff_partner'], $_POST['staff_partner_2'], null, $_POST['volunteer_lead_2'], $volunteer_name, $volunteer_email );
		} else if ( !empty( $volunteer_name_2 ) || !empty( $volunteer_email_2 ) ) {
			$priority_array = cc_aha_set_staff_for_priorities( $priority_id, $_POST['staff_partner'], $_POST['staff_partner_2'], $_POST['volunteer_lead'], null, null, null, $volunteer_name_2, $volunteer_email_2 );
		} else {
			//$priority_array = cc_aha_set_staff_for_priorities( $priority_id, $_POST['staff_partner'], $_POST['volunteer_lead'] );
			$priority_array = cc_aha_set_staff_for_priorities( $priority_id, $_POST['staff_partner'], $_POST['staff_partner_2'], $_POST['volunteer_lead'], $_POST['volunteer_lead_2']  );
		}
		
		/*
		//function cc_aha_set_staff_for_priorities( $priority_id, $staff_partner, $staff_partner2, $volunteer, $volunteer2, $volunteer_name = null, $volunteer_email = null, $volunteer_name_2 = null, $volunteer_email_2 = null ){
		1.
		$priority_array = cc_aha_set_staff_for_priorities( $priority_id, $_POST['staff_partner'], $_POST['staff_partner_2'], null, null, $volunteer_name, $volunteer_email, $volunteer_name_2, $volunteer_email_2  );
		2.
		$priority_array = cc_aha_set_staff_for_priorities( $priority_id, $_POST['staff_partner'], $_POST['staff_partner_2'], null, $_POST['volunteer_lead_2'], $volunteer_name, $volunteer_email  );
		3.
		$priority_array = cc_aha_set_staff_for_priorities( $priority_id, $_POST['staff_partner'], $_POST['staff_partner_2'], $_POST['volunteer_lead'], null, null, null, $volunteer_name_2, $volunteer_email_2  );
		4.
		$priority_array = cc_aha_set_staff_for_priorities( $priority_id, $_POST['staff_partner'], $_POST['staff_partner_2'], $_POST['volunteer_lead'], $_POST['volunteer_lead_2']  );
		
		
		
		*/
		
		//var_dump ( $priority_data['metro_id'] );
		//var_dump ( $priority_data['date']);
		//var_dump ( $priority_data['criteria_name']);
		
		$priority_id = current( $priority_array );
		//var_dump ($priority_id );
		if( $priority_id > 0 ){
			$error = wp_delete_post( $priority_id );		
		}
		
		// Try to save the form data
		if ( $error !== FALSE ) {
			bp_core_add_message( __( 'Your responses have been recorded.', $this->plugin_slug ) );
		} else {
			bp_core_add_message( __( 'There was a problem saving your responses.', $this->plugin_slug ), 'error' );
		}
	
	
		echo 'saved staff...probably';
		//echo check_ajax_referer( 'cc_aha_ajax_nonce', 'aha_nonce' );
		die();
	
	}
	
	/**
	 * Saves revised potential priorities ("top-3") on the assessment page (interim)
	 *
	 *
	 *
	 *
	*/
	public function save_board_potential_priority(){
	
		// Is the nonce good?
		if ( ! check_ajax_referer( 'cc_aha_ajax_nonce', 'aha_nonce' ) ) {
			return false;
		}
		
		//echo 'what';
		
		//$priority_id = isset( $_POST['data']['priority_id'] ) ? $_POST['data']['priority_id'] : null;
		$criteria_slug = isset( $_POST['criteria_slug'] ) ? $_POST['criteria_slug'] : null;
		
		//if no criteria_slug, return
		if ( $criteria_slug == null ) {
			return false;
		}
		$potential_priority = ( $_POST['potential_priority'] == 'Yes' ) ? 1 : 0;
		$metro_id = $_COOKIE['aha_summary_metro_id'];
		
		//$priority_array = cc_aha_set_staff_for_priorities( $priority_id, $priority_data['staff_partner'], $priority_data['volunteer_lead'] );
		$priority_saved = cc_aha_save_potential_priorities_by_board( $metro_id, $criteria_slug, $potential_priority );
		//var_dump ( $priority_data['metro_id'] );
		//var_dump ( $priority_data['date']);
		//var_dump ( $priority_data['criteria_name']);
		

		echo 'potential priority updated: ' . $priority_saved;
		
		die();
	
	}
	
	/**
	 * Returns list of group members
	 *
	 *
	 *
	 *
	*/
	public function get_autocomplete_members(){
	
		// Is the nonce good?
		if ( ! check_ajax_referer( 'cc_aha_ajax_nonce', 'aha_nonce' ) ) {
			return false;
		}
		
		$group_members = cc_aha_get_member_array();
		

		echo json_encode($group_members);
		
		die();
	
	}
	
	/*** Functions related to Action Steps ***/
	
	/* 

	 * Returns information json for Action Plan for specific priority
	 *
	 */
	public function get_priority_action_info(){
	
		// Is the nonce good?
		if ( ! check_ajax_referer( 'cc_aha_ajax_nonce', 'aha_nonce' ) ) {
			return false;
		}
		
		//We need to get this priority's specific information (post-meta)
		$post_id = $_POST[ 'priority_id' ];
		
		if( $post_id == 0 ){
			echo 'ERROR: post_id = 0';
			die();
		}
		
		$post_meta = get_metadata( 'post', $post_id );
		
		$data[ 'post_meta' ] = $post_meta;
		$data[ 'resources' ] = cc_aha_get_resources_by_criteria_name( $_POST['criteria_name'] );
		
		echo json_encode( $data );
		die();
		
	}
	
	/* 
	 * Returns json info for all priorities across boards for a priority type (taxonomy!)
	 *
	 */
	public function get_all_board_priorities(){
	
		// Is the nonce good?
		if ( ! check_ajax_referer( 'cc_aha_ajax_nonce', 'aha_nonce' ) ) {
			return false;
		}
		
		//We need to get this priority's specific information (post-meta)
		$metro_id = $_POST[ 'metro_id' ];
		
		if( $metro_id == '0' || empty( $metro_id ) ){
			echo 'ERROR: metro_id = 0 ' . $_POST[ 'metro_id' ];
			die();
		}
		
		//hmm, TODO: think about combining these functions
		$priorities = cc_aha_get_criteria_readable_by_board( $metro_id );
		
		if( empty ($priorities) ){
			echo json_encode( 0 );
		} else {
			echo json_encode( $priorities );
		}
		die();
		
	}
	
	/* 
	 * Saves information for National plan on Action Planning page 
	 *
	 * @param int PostID
	 */
	public function save_priority_national_plan(){
	
		// Is the nonce good?
		if ( ! check_ajax_referer( 'cc_aha_ajax_nonce', 'aha_nonce' ) ) {
			return false;
		}
		
		//We need to get this priority's specific information (post-meta)
		$post_id = $_POST[ 'priority_id' ];
		
		if( $post_id == 0 ){
			echo 'ERROR: post_id = 0';
			die();
		}
		//simple fields - TODO: put this into a var for js use
		$fields = cc_aha_get_national_plan_fields();

		$params = array();
		parse_str( $_POST['form_fields'], $params );
		
		foreach( $fields as $f ) {
			foreach( $params as $k => $v ){
				if ( $f == $k ) {
					if ( $v == '' || $v == '-1' ) {
						delete_post_meta( $post_id, $f );
					} else {
						update_post_meta( $post_id, $f, $v );
					}
					$data[$k] = $v;
				}
			}
		}
		
		//set flag for action step planning (for later opt-out)
		$action_planning_flag = get_post_meta( $post_id, 'action_plan_started', true );
		if ( $action_planning_flag == "" ) {
			update_post_meta( $post_id, 'action_plan_started', true );
		}
		
		$post_meta = get_metadata( 'post', $post_id );
		
		//echo json_encode( $post_meta );
		echo json_encode( $data );
		//echo json_encode( $params );
		//echo $_POST['form_fields']['shortterm_objective_answer'];
		die();
		
	}
	
	/* 
	 * Get postmeta for Priority (action plan) 
	 *
	 * @param int PostID
	 */
	public function get_action_plan(){
	
		// Is the nonce good?
		if ( ! check_ajax_referer( 'cc_aha_ajax_nonce', 'aha_nonce' ) ) {
			return false;
		}
		
		//We need to get this priority's specific information (post-meta)
		$post_id = $_POST[ 'priority_id' ];
		
		if( $post_id == 0 ){
			echo 'ERROR: post_id = 0';
			die();
		}
		
		$post_meta = get_metadata( 'post', $post_id );
		
		//echo json_encode( $post_meta );
		echo json_encode( $post_meta );
		//echo json_encode( $params );
		//echo $_POST['form_fields']['shortterm_objective_answer'];
		die();
		
	}
	
	/**
	 * Returns list of action steps with post_meta, given a post_id
	 *
	 * @param int Priority_id
	 * @return json_object
	 *
	*/
	public function get_action_steps(){
	
		// Is the nonce good?
		if ( ! check_ajax_referer( 'cc_aha_ajax_nonce', 'aha_nonce' ) ) {
			return false;
		}
		
		//We need to get this priority's specific information (post-meta)
		$post_id = $_POST[ 'priority_id' ];
		
		if( $post_id == 0 ){
			echo 'ERROR: post_id = 0';
			die();
		}
		
		
		$action_ids = cc_aha_get_action_steps_by_priority_id( $post_id );
		//$data['action_ids'] = $action_ids;
		
		if( empty( $action_ids )){
			
			$data = 0;
			echo json_encode($data);
		
			die();
		}
		foreach( $action_ids as $action_id ){
		
			//title
			$title = get_the_title( $action_id ); 
			
			//content (notes)
			$content_post = get_post($action_id);
			
			$content = $content_post->post_content;
			$content = apply_filters('the_content', $content);
			$content = strip_tags( $content );
			//$content = str_replace(']]>', ']]&gt;', $content);
			
			//start_date
			$start_date = get_post_meta( $action_id, 'start_date', true );
			
			//end_date
			$end_date = get_post_meta( $action_id, 'end_date', true );
			
			//volunteer lead
			//lead id, if commons user
			$lead_id = get_post_meta( $action_id, 'action_step_lead_id', true );
			if( ( $lead_id > 0 ) && ( $lead_id != "" ) ){
				//return username w ajax
				$lead_name = bp_core_get_user_displayname( $lead_id );
			} else {
				$lead_name = "";
			}
			
			//if write in, these will be populated
			$lead_write_in_name = get_post_meta( $action_id, 'action_step_lead_name', true );
			$lead_write_in_email = get_post_meta( $action_id, 'action_step_lead_email', true );
			
			//stage
			$stage = get_post_meta( $action_id, 'stage', true );
			
			//complete
			$complete_bool = get_post_meta( $action_id, 'completed', true );
		
			$data[ $action_id ] = array(
				'title' => $title,
				'notes' => $content,
				'start_date' => $start_date,
				'end_date' => $end_date,
				'lead_id' => $lead_id,
				'lead_name' => $lead_name,
				'lead_write_in_name' => $lead_write_in_name,
				'lead_write_in_email' => $lead_write_in_email,
				'stage' => $stage,
				'completed' => $complete_bool
				);
		
		}
		

		echo json_encode($data);
		
		die();
	
	}
	
	/**
	 * Saves an action step
	 *
	 * @param 
	 * @return json_object
	 *
	*/
	public function save_action_step(){
	
		// Is the nonce good?
		if ( ! check_ajax_referer( 'cc_aha_ajax_nonce', 'aha_nonce' ) ) {
			return false;
		}
		
		$priority_id = $_POST['priority_id'];
		if ( ! ( $priority_id > 0 ) ) {
			return false;		
		}
		
		//We need to get this priority's specific information (post-meta)
		$post_id = $_POST[ 'action_id' ];
		
		if( $post_id > 0 ){ //edit action step
		
			$post_args = array(
				'ID'			 => $post_id,
				'post_content'   => $_POST['notes'],
				'post_name'      => $_POST['title'], //TODO: make this better with taxonomies
				'post_title'     => $_POST['title'],
				'post_status'    => 'publish',
				'post_type'		 => 'aha-action-step',
				'post_parent'	 => $priority_id
				);
			
			$new_post_id = wp_update_post( $post_args );
				
		} else { //new action step
		
			$post_args = array(
				'post_content'   => $_POST['notes'],
				'post_name'      => $_POST['title'], //TODO: make this better with taxonomies
				'post_title'     => $_POST['title'],
				'post_status'    => 'publish',
				'post_type'		 => 'aha-action-step',
				'post_parent'	 => $priority_id
				);
		
			$new_post_id = wp_insert_post( $post_args );
			
		}
		
		//error check on post creation
		if( $new_post_id == 0 ){
			
			echo 'error creating object';
			die();
		
		} else {
			$data['action_step_id'] = $new_post_id; 
		}
		
		//next, save the post meta: update_post_meta returns false on same value or failure...not useful, so have to check for same val first, arg.
		//start-date
		$saved_start_date = get_post_meta( $new_post_id, 'start_date', true );
		if( $saved_start_date == $_POST['start_date'] ){
			$data['start_date'] = $_POST['start_date'];
		} else {
			$worked = update_post_meta( $new_post_id, 'start_date', $_POST['start_date'] );
			//TODO: error handling
			if( $worked === false ){
				$data['start_date'] = "error";
			} else {
				$data['start_date'] = $_POST['start_date'];
			}
		}
		
		//end-date
		$saved_end_date = get_post_meta( $new_post_id, 'end_date', true );
		if( $saved_end_date == $_POST['end_date'] ){
			$data['end_date'] = $_POST['end_date'];
		} else {
			$worked = update_post_meta( $new_post_id, 'end_date', $_POST['end_date'] );
			//TODO: error handling
			if( $worked === false ){
				$data['end_date'] = "error";
			} else {
				$data['end_date'] = $_POST['end_date'];
			}
		}
		
		//volunteer info
		update_post_meta( $new_post_id, 'action_step_lead_id', $_POST['lead_id'] );
		if( ( $_POST['lead_id'] > 0 ) && ($_POST['lead_id'] != "" ) ){
			//return username w ajax
			$data['lead_name'] = bp_core_get_user_displayname( $_POST['lead_id'] );
		} else {
			$data['lead_name'] = "";
		}
		
		$saved_lead_write_in_name = get_post_meta( $new_post_id, 'action_step_lead_name', true );
		if( $saved_lead_write_in_name == $_POST['lead_name'] ){
			$data['lead_write_in_name'] = $_POST['lead_name'];
		} else {
			$worked = update_post_meta( $new_post_id, 'action_step_lead_name', $_POST['lead_name'] );
			//TODO: error handling
			if( $worked === false ){
				$data['lead_write_in_name'] = "error";
			} else {
				$data['lead_write_in_name'] = $_POST['lead_name'];
			}
		}
		
		$saved_lead_write_in_email = get_post_meta( $new_post_id, 'action_step_lead_email', true );
		if( $saved_lead_write_in_email == $_POST['lead_email'] ){
			$data['lead_write_in_email'] = $_POST['lead_email'];
		} else {
			$worked = update_post_meta( $new_post_id, 'action_step_lead_email', $_POST['lead_email'] );
			//TODO: error handling
			if( $worked === false ){
				$data['lead_write_in_email'] = "error";
			} else {
				$data['lead_write_in_email'] = $_POST['lead_email'];
			}
		}
		
		//update_post_meta( $new_post_id, 'action_step_lead_name', $_POST['lead_name'] );
		//update_post_meta( $new_post_id, 'action_step_lead_email', $_POST['lead_email'] );
		
		//stage
		update_post_meta( $new_post_id, 'stage', $_POST['stage'] );
		$data['stage'] = $_POST['stage'];
		
		//completed
		update_post_meta( $new_post_id, 'completed', $_POST['completed'] );
		$data['completed'] = $_POST['completed'];
		
		//next, add the taxonomies of the parent to this action step; using current since singular: TODO: evaluate this decision...?
		//aha-board-term
		$terms = wp_get_post_terms( $priority_id, 'aha-board-term' );
		//$data['terms'] = $terms;
		$data['terms'] = current($terms)->term_id;
		$error = wp_set_post_terms( $new_post_id, current($terms)->term_id, 'aha-board-term' );
		//$data['error'] = $error;
		
		//aha-affiliate-term
		$terms = wp_get_post_terms( $priority_id, 'aha-affiliate-term' );
		wp_set_post_terms( $new_post_id, current($terms)->term_id, 'aha-affiliate-term' );
		
		//aha-state-term
		$terms = wp_get_post_terms( $priority_id, 'aha-state-term' );
		wp_set_post_terms( $new_post_id, current($terms)->term_id, 'aha-state-term' );
		
		//aha-criteria-term
		$terms = wp_get_post_terms( $priority_id, 'aha-criteria-term' );
		wp_set_post_terms( $new_post_id, current($terms)->term_id, 'aha-criteria-term' );
		
		
		//set flag for action step planning (for later opt-out)
		$action_planning_flag = get_post_meta( $new_post_id, 'action_plan_started', true );
		if ( $action_planning_flag == "" || $action_planning_flag != true ) {
			update_post_meta( $new_post_id, 'action_plan_started', true );
		}
		
		
		//echo json_encode( $post_meta );
		//echo json_encode( $data );
		//echo json_encode( $params );
		//echo $_POST['form_fields']['shortterm_objective_answer'];
		echo json_encode( $data );
		die();
		
	}
	
	/**
	 * Deletes an action step
	 *
	 * @param 
	 * @return json_object
	 *
	*/
	public function delete_action_step(){
	
		// Is the nonce good?
		if ( ! check_ajax_referer( 'cc_aha_ajax_nonce', 'aha_nonce' ) ) {
			return false;
		}
		
		$action_id = $_POST['action_id'];
		
		if ( ! ( $action_id > 0 ) ) {
			$data['error'] = 'No action ID';
			echo json_encode( $data );
			die();
			//return false;		
		}
		
		$delete_success = wp_delete_post( $action_id ); //returns false on error, post object on success
		
		if( $delete_success == false ){
			
			$data['error'] = 'Error deleting action step';
		
		} else {
		
			$data['success'] = 1; 
		}
		
		echo json_encode( $data );
		die();
		
	}
	
	/**
	 * Returns list of action steps with post_meta, given a post_id
	 *
	 * @param int Priority_id
	 * @return json_object
	 *
	*/
	public function get_national_action_steps(){
	
		// Is the nonce good?
		if ( ! check_ajax_referer( 'cc_aha_ajax_nonce', 'aha_nonce' ) ) {
			return false;
		}
		
		//We need to get this priority's specific information (post-meta)
		$post_id = $_POST[ 'priority_id' ];
		
		if( $post_id == 0 ){
			echo 'ERROR: post_id = 0';
			die();
		}
		
		//get all action steps 
		$action_ids = cc_aha_get_national_action_steps_by_criterion();
		$data['action_ids'] = $action_ids;

		echo json_encode($data);
		
		die();
	
	}
	
	/*** Functions related to Action Progress/Planning reporting ***/
	/* 
	 * Returns json info for all priorities across boards for a priority type (taxonomy!)
	 *
	 */
	public function get_priority_reports_info(){
	
		// Is the nonce good?
		if ( ! check_ajax_referer( 'cc_aha_ajax_nonce', 'aha_nonce' ) ) {
			return false;
		}
		
		//We need to get this priority's specific information (post-meta)
		$post_id = $_POST[ 'priority_id' ];
		
		if( $post_id == 0 ){
			echo 'ERROR: post_id = 0';
			die();
		}
		
		$post_meta = get_metadata( 'post', $post_id );
		
		$data[ 'post_meta' ] = $post_meta;
		$data[ 'resources' ] = cc_aha_get_resources_by_criteria_name( $_POST['criteria_name'] );
		
		echo json_encode( $data );
		die();
		
	}
	
	/**
	 * Saves an Community Planning Progress Report for the current date (WP CPT: aha-action-progress)
	 *
	 * @param json
	 * @return json
	 */
	public function save_action_progress(){
	
		// Is the nonce good?
		if ( ! check_ajax_referer( 'cc_aha_ajax_nonce', 'aha_nonce' ) ) {
			return false;
		}
	
		//We need to save as a child post of this priority
		$priority_id = $_POST[ 'priority_id' ];
		
		if( $priority_id == 0 ){
			echo 'ERROR: post_id = 0';
			die();
		}
		
		//We need to get this priority's specific information (post-meta)
		//$post_id = $_POST[ 'action_progress_id' ];
		
		//get CURRENT action progress, if it exists, to overwrite
		$post_id = get_current_action_progress_by_priority_id( $priority_id );  //returns int
		
		//naming convention = CriteriaName_MonYear_progress
		
		$criteria_name = $_POST['criteria_name']; //TODO, maybe? If this is empty..?  Would this be?  Can we check before?
		$date = date( 'MY' ); //thre-letter month, 4-num year
		$title = $criteria_name . "_" . $date . "_progress";
		
		//score - save to post_meta because there may be future scores
		$score = $_POST['score'];
		$preassess = $_POST['preassess_questions'];
		
		if( $post_id > 0 ){ //edit action progress
		
			$post_args = array(
				'ID'			 => $post_id,
				'post_content'   => "",
				'post_name'      => $title, //slug
				'post_title'     => $title,
				'post_status'    => 'publish',
				'post_type'		 => 'aha-action-progress',
				'post_parent'	 => $priority_id
				);
			
			$new_post_id = wp_update_post( $post_args );
				
		} else { //new action step
		
			$post_args = array(
				'post_content'   => "",
				'post_name'      => $title, //slug
				'post_title'     => $title,
				'post_status'    => 'publish',
				'post_type'		 => 'aha-action-progress',
				'post_parent'	 => $priority_id
				);
		
			$new_post_id = wp_insert_post( $post_args );
			
		}
		
		//error check on post creation
		if( $new_post_id == 0 ){
			
			echo 'error creating object';
			die();
		
		} else {
			$data['action_progress_id'] = $new_post_id; 
		}
		
		//next, save the post meta
		//score
		update_post_meta( $new_post_id, 'viability_score', $score );
		update_post_meta( $new_post_id, 'preassess_questions', $preassess );
		update_post_meta( $new_post_id, 'completed', $_POST['completed'] );
		
		//get latest action progress date
		$latest_progress_date = get_latest_action_progress_by_priority_id( $priority_id );
		$data['last_updated'] = $latest_progress_date;
		
		//TODO: are we saving completion?
		
		
		
		
		//next, add the taxonomies of the parent to this action step; using current since singular: TODO: evaluate this decision...?
		//aha-board-term
		$terms = wp_get_post_terms( $priority_id, 'aha-board-term' );
		//$data['terms'] = $terms;
		//$data['terms'] = current($terms)->term_id;
		$error = wp_set_post_terms( $new_post_id, current($terms)->term_id, 'aha-board-term' );
		//$data['error'] = $error;
		
		//aha-affiliate-term
		$terms = wp_get_post_terms( $priority_id, 'aha-affiliate-term' );
		wp_set_post_terms( $new_post_id, current($terms)->term_id, 'aha-affiliate-term' );
		
		//aha-state-term
		$terms = wp_get_post_terms( $priority_id, 'aha-state-term' );
		wp_set_post_terms( $new_post_id, current($terms)->term_id, 'aha-state-term' );
		
		//aha-criteria-term
		$terms = wp_get_post_terms( $priority_id, 'aha-criteria-term' );
		$data['terms'] = $terms;
		wp_set_post_terms( $new_post_id, current($terms)->term_id, 'aha-criteria-term' );
		
		//echo $_POST['form_fields']['shortterm_objective_answer'];
		echo json_encode( $data );
		die();
	
	
	}
	
	/*
	 * Returns json info for assessment scores
	 *
	 * @param json array
	 * @return json
	 */
	public function get_assessment_scores(){
	
		// Is the nonce good?
		if ( ! check_ajax_referer( 'cc_aha_ajax_nonce', 'aha_nonce' ) ) {
			return false;
		}
		
		//init array for return array
		$return_score_data = array();
		$return_score_data['which_form'] =  $_POST['data']['which_form'];
		
		//get time
		$current_only = $_POST['current_bool'];
		$ytd_bool = $_POST['ytd_bool'];
		$quarterly_bool = $_POST['quarterly_bool'];
		
		//get array of months for js to parse in Report table
		if( ( $current_only == "true" ) || ( $current_only === true ) ){
			$time = "now";
			$months_array = get_months_array_by_date_string( $time );
			
			$return_score_data['months_array'] = $months_array;
		} else if( ( $ytd_bool == "true" ) || ( $ytd_bool === true ) ){
			$time = "ytd";
			$months_array = get_months_array_by_date_string( $time );
			
			$return_score_data['months_array'] = $months_array;
		} else if( ( $quarterly_bool == "true" ) || ( $quarterly_bool === true ) ){
			$time = "quarterly";
			$months_array = get_months_array_by_date_string( $time );
		} else {
			$time = "range";
			//get time range
			$start_date = $_POST['start_date'];
			$end_date = $_POST['end_date'];
			
			$months_array = get_months_array_by_date_string( $time, $start_date, $end_date );
			$return_score_data['months_array'] = $months_array;
		}
		
		//if we're on the board-level-report, do this:
		if( $_POST['data']['which_form'] == "board-level-report" ) {
			
			if( !empty( $_POST['boards'] ) ){ 
				//get board data
				foreach( $_POST['boards'] as $metro_id => $priorities ){
					//make arrays for each metro_id
					$data[$metro_id] = $priorities;	//post_id
					$readable_priorities[$metro_id] = cc_aha_get_criteria_readable_by_board( $metro_id );
				
				}

				
				//EACH BOARD: get scores for each board_id and priority
				foreach( $data as $id => $priorities ){
				
					$priorities_array = array();  //array to hold priority-specific info for this board
					
					foreach( $priorities as $priority ){ //$priority = priority_id
						//Get readable name for priority
						$label = $readable_priorities[ $id ][$priority]['label'];
						
						//Get the score: look for children of type='aha-action-progress' in time frame
						switch( $time ){
							case "ytd":
								$progress_reports = get_action_progress_reports_by_priority_date( $priority, "ytd" );
								break;
							case "quarterly":
								$progress_reports = get_action_progress_reports_by_priority_date( $priority, "quarterly" );
								break;
							case "range":
								$progress_reports = get_action_progress_reports_by_priority_date( $priority, "range", $start_date, $end_date );
								break;
							case "now":
								$progress_reports = get_action_progress_reports_by_priority_date( $priority, "now" );
								break;
							default:
								$progress_reports = get_action_progress_reports_by_priority_date( $priority, "all" );
								break;
						
						}
						//add to $priorities array
						$priorities_array[ $priority ] = array(
							"priority_name" => $label,
							"scores" => $progress_reports
						);
						
					}
					//get board name
					$metro_data = cc_aha_get_single_metro_data( $id );
					$board_name = $metro_data['Board_Name'];
					
					//place in array //id = board_id, 
					$return_score_data['boards'][$id] = array( 
							"board_name" => $board_name,
							"board_id" => $id,
							"priorities" => $priorities_array
						);
				}
			}
			
		} else { //national//affiliate report
			//which group?
			$report_group = $_POST['data']['which_group'];
			$report_priority = $_POST['data']['which_priority']; //(int)id or "all"
			
			//get affiliates/states taxonomy ids
			$group_tax_ids = array(); //array to hold taxonomy ids of group
			$group_board_ids = array();
			$group_board_all = array();
			//$taxonomy_name = ""; //which taxonomy to include in query (looking through all is not efficient)
			
			//populate $group_board_ids lika this: 
			/*array(1) {
				["CT"]=>
				array(2) {
				[0]=>
				array(1) {
				  ["BOARD_ID"]=>
				  string(5) "FDA06"
				}
				[1]=>
				array(1) {
				  ["BOARD_ID"]=>
				  string(5) "FDA21"
				}
			}*/
			
			switch( $report_group ){
				case "affiliate":
					if( !empty( $_POST['affiliates'] ) ){
						
						//get boards associated with each affiliate
						foreach( $_POST['affiliates'] as $affiliate ){
							//array_push( $group_tax_ids, $affiliate ); //not efficient to call function for just one..
							$group_tax_ids[] = $affiliate;
							
							//get affiliate name and boards in that affiliate
							$term = get_term( $affiliate, 'aha-affiliate-term' );
							$group_board_ids[ $term->name ] = cc_aha_get_boards_by_affiliate_name( $term->name );
						}
						//$taxonomy_name = "aha-affiliate-term";
					}
					break;
				case "state":
					if( !empty( $_POST['states'] ) ){
						foreach( $_POST['states'] as $state ){
							//array_push( $group_tax_ids, $state );
							$group_tax_ids[] = $state;
							
							//get state name and boards in that state
							$term = get_term( $state, 'aha-state-term' );
							$group_board_ids[ $term->name ] = cc_aha_get_boards_by_state_name( $term->name );
						}
						//$taxonomy_name = "aha-state-term";
					}
					//var_dump( $group_board_ids );
					break;
				case "national":
					//get all boards, 
					$all_boards_array = cc_aha_get_metro_id_array();
				
					$group_board_ids["national"] = $all_boards_array;
					
					//var_dump(  $all_boards_array);
					break;
			}
			
			//now, given a non-empty $group_board_ids, cycle through and get priorities and scores in our time frame
			if( !empty( $group_board_ids) ) {
				//for each group's subgroup (specific affiliate or state), get board info
				foreach( $group_board_ids as $group	=> $boards ){
					//var_dump( $boards);
					//for each board, get the following in this format:
						
					/* 	[FDA01] => array(
							"board_id" => "FDA01",
							"board_name" => "Albany (NY)",
							"priorities" => array(
								id_num => array(
									"priority_name" => "name",
									"scores" => array(
										id => array(
											completed => "",
											month => "",
											month_year => "Mar 2015",
											preassess_questions => "yes",
											score => "2",
											year => "2015"
										);
									);
								);
							);
						);
					*/
					
					//FOREACH BOARD in our group
					foreach( $boards as $board_id ){
						$board_id =  current($board_id);
						
						//get board name
						$metro_data = cc_aha_get_single_metro_data( $board_id );
						$board_name = $metro_data['Board_Name'];
						
						//if priorities == "all", get all priorities for this board, with name/score data;
						if( $report_priority == "all" ){
							//get all priorities for this board_id in an array of ids
							$readable_priorities = cc_aha_get_criteria_readable_by_board( $board_id );
							//var_dump( $readable_priorities );
						} else {
							$priorities = $report_priority;
							
							//see if this board has the selected priority.  If not, do't add this board to $return_score_data and continue
							$priority_if_exists = cc_aha_get_single_criteria_readable_by_board_taxonomy( $board_id, $report_priority );
							if( empty( $priority_if_exists ) ){
								//output something that say 'no priorities' for debugging
								/*$return_score_data['boards'][$board_id] = array( 
									"board_name" => $board_name,
									"board_id" => $board_id,
									"priorities" => ""
								);*/
								
								continue;
							}
							
							$readable_priorities = $priority_if_exists;
							
						}
						
						$priorities_array = array();  //array to hold priority-specific info for this board
				
						//whether one or all
						foreach( $readable_priorities as $priority => $priority_details ){ //$priority = priority_id
						
							if( $report_priority == "all" ){
								$label = $priority_details['label'];
							} else { //single
								$label = $_POST['data']['which_priority_name'];
							}
							
							//var_dump( $label);
							//var_dump( $priority);
							
							//Get the score: look for children of type='aha-action-progress' in time frame
							switch( $time ){
								case "ytd":
									$progress_reports = get_action_progress_reports_by_priority_date( $priority, "ytd" );
									break;
								case "quarterly":
									$progress_reports = get_action_progress_reports_by_priority_date( $priority, "quarterly" );
									break;
								case "range":
									$progress_reports = get_action_progress_reports_by_priority_date( $priority, "range", $start_date, $end_date );
									break;
								case "now":
									$progress_reports = get_action_progress_reports_by_priority_date( $priority, "now" );
									break;
								/*default:
									$progress_reports = get_action_progress_reports_by_priority_date( $priority, "now" );
									break;
								*/
							}
							//add to $priorities array
							$priorities_array[ $priority ] = array(
								"priority_name" => $label,
								"scores" => $progress_reports
							);
							
						}
						
						//place in array //id = board_id, 
						$return_score_data['boards'][$board_id] = array( 
								"board_name" => $board_name,
								"board_id" => $board_id,
								"priorities" => $priorities_array
							);
					}
					
					
					$group_board_all[ $group ] = $all_board_info;
				
				}
			
			
			}
			
			
			
			//testing..
			$return_score_data['group_tax_ids'] = $group_tax_ids;
			$return_score_data['group_board_ids'] = $group_board_ids;
			
			//now, cycle through taxonomy ids and get ALL action_progress w/ that taxonomy
			/*foreach( $group_tax_ids as $tax_id ){
				$new_action_progress_ids = get_action_progress_by_taxonomy_id( $tax_id, $taxonomy_name );
				
				//array_push( $group_action_progress_ids, $new_action_progress_ids );
				//hmm, want this in a flat array
				foreach( $new_action_progress_ids as $progress_id ){
					$group_action_progress_ids[] = $progress_id;
				}
			} */
			
			//now that we have an array of aha-action-progress es, we need to get info for each
			//WAIT - can we just get boards by affiliates???  Dont' we have this somewhere?
			
			
			
			//which priority - this will be a taxonomy!
			$national_priority = $_POST['data']['which_priority'];
			
			$return_score_data['report_group'] = $report_group;
			$return_score_data['national_priority'] = $national_priority;
			$return_score_data['group_action_progress_ids'] = $group_action_progress_ids;
	
		}
		
		//what time?
		$return_score_data['time'] = $time;
		
		//echo json_encode( $data );
		echo json_encode( $return_score_data );
		die();
	}
	
	/**
	 * Returns list of Community Planning Action Reports
	 *
	 * @param json
	 * @return json object
	 */
	public function get_action_reports(){
	
		// Is the nonce good?
		if ( ! check_ajax_referer( 'cc_aha_ajax_nonce', 'aha_nonce' ) ) {
			return false;
		}
		
		//We need to save as a child post of this priority
		$priority_id = $_POST[ 'priority_id' ];
		
		if( $priority_id == 0 ){
			echo 'ERROR: post_id = 0';
			die();
		}
		
		//TODO: this isn't right for here. get current
		$current_progress = get_current_action_progress_by_priority_id( $priority_id );
		
		echo json_encode( $current_progress );
		
		die();
	
	}
	
	/**
	 * Returns list of Community Planning Action Reports for a given priority id
	 *
	 * @param json
	 * @return json object
	 */
	public function get_current_action_progress(){
	
		// Is the nonce good?
		if ( ! check_ajax_referer( 'cc_aha_ajax_nonce', 'aha_nonce' ) ) {
			return false;
		}
		
		//We need to save as a child post of this priority
		$priority_id = $_POST[ 'priority_id' ];
		$current_progress_id = 0;
		$latest_progress_date = "";
		
		if( $priority_id == 0 ){
			echo 'ERROR: post_id = 0';
			die();
		}
		
		//get current action progress id
		$current_progress_id = get_current_action_progress_by_priority_id( $priority_id );
		$data['current_action_progress_id'] = $current_progress_id;
		
		//get latest action progress date
		$latest_progress_date = get_latest_action_progress_by_priority_id( $priority_id );
		$data['last_updated'] = $latest_progress_date;
		
		//return id and viability score
		$viability_score = get_viability_score_for_single_action_progress( $current_progress_id );
		$data['viability_score'] = $viability_score;
		
		if( $current_progress_id > 0 ){
			$data['preassess_answer'] = get_post_meta( $current_progress_id, 'preassess_questions', true );
		} else {
			$data['preassess_answer'] = "";
		}
		
		
		echo json_encode( $data );
		die(); //buh-bye
	
	}
	
} // End class