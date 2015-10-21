<?php
/**
 * CC Transtria Extras - Analysis Database Bridge
 *
 * @package   CC Transtria Extras
 * @author    CARES staff
 * @license   GPL-2.0+
 * @copyright 2014 CommmunityCommons.org
 */


/**
 * Function to get all Studies in a Study Grouping
 *
 * @param int. Study Grouping ID
 * @return array. Array of Study IDs
 */
function get_study_ids_in_study_group( $study_group_id ){

	global $wpdb;
	
	//TODO, use wp->prepare
	$question_sql = 
		"
		SELECT StudyID
		FROM $wpdb->transtria_studies
		WHERE `StudyGroupingID` = $study_group_id
		"
		;
		
	$form_rows = $wpdb->get_results( $question_sql, ARRAY_N );
	
	return $form_rows;

}

/**
 * Returns all Indicator-Measure dyads for EACH ea tab (seq) in a given study)
 *
 * @param int. Study ID.
 * @return array?
 */
function get_unique_dyads_for_study( $study_id ){

	//how many ea tabs?
	$num_ea = cc_transtria_get_num_ea_tabs_for_study( $study_id );
	
	//for each ea tab in study, get IM dyads
	
	
	//foreach




}


/**
 * Sets (or resets) all Indicator-Measure dyads for EACH ea tab (seq) in a given study
 *
 * @param int. Study ID.
 * @return multivariable array. array( seq# => array( "ID" => study_id, "codetypeID" => int ,"result" => string/array ), seq#2...
 */
function set_unique_dyads_for_study( $study_id ){

	global $wpdb;
	
	//how many ea tabs?
	$num_ea = cc_transtria_get_num_ea_tabs_for_study( $study_id );
	
	//what indicators are allowed (selected on intervention/partnerships tab)?
	$allowed_inds = get_code_results_by_study_codetype( $study_id, "Indicator" );
	$flat_allowed_inds = array();
	
	//flatten our allowed indicators
	foreach( $allowed_inds as $one_ind ){
		
		$this_ind = current( $one_ind );
		$value = $this_ind["value"];
		$descr = $this_ind["descr"];
	
		$flat_allowed_inds[ $value ] = $descr;
	
	}
	
	//remove all IM dyads w/ this study id form intermediate table
	$intermediate_del_row = $wpdb->delete( 
		$wpdb->transtria_analysis_intermediate, 
		array( 
			'StudyID' => (int)$study_id
		)
	);
	
	$all_indicators = array();
	$all_measures = array();
	$these_indicators = array();
	$these_measures = array();
	
	//for each ea tab in study, get IM dyads: 2 types of indicators, 1 type of measure
	for( $i = 1; $i <= $num_ea; $i++ ){
	
		//get indicators for this seq
		$these_indicators = get_indicators_for_studyid_seq( $study_id, $i, $flat_allowed_inds );
		
		$these_measures = get_measures_for_studyid_seq( $study_id, $i );
	
		//append these indicators to all_indicators by seq
		$all_indicators[ $i ] = $these_indicators;
		$all_measures[ $i ] = $these_measures;
	
	}
	
	return $all_indicators;

}

/**
 * Gets all Indicator-Measure dyads for EACH ea tab (seq) in a given study GROUP
 *
 * @param int. Study ID.
 * @return array?
 */
function get_unique_dyads_for_study_group( $study_group_id ){

	global $wpdb;
	
	//get studies in this group
	$study_ids = get_study_ids_in_study_group( $study_group_id );
	
	$all_dyads = array();
	
	//get all study id dyads for this group
	foreach( $study_ids as $s_id ){
		$ea_sql = $wpdb->prepare( 
			"
			SELECT      *
			FROM        $wpdb->transtria_analysis_intermediate
			WHERE		StudyID = %d 
			",
			current( $s_id )
		); 
		
		$form_rows = $wpdb->get_results( $ea_sql, ARRAY_A );
		
		$all_dyads[ current( $s_id ) ] = $form_rows;
		//array_push( $all_dyads, current( $s_id ) );
	}
	
	return $all_dyads;
	
}



