<?php
/**
 * CC Transtria Extras
 *
 * @package   CC American Heart Association Extras
 * @author    CARES staff
 * @license   GPL-2.0+
 * @copyright 2014 CommmunityCommons.org
 */


/**
 * Returns array of questions based on page number (not updated)
 *
 * @since    1.0.0
 * @return 	array
 */
function cc_aha_get_questions( $metro_id, $page = 1 ){
	global $wpdb;
	$question_sql = 
		"
		SELECT * 
		FROM $wpdb->aha_assessment_questions
		WHERE page_number = $page
		";
		
	$form_rows = $wpdb->get_results( $question_sql, OBJECT );
	return $form_rows;

}

/**
 * Returns array of saved form data by metro id for the page being built.
 *
 * @since    1.0.0
 * @return 	array
 */
function cc_aha_get_form_data( $metro_id, $current_year = true ){

	global $wpdb;
	 
	//get board data from database
	//$table_name = "wp_aha_assessment_board";
	if( $current_year ){
		$form_rows = $wpdb->get_results( 
			$wpdb->prepare( 
			"
			SELECT * 
			FROM $wpdb->aha_assessment_board
			WHERE BOARD_ID = %s
			",
			$metro_id )
			, ARRAY_A
		);
	} else {
		$form_rows = $wpdb->get_results( 
			$wpdb->prepare( 
			"
			SELECT * 
			FROM $wpdb->last_year_board
			WHERE BOARD_ID = %s
			",
			$metro_id )
			, ARRAY_A
		);
	}

	// Grab the first array only
	$row = current( $form_rows );
	
	if( empty( $row ) ){
		return "no data";
	}

	// Process the data to remove escape characters and return
	return array_map( 'stripslashes', $row );
}

/**
 * Returns array of arrays of school district data by metro id.
 *
 * @since    1.0.0
 * @return 	array of arrays
 */
function cc_aha_get_school_data( $metro_id, $current_year = true  ){
	global $wpdb;
	
	//var_dump( $prior_year );
	//so we will return some data for the moment
	//TODO: prior year table once defined!
	if( $current_year ){
		$form_rows = $wpdb->get_results( 
			$wpdb->prepare( 
			"
			SELECT * 
			FROM $wpdb->aha_assessment_school
			WHERE AHA_ID = %s
			",
			$metro_id )
			, ARRAY_A
		);
	} else {
		$form_rows = $wpdb->get_results( 
			$wpdb->prepare( 
			"
			SELECT * 
			FROM $wpdb->last_year_school
			WHERE AHA_ID = %s
			",
			$metro_id )
			, ARRAY_A
		);
	}
	
	//var_dump( $form_rows);
	//if we have no data	
	if( empty( $form_rows ) ){
		return "no data";
	}
	
	//print_r( $form_rows );
	return $form_rows;
}

/**
 * Returns array of all saved board data. 
 *
 * @since    1.0.0
 * @return 	array
 */
function cc_aha_get_all_board_data( ){

	global $wpdb;
	 
	//get board data from database
	//$table_name = "wp_aha_assessment_board";
	$form_rows = $wpdb->get_results( 
		$wpdb->prepare( 
		"
		SELECT * 
		FROM $wpdb->aha_assessment_board
		",
		$metro_id )
		, ARRAY_A
	);

	return $form_rows;
}

/**
 * Returns array of all states in board table. 
 *
 * @since    1.0.0
 * @return 	array
 */
function cc_aha_get_unique_board_states( ){

	global $wpdb;
	
	$form_col = $wpdb->get_col( 
		$wpdb->prepare( 
		"
		SELECT State
		FROM $wpdb->aha_assessment_board
		",
		$metro_id )
	);

	$form_col = array_unique( $form_col );
	sort( $form_col );
	return $form_col;
}

/**
 * Returns array of all affiliates in board table. 
 *
 * @since    1.0.0
 * @return 	array
 */
function cc_aha_get_unique_board_affiliates( ){

	global $wpdb;
	
	$form_col = $wpdb->get_col( 
		$wpdb->prepare( 
		"
		SELECT Affiliate
		FROM $wpdb->aha_assessment_board
		",
		$metro_id )
	);

	$form_col = array_unique( $form_col );
	sort( $form_col );
	return $form_col;
}

/**
 * Returns array of school district data by qid and metro id. - is this too specific?
 *
 * @since   1.0.0
 * @return 	associative array $summary_responses Array of [ district name ] [ summary-labelled answer ]
 */
function cc_aha_get_assessment_school_results( $metro_id, $qid ){
	global $wpdb;
	
	//gosh, this is one ugly sql function, so let's split it up
	
	//get dist_name, qid (it's columns, grr) and values from school table
	$dist_data = cc_aha_get_school_data( $metro_id );
	//print_r( $dist_data );
	
	//get summary_label, qid, value from options table
	// could foreach this (or what is more clever...) if >1 $qid
	$options_data = $wpdb->get_results( 
		$wpdb->prepare( 
		"
		SELECT value, summary_value, summary_label 
		FROM $wpdb->aha_assessment_q_options
		WHERE qid = %s
		",
		$qid )
		, ARRAY_A
	);
	
	print_r( $options_data );
	
	//return $form_rows;
}

/**
 * Updates board and school database tables with answers from survey.
 *
 * Takes $_POST arrays of [board] and [school] on form submit,
 *	makes sure their values aren't null, false or empty (so we don't overwrite values
 *	that weren't set in the survey) and wpdb->update s the respective table 
 *
 * @since    1.0.0
 * @param 	array
 * @return	
 */
