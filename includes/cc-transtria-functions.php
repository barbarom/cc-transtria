<?php 
/**
 * CC Transtria Extras
 *
 * @package   CC Transtria Extras
 * @author    CARES staff
 * @license   GPL-2.0+
 * @copyright 2015 CommmunityCommons.org
 */

/**
 * Are we on the transtria extras tab?
 *
 * @since   1.0.0
 * @return  boolean
 */
function cc_transtria_is_component() {
    if ( bp_is_groups_component() && bp_is_current_action( cc_transtria_get_slug() ) )
        return true;

    return false;
}

/**
 * Is this the transtria group?
 *
 * @since    1.0.0
 * @return   boolean
 */
function cc_transtria_is_transtria_group( $group_id = 0 ){
    if ( ! $group_id ) {
        $group_id = bp_get_current_group_id();
    }
    return ( $group_id == cc_transtria_get_group_id() );
}

/**
 * Get the group id based on the context
 *
 * @since   1.0.0
 * @return  integer
 */
function cc_transtria_get_group_id(){
    switch ( get_home_url() ) {
        case 'http://localhost/wordpress':
            $group_id = 597;  //Mike's machine
            break;
		case 'http://localhost/cc_local':
            $group_id = 690;  //Mel's compy
            break;
        case 'http://dev.communitycommons.org':
            $group_id = 592; //TODO
            break;
        default:
            $group_id = 594;  //TODO
            break;
    }
    return $group_id;
}

/**
 * Get various slugs
 * These are gathered here so when, inevitably, we have to change them, it'll be simple
 *
 * @since   1.0.0
 * @return  string
 */
function cc_transtria_get_slug(){
    return 'study-form';
}
function cc_transtria_get_assignments_slug(){
    return 'assignments';
}
function cc_transtria_get_quick_survey_summary_slug(){
    return 'quick-summary';
}
function cc_transtria_get_analysis_slug(){
    return 'analysis';
}


/**
 * Get URIs for the various pieces of this tab
 * 
 * @return string URL
 */
function cc_transtria_get_home_permalink( $group_id = false ) {
    $group_id = ( $group_id ) ? $group_id : bp_get_current_group_id() ;
    $permalink = bp_get_group_permalink( groups_get_group( array( 'group_id' => $group_id ) ) ) .  cc_transtria_get_slug() . '/';
    return apply_filters( "cc_transtria_home_permalink", $permalink, $group_id);
}
function cc_transtria_get_assignments_permalink( $page = 1, $group_id = false ) {
    $permalink = cc_transtria_get_home_permalink( $group_id ) . cc_transtria_get_assignments_slug() . '/' . $page . '/';
    return apply_filters( "cc_transtria_assignments_permalink", $permalink, $group_id);
}
function cc_transtria_get_analysis_permalink( $section = false, $metro_id = false ) {
    $permalink = cc_transtria_get_home_permalink( $group_id ) . cc_transtria_get_analysis_slug() . '/' . $page . '/';
    return apply_filters( "cc_transtria_analysis_permalink", $permalink, $group_id);

    // If we've specified a section, build it, else assume health.
    // Expects 'revenue' or 'health'
    //$section_string = ( $section == 'revenue' ) ? cc_transtria_get_analysis_revenue_slug() . '/' : cc_transtria_get_analysis_health_slug() . '/';

    // $permalink = cc_transtria_get_home_permalink() . cc_transtria_get_analysis_slug() . '/' . $metro_id_string . $section_string;
    // return apply_filters( "cc_transtria_analysis_permalink", $permalink, $section, $metro_id);
}



/**
 * Where are we?
 * Checks for the various screens
 *
 * @since   1.0.0
 * @return  string
 */
function cc_transtria_on_main_screen(){
    // There should be no action variables if on the main tab
    if ( cc_transtria_is_component() && ! ( bp_action_variables() )  ){
        return true;
    } else {
        return false;
    }
}
function cc_transtria_on_assignments_screen(){
    if ( cc_transtria_is_component() && bp_is_action_variable( cc_transtria_get_assignments_slug(), 0 ) ){
        return true;
    } else {
        return false;
    }
}
function cc_transtria_on_analysis_screen( $section = null ){
    // If we're checking for a specific subsection, check for it.
    // if ( $section && in_array( $section, array(  cc_transtria_get_analysis_slug(), cc_transtria_get_analysis_revenue_slug() ) ) ) {

        // if ( cc_transtria_is_component() && bp_is_action_variable( cc_transtria_get_analysis_slug(), 0 ) && bp_is_action_variable( $section, 2 ) ){
            // return true;
        // } else {
            // return false;
        // }

    // }

   if ( cc_transtria_is_component() && bp_is_action_variable( cc_transtria_get_analysis_slug(), 0 ) ){
        return true;
    } else {
        return false;
    }
}
function cc_transtria_on_survey_quick_summary_screen(){
    if ( cc_transtria_is_component() && bp_is_action_variable( cc_transtria_get_quick_survey_summary_slug(), 0 ) ){
        return true;
    } else {
        return false;
    }
}
function cc_transtria_on_analysis_complete_report_screen(){
   if ( cc_transtria_is_component() && bp_is_action_variable( 'all', 3 ) ){
        return true;
    } else {
        return false;
    }
}