/**
 * Sets (unique, seq id) and Returns all UNIQUE Indicator
 *
 * @param
 * @return
 */
function get_unique_dyads_for_study_whaaat( $study_id ){



}

/**
 * Returns indicators for study id & seq (there are 2 kinds of indicators: codetbl indicators, study table indicators)
 *
 * @param int, int, array. StudyID, seq (EA tab num), List of allowed indicators (those selected on Intervention/Partnerships tab) because legacy data
 * @return array. Array of Indicator ints, strings (where additional indicators added)
 */
function get_indicators_for_studyid_seq( $study_id, $seq, $indicator_list ){

	global $wpdb;
	
	//first, get all indicators set for this seq in the code table
	//Part I. What's our code? codetype of form "ea_# Indicator"
	$codename = "ea_" . $seq . " Indicator";
	
	$codetype_sql = $wpdb->prepare( 
		"
		SELECT      codetypeID
		FROM        $wpdb->transtria_codetype
		WHERE		codetype = %s 
		",
		$codename
	); 
	
	//single codetype id returned
	$codetype_id = $wpdb->get_var( $codetype_sql ); //get_var returns single var

	//if we've got nothing, bounce
	if( empty( $codetype_id ) ){
		return false;
	}
	
	//get all in code_results for this codetype_id
	$results_sql = $wpdb->prepare(
		"
		SELECT *
		FROM $wpdb->transtria_code_results
		WHERE codetypeID = %d
		AND ID = %d
		",
		$codetype_id,
		$study_id
	);
	
	//code table indicators
	$code_table_indicators = $wpdb->get_results( $results_sql, ARRAY_A );
	$new_code_table_inds = array();
	
	//go through all these indicators and MAKE SURE they are in the indicators_list (b/c lagacy data! Fun!)
	foreach( $code_table_indicators as $indicators ){
		if( array_key_exists( $indicators["result"], $indicator_list ) ){
			//if this indicator shows on the intervention/partnership tab, allow it!
			//array_push( $new_code_table_inds, array( $indicators["result"] => $indicator_list[ $indicators["result"] ] ) );
			$new_code_table_inds[ $indicators["result"] ] = $indicator_list[ $indicators["result"] ];
		
		}
		
	}
	
	//Part II: now, get all indicators that are selected from the study:
	//first, identify which indicators selected in this seq
	$ea_sql = $wpdb->prepare( 
		"
		SELECT      other_indicators
		FROM        $wpdb->transtria_effect_association
		WHERE		StudyID = %d 
		AND 		seq = %d
		",
		$study_id,
		$seq
	); 
	
	$ea_rows = $wpdb->get_results( $ea_sql, ARRAY_A );
	$new_ea_table_inds = array();
	
	//parse each of the string entries here (so we have the ACTUAL indicator descr and not just the field name in study table)
	foreach( $ea_rows as $ea_ind ){
	
		//checking for empty strings
		if( $ea_ind["other_indicators"] != "" ){
			$exploded = explode( ",", $ea_ind["other_indicators"] );
			//checking for empty strings
			
			foreach( $exploded as $this_explode ){
				if( $this_explode != "" ){
					//get this string value from the study table
					$value = get_single_field_value_study_table( $study_id, $this_explode );
					
					//append all this to the $new_ea_table_inds array
					$new_ea_table_inds[ $this_explode ] = $value;
				}
			}
		}
	}
	
	//combine and return both inds lists
	$both_sets_indicators = array_merge( $new_code_table_inds, $new_ea_table_inds );
		
	return $both_sets_indicators;


}

/**
 * Returns measures for study id & seq (although set as Multi, there should only be one...not sure how to handle)
 *
 * @param int, int, array. StudyID, seq (EA tab num).
 * @return array. Array of Measure ints: string_descr
 */