function cc_aha_update_form_data( $board_id = null ){
	
	// $towrite = PHP_EOL . '$_POST: ' . print_r( $_POST, TRUE);
	// $fp = fopen("c:\\xampp\\logs\\aha_log.txt", 'a');
	// fwrite($fp, $towrite);
	// fclose($fp);

	global $wpdb;
		
	//get our board vars for the wpdb->update statement
	// If we haven't supplied a board ID, use the cookie setting
	//TODO: Check saving summary responses with two diff cookie vals.
	if ( ! $board_id )
		$board_id = $_COOKIE['aha_active_metro_id']; // 'BOARD_ID' column in wp_aha_assessment_board; our WHERE clause
	$board_table_name = $wpdb->aha_assessment_board;
	$board_where = array(
		'BOARD_ID' => $board_id 
	);
	
	//get key => value pairs for $_POST['board']!
	$update_board_data = array();
	$update_board_data = $_POST['board'];
	$numeric_inputs = cc_aha_get_number_type_questions();
	
	$followup_question = array();
	 
	// Input data cleaning
	foreach ($update_board_data as $key => $value) {
		
		//just triple-checking to make sure we won't clear out OTHER followup values of non-displayed questions..
		// Serialize data if necessary
		if ( is_array( $value ) )
			$update_board_data[ $key ] = maybe_serialize( $value );

		// Strip dollar signs and percent signs from numeric entries
		if ( in_array( $key, $numeric_inputs ) )
			$update_board_data[ $key ] = str_replace( array( '$', '%', ','), '', $value);
		
		//Empty disabled form fields in the db (or those that ARE followups whose followee question option != followup_id of $this)
		
		//Get followup questions for this question (TODO: make sure this assumption-of-one for board is valid)
		$followup_question = current( cc_aha_get_follow_up_questions( $key ) ); //we'll only ever have one, yes?  Or is this not safe enough?
		
		$followup_question_id =  $followup_question[ 'QID' ];		
		
		//if we have a followup question, let's see if it's value is $_POSTed and, if not, set it to update to NULL in the db
		// if the input has disabled attribute, it won't submit
		if ( !empty( $followup_question_id ) ) {
			//get the value of the followup question
			$followup_question_value = $update_board_data[ $followup_question_id ];
			
			//if there IS no value to a followup question, it's been disabled
			if ( empty( $followup_question_value ) && ( $followup_question_value != '0' ) ) { //because 0 means empty to PHP
			
				//update the value in the db to NULL
				$update_board_data[ $followup_question_id ] = NULL;
			}
		}
	}
	 
	//if we have [board] values set by the form, update the table
	// wpdb->update is perfect for this. Wow. Ref: https://codex.wordpress.org/Class_Reference/wpdb#UPDATE_rows
	if ( !empty ( $update_board_data ) ) {
		$num_board_rows_updated = $wpdb->update( $board_table_name, $update_board_data, $board_where, $format = null, $where_format = null );
	}
	
	//get our school vars for the wpdb->update statement
	$school_table_name = $wpdb->aha_assessment_school;
	
	//get key => value pairs for $_POST['school']!
	$update_school_data = array();
	$update_school_data = $_POST['school'];
	
	$followup_questions = array();
	$nested_followup_questions = array();
	
	//foreach district in survey, update db
	foreach ( $update_school_data as $key => $value ){
		
		$district_id = $key;
		
		//set where clause with this district and board
		$school_where = array(
			'AHA_ID' => $board_id,
			'DIST_ID' => $district_id
		);
		
		//the array in value is the district-specific data
		$update_school_dist_data = $value;

		// Input data cleaning - WPDB does the heavy lifting 
		foreach ($update_school_dist_data as $key => $value) {
			// Serialize data if necessary
			if ( is_array( $value ) )
				$update_school_dist_data[ $key ] = maybe_serialize( $value );

			// Strip dollar signs and percent signs from numeric entries
			if ( in_array( $key, $numeric_inputs ) )
				$update_school_dist_data[ $key ] = str_replace( array( '$', '%', ','), '', $value);
				
				
			/** Set currently-disabled form fields to NULL in the db update 
			 **(or those that ARE followups whose followee question option != followup_id of them.  Niner.) **/
		
			//Get followup questions for this question - in school, there are multiple.  Should rollout to board if necessary
			$followup_questions =  cc_aha_get_follow_up_questions( $key ); 
			
			foreach( $followup_questions as $followup_question ) {
				//Get the followup question id
				$followup_question_id =  $followup_question[ 'QID' ];
				
				//if we have a followup question, let's see if it's value is $_POSTed and, if not, set it to update to NULL in the db
				// if the input has disabled attribute, it won't submit
				if ( !empty( $followup_question_id ) ) {
					//get the value of the followup question
					$followup_question_value = $update_school_dist_data[ $followup_question_id ];
					//$towrite .= 'not empty id: ' . print_r($followup_question_id, TRUE) . ', value: ' . $followup_question_value;
					
					//if there IS no value to a followup question, it's been disabled
					if ( empty( $followup_question_value ) && ( $followup_question_value != '0' ) ) {
						//update the value in the db to NULL
						$update_school_dist_data[ $followup_question_id ] = NULL;
					}
				}
				
				//Do we have nested followup questions?
				$nested_followup_questions = cc_aha_get_follow_up_questions ( $followup_question_id );
				
				//disabled is not being properly set on some of these...maybe just hard-code for now, since it's one case
				
				foreach( $nested_followup_questions as $nested_followup_question ) {
					
					$nested_followup_question_id =  $nested_followup_question[ 'QID' ];
					//$towrite .= 'Nested followup question id: ' . print_r( $nested_followup_question_id, TRUE ) . "\r\n";
					//$towrite .= 'Nested followup question: ' . print_r( $nested_followup_question, TRUE ) . "\r\n";
					
					//if we have a followup question, let's see if it's value is $_POSTed and, if not, set it to update to NULL in the db
					// if the input has disabled attribute, it won't submit
					if ( !empty( $nested_followup_question_id ) ) {
						//get the value of the followup question
						$nested_followup_question_value = $update_school_dist_data[ $nested_followup_question_id ];
						//$towrite .= 'not empty id: ' . print_r($nested_followup_question_id, TRUE) . ', value: ' . $nested_followup_question_value;
						
						//if there IS no value to a followup question, it's been disabled
						if ( empty( $nested_followup_question_value ) && ( $nested_followup_question_value != '0' ) ) {
							//update the value in the db to NULL
							//$towrite .= '   IS EMPTY' . "\r\n";
							$update_school_dist_data[ $nested_followup_question_id ] = NULL;
						}
					}
				}
								
			}

		}

		//update the table for this district
		$num_school_rows_updated = $wpdb->update( $school_table_name, $update_school_dist_data, $school_where, $format = null, $where_format = null );
	
	}

	
	//$towrite .= print_r($update_board_data, TRUE);
	//$fp = fopen("c:\\xampp\\logs\\aha_log.txt", 'a');
	//fwrite($fp, $towrite);
	//fclose($fp);

	//wpdb->update returns num rows on success, 0 if no data change, FALSE on error
	//either wpdb->update return error?
	if ( $num_board_rows_updated === FALSE || $num_school_rows_updated === FALSE  ) {
		return false; //we have a problem updating
	} else {
		return ( $num_board_rows_updated ); 
	}

}

/**
 * Returns array of arrays of questions to build for the requested page of the form.
 *
 * @since    1.0.0
 * @return 	array of arrays
 */
function cc_aha_get_form_questions( $page = 1 ){
	global $wpdb;
	
	$questions = $wpdb->get_results( 
		$wpdb->prepare( 
		"
		SELECT * 
		FROM $wpdb->aha_assessment_questions
		WHERE page = %d
		",
		$page )
		, ARRAY_A
	);

	return $questions;
}

/**
 * Returns array of arrays of all questions that should appear on form.
 * Questions with a page of 0 shouldn't appear on the form
 *
 * @since    1.0.0
 * @return 	array of arrays
 */
function cc_aha_get_all_form_questions(){
	global $wpdb;
	
	$questions = $wpdb->get_results( 
		"
		SELECT * 
		FROM $wpdb->aha_assessment_questions
		WHERE page != 0
		",
		ARRAY_A
	);

	return $questions;
}

/**
 * Returns single question info.
 *
 * @since    1.0.0
 * @return 	array 
 */
function cc_aha_get_question( $qid ){
	global $wpdb;
	
	$question = $wpdb->get_results( 
		$wpdb->prepare( 
		"
		SELECT * 
		FROM $wpdb->aha_assessment_questions
		WHERE QID = %s
		",
		$qid )
		, ARRAY_A
	);

	return current( $question );
}

/**
 * Returns array of arrays of questions to build for the requested page of the form.
 *
 * @since    1.0.0
 * @return 	array of arrays
 */
function cc_aha_get_options_for_question( $qid ){
	global $wpdb;
	
	$options = $wpdb->get_results( 
		$wpdb->prepare( 
		"
		SELECT * 
		FROM $wpdb->aha_assessment_q_options
		WHERE qid = %s
		",
		$qid )
		, ARRAY_A
	);

	return $options;

}

/**
 * Returns array of arrays of followup questions.
 *
 * @since    1.0.0
 * @return 	array of arrays
 */
function cc_aha_get_follow_up_questions( $qid ){
	global $wpdb;
	
	$questions = $wpdb->get_results( 
		$wpdb->prepare( 
		"
		SELECT * 
		FROM $wpdb->aha_assessment_questions
		WHERE follows_up = %s
		",
		$qid )
		, ARRAY_A
	);

	return $questions;
}

/**
 * Get question IDs of type=number questions.
 * Used for data validation
 *
 * @since    1.0.0
 * @return 	array of question IDs
 */
function cc_aha_get_number_type_questions(){
	global $wpdb;
	
	$questions = $wpdb->get_col( 
		"
		SELECT * 
		FROM $wpdb->aha_assessment_questions
		WHERE type = 'number'
		"
		, 2
	);

	return $questions;

}

/**
 * Fetch array of arrays of all Metro IDs and names, affiliate only
 * 
 * @since   1.0.0
 * @return  array of arrays
 */
function cc_aha_get_metro_id_array(){
	global $wpdb;

	$metros = $wpdb->get_results( 
		"
		SELECT BOARD_ID, Board_Name, Affiliate
		FROM $wpdb->aha_assessment_board
		ORDER BY BOARD_ID
		", ARRAY_A
	);
	
	return $metros;
}

/**
 * Returns info array for single Metro ID.
 *
 * @since   1.0.0
 * @return 	array
 */
function cc_aha_get_single_metro_data( $metro_id ){
	global $wpdb;
	 
	$metro = $wpdb->get_results( 
		$wpdb->prepare( 
		"
		SELECT BOARD_ID, Board_Name, State, State2, Affiliate, Nearest_MSA
		FROM $wpdb->aha_assessment_board
		WHERE BOARD_ID = %s
		",
		$metro_id )
		, ARRAY_A
	);
	
	// We want to return a single result, so the first will do.
	return current( $metro );
}

/**
 * Returns all the counties sharing a metro ID.
 *
 * @since   1.0.0
 * @return 	array of arrays
 */
function cc_aha_get_county_data( $metro_id ){
	global $wpdb;
	 
	$counties = $wpdb->get_results( 
		$wpdb->prepare( 
		"
		SELECT *
		FROM $wpdb->aha_assessment_counties
		WHERE board_id = %s
		",
		$metro_id )
		, ARRAY_A
	);
	
	return $counties;
}

/**
 * Returns all the complete streets entries for a metro ID.
 *
 * @since   1.0.0
 * @return 	array
 */
function cc_aha_get_complete_streets_data( $metro_id ){
	global $wpdb;
	 
	$results = $wpdb->get_results( 
		$wpdb->prepare( 
		"
		SELECT *
		FROM $wpdb->aha_assessment_complete_streets
		WHERE board_id = %s
		",
		$metro_id )
		, ARRAY_A
	);
	
	return $results;
}

