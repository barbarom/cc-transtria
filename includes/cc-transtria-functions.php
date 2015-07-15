<?php 
/**
 * CC American Heart Association Extras
 *
 * @package   CC American Heart Association Extras
 * @author    CARES staff
 * @license   GPL-2.0+
 * @copyright 2014 CommmunityCommons.org
 */

/**
 * Are we on the AHA extras tab?
 *
 * @since   1.0.0
 * @return  boolean
 */
function cc_aha_is_component() {
    if ( bp_is_groups_component() && bp_is_current_action( cc_aha_get_slug() ) )
        return true;

    return false;
}

/**
 * Is this the AHA group?
 *
 * @since    1.0.0
 * @return   boolean
 */
function cc_aha_is_aha_group( $group_id = 0 ){
    if ( ! $group_id ) {
        $group_id = bp_get_current_group_id();
    }
    return ( $group_id == cc_aha_get_group_id() );
}

/**
 * Get the group id based on the context
 *
 * @since   1.0.0
 * @return  integer
 */
function cc_aha_get_group_id(){
    switch ( get_home_url() ) {
        case 'http://commonsdev.local':
            $group_id = 55;
            break;
		case 'http://localhost/cc_local':
            $group_id = 594;  //592
            break;
        case 'http://dev.communitycommons.org':
            $group_id = 592;
            break;
        default:
            $group_id = 594;
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
function cc_aha_get_slug(){
    return 'assessment';
}
function cc_aha_get_survey_slug(){
    return 'survey';
}
function cc_aha_get_quick_survey_summary_slug(){
    return 'quick-summary';
}
function cc_aha_get_analysis_slug(){
    return 'analysis';
}
function cc_aha_get_analysis_health_slug(){
    return 'health';
}
function cc_aha_get_analysis_revenue_slug(){
    return 'revenue';
}
function cc_aha_get_reports_slug(){
    return 'reports';
}
function cc_aha_get_board_level_report_slug(){
    return 'board-level';
}
function cc_aha_get_nat_level_report_slug(){
    return 'national-level';
}
function cc_aha_get_report_card_slug(){
    return 'report-card';
}
function cc_aha_get_revenue_report_card_slug(){
    return 'revenue-report-card';
}
//Phase II: Action Progress and Planning, Reports
function cc_aha_get_action_planning_slug(){
    return 'action-planning';
}
function cc_aha_get_action_planning_health_slug(){
    return 'health';
}
function cc_aha_get_action_planning_revenue_slug(){
    return 'revenue';
}
function cc_aha_get_action_planning_edit_slug(){
    return 'national-steps';
}
function cc_aha_get_action_plan_slug(){
    return 'action-plan';
}

/**
 * Get URIs for the various pieces of this tab
 * 
 * @return string URL
 */
function cc_aha_get_home_permalink( $group_id = false ) {
    $group_id = ( $group_id ) ? $group_id : bp_get_current_group_id() ;
    $permalink = bp_get_group_permalink( groups_get_group( array( 'group_id' => $group_id ) ) ) .  cc_aha_get_slug() . '/';
    return apply_filters( "cc_aha_home_permalink", $permalink, $group_id);
}
function cc_aha_get_survey_permalink( $page = 1, $group_id = false ) {
    $permalink = cc_aha_get_home_permalink( $group_id ) . cc_aha_get_survey_slug() . '/' . $page . '/';
    return apply_filters( "cc_aha_survey_permalink", $permalink, $group_id);
}
function cc_aha_get_analysis_permalink( $section = false, $metro_id = false ) {
    // If none is specified, we need to insert a placeholder, so that the bp_action_variables stay in the correct position.
    // if we pass a metro_id, it trumps all
    if ( $metro_id ) {
        $metro_id_string = $metro_id . '/';
    } else {
        $metro_id_string = ( $metro_id = $_COOKIE['aha_summary_metro_id'] ) ? $metro_id . '/' : '00000/';
    }

    // If we've specified a section, build it, else assume health.
    // Expects 'revenue' or 'health'
    $section_string = ( $section == 'revenue' ) ? cc_aha_get_analysis_revenue_slug() . '/' : cc_aha_get_analysis_health_slug() . '/';

    $permalink = cc_aha_get_home_permalink() . cc_aha_get_analysis_slug() . '/' . $metro_id_string . $section_string;
    return apply_filters( "cc_aha_analysis_permalink", $permalink, $section, $metro_id);
}

function cc_aha_get_reports_permalink( $group_id = false ) {

    //$permalink = cc_aha_get_home_permalink( $group_id ) . cc_aha_get_reports_slug();  //If we need intro text..
	
    $permalink = cc_aha_get_home_permalink( $group_id ) . cc_aha_get_reports_slug() . '/' . cc_aha_get_board_level_report_slug();
	return apply_filters( "cc_aha_get_reports_permalink", $permalink, $group_id);
}

function cc_aha_get_board_level_report_permalink( $group_id = false ) {

    $permalink = cc_aha_get_home_permalink( $group_id ) . cc_aha_get_reports_slug() . '/' . cc_aha_get_board_level_report_slug();
	return apply_filters( "cc_aha_get_board_level_report_permalink", $permalink, $group_id);
}
function cc_aha_get_nat_level_report_permalink( $group_id = false ) {

    $permalink = cc_aha_get_home_permalink( $group_id ) . cc_aha_get_reports_slug() . '/' . cc_aha_get_nat_level_report_slug();
	return apply_filters( "cc_aha_get_nat_level_report_permalink", $permalink, $group_id);
}
function cc_aha_get_report_card_permalink( $group_id = false ) {

    $permalink = cc_aha_get_home_permalink( $group_id ) . cc_aha_get_report_card_slug();
	return apply_filters( "cc_aha_report_card_permalink", $permalink, $group_id);
}

function cc_aha_get_revenue_report_card_permalink( $group_id = false ) {

    $permalink = cc_aha_get_home_permalink( $group_id ) . cc_aha_get_revenue_report_card_slug();
	return apply_filters( "cc_aha_revenue_report_card_permalink", $permalink, $group_id);
}

//Phase II: Action Progress and Planning, Reports
function cc_aha_get_action_planning_permalink( $metro_id = false  ) {

    // If none is specified, we need to insert a placeholder, so that the bp_action_variables stay in the correct position.
    // if we pass a metro_id, it trumps all
    if ( $metro_id ) {
        $metro_id_string = $metro_id . '/';
    } else {
        $metro_id_string = ( $metro_id = $_COOKIE['aha_action_planning_metro_id'] ) ? $metro_id . '/' : '00000/';
    }
	
    $permalink = cc_aha_get_home_permalink() . cc_aha_get_action_planning_slug() . '/' . $metro_id_string;
	
	return apply_filters( "cc_aha_action_planning_permalink", $permalink, $metro_id);
}

//read-only plan permalink
function cc_aha_get_action_plan_permalink( $metro_id = false  ) {

    // If none is specified, we need to insert a placeholder, so that the bp_action_variables stay in the correct position.
    // if we pass a metro_id, it trumps all
    if ( $metro_id ) {
        $metro_id_string = $metro_id . '/';
    } else {
        $metro_id_string = ( $metro_id = $_COOKIE['aha_action_plan_readonly_metro_id'] ) ? $metro_id . '/' : '00000/';
    }
	
    $permalink = cc_aha_get_home_permalink() . cc_aha_get_action_plan_slug() . '/' . $metro_id_string ;
	
	return apply_filters( "cc_aha_action_plan_permalink", $permalink, $metro_id);
}

//read-only plan permalink
//TODO: finish this or remove it!
function cc_aha_get_action_plan_permalink_w_priority( $metro_id = false, $priority = false  ) {

    // If none is specified, we need to insert a placeholder, so that the bp_action_variables stay in the correct position.
    // if we pass a metro_id, it trumps all
    if ( $metro_id ) {
        $metro_id_string = $metro_id . '/';
    } else {
        $metro_id_string = ( $metro_id = $_COOKIE['aha_action_plan_readonly_metro_id'] ) ? $metro_id . '/' : '00000/';
    }
	
    $permalink = cc_aha_get_home_permalink() . cc_aha_get_action_plan_slug() . '/' . $metro_id_string . '/' . $priority ;
	
	return apply_filters( "cc_aha_action_plan_permalink_w_priority", $permalink, $metro_id);
}

function cc_aha_get_report_card_sub_permalink( $group_id = false ) {

    $permalink = cc_aha_get_home_permalink( $group_id ) . cc_aha_get_reports_slug() . '/' . cc_aha_get_report_card_slug();
	return apply_filters( "cc_aha_get_report_card_sub_permalink", $permalink, $group_id);
}
function cc_aha_get_revenue_report_card_sub_permalink( $group_id = false ) {

    $permalink = cc_aha_get_home_permalink( $group_id ) . cc_aha_get_reports_slug() . '/' . cc_aha_get_revenue_report_card_slug();
	return apply_filters( "cc_aha_get_revenue_report_card_sub_permalink", $permalink, $group_id);
}
/**
 * Can this user fill out the assessment and such?
 * 
 * @return boolean
 */
function cc_aha_user_can_do_assessment(){
    // Only members who have an "@heart.org" email address (and site admins) are allowed to fill out the assessment 
    if ( ! is_user_logged_in() ) {
        return false;
    } else if ( current_user_can( 'delete_others_posts' ) ) {
        return true;
    } else {
        $current_user = wp_get_current_user();
        $email_parts = explode('@', $current_user->user_email);
        if ( $email_parts[1] == 'heart.org' ) {
            return true;
        } 
    }
    // If none of the above fired...
    return false;
}

/**
 * Super access for secret development
 * 
 * @return boolean
 */
function cc_aha_user_has_super_secret_clearance(){
    // Only members who have an "@heart.org" email address (and site admins) are allowed to fill out the assessment 
    if ( ! is_user_logged_in() ) {
        return false;
    } else if ( current_user_can( 'delete_others_posts' ) ) {
        return true;
    } else {
        $current_user = wp_get_current_user();
        if ( in_array( strtolower( $current_user->user_email ), cc_aha_super_secret_access_list() ) ) {
            return true;
        } 
    }
    // If none of the above fired...
    return false;
}

function cc_aha_super_secret_access_list(){
    return array(
            'ben.weittenhiller@heart.org', 'christian.caldwell@heart.org', 'johnsonange@missouri.edu', 'david.cavins+cassie@gmail.com', 'debartolomeom@mizzou.edu'
        );

}

function cc_aha_resolve_summary_metro_id(){
    // Cookies set with setcookie() aren't available until next page load. So we compare the URL to the cookie and see what's what. We prefer the URL info.

    // We need to compare to exclude action .

    if ( bp_action_variable( 1 ) != '00000' && $metro_id = bp_action_variable( 1 ) )
        return $metro_id;

    if ( $metro_id = $_COOKIE['aha_summary_metro_id'] )
        return $metro_id;

    return false;

}

/**
 * Where are we?
 * Checks for the various screens
 *
 * @since   1.0.0
 * @return  string
 */
function cc_aha_on_main_screen(){
    // There should be no action variables if on the main tab
    if ( cc_aha_is_component() && ! ( bp_action_variables() )  ){
        return true;
    } else {
        return false;
    }
}
function cc_aha_on_survey_screen(){
    if ( cc_aha_is_component() && bp_is_action_variable( cc_aha_get_survey_slug(), 0 ) ){
        return true;
    } else {
        return false;
    }
}
function cc_aha_on_analysis_screen( $section = null ){
    // If we're checking for a specific subsection, check for it.
    if ( $section && in_array( $section, array(  cc_aha_get_analysis_health_slug(), cc_aha_get_analysis_revenue_slug() ) ) ) {

        if ( cc_aha_is_component() && bp_is_action_variable( cc_aha_get_analysis_slug(), 0 ) && bp_is_action_variable( $section, 2 ) ){
            return true;
        } else {
            return false;
        }

    }

   if ( cc_aha_is_component() && bp_is_action_variable( cc_aha_get_analysis_slug(), 0 ) ){
        return true;
    } else {
        return false;
    }
}
function cc_aha_on_survey_quick_summary_screen(){
    if ( cc_aha_is_component() && bp_is_action_variable( cc_aha_get_quick_survey_summary_slug(), 0 ) ){
        return true;
    } else {
        return false;
    }
}
function cc_aha_on_analysis_complete_report_screen(){
   if ( cc_aha_is_component() && bp_is_action_variable( 'all', 3 ) ){
        return true;
    } else {
        return false;
    }
}
function cc_aha_on_reports_screen(){
	if ( cc_aha_is_component() && bp_is_action_variable( cc_aha_get_reports_slug(), 0 ) ){
        return true;
    } else {
        return false;
    }
}
function cc_aha_on_report_card_screen(){
	if ( cc_aha_is_component() && bp_is_action_variable( cc_aha_get_report_card_slug(), 0 ) ){
        return true;
    } else {
        return false;
    }
}
function cc_aha_on_revenue_report_card_screen(){
	if ( cc_aha_is_component() && bp_is_action_variable( cc_aha_get_revenue_report_card_slug(), 0 ) ){
        return true;
    } else {
        return false;
    }
}

//Phase II: Action Progress and Planning, Reports
function cc_aha_on_action_planning_screen(){
	if ( cc_aha_is_component() && bp_is_action_variable( cc_aha_get_action_planning_slug(), 0 ) ){
        return true;
    } else {
        return false;
    }
}
function cc_aha_on_action_plan_screen(){
	if ( cc_aha_is_component() && bp_is_action_variable( cc_aha_get_action_plan_slug(), 0 ) ){
        return true;
    } else {
        return false;
    }
}

function cc_aha_on_board_level_report_screen(){
	if ( cc_aha_is_component() && bp_is_action_variable( cc_aha_get_board_level_report_slug(), 1 ) ){
        return true;
    } else {
        return false;
    }
}
function cc_aha_on_nat_level_report_screen(){
	if ( cc_aha_is_component() && bp_is_action_variable( cc_aha_get_nat_level_report_slug(), 1 ) ){
        return true;
    } else {
        return false;
    }
}
function cc_aha_on_report_card_sub_screen(){
	if ( cc_aha_is_component() && bp_is_action_variable( cc_aha_get_report_card_slug(), 1 ) ){
        return true;
    } else {
        return false;
    }
}
function cc_aha_on_revenue_report_card_sub_screen(){
	if ( cc_aha_is_component() && bp_is_action_variable( cc_aha_get_revenue_report_card_slug(), 1 ) ){
        return true;
    } else {
        return false;
    }
}

/**
 * Retrieve a user's metro affiliation
 * 
 * @since   1.0.0
 * @return  array of metro IDs, empty array if none (helps with counting later)
 */
function cc_aha_get_array_user_metro_ids() {
    $selected = get_user_meta( get_current_user_id(), 'aha_board', true );

    if ( ! is_array( $selected ) )
        $selected = array();

    return $selected;
}
/**
 * Create a "nice" version of the metro's info
 * 
 * @since   1.0.0
 * @return  string
 */
function cc_aha_get_metro_nicename( $metro_id = null ) {
    if ( ! $metro_id )
        $metro_id = $_COOKIE[ 'aha_active_metro_id' ];

    if ( ! $metro_id )
        return "none selected";

    $metro = cc_aha_get_single_metro_data( $metro_id );

    return $metro['Board_Name'];
    // return $metro['Board_Name'] . ' &ndash; ' . $metro['BOARD_ID'];

}
/**
 * Find the human-readable option label for a question's saved response.
 * 
 * @since   1.0.0
 * @return  string
 */
function cc_aha_get_matching_option_label( $qid, $value ){

	$question = cc_aha_get_survey_answers_board( $qid );
	//var_dump( $question);
	return $question[ $value ];

	//2015 method, w questions/options in db
/*
    $question = cc_aha_get_question( $qid );
    $options = cc_aha_get_options_for_question( $qid );
    
    if ( $question[ 'type' ] == 'radio' ) {
        foreach ($options as $option) {
            if ( $option[ 'value' ] == $value ){
                $retval = $option[ 'label' ];
                break; // Once we have a match, we can stop.
            }
        }
    } else {
        // must be checkboxes
        $response_array = array_keys( maybe_unserialize( $value ) );
        $selected_options = array();

        foreach ($options as $option) {
            if ( in_array( $option[ 'value' ], $response_array ) ){
                $selected_options[] = $option[ 'label' ];
            }
        }
        $retval = implode(', ', $selected_options);
    }

    return $retval;
*/
}

/**
 * Check for a survey page's completeness by comparing the questions to the saved data
 * 
 * @since   1.0.0
 * @return  boolean
 */
function aha_survey_page_completed( $page, $board_data, $school_data ) {
    //$questions = cc_aha_get_form_questions( $page );
    $questions = questions_lookup( $page );
	//var_dump($questions);

    // Some pages need to be handled differently. 
    $form_pages = cc_aha_form_page_list();

    // CPR is weird. If the state has requirements, we don't need to ask.
    $cpr_page = array_search( 'Chain of Survival - CPR Graduation Requirements', $form_pages );
    if ( $page == $cpr_page && $board_data['5.1.1'] ){
        return true;
    }

    // Top 25 Companies is weird. We have no idea how "complete" this section is, since it's done off-site.
    $top_25 = array_search( 'Top 25 Companies', $form_pages ); 
    if ( $page == $top_25 ){
        return false;
    }

    //foreach ($questions as $question) {
    foreach ($questions['questions'] as $key => $question) {
        // Ignore the follow-up questions
        if ( $question['follows_up'] )
            continue;

		//TODO: rethink this for new lookup based question
        // If any of the data points are null (they might be 0, which is OK), we return false.
        //if ( $question['loop_schools'] ){
        if ( $question['level'] == 'school' ){
            // This data will be in the schools table. And we'll need to loop through
            foreach ($school_data as $district) {
                //if ( $district[ $question['QID'] ] == '' ) {
                if ( $district[ $key ] == '' ) {
                    return false;   
                }
            }
        } else {
            // This data will be in the board table
            //if ( $board_data[ $question['QID'] ] == '' ) {
            if ( $board_data[ $key ] == '' ) {
                return false;   
            }
        }
    }
    // If we make it out of the foreach, all is well.
    return true;
}

/**
 * Get the FIPS codes for a metro_id
 *
 * @since   1.0.0
 * @return  comma-delimited string.
 */ 
function cc_aha_get_fips( $cleaned = false ){
    if ( ! $metro_id = cc_aha_resolve_summary_metro_id() )
        return false;

    //MB added JSON service to get FIPS using selected metro id.
    $response = wp_remote_get( 'http://maps.communitycommons.org/api/service.svc/json/AHAfips/?metroid=' . $metro_id );

    //read JSON response
     if( is_array( $response ) ) {
         $r = wp_remote_retrieve_body( $response );
         $output = json_decode( $r, true );
         //var_dump($output);
         $fips = $output['getAHAfipsResult'][0]['fips'];
         $cleanedfips = str_replace('05000US','',$fips); 
         
         return ( $cleaned ) ? $cleanedfips : $fips;
     } 

}


/**
 * Not a statistically sound calculation of % of students that receive a certain number of PE minutes per week.
 *
 * @since   1.0.0
 * @arguments   $metro_id and $level which is one of elem, midd, high, or all
 * @return  integer (formatted like a percentage)
 */ 
function cc_aha_top_5_school_pe_calculation( $metro_id, $school_data, $level = 'all', $current_year_flag = true ) {

    //$school_data = cc_aha_get_school_data( $metro_id, $current_year_flag );
	
	//var_dump( $current_year_flag );
	//var_dump( $school_data );
	
	//last year will return "no data" if no data
	if( $school_data == "no data" ){
		return 'no data';
	}
	
    $total_pop = 0;
    $covered_pop = 0;
    foreach ( $school_data as $district ) {
        if ( $level == 'elem' || $level == 'all' ) {
            $total_pop = $total_pop + (int) $district['ELEM'];
            if ( $district['2.1.4.1.1'] ) {
                $covered_pop = $covered_pop + (int) $district['ELEM'];
            }
        }
        if ( $level == 'midd' || $level == 'all' ) {
            $total_pop = $total_pop + (int) $district['MIDD'];
            if ( $district['2.1.4.1.2'] ) {
                $covered_pop = $covered_pop + (int) $district['MIDD'];
            }
        }
        if ( $level == 'high' || $level == 'all' ) {
            $total_pop = $total_pop + (int) $district['HIGH'];
            if ( $district['2.1.4.1.3'] ) {
                $covered_pop = $covered_pop + (int) $district['HIGH'];
            }
        }
    }

    return ( $total_pop ) ? round( $covered_pop / $total_pop * 100 ) : 0;

}

/**
 * % of districts that have a particular value for a particular question.
 *
 * @since   1.0.0
 * @arguments   $metro_id and $level which is one of elem, midd, high, or all
 * @return  integer (formatted like a percentage)
 */ 
function cc_aha_top_5_school_percent_match_value( $metro_id, $qid, $value, $school_data, $current_year_flag = true ) {

    $school_data = cc_aha_get_school_data( $metro_id, $current_year_flag );
	
	//last year will return 0 if no data
	if( $school_data == "no data" ){
		return 'no data';
	}
	
    $matches = 0;
    $num_disticts = count( $school_data );
    foreach ( $school_data as $district ) {
        if ( $district[ $qid ] == $value )
            ++$matches;
    }

    return ( $num_disticts ) ? round( $matches / $num_disticts * 100 ) : 0;

}

/*
 * Returns array of members of AHA Group
 *
 * @params int Group_ID
 * @return array Array of Member ID => name
 */
function cc_aha_get_member_array( ){

	global $bp;
	$group_id = cc_aha_get_group_id();
	
	$group = groups_get_group( array( 'group_id' => $group_id ) );
	//var_dump($group);
	
	//set up group member array for drop downs
	$group_members = array();
	if ( bp_group_has_members( array( 'group_id' => $group_id, 'per_page' => 9999 ) ) ) {
	
		//iterate through group members, creating array for form list (drop down)
		while ( bp_group_members() ) : bp_group_the_member(); 
			$group_members[bp_get_group_member_id()] = bp_get_group_member_name();
		endwhile; 
		
		//var_dump ($group_members);  //works!
	}
	
	return $group_members;
	
}

/*
 * Returns associative array of members of AHA Group
 *
 * @params int Group_ID
 * @return array Array of Member ID => name
 */
function cc_aha_get_member_array_autocomplete( ){

	global $bp;
	$group_id = cc_aha_get_group_id();
	
	$group = groups_get_group( array( 'group_id' => $group_id ) );
	//var_dump($group);
	
	//set up group member array for drop downs
	$group_members = array();
	
	$group_json_values = array();


	if ( bp_group_has_members( array( 'group_id' => $group_id, 'per_page' => 9999 ) ) ) {
	
		//iterate through group members, creating array for form list (drop down)
		while ( bp_group_members() ) : bp_group_the_member(); 
			$user_info = get_userdata( bp_get_group_member_id() );
			$user_email = $user_info->user_email;
			
			$group_members[bp_get_group_member_id()] = bp_get_group_member_name() . " \r\n(" . $user_email . ")";
		endwhile; 
		
		//var_dump ($group_members);  //works!
	}
	
	foreach ($group_members as $k => $v) {
		$group_json_values[] = array(
			'label' => $v,   //This is the label show
			'value' => (string)$k    //This is the value setted
		);
	}
	
	return $group_json_values;
	
}


/*
 * Returns flat array of member names of AHA Group
 *
 * @params int Group_ID
 * @return array Array of Member name
 */
function cc_aha_get_member_names_array( ){

	global $bp;
	$group_id = cc_aha_get_group_id();
	
	$group = groups_get_group( array( 'group_id' => $group_id ) );
	//var_dump($group);
	
	//set up group member array for drop downs
	$group_members = array();
	
	$group_json_values = array();


	if ( bp_group_has_members( array( 'group_id' => $group_id, 'per_page' => 9999 ) ) ) {
	
		//iterate through group members, creating array for form list (drop down)
		while ( bp_group_members() ) : bp_group_the_member(); 
			$user_info = get_userdata( bp_get_group_member_id() );
			$user_email = $user_info->user_email;
			
			$group_members[bp_get_group_member_id()] = bp_get_group_member_name() . " (" . $user_email . ")";
		endwhile; 
		
		//var_dump ($group_members);  //works!
	}
	
	foreach ($group_members as $k => $v) {
		array_push( $group_json_values, $v );
	}
	
	return $group_json_values;
	
}

/*
 * Returns array of members of AHA Group for current board
 *
 * @params int Group_ID
 * @return array Array of Member ID => name
 */
function cc_aha_get_member_array_current_board( ){

	if ( ! $metro_id = cc_aha_resolve_summary_metro_id() )
		return false;
		
	global $bp;
	$group_id = cc_aha_get_group_id();
	
	$group = groups_get_group( array( 'group_id' => $group_id ) );
	//var_dump($group);
	
	//set up group member array for drop downs
	$group_members = array();
	$member_boards = array();
	if ( bp_group_has_members( array( 'group_id' => $group_id, 'per_page' => 9999 ) ) ) {
		//var_dump( $group_id );
		//iterate through group members, creating array for form list (drop down)
		while ( bp_group_members() ) : bp_group_the_member(); 
			//var_dump( bp_get_group_member_id() );
			//if this member has
			$member_boards = maybe_unserialize( current( get_user_meta( bp_get_group_member_id(), "aha_board", false ) ) );
			//$member_boards = maybe_unserialize( get_user_meta( bp_get_group_member_id(), "aha_board", false ) );
			//var_dump( $member_boards );
			if( is_array( $member_boards ) ) {
				if( in_array( $metro_id, $member_boards ) ){
					$group_members[ bp_get_group_member_id() ] = bp_get_group_member_name();
				}
			}
			
		endwhile; 
		
		//var_dump ($group_members);  //works!
	}
	
	return $group_members;
	
}

/* 
 * Returns fields for National Plan (TODO: round this out w questions, field type, etc
 *
 * @return array
 */
function cc_aha_get_national_plan_fields(){
	$fields = array(
		'shortterm_objective_answer', 'intermediate_objective_answer', 'longterm_objective_answer', 
		'strengths_answer', 'weaknesses_answer', 'opportunities_answer', 'threats_answer', 
		'allies_answer', 'constituents_answer', 'opponents_answer',
		'primary_targets_answer', 'secondary_targets_answer',
		'resources_onhand_answer', 'resources_needed_answer'
	);
	
	return $fields;
}
