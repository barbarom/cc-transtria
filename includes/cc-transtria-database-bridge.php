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
 * Returns all study data given a single study id
 *
 * @param int. Study ID.
 * @return array
 */
function cc_transtria_get_all_data_one_study( $study_id = 0 ){

	$meta_data = cc_transtria_get_study_metadata( $study_id = 0 );
	$single_data = cc_transtria_get_single_study_data( $study_id );

	$pops_data_single = cc_transtria_get_pops_study_data_single( $study_id );
	$pops_data_multiple = cc_transtria_get_pops_study_data_multiple( $study_id );

}

/**
 * Returns metadata given a study id (num ese tabs, num ea tabs)
 *
 * @param int. Study ID.
 * @return array
 */
function cc_transtria_get_study_metadata( $study_id = 0 ){

	global $wpdb;
	
	//TODO, use wp->prepare
	$question_sql = 
		"
		SELECT variablename, value
		FROM $wpdb->transtria_metadata
		WHERE `StudyID` = $study_id
		AND 
			( `variablename` = 'ea tabCount' OR `variablename` = 'ese tabCount')
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
 * Returns array of string->values for population data in populations table
 *
 * @since    1.0.0
 * @return 	array
 */
function cc_transtria_get_pops_study_data_single( $study_id = 0 ){

	global $wpdb;
	
	//there are multiple rows in the pops table per study
	$populations_sql = $wpdb->prepare( 
		"
		SELECT      *
		FROM        $wpdb->transtria_population
		WHERE		StudyID = %s 
		",
		$study_id
	); 
	
	$form_rows = $wpdb->get_results( $populations_sql, OBJECT );
	return $form_rows;


}


/**
 * Returns array of string->values for population data in code_results table (drop downs)
 *
 * @since    1.0.0
 * @return 	array
 */
function cc_transtria_get_pops_study_data_multiple( $study_id = 0 ){

	//get text ids for pops stuff
	$pops_ids = cc_transtria_get_multiple_dropdown_ids_populations();
	
	//get lookup codes
	$lookup_codes = [];
	foreach( $pops_id as $k => $v ){
		
		//$this_code = cc_transtria_get_code_by_name
	
	
	}





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
 * Function to get form drop down options.  First pass takes input array as lookup and foreachs..
 *
 * @return Array. Array of arrays.
 */
function cc_transtria_get_dropdown_options( ){

	//list of dropdowns with single instance on the form (i.e., no other dropdowns of that type exist)
	$dd_options_singletons = cc_transtria_get_singleton_dropdown_options();
	
	//list of dropdowns with multiple instances (Populations tabs)
	$dd_options_multiples_pops = cc_transtria_get_multiple_dropdown_options_populations();
	
	//list of dropdowns with multiple instances (EA tabs)
	$dd_options_multiples_ea = cc_transtria_get_multiple_dropdown_options_ea();




}

/**
 * Gets an array of SINGLETON lookups to do and does those lookups
 *
 * @return Array.  Array of arrays of options for all singleton dropwdown fields (dropdowns that only exist ONCE in the form).
 *
 */
function cc_transtria_get_singleton_dropdown_options(){

	$dd_ids = cc_transtria_get_singleton_dropdown_ids();
	
	//array to hold all the options, indexed by div_id name
	$dd_options = [];
	
	//Now, perform lookup.
	foreach( $dd_ids as $div_id => $lookup_name ){
	
		$dd_options[ $div_id ] = cc_transtria_get_options_from_db( $lookup_name );
	
	}
	
	//var_dump( $dd_options );
	
	return $dd_options;

}

/**
 * Gets an array of MULTIPLE lookups for POPULATIONS  and does those lookups
 *
 * @return Array.  Array of arrays of options for all multiple POPULATION dropwdown fields (dropdowns that exist in Populations tabs in form).
 *
 */
function cc_transtria_get_multiple_dropdown_options_populations(){

	$dd_ids = cc_transtria_get_multiple_dropdown_ids_populations();
	
	//Now, perform lookup for all pops types
	foreach( $dd_ids as $div_id => $lookup_name ){
		
		$dd_options[ $div_id ] = cc_transtria_get_options_from_db( $lookup_name );
	
	}
	
	return $dd_options;

}

/**
 * Gets an array of SINGLETON lookups and does those lookups
 *
 * @return Array.  Array of arrays of options for all singleton dropdown fields (dropdowns that only exist ONCE in the form).
 *
 */
function cc_transtria_get_multiple_dropdown_options_ea(){

	$dd_ids = cc_transtria_get_multiple_dropdown_ids_ea();
	
	//Now, perform lookup.


}


/**
 * Function to actually get options from the 2 code tables, given a lookup name string
 *
 * @param string. Name of lookup
 * @return array. 
 */
function cc_transtria_get_options_from_db( $code_name = NULL ){

	
	if( $code_name == NULL ){ //your cover has been blown.  Go to page 38.
		
		return 0; //?
	
	}

	global $wpdb;
	
	$codetype_sql = $wpdb->prepare( 
		"
		SELECT      codetypeID
		FROM        $wpdb->transtria_codetype
		WHERE		codetype = %s 
		",
		$code_name
	); 
	
	//single codetype id returned
	$codetype_id = $wpdb->get_var( $codetype_sql ); //get_var returns single var

	//take that codetypeid and get all the options for it in the transtria_codetbl
	$codetype_sql = $wpdb->prepare( 
		"
		SELECT      value, descr
		FROM        $wpdb->transtria_codetbl
		WHERE		codetypeID = %d
		AND			inactive_flag != 'Y'
		ORDER BY	sequence
		",
		$codetype_id
	); 
	
	$codetype_array = $wpdb->get_results( $codetype_sql, OBJECT_K ); //OBJECT_K - result will be output as an associative array of row objects, using first column's values as keys (duplicates will be discarded). 
	
	return $codetype_array;

}
















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