/**
 * Returns all the hospital entries for a metro ID.
 *
 * @since   1.0.0
 * @return 	array
 */
function cc_aha_get_hospital_data( $metro_id ){
	global $wpdb;
	 
	$results = $wpdb->get_results( 
		$wpdb->prepare( 
		"
		SELECT *
		FROM $wpdb->aha_assessment_hospitals
		WHERE `BOARD_ID` = %s
		",
		$metro_id )
		, ARRAY_A
	);
	
	return $results;
}

/**
 * Returns all the Top Opportunity hospital entries for a metro ID.
 *
 * @since   1.0.0
 * @return 	array
 */
function cc_aha_get_hospital_data_top_opp( $metro_id ){
	global $wpdb;
	 
	$results = $wpdb->get_results( 
		$wpdb->prepare( 
		"
		SELECT *
		FROM $wpdb->aha_assessment_hospitals_top_opp
		WHERE `Board_ID` = %s
		",
		$metro_id )
		, ARRAY_A
	);
	
	return $results;
}

/**
 * Returns all the Worse than Average hospital entries for a metro ID.
 *
 * @since   1.0.0
 * @return 	array
 */
function cc_aha_get_hospital_data_worse_than_avg( $metro_id ){
	global $wpdb;
	 
	$results = $wpdb->get_results( 
		$wpdb->prepare( 
		"
		SELECT *
		FROM $wpdb->aha_assessment_hospitals_worse_than_avg
		WHERE `Board_ID` = %s
		",
		$metro_id )
		, ARRAY_A
	);
	
	return $results;
}


/**
 * Returns all the ssb entries for a metro ID.
 *
 * @since   1.0.0
 * @return 	array
 */
function cc_aha_get_ssb_data( $metro_id ){
	global $wpdb;
	 
	$results = $wpdb->get_results( 
		$wpdb->prepare( 
		"
		SELECT *
		FROM $wpdb->aha_assessment_ssb
		WHERE board_id = %s
		",
		$metro_id )
		, ARRAY_A
	);
	
	return $results;
}

/**
 * Retrieve all of the questions that should appear in an analysis criterion.
 *
 * @since   1.0.0
 * @return 	array of arrays
 */
function cc_aha_get_questions_for_summary_criterion( $criterion = null ){
	global $wpdb;
	
	$questions = $wpdb->get_results( 
		$wpdb->prepare( 
		"
		SELECT * 
		FROM $wpdb->aha_assessment_questions
		WHERE summary_section = %s
		ORDER BY QID
		",
		$criterion )
		, ARRAY_A
	);

	return $questions;
}

/*********  PRIORITIES/ PRIORITY ********/
/*
 * Get priorities based on metro id
 *
 * @param int
 * @returns array
 */
function cc_aha_get_all_priorities( ){
	//TODO: consider extending this function to ask for return type
	//search aha-priority cpt by aha-board taxonomy taxonomy
	$args = array(
		'post_type' => 'aha-priority',
		'post_status' => 'publish',
		'posts_per_page' => -1
	);

	//var_dump( $args);
	$priority_query = new WP_Query( $args );
	
	//var_dump($priority_query);
	//array to hold ids of priorities
	$priority_array = array();
	
	//TODO: can this be more efficient?
	if ( $priority_query->have_posts() ) {
	
		while ( $priority_query->have_posts() ) {
			$priority_query->the_post();
			
			//array_push( $priority_array, get_the_ID() );
			$metro_id = wp_get_object_terms( get_the_ID(), 'aha-board-term' );
			$metro_id = current( $metro_id );
			$meta_value = get_post_meta( get_the_ID(), "criteria_slug", true );
			$inner_array = array(
				"board_id" => $metro_id,
				"criteria_slug" => $meta_value
				);
				
			//array_push( $priority_array, $metro_id );
			//var_dump( current( $metro_id ));
			//see if 
			if ( array_key_exists( $metro_id->name, $priority_array ) ){
				//add next meta_value to existing board array
				$temp = $priority_array[ $metro_id->name ];
				//var_dump( ($temp));
				array_push( $temp, $meta_value );
				//var_dump( $meta_value );
				$priority_array[ $metro_id->name ] = $temp;
			} else {
				$priority_array[ $metro_id->name ] = array( $meta_value );
			}
			
		}
	} else {
		// no posts found
	}

	return $priority_array;

}

/*
 * Save potential priorities based on metro id
 *
 * @param int
 * @returns array
 */
function cc_aha_save_potential_priorities_by_board( $metro_id, $criteria_slug, $potential_priority ){
	//TODO: deal with 2017 data...where will this go?
	//search aha-priority cpt by aha-board taxonomy taxonomy
	
	global $wpdb;
	
	//build out vars for update query
	$board_table_name = $wpdb->aha_assessment_board;
	$board_where = array(
			'BOARD_ID' => $metro_id 
		);
	
	$update_board_data = array( 
			$criteria_slug => $potential_priority
			//'community-phys-1-top-3' => '1'
		);
	
	//var_dump ( $board_table_name );
	//var_dump ( $update_board_data );
	//var_dump ( $update_board_data );
	if ( !empty ( $update_board_data ) ) {
		$num_board_rows_updated = $wpdb->update( $board_table_name, $update_board_data, $board_where, $format = null, $where_format = null );
	}
	
	//echo 'hollaaa';
	return $num_board_rows_updated;

}

/*
 * Get priorities based on metro id
 *
 * @param int
 * @returns array
 */
function cc_aha_get_priorities_by_board( $metro_id ){
	//TODO: consider extending this function to ask for return type
	//search aha-priority cpt by aha-board taxonomy taxonomy
	$args = array(
		'post_type' => 'aha-priority',
		'tax_query' => array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'aha-board-term',
				'field'    => 'name',
				'terms'    => $metro_id
			)
		),
		'posts_per_page' => -1
	);

	//var_dump( $args);
	$priority_query = new WP_Query( $args );
	//array to hold ids of priorities
	$priority_array = array();
	
	if ( $priority_query->have_posts() ) {
	
		while ( $priority_query->have_posts() ) {
			$priority_query->the_post();
			array_push( $priority_array, get_the_ID() );
		}
	} else {
		// no posts found
	}

	return $priority_array;

}

/*
 * Get health priorities based on metro id
 *
 * @param int
 * @returns array Array[ (int)post_id ] = (string)criteria_name
 */
function cc_aha_get_health_priorities_by_board( $metro_id ){

	//get all priorities for a board
	$args = array(
		'post_type' => 'aha-priority',
		'tax_query' => array(
			array(
				'taxonomy' => 'aha-board-term',
				'field'    => 'name',
				'terms'    => $metro_id
			)
		),
		'posts_per_page' => -1
	);

	//var_dump ($metro_id);
	$priority_query = new WP_Query( $args );
	
	//arrays to hold ids of priorities, terms
	$priority_array = array();
	$term_array = array(); //should be flat if we use current, yeah?  TODO: test with no criteria selected for post
	$total_array = array(); //$k = priority_id, $v = associative array { 'criteria_term' => string, 'action_plan_started' => bool }
	$health_only_array = array();
		
	if ( $priority_query->have_posts() ) {
	
		while ( $priority_query->have_posts() ) {
			$priority_query->the_post();
			array_push( $priority_array, get_the_ID() );
			
			//get terms for this post
			$term = wp_get_post_terms( get_the_ID(), 'aha-criteria-term' );
			$this_term = current( $term );
			//var_dump( $this_term );
			
			//get whether this priority has an action plan started
			$action_plan_started = get_post_meta( get_the_ID(), 'action_plan_started', true );
			
			array_push( $term_array, $this_term );
			
			//$total_array[ get_the_ID() ] = $this_term->name;
			$total_array[ get_the_ID() ] = array( 
								'criteria_name' => $this_term->name,
								'action_plan_started' => $action_plan_started
							);
			
		}
	}
	
	//var_dump( $priority_array );
	
	//get original sections from AHA-given array
	$sections = cc_aha_get_summary_sections( $metro_id );
	
	//create flat arrays of labels
	$health_labels = array();
	
	//get original name from list programmed from AHA
	foreach ($sections as $section_name => $section_data) {
		foreach ( $section_data['impact_areas'] as $impact_area_name => $impact_area_data ) {
			foreach ( $impact_area_data['criteria'] as $crit_key => $criteria_data ) {
				$criteria_squished = str_replace(' ', '', $criteria_data['label']);
				array_push( $health_labels, $criteria_squished); //flat array of criteria names
				//$health_labels[ $criteria_squished ] = $criteria_data['label'];	
			}
		}
	}
	
	//if our term is in the health labels array / total array, keep it!
	foreach( $total_array as $post_id => $values){
	
		$term_name = $values['criteria_name'];
		$action_plan_started = $values['action_plan_started'];
		//var_dump( $term_name );
		
		if( in_array( $term_name, $health_labels ) ){ //everything should be, since this is whence it came
			$health_only_array[ $post_id ] = array( 
								'criteria_name' => $term_name,
								'action_plan_started' => $action_plan_started
							);
		}
	}
	
	//var_dump ( $health_only_array );
	return $health_only_array;

}

