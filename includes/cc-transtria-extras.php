<?php
/**
 * CC American Heart Association Extras
 *
 * @package   CC American Heart Association Extras
 * @author    CARES staff
 * @license   GPL-2.0+
 * @copyright 2014 CommmunityCommons.org
 */

class CC_Transtria_Extras {

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
	protected $plugin_slug = 'cc-transtria-extras';

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
	// public static cc_transtria_get_group_id();// ( get_home_url() == 'http://commonsdev.local' ) ? 55 : 594 ; //594 on staging and www, 55 on local

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
		//add_action( 'init', array( $this, 'register_aha_priorities' ) ); 
		
		// Register taxonomies
		//add_action( 'init', array( $this,  'aha_board_taxonomy_register' ) );

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

		// Filters array provided by registration_form_interest_query_string
		// @returns array with new element (or not)
		add_filter( 'registration_form_interest_query_string', array( $this, 'add_registration_interest_parameter' ), 12, 1 );

		// Registration form additions 
		add_action( 'bp_before_account_details_fields', array( $this, 'registration_form_intro_text' ), 60 );
		add_action( 'bp_before_registration_submit_buttons', array( $this, 'registration_section_output' ), 60 );
		add_action( 'bp_core_signup_user', array( $this, 'registration_extras_processing'), 71, 1 );
		// BuddyPress redirects logged-in users away fromm the registration page. Catch that request and redirect requests that include the AHA parameter to the AHA group.
        add_filter( 'bp_loggedin_register_page_redirect_to', array( $this, 'loggedin_register_page_redirect_to' ) );

        // If a user with an @heart email address makes a request, approve it automatically
        add_action( 'groups_membership_requested', array( $this, 'approve_member_requests' ), 12, 4 );


        add_filter( 'group_reports_create_new_label', array( $this, 'change_group_create_report_label' ), 32, 2 );

		// Add filter to catch form submission -- both "metro ID" and questionnaire answers
		//add_action( 'bp_init', array( $this, 'save_form_submission'), 75 );

		// Checks existing metro ID cookie value and tries to gracefully set cookie value for Metro ID on page load.
		//add_action( 'bp_init', array( $this, 'set_metro_id_cookie_on_load'), 22 );
		
		// Checks requested analysis URL for specified metro_id. Sets cookie if not in agreement.
		//add_action( 'bp_init', array( $this, 'check_summary_metro_id_cookie_on_load'), 11 );

		//Transtria ajaxing goes here!
		// gets study data via ajax...
		add_action( 'wp_ajax_get_study_data' , array( $this, 'get_study_data' ) );
		add_action( 'wp_ajax_save_study_data' , array( $this, 'save_study_data' ) );
		
		//get endnote citation info 
		add_action( 'wp_ajax_get_citation_info' , array( $this, 'get_citation_info' ) );
		
		//get/save data for assigments tab
		add_action( 'wp_ajax_get_assignments' , array( $this, 'get_assignments' ) );
		add_action( 'wp_ajax_save_assignments' , array( $this, 'save_assignments' ) );
		
		add_action( 'wp_ajax_create_evaluation_sample_div' , array( $this, 'create_evaluation_sample_div' ) );		
		
		//get things for analysis tab
		add_action( 'wp_ajax_get_im_dyads_by_group' , array( $this, 'get_im_dyads_by_group' ) );
		add_action( 'wp_ajax_run_intermediate_analysis' , array( $this, 'run_intermediate_analysis' ) );
		add_action( 'wp_ajax_run_analysis' , array( $this, 'run_analysis' ) );

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

