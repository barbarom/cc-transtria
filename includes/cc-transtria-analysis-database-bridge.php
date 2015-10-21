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
 * Gets all Indicator-Measure dyads for EACH ea tab (seq) in a given study
 *
 * @param int. Study ID.
 * @return multivariable array. array( seq# => array( "ID" => study_id, "codetypeID" => int ,"result" => string/array ), seq#2...
 */
function get_unique_dyads_for_study( $study_id ){

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
	
	return array( 'indicators' => $all_indicators, 'measures' => $all_measures );

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
 * Sets all Indicator-Measure dyads for EACH ea tab (seq) in a given study GROUP
 *
 * @param int. Study ID.
 * @return array?
 */
function set_unique_dyads_for_study_group( $study_group_id ){

	global $wpdb;
	
	//get studies in this group
	$study_ids = get_study_ids_in_study_group( $study_group_id );
	$all_dyads = array();
	$values_string = "";  //for the impending massive INSERT INTO statement..
	$count = 1;
	
	//get all indicators/measures for each study
	foreach( $study_ids as $study_id ){
	
		//Part I: remove all IM dyads w/ this study id form intermediate table
		$intermediate_del_row = $wpdb->delete( 
			$wpdb->transtria_analysis_intermediate, 
			array( 
				'StudyID' => (int)$study_id[0]
			)
		);
		//var_dump( $study_id[0] );
		//what's our highest index?
		$index_sql = 
			"
			SELECT unique_id
			FROM $wpdb->transtria_analysis_intermediate
			ORDER BY unique_id DESC LIMIT 0, 1
			"
			;
		
		$highest_index = $wpdb->get_var( $index_sql );
		$count = $highest_index;
	
		//get number of ea tabs
		$num_ea = cc_transtria_get_num_ea_tabs_for_study( $study_id );
		$this_im = get_unique_dyads_for_study( (int) $study_id[0] ); //array index = seq number (ea tab number)
		$info_id = ""; //TODO: this...how, what?
		
		if( $num_ea > 0 ){ //if we even HAVE ea tabs
			for( $i=1; $i <= $num_ea; $i++ ){ //$i = seq
			
				//start VALUES string
				$values_start_string = "(" . $count . ", , " . (int) $study_id[0] . ", " . $i . ", ";
				//end VALUES string
				$values_end_string = " )";
				
				//go through each measure - should be one, might not be
				foreach( $this_im[ "measures" ][$i] as $single_measure ){
					
					//for each measure, cycle through all indicators
					foreach( $this_im[ "indicators" ][$i] as $single_ind ){
							
						//if we have something in the VALUES string already, prepend with comma
						if( $values_string != "" ){
							$values_string .= ",";
						}
						
						//TODO: optimize this...for each...study? Can we make this wpdb statement more dynamical?
						$metakey	= "Harriet's Adages";
						$metavalue	= "WordPress' database interface is like Sunday Morning: Easy.";

						$wpdb->query( $wpdb->prepare( 
							"
								INSERT INTO $wpdb->transtria_analysis_intermediate
								( unique_id, info_id, StudyID, ea_seq_id, indicator, measure )
								VALUES ( %d, %s, %d, %d, %s, %s )
							", 
							$count,
							$count, 
							$study_id[0],
							$i,
							$single_ind,
							$single_measure 
						) );
						
						
						//HERE, construct the VALUES statements!
						$values_string .= $values_start_string;
						$values_string .= $single_ind . ", " . $single_measure;
						$values_string .= $values_end_string;
						
						//update the count
						$count++;
					
					}
				
				}
				
			}
		}
	}
	
	var_dump( $values_string );	
	return $study_ids; 
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
			//if this indicator shows on the intervention/partnership tab, allow it! Bonus: indicators list has string descr of ind!
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
	
	//Part I: get all meaures set for this seq in the code table
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
	
	//code table measures
	$code_table_measures = $wpdb->get_results( $results_sql, ARRAY_A );
	
	//get all string descriptions of measures.  If special measures, add addtnl text to description
	$all_measure_numbers_names = array();
	$descr = "";
	
	//special measures index
	$special_measures_list = cc_transtria_measures_w_extra_text( false );
	//var_dump( $special_measures_list );
	foreach( $code_table_measures as $measures ){
		//for each "result", look it up and give it a name - "Meaures" is codetypeID = 
		$descr = get_single_codetypeid_descr_by_value( 136, $measures[ "result" ] );
		//var_dump( $measures[ "codetypeID" ] );
		//var_dump( $descr );
		
		//Part II: if any of these measures is in the 'special measures' list, get textboxe(s) and treat EACH as separate measure
		if( in_array( $measures[ "result" ], $special_measures_list ) ){
			//get all measures in ea table (serialized), append to descr 
			$special_measures_textbox = get_measures_textboxes_by_study_seq_value( $study_id, $seq, $measures[ "result" ] );
						
			//IF we have special measures with addtnl text, add that text to descr
			if( ( $special_measures_textbox == "" ) || ( empty( $special_measures_textbox ) ) ){
				//add like normal to the measures list
				$all_measure_numbers_names[ $measures[ "result" ] ] = $descr;
			} else {
				//add text to descr
				$all_measure_numbers_names[ $measures[ "result" ] ] =  $descr . ": " . $special_measures_textbox;
			}
			
		} else {
			//add this measure val and descr, as is, to the measures list.
			$all_measure_numbers_names[ $measures[ "result" ] ] = $descr;
		}
		
	}
	
	return $all_measure_numbers_names;

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

/**
 * Returns lookup value for codetypeid and value
 *
 * @param int, string.
 * @return string. Description of codetypeid value.
 */
function get_single_codetypeid_descr_by_value( $codetype_id, $value ){

	global $wpdb;
	
	//what is the codetypeID (in codetype table) given a codetype string
	$codetbl_sql = $wpdb->prepare( 
		"
		SELECT      descr
		FROM        $wpdb->transtria_codetbl
		WHERE		codetypeID = %d
		AND 		value = %s
		",
		$codetype_id,
		$value
	); 
	
	//single codetype id returned
	$codetbl_val = $wpdb->get_var( $codetbl_sql ); //get_var returns single var

	//var_dump( $codetbl_sql );
	
	return $codetbl_val;

}

/**
 * Returns measure text boxes, unserialized
 *
 * @param int, int, string. StudyID, seq number (ea tab number), Measure value ("Measures" = 136 in codetype_tbl.
 * @return string (or array?)
 */
function get_measures_textboxes_by_study_seq_value( $study_id, $seq, $measure_val ){

	global $wpdb;
	
	//get name of field
	$all_measures_w_text = cc_transtria_measures_w_extra_text();
	$field_name = $all_measures_w_text[ $measure_val ]['short_name'] . "_measures";
	
	//get field val

	//what is the codetypeID (in codetype table) given a codetype string
	$ea_sql = $wpdb->prepare( 
		"
		SELECT      $field_name
		FROM        $wpdb->transtria_effect_association
		WHERE		StudyID = %d
		AND 		seq = %d
		",
		$study_id,
		$seq
	); 
	
	//single codetype id returned
	$ea_field_val = $wpdb->get_var( $ea_sql ); //get_var returns single var
	return $ea_field_val;

}