/* TODO: fix this to return multi-array a la health function of same
 * Get revenue priorities based on metro id
 *
 * @param int
 * @returns array Array[ (int)post_id ] = (string)criteria_name
 */
function cc_aha_get_revenue_priorities_by_board( $metro_id ){

	//get all priorities for a board
	$args = array(
		'post_type' => 'aha-priority',
		'tax_query' => array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'aha-board-term',
				'field'    => 'name',
				'terms'    => $metro_id
			)
		),
		'posts_per_page' => -1
	);
	
	$priority_query = new WP_Query( $args );
	
	//arrays to hold ids of priorities, terms, placeholders
	$priority_array = array();
	$term_array = array(); //should be flat if we use current, yeah?  TODO: test with no criteria selected for post
	$total_array = array(); //$k = priority_id, $v = criteria_term
	$revenue_only_array = array();

	if ( $priority_query->have_posts() ) {
	
		while ( $priority_query->have_posts() ) {
			$priority_query->the_post();
			array_push( $priority_array, get_the_ID() );
			//get terms for this post
			$term = wp_get_post_terms( get_the_ID(), 'aha-criteria-term' );
			$this_term = current( $term );
			//var_dump( $this_term );
			
			array_push( $term_array, $this_term );
			
			$total_array[ get_the_ID() ] = $this_term->name;
		}
	}
	
	//var_dump( $priority_array );
	
	//get original sections
	$revenue_sections = cc_aha_get_summary_revenue_sections();
	
	//instantiate flat arrays of labels
	$revenue_labels = array();
	
	//get original name from list programmed from AHA
	foreach ( $revenue_sections as $revenue_name => $revenue_section ) { 
		$criteria_squished = str_replace(' ', '', $revenue_section['label']);
		array_push( $revenue_labels, $criteria_squished);
	}
	
	//if our term is in the revenue labels array / total array, keep it!
	foreach( $total_array as $post_id => $term_name){
	
		if( in_array( $term_name, $revenue_labels ) ){ //everything should be, since this is whence it came
			$revenue_only_array[ $post_id ] = $term_name;
		}
	}

	//var_dump( $revenue_only_array );
	return $revenue_only_array; // Array[ (int)post_id ] = (string)criteria_name

}


/*
 * Get priorities based on metro id
 *
 * @param int
 * @returns array
 */
function cc_aha_get_priorities_and_action_status_by_board( $metro_id ){
	//TODO: consider extending this function to ask for return type
	//search aha-priority cpt by aha-board taxonomy taxonomy
	$args = array(
		'post_type' => 'aha-priority',
		'tax_query' => array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'aha-board-term',
				'field'    => 'name',
				'terms'    => $metro_id
			)
		),
		'posts_per_page' => -1
	);

	//var_dump( $args);
	$priority_query = new WP_Query( $args );
	//array to hold ids of priorities
	$priority_array = array();
	
	//go to original AHA-given lists to mark these as either health or revenue
	
	//get original sections
	$sections = cc_aha_get_summary_sections( $metro_id );
	$revenue_sections = cc_aha_get_summary_revenue_sections();
	
	//create flat arrays of labels
	$health_array = array();
	$revenue_array = array();
	$total_label_array = array();
	
	
	//get original name from list programmed from AHA
	foreach ($sections as $section_name => $section_data) {
		foreach ( $section_data['impact_areas'] as $impact_area_name => $impact_area_data ) {
			foreach ( $impact_area_data['criteria'] as $crit_key => $criteria_data ) {
				$priority_squished = str_replace(' ', '', $criteria_data['label']);
				array_push( $health_array, $priority_squished);
				
				//$total_label_array[ $priority_squished ] = $criteria_data['label'];
			}
		}
	}
	
	foreach ( $revenue_sections as $revenue_name => $revenue_section ) { 
		$priority_squished = str_replace(' ', '', $revenue_section['label']);
		array_push( $revenue_array, $priority_squished);
		
		//$total_label_array[ $priority_squished ] = $revenue_section['label'];
	}

	if ( $priority_query->have_posts() ) {
	
		while ( $priority_query->have_posts() ) {
			$priority_query->the_post();
			//array_push( $priority_array, get_the_ID() );
			
			//get terms for this post
			$term = wp_get_post_terms( get_the_ID(), 'aha-criteria-term' );
			$this_term = current( $term );
			
			//get whether this priority has an action plan started
			$action_plan_started = get_post_meta( get_the_ID(), 'action_plan_started', true );
			
			if( in_array( $this_term->name, $health_array ) ){
				$which_type = "health";
			} else {
				$which_type = "revenue";
			}
			
			$priority_array[ get_the_ID() ] = array( 
								'criteria_name' =>  $this_term->name,
								'action_plan_started' => $action_plan_started,
								'priority_type' => $which_type
							);
		}
	} else {
		// no posts found
	}
	

	return $priority_array;

}
/*
 * Get priorities based on metro id, date and (opt)return data
 *
 * @param int, array(ints), string
 * @returns array
 */
function cc_aha_get_priorities_by_board_date( $metro_id, $date, $return_criteria = null ){
	
	
	
	//search aha-priority cpt by aha-board taxonomy taxonomy
	$args = array(
		'post_type' => 'aha-priority',
		'tax_query' => array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'aha-board-term',
				'field'    => 'name',
				'terms'    => $metro_id
			),
			array(
				'taxonomy' => 'aha-benchmark-date-term',
				'field'    => 'name',
				'terms'    => $date
			),
		),
		'posts_per_page' => -1
	);

	//var_dump( $args);
	$priority_query = new WP_Query( $args );
	//array to hold ids of priorities
	$priority_array = array();
	
	if ( $priority_query->have_posts() ) {
	
		while ( $priority_query->have_posts() ) {
			$priority_query->the_post();
			if ( $return_criteria != null ){
				//associative array with ['criteria_name'] = ID
				$criteria_name = wp_get_post_terms( get_the_ID(), 'aha-criteria-term' );
				//var_dump( current( $criteria_name )->name );
				$priority_array[ current( $criteria_name )->name ] = get_the_ID();
			} else {
				array_push( $priority_array, get_the_ID() );
			}
		}
	} else {
		// no posts found
	}

	return $priority_array;

}

/*
 * Get priorities based on criterion
 *
 * @param string
 * @returns array
 */
 
function cc_aha_get_priorities_by_criterion( $criterion ){
	//TODO: consider extending this function to ask for return type
	//search aha-priority cpt by aha-board taxonomy taxonomy
	$args = array(
		'post_type' => 'aha-priority',
		'tax_query' => array(
			array(
				'taxonomy' => 'aha-criteria-term',
				'field'    => 'name',
				'terms'    => $criterion
			)
		),
		'posts_per_page' => -1
	);

	//var_dump( $args);
	$priority_query = new WP_Query( $args );
	//array to hold ids of priorities
	$priority_array = array();
	
	if ( $priority_query->have_posts() ) {
	
		while ( $priority_query->have_posts() ) {
			$priority_query->the_post();
			array_push( $priority_array, get_the_ID() );
		}
	} else {
		// no posts found
	}

	return $priority_array;

}

/*
 * Get priorities based on metro id, date, criterion
 *
 * @param int, int, string
 * @returns array
 */
 
function cc_aha_get_priorities_by_board_date_criterion( $metro_id, $date, $criterion ){
	//TODO: consider extending this function to ask for return type
	//search aha-priority cpt by aha-board taxonomy taxonomy
	$args = array(
		'post_type' => 'aha-priority',
		'tax_query' => array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'aha-board-term',
				'field'    => 'name',
				'terms'    => $metro_id
			),
			array(
				'taxonomy' => 'aha-benchmark-date-term',
				'field'    => 'name',
				'terms'    => $date
			),
			array(
				'taxonomy' => 'aha-criteria-term',
				'field'    => 'name',
				'terms'    => $criterion
			)
		),
		'posts_per_page' => -1
	);

	//var_dump( $args);
	$priority_query = new WP_Query( $args );
	//array to hold ids of priorities
	$priority_array = array();
	
	if ( $priority_query->have_posts() ) {
	
		while ( $priority_query->have_posts() ) {
			$priority_query->the_post();
			array_push( $priority_array, get_the_ID() );
		}
	} else {
		// no posts found
	}

	return $priority_array;

}

/**
 * Updates/Adds board priorities
 *
 * Takes $_POST array of priority-specific data from health/revenue summary,
 *	makes sure that priority of same board and criteria and date doesn't already exist...somehow
 *
 * @since    1.0.0
 * @param 	string, int, string
 * @return	
 */
