<?php
/*
Plugin Name: CC Transtria
Description: Builds the monstrous Transtria Form
Version: 1.0
*/
/**
 * CC Transtria
 *
 * @package   CC Transtria
 * @author    CARES staff
 * @license   GPL-2.0+
 * @copyright 2015 CommmunityCommons.org
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

function cc_transtria_class_init(){
	// Get the class fired up
	// Helper and utility functions
	require_once( dirname( __FILE__ ) . '/includes/cc-aha-functions.php' );
	// Template-y functions
	require_once( dirname( __FILE__ ) . '/includes/cc-aha-template-tags.php' );
	
	//lookup files for the assessment/survey questions
	//TODO: determine whether to auto-include appropriate files or specify functions by year...
	//require_once( dirname( __FILE__ ) . '/includes/lookups/fy2015_health.php' );
	require_once( dirname( __FILE__ ) . '/includes/lookups/fy2016_health_revenue.php' );
	
	// Template functions for the form
	require_once( dirname( __FILE__ ) . '/includes/cc-aha-survey-template-tags.php' );
	
	// Template functions for the summary pages
	require_once( dirname( __FILE__ ) . '/includes/cc-aha-summary-template-tags.php' );
	require_once( dirname( __FILE__ ) . '/includes/cc-aha-summary-template-tags-2.php' );
	require_once( dirname( __FILE__ ) . '/includes/cc-aha-summary-template-tags-revenue.php' );
	
	//Template functions for the Summary Report Card, Revenue Report Card
	require_once( dirname( __FILE__ ) . '/includes/cc-aha-report-card-template-tags.php' );
	require_once( dirname( __FILE__ ) . '/includes/cc-aha-all-revenue-report-card-template-tags.php' );
	//Template functions for Action Planning
	require_once( dirname( __FILE__ ) . '/includes/cc-aha-action-planning-template-tags.php' );

	//Template functions for the Reports pages
	require_once( dirname( __FILE__ ) . '/includes/cc-aha-reports-template-tags.php' );
	
	//Template functions for the readonly Action Plan
	require_once( dirname( __FILE__ ) . '/includes/cc-aha-action-plan-readonly-template-tags.php' );

	// Database helper functions
	require_once( dirname( __FILE__ ) . '/includes/cc-transtria-database-bridge.php' );
	
	// The main class
	require_once( dirname( __FILE__ ) . '/includes/cc-aha-extras.php' );
	add_action( 'bp_include', array( 'CC_AHA_Extras', 'get_instance' ), 21 );
	
	
	//Mel...is this overkill
	global $wpdb;
	
	//Read only tables 
    $wpdb->aha_assessment_questions = $wpdb->prefix . 'aha_assessment_questions';
    $wpdb->aha_assessment_q_options = $wpdb->prefix . 'aha_assessment_q_options';
    $wpdb->aha_assessment_counties = $wpdb->prefix . 'aha_assessment_counties';
    $wpdb->aha_assessment_complete_streets = $wpdb->prefix . 'aha_assessment_complete_streets';
    $wpdb->aha_assessment_hospitals = $wpdb->prefix . 'aha_assessment_hospitals';
    $wpdb->aha_assessment_hospitals_worse_than_avg = $wpdb->prefix . 'aha_assessment_hospitals_worse_than_avg';
    $wpdb->aha_assessment_hospitals_top_opp = $wpdb->prefix . 'aha_assessment_hospitals_top_opp';
    $wpdb->aha_assessment_ssb = $wpdb->prefix . 'aha_assessment_ssb';
	
	//write-to tables
    $wpdb->aha_assessment_school = $wpdb->prefix . 'aha_assessment_school';
    $wpdb->aha_assessment_board = $wpdb->prefix . 'aha_assessment_board';
	
	//TODO: is this the best way?  Manual? Document me!
	//last year's tables
	$wpdb->last_year_board = $wpdb->prefix . 'aha_assessment_board_2015';
	$wpdb->last_year_school = $wpdb->prefix . 'aha_assessment_school_2015'; //TODO update this when school table done
}
add_action( 'bp_include', 'cc_transtria_class_init' );

/* Only load the component if BuddyPress is loaded and initialized. */
function startup_aha_extras_group_extension_class() {
	require( dirname( __FILE__ ) . '/includes/class-bp-group-extension.php' );
}
add_action( 'bp_include', 'startup_aha_extras_group_extension_class', 24 );