		if ( cc_transtria_is_component() ) {
			wp_enqueue_style( 'transtria-extras-tab', plugins_url( 'css/transtria-extras-tab.css', __FILE__ ), array(), '1.02' );
			wp_enqueue_style( 'components', plugins_url( 'css/components.css', __FILE__ ), array(), '1.01' );
			wp_enqueue_style( 'multiselect', plugins_url( 'css/jquery.multiselect.css', __FILE__ ), array(), '1.01' );
			wp_enqueue_style( 'datetimepicker', plugins_url( 'css/jquery.datetimepicker.css', __FILE__ ), array(), '1.01' );
			wp_enqueue_style( 'jqueryui', plugins_url( 'css/jquery-ui.css', __FILE__ ), array(), '1.01' );
			//Mel asks: do we need this one?
			//wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'css/jquery.ptTimeSelect.css', __FILE__ ), array(), '1.01' );
		}

	}

	public function enqueue_registration_styles() {
	    if( bp_is_register_page() && isset( $_GET['transtria'] ) && $_GET['transtria'] ) {}
	      //wp_enqueue_style( 'aha-section-register-css', plugins_url( 'css/aha_registration_extras.css', __FILE__ ), array(), '0.1', 'screen' );
	}

	/**
	 * Register and enqueue public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		if ( cc_transtria_is_component() ) {
			//wp_enqueue_script( 'jquery-ui', plugins_url( 'js/jquery-ui.js', __FILE__ ), array( 'jquery' ), 1.7 );
			
			//wp_dequeue_script();
			//wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'js/aha-group-pane-js.js', __FILE__ ), array( 'jquery' ), 1.7 );
			//wp_enqueue_script( 'autocomplete', plugins_url( 'js/jquery-ui.min.js', __FILE__ ), array( 'jquery' ), self::VERSION );
			
			//wp_enqueue_script( 'custom_combobox', plugins_url( 'js/custom_combobox.js', __FILE__ ), array( 'jquery' ), '1.1' );
			//global $wp_scripts, $wp_styles;
			//var_dump( $wp_scripts );
			//requirements
			wp_enqueue_script( 'jquery-ui-core-mel', "/wp-includes/js/jquery/ui/core.min.js", array( "jquery") );
			wp_enqueue_script( 'jquery-ui-widget-mel', "/wp-includes/js/jquery/ui/widget.min.js", array( "jquery") ); 
			//wp_enqueue_script( 'jquery-ui-datepicker' );
			
			//our files
			
			wp_enqueue_script( $this->plugin_slug . 'multiselect', plugins_url( 'js/jquery.multiselect.min.js', __FILE__ ), array( 'jquery' ), '1.1' );
			wp_enqueue_script( $this->plugin_slug . 'tablesorter', plugins_url( 'js/tablesorter/jquery.tablesorter.min.js', __FILE__ ), array( 'jquery' ), '1.1' );
			wp_enqueue_script( $this->plugin_slug . 'datetimepicker', plugins_url( 'js/datetimepicker-master/jquery.datetimepicker.js', __FILE__ ), array( 'jquery' ), '1.1' );
			wp_enqueue_script( $this->plugin_slug . 'validation', plugins_url( 'js/jquery.validate.min.js', __FILE__ ), array( 'jquery' ), '1.0' );
			
			//wp_enqueue_script( $this->plugin_slug . 'dynamic_page_components', plugins_url( 'js/dynamic_components.js', __FILE__ ), array( 'jquery' ), '1.1' );
			//wp_enqueue_script( $this->plugin_slug . 'transtria_basic_js', plugins_url( 'js/transtria_basic.js', __FILE__ ), array( 'jquery' ), '1.1' );
			wp_enqueue_script( $this->plugin_slug . 'transtria_revamp_js', plugins_url( 'js/transtria_revamp.js', __FILE__ ), array( 'jquery' ), '1.3' );
			
			if( cc_transtria_on_assignments_screen() ){
				wp_enqueue_script( $this->plugin_slug . 'transtria_revamp_assignments_js', plugins_url( 'js/transtria_revamp_assignments.js', __FILE__ ), array( 'jquery' ), '1.0' );
			}
			
			if( cc_transtria_on_analysis_screen() ){
				wp_enqueue_script( $this->plugin_slug . 'transtria_revamp_analysis_js', plugins_url( 'js/transtria_revamp_analysis.js', __FILE__ ), array( 'jquery' ), '1.0' );
			}
			
			wp_localize_script( 
				$this->plugin_slug . 'transtria_revamp_js', 
				'transtria_ajax',
				array( 
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'ajax_nonce' => wp_create_nonce( 'cc_transtria_ajax_nonce' ),
					'study_home' => cc_transtria_get_home_permalink(),
					'all_studies' => cc_transtria_get_study_ids(),
					'measures_w_text' => cc_transtria_measures_w_extra_text(),
					'measures_w_text_short' => cc_transtria_measures_w_extra_text( false )
				)
			);
			
			wp_enqueue_script( $this->plugin_slug . '-js-vars' );
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
		if ( ! cc_transtria_is_transtria_group() &&
		! ( bp_is_register_page() && ( isset( $_GET['transtria'] ) && $_GET['transtria'] ) ) )
			return false;

		// echo '<p class="description">The Robert Wood Johnson Foundation is offering access to the Childhood Obesity GIS collaborative group space to all current Childhood Obesity grantees free of charge. Within this space you can create maps, reports and documents collaboratively on the Commons. If you are interested in accessing this collaborative space, select your grant name from the list below. We&rsquo;ll respond with access within 24 hours.</p>';
	}

	// Registration form additions
	function registration_form_intro_text() {
	  if ( isset( $_GET['transtria'] ) && $_GET['transtria'] ) :
	  ?>
	    <p class="">
		  If you are already a Community Commons member, simply visit the <a href="<?php 
    		echo bp_get_group_permalink( groups_get_group( array( 'group_id' => cc_transtria_get_group_id() ) ) );
    	?>">Transtria group</a> to get started. 
	    </p>
	    <?php
	    endif;
	}

	function registration_section_output() {
	  if ( isset( $_GET['aha'] ) && $_GET['aha'] ) :
	  ?>
	    <div id="transtria-interest-opt-in" class="register-section checkbox">
		    <?php  $avatar = bp_core_fetch_avatar( array(
				'item_id' => cc_transtria_get_group_id(),
				'object'  => 'group',
				'type'    => 'thumb',
				'class'   => 'registration-logo',

			) ); 
			echo $avatar; ?>
	      <h4 class="registration-headline">Join the Group: <em>Transtria</em></h4>

   	      <?php $this->print_descriptive_text(); ?>
	      
	      <label><input type="checkbox" name="transtria_interest_group" id="transtria_interest_group" value="agreed" <?php $this->determine_checked_status_default_is_checked( 'transtria_interest_group' ); ?> /> Yes, I’d like to request membership in the group.</label>

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
	  	if ( isset( $_GET['transtria'] ) && $_GET['transtria'] ) {
	  		$redirect_to = bp_get_group_permalink( groups_get_group( array( 'group_id' => cc_transtria_get_group_id() ) ) );
	  	}

	  	return $redirect_to;
	}
	/**
	* Accept requests that come from members with @heart.org email addresses
	* @since 0.1
	*/
	function approve_member_requests( $user_id, $admins, $group_id, $membership_id ) {

		if ( cc_transtria_get_group_id() == $group_id ) {

		}

	}

	/**
	* Update usermeta with custom registration data
	* @since 0.1
	*/
	public function registration_extras_processing( $user_id ) {
	  
	  if ( isset( $_POST['trantria_interest_group'] ) ) {
	  	// Create the group request
	  	$request = groups_send_membership_request( $user_id, cc_transtria_get_group_id() );
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

	    if ( bp_is_groups_component() && cc_transtria_is_transtria_group() ) {
	    	$interests[] = 'transtria';
		}

	    return $interests;
	}

	/**
	 * Changes the label of the "Create New Report" button on the transtria page, since it will go to a different report
	 *
	 * @since    1.0.0
	 */
	public function change_group_create_report_label( $label, $group_id ) {

		if ( cc_transtria_is_transtria_group( $group_id ) ) {
			$label = 'Create a Transtria Report'; 
		}

		return $label;
	}



	/**
	 * Returns arrays of Study Data fora  given study ID
	 *
	 *
	*/
	public function get_study_data(){
	
		// Is the nonce good?
		if ( ! check_ajax_referer( 'cc_transtria_ajax_nonce', 'transtria_nonce' ) ) {
			return false;
		}
		
		//TODO: build this out
		
		$this_study_id = $_POST["this_study_id"];

		$study_data['single'] = cc_transtria_get_single_study_data( $this_study_id );
		$study_data['population_single'] = cc_transtria_get_pops_study_data_single( $this_study_id );
		$study_data['num_ea_tabs'] = cc_transtria_get_num_ea_tabs_for_study( $this_study_id );
		$study_data['num_other_indicators'] = cc_transtria_get_num_other_ind_for_study( $this_study_id );
		$study_data['num_other_outcomes'] = cc_transtria_get_num_other_ind_for_study( $this_study_id );
		$study_data['ea'] = cc_transtria_get_ea_tab_data_for_study( $this_study_id );
		$study_data['multiple'] = cc_transtria_get_study_data_multiple( $this_study_id );
		$study_data['special_data'] = cc_transtria_get_special_data( $this_study_id );
		
		//can we put these all into a flat array?
		$study_data_flat = $study_data['single'];
		
		/*foreach( $study_data['population_single'] as $pop => $val_array ){
		
		
			//array_push( $study_data_flat, $val_array );
		
		}*/
		
		//echo json_encode( $study_data['single'] );
		echo json_encode( $study_data );
		
		die();
	
	}
	
	/**
	 * Returns citation info for a given endnote id
	 *
	 *
	*/
	public function get_citation_info(){
	
		// Is the nonce good?
		if ( ! check_ajax_referer( 'cc_transtria_ajax_nonce', 'transtria_nonce' ) ) {
			return false;
		}
		
		$endnote_id = $_POST["endnote_id"];

		$citation_info = cc_transtria_get_endnote_citation_info( $endnote_id );
		
		echo json_encode( $citation_info );
		
		die();
	
	}
	
	/**
	 * Returns citation info for a given endnote id
	 *
	 *
	*/
	public function get_assignments(){
	
		// Is the nonce good?
		if ( ! check_ajax_referer( 'cc_transtria_ajax_nonce', 'transtria_nonce' ) ) {
			return false;
		}

		$data['assignments_info'] = cc_transtria_get_assignments_info();
		$data['endnotes_info'] = cc_transtria_get_endnote_for_assignments();
		
		echo json_encode( $data );
		
		die();
	
	}
	
		
	/**
	 * Returns citation info for a given endnote id
	 *
	 *
	*/
	public function save_assignments(){
	
		global $wpdb;
		
		// Is the nonce good?
		if ( ! check_ajax_referer( 'cc_transtria_ajax_nonce', 'transtria_nonce' ) ) {
			return false;
		}
		
		$incoming_studygroupings = $_POST['studygrouping_data'];
		$incoming_analysisinfo = $_POST['ready_analysis'];
		
		
		//for now, just save study grouping assignment and ready analysis 
		$data['studygrouping_success'] = cc_transtria_update_study_groupings( $incoming_studygroupings );
		$data['readyanalysis_success'] = cc_transtria_update_ready_analysis( $incoming_analysisinfo );
		
		echo json_encode( $data );
		
		die();
	
	}
	
	
	/**
	 * saves Study Data for a given study ID or creates next study id and saves to that
	 *
	 *
	*/
	public function save_study_data(){
	
		// Is the nonce good?
		if ( ! check_ajax_referer( 'cc_transtria_ajax_nonce', 'transtria_nonce' ) ) {
			return false;
		}
		
		//TODO: build this out
		
		$this_study_id = $_POST["this_study_id"];
		$new_study = false;
	//	var_dump( $this_study_id );
		if( empty( $this_study_id ) || ( $this_study_id == "-1" ) ){
			$this_study_id = cc_transtria_get_next_study_id();
			$new_study = true;
		}

		$data['study_id'] = $this_study_id;
		
		//load in form parts
		$studies_data = $_POST['studies_table_vals'];
		$pops_data = $_POST['population_table_vals'];
		$ea_data = $_POST['ea_table_vals'];
		$code_results_data = $_POST['code_table_vals'];
		
		//these need to be handles differently, depending on what they are.  New paradigms, yaay!
		$special_data = $_POST['special_vals'];
		
		$num_ese_tabs = $_POST['num_ese_tabs'];
		$num_ea_tabs = $_POST['num_ea_tabs'];
		$num_other_ind = $_POST['num_other_ind'];
		$num_other_out = $_POST['num_other_out'];
		
		//update metadata table
		$meta_success = cc_transtria_save_to_metadata_table( $this_study_id, $num_ese_tabs, $num_ea_tabs, $num_other_ind, $num_other_out );
		
		//convert to db field names
		$converted_to_db_fields = cc_transtria_match_div_ids_to_studies_columns( $studies_data, true );
		//$converted_to_db_fields_pops = cc_transtria_match_div_ids_to_pops_columns_single( $pops_data, true );
		
		//save to tables
		$studies_success = cc_transtria_save_to_studies_table( $converted_to_db_fields, $this_study_id, $new_study );
		$pops_success = cc_transtria_save_to_pops_table_raw( $pops_data, $this_study_id, $new_study, $num_ese_tabs ); //convert to db field names in the pops save function
		
		if( (int)$num_ea_tabs > 0 ){
			$ea_success = cc_transtria_save_to_ea_table_raw( $ea_data, $this_study_id, $new_study, $num_ea_tabs );
		} else {
			$ea_success = 'no ea tabs';
		}
		
		if( !empty( $code_results_data ) ){
			$code_results_success = cc_transtria_save_to_code_results( $code_results_data, $this_study_id );
		} else {
			$code_results_success = 'no multis selected';
		}
		
		if( !empty( $special_data ) ){
			$special_results_success = cc_transtria_save_special_data( $special_data, $this_study_id );
		} else {
			$special_results_success = 'no special selected';
		}
		
		$data['studies_success'] = $studies_success;
		$data['pops_success'] = $pops_success;
		$data['meta_success'] = $meta_success;
		$data['ea_success'] = $ea_success;
		$data['code_results_success'] = $code_results_success;
		$data['special_results_success'] = $special_results_success;
		
		//echo json_encode( $study_data['single'] );
		echo json_encode( $data );
		
		die();
	
	}
	
	
	public function create_evaluation_sample_div(){
	
		// Is the nonce good?
		if ( ! check_ajax_referer( 'cc_transtria_ajax_nonce', 'transtria_nonce' ) ) {
			return false;
		}
		$mmm = $_POST["new_tab_id"];
		//echo "Mikes Test " + $_POST["new_tab_id"];
		echo json_encode( $mmm );
		
		die();
	
	}		
	
	//ajax get all study ids in a study group
	public function get_im_dyads_by_group(){
		// Is the nonce good?
		if ( ! check_ajax_referer( 'cc_transtria_ajax_nonce', 'transtria_nonce' ) ) {
			return false;
		}
		
		$study_group = $_POST["this_study_group"];
		
		$dyad_array = get_dyads_for_study_group( $study_group );
		$study_data = get_study_level_for_intermediate( $study_group );
		$analysis_data = get_analysis_vars_for_group( $study_group );
		
		//set data
		$data[ 'intermediate_vars' ] = $dyad_array;
		$data[ 'intermediate_vars_study' ] = $study_data;
		$data[ 'analysis_vars' ] = $analysis_data;
		
		//echo json_encode( $data );
		echo json_encode( $data );
		
		die();
	
	}
	
	//ajax run analysis
	public function run_intermediate_analysis(){
		// Is the nonce good?
		if ( ! check_ajax_referer( 'cc_transtria_ajax_nonce', 'transtria_nonce' ) ) {
			return false;
		}
		
		$study_group = $_POST["this_study_group"];
		
		$study_id_array = set_dyads_for_study_group( $study_group );
		
		//$setting_dyad_test = get_unique_dyads_for_study( 346 );
		
		//echo json_encode( $study_id_array );
		echo json_encode( $study_id_array );
		
		die();
	
	}
	
	//ajax run analysis
	public function run_analysis(){
		// Is the nonce good?
		if ( ! check_ajax_referer( 'cc_transtria_ajax_nonce', 'transtria_nonce' ) ) {
			return false;
		}
		
		//get all I-M dyads for this Study Group; count duplicates once; 
		
		$study_group = $_POST["this_study_group"];
		
		$unique_ims = set_unique_analysis_ids_for_group( $study_group );
		
		//$setting_dyad_test = get_unique_dyads_for_study( 346 );
		
		//echo json_encode( $unique_ims );
		echo json_encode( 'run analysis extras' );
		
		die();
	
	}
	
	
} // End class