function cc_aha_update_priority( $metro_id, $date, $criteria, $criteria_slug ){
	
	global $wpdb;
	$current_user = wp_get_current_user();
	
	//Check to see if priority (of this board, criterion and date) exists
	$priorities = cc_aha_get_priorities_by_board_date_criterion( $metro_id, $date, $criteria );
	//var_dump( $metro_id );
	//var_dump( $date );
	//var_dump( $criteria );
		
	//if it returns more than 1 (it really shouldn't!), take current only
	$priority_id = current( $priorities );
	
	//If exists, update data; if not, add
	if ( empty( $priority_id ) ){
	
		//create new priority!
		$post_args = array(
			'post_title'    => $metro_id . '-' . $criteria . '-' . $date,
			'post_status'   => 'publish',
			'post_type'		=> 'aha-priority',
			'post_author'   => $current_user->ID
		);
		$post_id = wp_insert_post( $post_args, $wp_error );
		
		$affiliate_state_array = current( cc_aha_get_affiliate_state_by_board( $metro_id ));
		//var_dump ( current ( $affiliate_state_array['Affiliate'] ));
	
		
		if( $post_id > 0 ){  
			//add taxonomy to new priority
			$error = wp_set_object_terms( $post_id, $metro_id, 'aha-board-term' );
			$error = wp_set_object_terms( $post_id, $date, 'aha-benchmark-date-term' );
			$error = wp_set_object_terms( $post_id, $criteria, 'aha-criteria-term' );
			$error = wp_set_object_terms( $post_id, $affiliate_state_array["Affiliate"], 'aha-affiliate-term' );
			$error = wp_set_object_terms( $post_id, $affiliate_state_array["State"], 'aha-state-term' );
		
			//add criteria-slug (to sync w/ potential priorities - top-3 - in the assessment_board table)
			$criteria_slug_success = update_post_meta( $post_id, "criteria_slug", $criteria_slug );
			$current_date = date("Y-m-d H:i:s"); //not really worried about the time, are we?
			$date_selected_success = update_post_meta( $post_id, "date_selected", $current_date );
		
			echo $post_id; //send new priority id back to server
		} else {
			echo '0';
		}
	} else {
		//update priority of id
		
		//echo 'yes, priorities: ' . $priority_id;
	}
	//var_dump( $priorities );
	//echo 'hello';
	//die();
	
}

/* Sets the staff partner and volunteer lead for a priority (1June2015, updated to allow for 2 staff and 2 volunteers)
 *
 * @params int, int, int, string, string, string, string. PriorityId...
 * @params int PriorityID, string, string
 * @returns
 */
//TODO: update function def if works!
//function cc_aha_set_staff_for_priorities( $priority_id, $staff_partner, $volunteer, $volunteer_name = null, $volunteer_email = null ){
function cc_aha_set_staff_for_priorities( $priority_id, $staff_partner, $staff_partner2, $volunteer, $volunteer2, $volunteer_name = null, $volunteer_email = null, $volunteer_name_2 = null, $volunteer_email_2 = null ){
	
	global $wpdb;
	//$current_user = wp_get_current_user();
	
	//Make sure requisite $_POST variables exist
	if( $priority_id <= 0 || $priority_id == false ){
		return;
	}
	
	$staff_success = true;
	$volunteer_success = true;
	//update_post_meta returns false if value is the same as in db OR if there's an actual error...SUPER helpful
	$staff_success = update_post_meta( $priority_id, "staff_partner", $staff_partner );
	$staff_2_success = update_post_meta( $priority_id, "staff_partner_2", $staff_partner2 );
	
	//first volunteer
	if (!empty ( $volunteer ) ){
		$volunteer_success = update_post_meta( $priority_id, "volunteer_lead", $volunteer );
		delete_post_meta( $priority_id, "typein_volunteer_name" );
		delete_post_meta( $priority_id, "typein_volunteer_email" );
	} else if ( !empty( $volunteer_name) || !empty( $volunteer_email ) ) {
		if( !empty( $volunteer_name ) ){
			$volunteer_success = update_post_meta( $priority_id, "typein_volunteer_name", $volunteer_name );
			delete_post_meta( $priority_id, "volunteer_lead" );
		}
		if( ! empty( $volunteer_email ) ){
			$volunteer_success = update_post_meta( $priority_id, "typein_volunteer_email", $volunteer_email );
			delete_post_meta( $priority_id, "volunteer_lead" );
		}
	}
	
	//second volunteer
	if (!empty ( $volunteer2 ) ){
		$volunteer_success_2 = update_post_meta( $priority_id, "volunteer_lead_2", $volunteer2 );
		delete_post_meta( $priority_id, "typein_volunteer_name_2" );
		delete_post_meta( $priority_id, "typein_volunteer_email_2" );
	} else if ( !empty( $volunteer_name_2) || !empty( $volunteer_email_2 ) ) {
		if( !empty( $volunteer_name_2 ) ){
			$volunteer_success_2 = update_post_meta( $priority_id, "typein_volunteer_name_2", $volunteer_name_2 );
			delete_post_meta( $priority_id, "volunteer_lead_2" );
		}
		if( ! empty( $volunteer_email_2 ) ){
			$volunteer_success_2 = update_post_meta( $priority_id, "typein_volunteer_email_2", $volunteer_email_2 );
			delete_post_meta( $priority_id, "volunteer_lead_2" );
		}
	}
	
	/*if( $staff_success == false ) {
		echo 'error on saving staff partner'; //or...no change
		die();
	
	}
	if( $volunteer_success == false ){
		echo 'error on saving volunteer'; //or...no change
		die();
	} else {
	*/
		echo 'staff saved.';
		die();
	//}


}

/*
 * Gets the affiliate and state of a particular board
 *
 * @param string $metro_id
 * @return array
 */
function cc_aha_get_affiliate_state_by_board( $metro_id ){
	
	global $wpdb;
	 
	//get board data from database
	//$table_name = "wp_aha_assessment_board";
	$affiliate_state_array = $wpdb->get_results( 
		$wpdb->prepare( 
		"
		SELECT State, Affiliate
		FROM $wpdb->aha_assessment_board
		WHERE BOARD_ID = %s
		",
		$metro_id )
		, ARRAY_A
	);

	return $affiliate_state_array;
 
}

/*
 * Gets the boards given a particular affiliate
 *
 * @param string $affiliate_name
 * @return array
 */
function cc_aha_get_boards_by_affiliate_name( $affiliate_name ){
	
	global $wpdb;
	 
	//get board data from database
	//$table_name = "wp_aha_assessment_board";
	$board_array = $wpdb->get_results( 
		$wpdb->prepare( 
		"
		SELECT BOARD_ID
		FROM $wpdb->aha_assessment_board
		WHERE Affiliate = %s
		",
		$affiliate_name )
		, ARRAY_A
	);

	return $board_array;
 
}

/*
 * Gets the boards given a particular affiliate
 *
 * @param string $affiliate_name
 * @return array
 */
function cc_aha_get_boards_by_state_name( $state_name ){
	
	global $wpdb;
	 
	//get board data from database
	//$table_name = "wp_aha_assessment_board";
	$board_array = $wpdb->get_results( 
		$wpdb->prepare( 
		"
		SELECT BOARD_ID
		FROM $wpdb->aha_assessment_board
		WHERE State = %s
		",
		$state_name )
		, ARRAY_A
	);

	return $board_array;
 
}

 /*
 * Get human-readable associative array of all criteria
 *
 * @returns array Array[int Term ID] = array( 'label' => Human-readable Label, 'type' => 'health' or 'revenue' )
 */
