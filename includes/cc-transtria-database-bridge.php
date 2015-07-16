<?php
/**
 * CC Transtria Extras
 *
 * @package   CC Transtria Extras
 * @author    CARES staff
 * @license   GPL-2.0+
 * @copyright 2014 CommmunityCommons.org
 */


/**
 * Returns array of info from studies table based on study id
 *
 * @since    1.0.0
 * @return 	array
 */
function cc_transtria_get_study_from_studies( $study_id = 0 ){
	global $wpdb;
	
	//TODO, use wp->prepare
	$question_sql = 
		"
		SELECT * 
		FROM $wpdb->transtria_studies
		WHERE `StudyID` = $study_id
		";
		
	$form_rows = $wpdb->get_results( $question_sql, OBJECT );
	return $form_rows;

}

/**
 * Returns array of string->values for single data in studies table
 *
 * @since    1.0.0
 * @return 	array
 */
function cc_transtria_get_single_study_data( $study_id = 0 ){
	
	global $wpdb;
	
	//TODO, use wp->prepare
	$question_sql = 
		"
		SELECT * 
		FROM $wpdb->transtria_studies
		WHERE `StudyID` = $study_id
		";
		
	$form_rows = $wpdb->get_results( $question_sql, OBJECT );
	return current($form_rows);

}


/**
 * Returns array of ints of study ids already in studies table
 *
 * @since    1.0.0
 * @return 	array
 */
function cc_transtria_get_study_ids( ){
	global $wpdb;
	
	//TODO, use wp->prepare
	$question_sql = 
		"
		SELECT StudyID 
		FROM $wpdb->transtria_studies
		";
		
	$form_rows = $wpdb->get_results( $question_sql, ARRAY_N );
	
	//TODO: can we just use get_results instead of this mess?  Probably..
	//declare our array to hold study vals
	$study_array = [];
	
	//cycle through the array and get the int values of study id
	foreach ( $form_rows as $row ){ //intval("string")

		array_push( $study_array, intval( $row[0] ) ); //we could do string here if need be...
		
	}
	
	return $study_array;

}

/**
 * Returns array of ints of endnote ids from rec-number in phase2 table
 *
 * @since    1.0.0
 * @return 	array
 */
function cc_transtria_get_endnote_id_title( ){
	global $wpdb;
	
	//TODO, use wp->prepare
	$question_sql = 
		"
		SELECT `rec-number`, `titles_title`
		FROM $wpdb->transtria_phase2
		";
		
	$form_rows = $wpdb->get_results( $question_sql, ARRAY_A );
	
	//TODO: can we just use get_results instead of this mess?  Probably..
	//declare our array to hold study vals
	$endnote_array = [];
	
	//cycle through the array and get the int values of study id
	foreach ( $form_rows as $row ){ //intval("string")
	
		if( $row["rec-number"] !== NULL ){ //some of these are NULL in the transfer until Mel does it right.
			$endnote_array[ intval( $row["rec-number"] ) ] = $row["titles_title"]; //we could do string here if need be...
		}
		
	}
	
	return $endnote_array;

}

//TODO: functions for lookups for fields...


/**
 * 
 *
 *
 *
 */

 
 


/***** EXAMPLES FROM AHA ******/



/**
 * Returns all the Worse than Average hospital entries for a metro ID.
 *
 * @since   1.0.0
 * @return 	array
 */
function cc_transtria_get_effect_association( $study_id = 0 ){
	global $wpdb;
	 
	$results = $wpdb->get_results( 
		$wpdb->prepare( 
		"
		SELECT *
		FROM $wpdb->transtria_effect_association
		WHERE `StudyID` = %s
		",
		$study_id )
		, ARRAY_A
	);
	
	return $results;

}

//TODO: code table things....