function get_measures_for_studyid_seq( $study_id, $seq ){

	global $wpdb;
	
	//first, get all indicators set for this seq in the code table
	//Part I. What's our code? codetype of form "ea_# Indicator"
	$codename = "ea_" . $seq . " Measures";
	
	$codetype_sql = $wpdb->prepare( 
		"
		SELECT      codetypeID
		FROM        $wpdb->transtria_codetype
		WHERE		codetype = %s 
		",
		$codename
	); 
	
	//single codetype id returned
	$codetype_id = $wpdb->get_var( $codetype_sql ); //get_var returns single var

	//if we've got nothing, bounce
	if( empty( $codetype_id ) ){
		return false;
	}
	
	//get all in code_results for this codetype_id
	$results_sql = $wpdb->prepare(
		"
		SELECT *
		FROM $wpdb->transtria_code_results
		WHERE codetypeID = %d
		AND ID = %d
		",
		$codetype_id,
		$study_id
	);
	
	//code table indicators
	$code_table_measures = $wpdb->get_results( $results_sql, ARRAY_A );
	
	var_dump( $results_sql );
	$new_code_table_meaures = array();
	
	//go through all these indicators and MAKE SURE they are in the indicators_list (b/c lagacy data! Fun!)
	foreach( $code_table_measures as $measures ){
		/*if( array_key_exists( $indicators["result"], $indicator_list ) ){
			$new_code_table_inds[ $indicators["result"] ] = $indicator_list[ $indicators["result"] ];
		
		}*/
		var_dump( $measures );
		
	}
	
	return $new_code_table_meaures;

}




/*** GENERAL DATABASE HELPER FUNCTIONS (How do you like my Modularity, now?) *****/

/**
 * Returns array of code results given a study_id and a codetype (string name)
 *
 * @param int, string. Study ID, codetype
 * @return array. Array of string name => array( which_selected )
 */
function get_code_results_by_study_codetype( $study_id, $codetype ){

	global $wpdb;
	
	//what is the codetypeID (in codetype table) given a codetype string
	$codetype_sql = $wpdb->prepare( 
		"
		SELECT      codetypeID
		FROM        $wpdb->transtria_codetype
		WHERE		codetype = %s 
		",
		$codetype
	); 
	
	//single codetype id returned
	$codetype_id = $wpdb->get_var( $codetype_sql ); //get_var returns single var

	//what are the selected values for the codetype_id for this study_id
	$coderesult_sql = $wpdb->prepare( 
		"
		SELECT      result
		FROM        $wpdb->transtria_code_results
		WHERE		codetypeID = %s 
		AND			ID = %d
		",
		$codetype_id,
		$study_id
	); 

	$cr_rows = $wpdb->get_results( $coderesult_sql, ARRAY_A );
	//var_dump( $cr_rows );
	$all_results = array();
	
	//look up descr for each cr_rows, since values are coded
	foreach( $cr_rows as $coderesult ){
		//var_dump( current( $coderesult ) );
		$this_code_result = current( $coderesult );
		//take that codetypeid and get all the options for it in the transtria_codetbl
		$codetype_sql = $wpdb->prepare( 
			"
			SELECT      value, descr
			FROM        $wpdb->transtria_codetbl
			WHERE		codetypeID = %d
			AND			inactive_flag != 'Y'
			AND			value = %s
			ORDER BY	sequence
			",
			$codetype_id,
			$this_code_result
		); 
		
		$codetype_array = $wpdb->get_results( $codetype_sql, ARRAY_A ); //OBJECT_K - result will be output as an associative array of row objects, using first column's values as keys (duplicates will be discarded). 
		
		//var_dump( $codetype_array );
		//append these to all results
		//$all_results[ current( $coderesult ) ] = $codetype_array;
		array_push( $all_results, $codetype_array );
	
	}

	//echo $all_results;
	return $all_results;

}


/**
 * Returns field value for single column/study_id in table
 *
 * @param int, text. StudyID, column name.
 * @return int/string/text
 */
function get_single_field_value_study_table( $study_id, $field_name ){

	global $wpdb;
	
	//what is the codetypeID (in codetype table) given a codetype string
	$studies_sql = $wpdb->prepare( 
		"
		SELECT      $field_name
		FROM        $wpdb->transtria_studies
		WHERE		StudyID = %d 
		",
		$study_id
	); 
	
	//single codetype id returned
	$study_val = $wpdb->get_var( $studies_sql ); //get_var returns single var

	return $study_val;

	}