function cc_aha_get_all_criteria_readable(){

	$taxonomies = array( 
		'aha-criteria-term',
	);

	$args = array(
		'orderby'           => 'name', 
		'order'             => 'ASC',
		'hide_empty'        => false
	); 

	$terms = get_terms($taxonomies, $args);
	$term_array = array();
	
	//get original sections
	$sections = cc_aha_get_summary_sections( $metro_id );
	$revenue_sections = cc_aha_get_summary_revenue_sections();
	
	//create flat arrays of labels
	$health_array = array();
	$revenue_array = array();
	$total_label_array = array();
	
	//get original name from list programmed from AHA
	foreach ($sections as $section_name => $section_data) {
		foreach ( $section_data['impact_areas'] as $impact_area_name => $impact_area_data ) {
			foreach ( $impact_area_data['criteria'] as $crit_key => $criteria_data ) {
				$priority_squished = str_replace(' ', '', $criteria_data['label']);
				array_push( $health_array, $priority_squished);
				
				$total_label_array[ $priority_squished ] = $criteria_data['label'];
			}
		}
	}
	
	foreach ( $revenue_sections as $revenue_name => $revenue_section ) { 
		$priority_squished = str_replace(' ', '', $revenue_section['label']);
		array_push( $revenue_array, $priority_squished);
		
		$total_label_array[ $priority_squished ] = $revenue_section['label'];
	}

	//foreach through and make human-readable
	foreach( $terms as $term ){
	
		if( array_key_exists( $term->name, $total_label_array ) ){ //everything should be, since this is whence it came
			$term_array[ $term->term_id ] = $total_label_array[ $term->name ];
		}
		/*$readable_name = preg_replace('/(?<=\\w)(?=[A-Z])/'," $1", $term->name);
		$readable_name = trim($readable_name);
		
		$term_array[ $term->term_id ] = $readable_name;
		*/
		//match slug to original array
		//$term_array[ $term->term_id ] = $term->name;
	}
	
	//var_dump( $term_array );
	return $term_array;

}

 /*
 * Get human-readable associative array of all criteria by metro_id
 *
 * @returns array Array[int Term ID] = array( 'state' => current( $state )->name, 'date' => current( $date )->name,
					'affiliate' => current( $affiliate )->name,'label' => Human-readable Label, 'type' => 'health' or 'revenue' )
 */
function cc_aha_get_criteria_readable_by_board( $metro_id = null ){

	$taxonomies = array( 
		'aha-criteria-term',
	);

	$args = array(
		'orderby'           => 'name', 
		'order'             => 'ASC',
		'hide_empty'        => false
	); 

	$terms = get_terms($taxonomies, $args);
	$term_array = array();
	
	//get original sections
	$sections = cc_aha_get_summary_sections( $metro_id );
	$revenue_sections = cc_aha_get_summary_revenue_sections();
	
	//create flat arrays of labels
	$health_array = array();
	$revenue_array = array();
	$total_label_array = array(); //array[priority_squished_label] = human_readable_label
	$total_array = array(); //array[ $priority_squished ] = array( 'label'=> human readable, 'type' => 'health' or 'revenue');
	
	//get original name from list programmed from AHA
	foreach ($sections as $section_name => $section_data) {
		foreach ( $section_data['impact_areas'] as $impact_area_name => $impact_area_data ) {
			foreach ( $impact_area_data['criteria'] as $crit_key => $criteria_data ) {
				$priority_squished = str_replace(' ', '', $criteria_data['label']);
				array_push( $health_array, $priority_squished);
				
				$total_label_array[ $priority_squished ] = $criteria_data['label'];
				$total_array[ $priority_squished ] = array( 'label' => $criteria_data['label'], 'type' => 'health' );
			}
		}
	}
	
	foreach ( $revenue_sections as $revenue_name => $revenue_section ) { 
		$priority_squished = str_replace(' ', '', $revenue_section['label']);
		array_push( $revenue_array, $priority_squished);
		
		$total_label_array[ $priority_squished ] = $revenue_section['label'];
		//a little convoluted, but I'm recycling code, ok?  RECYCLING
		$total_array[ $priority_squished ] = array( 'label' => $revenue_section['label'], 'type' => 'revenue' ); 
	}

	
	//now get the post_id, term label (readable) and term type for each priority in this metro id
	$args = array(
		'post_type' => 'aha-priority',
		'tax_query' => array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'aha-board-term',
				'field'    => 'name',
				'terms'    => $metro_id
			)
		),
		'posts_per_page' => -1
	);

	//var_dump( $args);
	$priority_query = new WP_Query( $args );
	//array to hold ids of priorities
	$priority_array = array();
	
	if ( $priority_query->have_posts() ) {
	
		while ( $priority_query->have_posts() ) {
			$priority_query->the_post();
			
			//get post terms
			$date = wp_get_post_terms( get_the_ID(), 'aha-benchmark-date-term' );
			$affiliate = wp_get_post_terms( get_the_ID(), 'aha-affiliate-term' );
			$state = wp_get_post_terms( get_the_ID(), 'aha-state-term' );
			
			//get criteria terms for processing
			$terms = wp_get_post_terms( get_the_ID(), 'aha-criteria-term' );
					
			//look at current(term) and make human-readable - should only be one
			//foreach( $terms as $term ){
			
			$term = current( $terms );
			$type = "";
			$label = "";
			
			if( array_key_exists( $term->name, $total_label_array ) ){ //everything should be, since this is whence it came
				//get type
				$type = $total_array[ $term->name ]['type'];
				$label = $total_label_array[ $term->name ];
				//$term_array[ $term->term_id ] = array( 'label' => $total_label_array[ $term->name ], 'type' => $type );
			}
			//}
			
			
			//set up priority array
			$priority_array[ get_the_ID() ] = array(
					'state' => current( $state )->name,
					'date' => current( $date )->name,
					'affiliate' => current( $affiliate )->name,
					'type' => $type,
					'label' => $label
					);
		}
	} else {
		// no posts found
	}

	return $priority_array;
	//var_dump( $term_array );
	//return $term_array;

}

 /*
 * Get human-readable associative array of all criteria by metro_id
 *
 * @param int, int Metro_id, Criteria_taxonomy_id
 * @returns array Array[int Term ID] = array( 'state' => current( $state )->name, 'date' => current( $date )->name,
					'affiliate' => current( $affiliate )->name
 *
 */
function cc_aha_get_single_criteria_readable_by_board_taxonomy( $metro_id = null, $tax_id = null ){

	//set up args for query
	$args = array(
		'post_type' => 'aha-priority',
		'tax_query' => array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'aha-board-term',
				'field'    => 'name',
				'terms'    => $metro_id
			),
			array(
				'taxonomy' => 'aha-criteria-term',
				'field'    => 'id',
				'terms'    => $tax_id
			),
		),
		'posts_per_page' => -1
	);

	//var_dump( $args);
	$priority_query = new WP_Query( $args );
	//array to hold ids of priorities
	$priority_array = array();
	
	if ( $priority_query->have_posts() ) {
	
		while ( $priority_query->have_posts() ) {
			$priority_query->the_post();
			
			//get post terms
			$date = get_the_date( get_the_ID() ) ; //TODO, roll this to function above
			$affiliate = wp_get_post_terms( get_the_ID(), 'aha-affiliate-term' );
			$state = wp_get_post_terms( get_the_ID(), 'aha-state-term' );
			//var_dump( $state );
			
			//set up priority array
			$priority_array[ get_the_ID() ] = array(
					'state' => current($state)->name,
					'date' =>$date,
					'affiliate' => current($affiliate)->name
					);
		}
	} else {
		// no posts found
	}

	return $priority_array;

}

/* 
 * Get 'Resources to Success' href, given a criteria/priority name string
 *
 * @params string
 * @return string
 */
function cc_aha_get_resources_by_criteria_name( $criteria_name = null ){
	
	//get original sections, which contain doc_href info
	$sections = cc_aha_get_summary_sections( $metro_id );
	$revenue_sections = cc_aha_get_summary_revenue_sections();
	
	//create flat arrays of labels
	$health_array = array();
	$revenue_array = array();
	$total_label_array = array();
	
	//get original name from list programmed from AHA
	foreach ($sections as $section_name => $section_data) {
		foreach ( $section_data['impact_areas'] as $impact_area_name => $impact_area_data ) {
			foreach ( $impact_area_data['criteria'] as $crit_key => $criteria_data ) {
				$priority_squished = str_replace(' ', '', $criteria_data['label']);
				
				//if this is our priority, get doc_href and get out!
				if( $criteria_name == $priority_squished ){
					return $criteria_data['doc_href'];
				}
				
			}
		}
	}
	
	foreach ( $revenue_sections as $revenue_name => $revenue_section ) { 
		$priority_squished = str_replace(' ', '', $revenue_section['label']);
		
		if( $criteria_name == $priority_squished ){
			return $revenue_section['doc_href'];
		}
	}

}

/*
 * Get all action step ids for a given priority (criterion)
 *
 * @param int $criterion_id (taxonomy 'aha-criteria-terms' )
 * @return array
 */
function cc_aha_get_action_steps_by_priority_id( $priority_id = null ){
	
	//get all action steps for a board's priority
	$args = array(
		'post_type' => 'aha-action-step',
		'post_parent' => (int)$priority_id,
		'posts_per_page' => -1
		//'meta_key'   => 'parent-priority',
		//'meta_value' => $priority_id,
	);
	
	$action_step_query = new WP_Query( $args );
	$action_step_id_array = array();
	
	if ( $action_step_query->have_posts() ) {
	
		while ( $action_step_query->have_posts() ) {
			$action_step_query->the_post();
			array_push( $action_step_id_array, get_the_ID() );
		}
	} 

	return $action_step_id_array;
	//return $args;
	
}

/*
 * Get all national action steps for a given priority (criterion)
 *
 * @param int $criterion_id (taxonomy 'aha-criteria-terms' )
 * @return array
 */
