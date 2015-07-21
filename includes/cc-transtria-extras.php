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
		//add_action( 'bp_init', array( $this, 'set_metro_id_cookie_on_load'), 22 );
		
		// Checks requested analysis URL for specified metro_id. Sets cookie if not in agreement.
		//add_action( 'bp_init', array( $this, 'check_summary_metro_id_cookie_on_load'), 11 );

		//TODO: Transtria ajaxing goes here!
		// Adds ajax function for board-approved checkbox on the Health Analysis Rreport page ("interim" piece)
		add_action( 'wp_ajax_get_autocomplete_members' , array( $this, 'get_autocomplete_members' ) );
		

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

	
		//if ( cc_transtria_is_component() ) {
			wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'css/transtria-extras-tab.css', __FILE__ ), array(), '1.38' );
			wp_enqueue_style( 'jquery-ui', plugins_url( 'css/jquery-ui.css', __FILE__ ), array(), '1.00' );
		//}

		if ( cc_transtria_is_component() ) {
			wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'css/transtria-extras-tab.css', __FILE__ ), array(), '1.01' );
			wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'css/components.css', __FILE__ ), array(), '1.01' );
			wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'css/jquery.multiselect.css', __FILE__ ), array(), '1.01' );
			//Mel asks: do we need this one?
			wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'css/jquery.ptTimeSelect.css', __FILE__ ), array(), '1.01' );
		}

		
		/*
		if ( cc_aha_on_reports_screen() ){
			wp_enqueue_style('jquery-multiselect-css', plugins_url( 'css/jquery.multiselect.css', __FILE__ ), array(), '1.0' );
		}
		*/
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
			
			//requirements
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-ui-widget' );
			//wp_enqueue_script( 'jquery-ui-datepicker' );
			
			//our files
			
			wp_enqueue_script( $this->plugin_slug . 'multiselect', plugins_url( 'js/jquery.multiselect.min.js', __FILE__ ), array( 'jquery' ), '1.1' );
			wp_enqueue_script( $this->plugin_slug . 'tablesorter', plugins_url( 'js/tablesorter/jquery.tablesorter.min.js', __FILE__ ), array( 'jquery' ), '1.1' );
			wp_enqueue_script( $this->plugin_slug . 'datetimepicker', plugins_url( 'js/datetimepicker-master/jquery.datetimepicker.js', __FILE__ ), array( 'jquery' ), '1.1' );
			
			//wp_enqueue_script( $this->plugin_slug . 'dynamic_page_components', plugins_url( 'js/dynamic_components.js', __FILE__ ), array( 'jquery' ), '1.1' );
			//wp_enqueue_script( $this->plugin_slug . 'transtria_basic_js', plugins_url( 'js/transtria_basic.js', __FILE__ ), array( 'jquery' ), '1.1' );
			wp_enqueue_script( $this->plugin_slug . 'transtria_revamp_js', plugins_url( 'js/transtria_revamp.js', __FILE__ ), array( 'jquery' ), '1.0' );
			
			
			wp_localize_script( 
				$this->plugin_slug . '-plugin-script', 
				'transtria_ajax',
				array( 
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'ajax_nonce' => wp_create_nonce( 'cc_transtria_ajax_nonce' )
				)
			);
		}

		/*
		//Tab-specific enqueueing
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
		*/
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
    		echo bp_get_group_permalink( groups_get_group( array( 'group_id' => cc_transtria_get_group_id() ) ) );
    	?>">Transtria group</a> to get started. 
	    </p>
	    <?php
	    endif;
	}

	function registration_section_output() {
	  if ( isset( $_GET['aha'] ) && $_GET['aha'] ) :
	  ?>
	    <div id="aha-interest-opt-in" class="register-section checkbox">
		    <?php  $avatar = bp_core_fetch_avatar( array(
				'item_id' => cc_transtria_get_group_id(),
				'object'  => 'group',
				'type'    => 'thumb',
				'class'   => 'registration-logo',

			) ); 
			echo $avatar; ?>
	      <h4 class="registration-headline">Join the Group: <em>Transtria</em></h4>

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
	  		$redirect_to = bp_get_group_permalink( groups_get_group( array( 'group_id' => cc_transtria_get_group_id() ) ) );
	  	}

	  	return $redirect_to;
	}
	/**
	* Accept requests that come from members with @heart.org email addresses
	* @since 0.1
	*/
	function approve_member_requests( $user_id, $admins, $group_id, $membership_id ) {

		// For the AHA group, accept requests that come from members with @heart.org email addresses
		if ( cc_transtria_get_group_id() == $group_id ) {

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
			$label = 'Create a Transtria Report'; 
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
	
	
	
} // End class