function cc_aha_get_national_action_steps_by_criterion(){

	//get all national action steps
	$args = array(
		'post_type' => 'aha-action-step',
		'meta_key'   => 'is-national-step',
		'meta_value' => true,
		'posts_per_page' => -1
	);
	
	$action_step_query = new WP_Query( $args );
	$action_step_id_array = array();
	
	if ( $action_step_query->have_posts() ) {
	
		while ( $action_step_query->have_posts() ) {
			$action_step_query->the_post();
			array_push( $action_step_id_array, get_the_ID() );
		}
	} 

	return $action_step_id_array;

}

/*
 * Get all affiliates
 *
 * @return array Array[taxonomy_id] = [taxonomy_label]
 */
function cc_aha_get_affiliates(){

	$affiliates = get_terms( 'aha-affiliate-term');
	$affiliates_redux = array();
	
	foreach ( $affiliates as $affiliate ){
	
		$affiliates_redux[ $affiliate->term_id ] = $affiliate->name;
	}
	
	return $affiliates_redux;

}

/*
 * Get all states
 *
 * @return array Array[taxonomy_id] = [taxonomy_label]
 */
function cc_aha_get_states(){

	$states = get_terms( 'aha-state-term');
	$states_redux = array();
	
	foreach ( $states as $state ){
	
		$states_redux[ $state->term_id ] = $state->name;
	}
	
	return $states_redux;

}


/***** ACTION PROGRESS REPORTS / COMMUNITY PLANNING PRORGESS REPORTS  *****/

/**
 * Get all Community Planning Progress Reports for a specific board's priority (WP CPT: aha-action-progress)
 *
 * @param int WP Post ID
 * @returns array
 */
function get_all_action_progress_by_priority_id( $priority_id = null ){
	
	//args for all action progress children of this priority_id
	$args = array(
		'post_type' => 'aha-action-progress',
		'post_parent' => (int)$priority_id,
		'posts_per_page' => -1
	);
	
	$action_progress_query = new WP_Query( $args );
	$action_progress_id_array = array();
	
	if ( $action_progress_query->have_posts() ) {
	
		while ( $action_progress_query->have_posts() ) {
			$action_progress_query->the_post();
			array_push( $action_progress_id_array, get_the_ID() );
		}
	} 

	return $action_progress_id_array;
	//return $args;
	
}

/**
 * Get current month's Community Planning Progress Report for a specific board's priority (WP CPT: aha-action-progress)
 *
 * @param int WP Post ID
 * @returns int Action Progress ID
 */
function get_current_action_progress_by_priority_id( $priority_id = null ){

	//what IS today?
	$today = getdate();
	$args = array(
		'post_type' => 'aha-action-progress',
		'year' => $today['year'],
		'monthnum' => $today['mon'],
		'post_parent' => (int)$priority_id,
		'posts_per_page' => 1
	);
	
	$action_progress_query = new WP_Query( $args );
	//$action_progress_id_array = array();
	$action_progress_id = 0; 
	
	//we should only have ONE post.
	if ( $action_progress_query->have_posts() ) {
	
		while ( $action_progress_query->have_posts() ) {
			$action_progress_query->the_post();
			//array_push( $action_progress_id_array, get_the_ID() );
			$action_progress_id = get_the_ID();
		}
	} 

	//return $action_progress_id_array;
	return $action_progress_id;
	//return $args;
	
}

/*
 * Get date of last-entered Action Progress Report
 *
 * @param int Priority_id
 * @return string month_year String
 *
 */
function get_latest_action_progress_by_priority_id( $priority_id = null ){
	//get latest post of type aha-action-progress
	$args = array(
		'post_type' => 'aha-action-progress',
		'post_parent' => (int)$priority_id,
		'orderby' => 'date',
		'order' => 'DESC',
		'posts_per_page' => 1
	);
	
	$action_progress_query = new WP_Query( $args );
	
	$action_progress_date = ""; 
	$blah = "";
	//we should only have ONE post.
	if ( $action_progress_query->have_posts() ) {
	
		$blah = "balh";
		while ( $action_progress_query->have_posts() ) {
			$action_progress_query->the_post();
			
			$id = get_the_ID();
			
			$action_progress_date = get_the_date( 'M_Y', $id );
		}
	} 

	return $action_progress_date;

}

/**
 * Returns viability score for a particular Community Planning Progress Report
 *
 * @param int Post_id
 * @return int Viability score
 *
 */
function get_viability_score_for_single_action_progress( $progress_id ){
	//TODO: Y U NO WORKKKKKKKKKK
	$viability_score = get_post_meta( $progress_id, 'viability_score', true );

	return $viability_score;
	//return $progress_id;
	//return 3;

}

/**
 * Get a board's priority's Action Reports (Community Planning Progress Reports)
 *
 * @param int, string Priority_ID (NOT taxonomy id), Date String, Start Date (opt), End Date (opt)
 * @returns array Array of ints of Action Progress ID
 */
function get_action_progress_reports_by_priority_date( $priority_id, $date_string, $start_date = null, $end_date = null ){
	
	//what IS today?
	$today = getdate();
	$this_year = $today['year'];
	$this_month = $today['mon']; //1-12
	
	
	//get $args for wp query based on incoming date params
	switch( $date_string ){
		case "ytd":
		
			//need to get all posts since last June, whenever that is.			
			if( $this_month < 7 ){
				//we are in the end of the FY (Jan - June), so get datestring of last june (current year - 1)
				$to_convert = "July " + ( $this_year - 1 );
				$after = strtotime( $to_convert );
			} else {
				//we are in the beginning of the FY (July - Dec)
				$to_convert = "July " + ( $this_year );
				$after = strtotime( $to_convert );
			}
			
			$args = array(
				'post_type' => 'aha-action-progress',
				'date_query' => array(
					array(
						'after'     => $after,
						'inclusive' => true
					),
				),
				'post_parent' => (int)$priority_id,
				'posts_per_page' => -1
			);
			
			break;
		case "quarterly":
			//$progress_reports = get_action_progress_reports_by_priority_date( $priority, "quarterly" );
			//TODO: this!
			break;
		case "range":
			//we need to add a month to end date
			$end_datetime = new DateTime( $end_date );
			$end_month = $end_datetime->format('m'); //1 thru 12
			$end_datetime->modify( 'first day of next month' );
			
			$new_end_date = $end_datetime->format( 'Y-m' );
			
			$args = array(
				'post_type' => 'aha-action-progress',
				'date_query' => array(
					array(
						'after'     => $start_date,
						'before'	=> $new_end_date,
						'inclusive' => true
					),
				),
				'post_parent' => (int)$priority_id,
				'posts_per_page' => -1
			);
			
			
			break;
		case "now":
			$to_convert = "July 1," + $this_year;
			$after = strtotime( $to_convert );
			
			$args = array(
				'post_type' => 'aha-action-progress',
				'year' => $today['year'],
				'monthnum' => $today['mon'],
				'post_parent' => (int)$priority_id,
				'posts_per_page' => 1  //there should only be one, since it's current month.
			);
			break;
		default: //all?
		
			$args = array(
				'post_type' => 'aha-action-progress',
				'post_parent' => (int)$priority_id,
				'posts_per_page' => -1
			);
			break;
	
	}
		
	$action_progress_query = new WP_Query( $args );
	$action_progress_array = array();  //[id] = [score]
	$action_progress_id = 0; 
	
	//we should only have ONE post.
	if ( $action_progress_query->have_posts() ) {
	
		while ( $action_progress_query->have_posts() ) {
			$action_progress_query->the_post();
			//array_push( $action_progress_id_array, get_the_ID() );
			$action_progress_id = get_the_ID();
			
			$month_year = get_the_date( "M", $action_progress_id ) . " " . get_the_date( "Y", $action_progress_id );
			
			$action_progress_array[ $action_progress_id ] = array(
					"score" => get_post_meta( get_the_ID(), 'viability_score', true ),
					"preassess_questions" =>  get_post_meta( get_the_ID(), 'preassess_questions', true ),
					"completed" =>  get_post_meta( get_the_ID(), 'completed', true ),
					"month_year" => $month_year,
					"month" => get_the_date( "M", $action_progress_id ),
					"year" => get_the_date( "Y", $action_progress_id )
				);
			
		}
	} 

	//return $action_progress_id_array;
	return $action_progress_array;
	//return $args;
	
}

/*
 * Returns array of "month year" strings, given a pre-defined date string
 *
 * @param string, string(opt): strtotime-convertible Start Date, string(opt): strtotime-convertible End Date
 * @return array of string DAte Strong in format ["3-letter-Mon Year"] (e.g., array( ["Dec 2014", "Jan 2015"]
 *
 */
function get_months_array_by_date_string( $time_frame = null, $start_date = null, $end_date = null ){

	//what IS today?
	$today = getdate();
	$this_year = $today['year'];
	$this_month = $today['mon']; //1-12
	
	$date_array = array(); //to hold "Mon Year" strings
	$first_progress_date = get_first_date_of_action_reports();
	
	
	//get $args for wp query based on incoming date params
	switch( $time_frame ){
		case "ytd":
			
		$d1 = new DateTime( ); //now
		
		if( $first_progress_report !== 0 ) {
			$d2 = new DateTime( (string)$first_progress_date );
		} else {
			$d2 = new DateTime( "now" );
		}
		$first_progress_year = (int)$d2->format('Y');
		$first_progress_month_int = (int)$d2->format('m');
		//return $first_progress_month_int;
		
		//get difference between first post and now
		$month_diff = $d1->diff($d2)->m + ($d1->diff($d2)->y*12); // int(8)
		//return $month_diff; //works!
		
			//if number of months since first report > 12, treat normally, otherwise calc from start date
			if( $month_diff < 12 ) {
				//need to get all months since last June, whenever that is.			
				if( $this_month < 7 ){ //we are between Jan - Jun
				
					//if our first post is this year, get since then
					if( $first_progress_year == $this_year ){ //$first_progress = Jan-Jun; current = Jan-Jun
					
						for( $i = $first_progress_month_int; $i <= $this_month; $i++ ){
							$pre_date_string = $this_year ."-" . $i . "-01";
							$i_month = date('M', strtotime( $pre_date_string ));
							$date_string = $i_month . " " . $this_year;
							array_push( $date_array, (string) $date_string );
						}
						//return our array of "M Y"
						return $date_array;
						
					} else { //$first_progress = Jul-Dec LASTYEAR; current = Jan-Jun
						//we have some months from last year + this year
					
						//last year
						for( $i = $first_progress_month_int; $i < 13; $i++ ){
							$last_year = (int)$this_year - 1;
							$pre_date_string = $last_year ."-" . $i . "-01";
							$i_month = date('M', strtotime( $pre_date_string ));
							$date_string = $i_month . " " . $last_year;
							array_push( $date_array, (string) $date_string );
						}
						
						//this year
						for( $i = 1; $i <= $this_month; $i++ ){
							$pre_date_string = $this_year ."-" . $i . "-01";
							$i_month = date('M', strtotime( $pre_date_string ));
							$date_string = $i_month . " " . $this_year;
							array_push( $date_array, (string) $date_string );
						}
						//return our array of "M Y"
						return $date_array;
					}
				} else { //current = July-Dec: NO NEED TO WORRY ABOUT LAST YEAR!
					
					//if our first post occurred before or on July, get all since (and including) this July
					if( $first_progress_month_int <= 7 ){ //$first_progress = Jan-Jun; current = July-Dec
					
						for( $i = 7; $i <= $this_month; $i++ ){
							$pre_date_string = $this_year ."-" . $i . "-01";
							$i_month = date('M', strtotime( $pre_date_string ));
							$date_string = $i_month . " " . $this_year;
							array_push( $date_array, (string) $date_string );
						}
						//return our array of "M Y"
						return $date_array;
						
					} else if ( $first_progress_month < $this_month ) { //$first_progress = July-Dec; current = July-Dec
					
						//we started this (first_progress report) after July, so get since beginning
						for( $i = $first_progress_month_int; $i <= $this_month; $i++ ){
							$pre_date_string = $this_year ."-" . $i . "-01";
							$i_month = date('M', strtotime( $pre_date_string ));
							$date_string = $i_month . " " . $this_year;
							array_push( $date_array, (string) $date_string );
						}
						//return our array of "M Y"
						return $date_array;
					} else { //$first_progress = July-Dec LAST YEAR; current = July-Dec
					
						for( $i = 7; $i <= $this_month; $i++ ){
							$pre_date_string = $this_year ."-" . $i . "-01";
							$i_month = date('M', strtotime( $pre_date_string ));
							$date_string = $i_month . " " . $this_year;
							array_push( $date_array, (string) $date_string );
						}
						//return our array of "M Y"
						return $date_array;
						
					}
				}
			
			} else { //we have been doing this for over a year
			
				//need to get all months since last June, whenever that is.			
				if( $this_month < 7 ){ //we are btw Jan - Jun
					//last year
					for( $i = 7; $i < 13; $i++ ){ //Jul-Dec LASY YEAR
						$last_year = (int)$this_year - 1;
						$pre_date_string = $last_year ."-" . $i . "-01";
						$i_month = date('M', strtotime( $pre_date_string ));
						$date_string = $i_month . " " . $last_year;
						array_push( $date_array, (string) $date_string );
					}
					
					//this year
					for( $i = 1; $i <= $this_month; $i++ ){ //Jan - this month
						$pre_date_string = $this_year ."-" . $i . "-01";
						$i_month = date('M', strtotime( $pre_date_string ));
						$date_string = $i_month . " " . $this_year;
						array_push( $date_array, (string) $date_string );
					}
					
					//return our array of "M Y"
					return $date_array;
					
				} else { //we need to count since and including July OF THIS YEAR
				
					for( $i = 7; $i <= $this_month; $i++ ){ //Jul - this month
						$pre_date_string = $this_year ."-" . $i . "-01";
						$i_month = date('M', strtotime( $pre_date_string ));
						$date_string = $i_month . " " . $this_year;
						array_push( $date_array, (string) $date_string );
					}
					
					//return our array of "M Y"
					return $date_array;
				}
			}
			
			break;
			
		case "quarterly":
			//TODO: what does this mean?  From YTD or one year or all...?
			return 4;
			break;
		case "now":
			$pre_date_string = $this_year ."-" . $this_month . "-01";
			$i_month = date('M', strtotime( $pre_date_string ));
			$date_string = $i_month . " " . $this_year;
			array_push( $date_array, (string) $date_string );
			
			//return our array of "M Y"
			return $date_array;
					
			break;
		case "range":
			//todo: this
			$d1 = new DateTime( $start_date );
			$d2 = new DateTime( $end_date );

			$start_month_number = date('m', strtotime( $start_date ));
			$start_month = date('M', strtotime( $start_date ));
			$start_year = date('Y', strtotime( $start_date ));
			$end_month_number = date('m', strtotime( $end_date ));
			$end_month = date('M', strtotime( $end_date ));
			$end_year = date('Y', strtotime( $end_date ));
			
			//diff returns DateInterval object; num months diff
			$num_months_diff = $d1->diff( $d2 )->m + ( $d1->diff( $d2 )->y*12 ); // 
			
			//end for
			$end_for_number = $start_month_number + $num_months_diff;
			
			for( $i = $start_month_number; $i <= $end_for_number; $i++ ){
				if( ( ( $i - 1 ) % 12 ) == 0 ){ //we're in January?
					$start_year++; //we're in a new year, baby
				}
				
				$pre_date_string = $start_year ."-" . $i . "-01";
				$i_month = date('M', strtotime( $pre_date_string ));
				
				$date_string = $i_month . " " . $start_year;
				array_push( $date_array, (string) $date_string );
				
			}
				
			return $date_array;
			break;
	}


}

/*
 * Return the beginning of Community Planning Progress Report time
 *
 * @return DateTime object
 *
 */
function get_first_date_of_action_reports(){
	
	//set up args to get first aha-action-progress post
	$args = array(
			'post_type' => 'aha-action-progress',
			'order' => 'ASC',
			'orderby' => 'date',
			'posts_per_page' => 1  //there should only be one, since we want the earliEST
		);
	
	$progress_query = new WP_Query( $args );
	
	// The Loop
	if ( $progress_query->have_posts() ) {

		while ( $progress_query->have_posts() ) {
			$progress_query->the_post();
			$date = get_the_date('M Y');
		}
		
	} else {
		$date = 0;
	}
	
	return $date;

}

/* 
 * Returns ids of aha-action-progress given a certain taxonomy
 *
 * @param int, string Taxonomy_id, Taxonomy Name.
 * @return array Array of aha-action-progress ids
 *
 */
function get_action_progress_by_taxonomy_id( $tax_id = null, $taxonomy_name = null ){

	//array for post ids
	$progress_ids = array();
	
	//set up args
	$args = array(
		'post_type' => 'aha-action-progress',
		'tax_query' => array(
		//'relation' => 'OR',
		array(
			'taxonomy' => $taxonomy_name,
			'field'    => 'term_id',
			'terms'    => array( $tax_id ),
		),
	), 
		'posts_per_page' => -1
	);
	
	$progress_query = new WP_Query( $args );
	
	// The Loop
	if ( $progress_query->have_posts() ) {

		while ( $progress_query->have_posts() ) {
			$progress_query->the_post();
			$id = get_the_id();
			array_push( $progress_ids, $id );
		}
		
	} else {
	}

	return $progress_ids;



}