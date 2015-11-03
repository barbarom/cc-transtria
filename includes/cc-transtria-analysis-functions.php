<?php 
/**
 * CC Transtria Analysis Functions
 *
 * @package   CC Transtria Extras
 * @author    CARES staff
 * @license   GPL-2.0+
 * @copyright 2015 CommmunityCommons.org
 */
 
/**
 * Returns the lookup int for Outcome Type
 *
 * @param string. Value from lookup table
 * @return int.
 */
function cc_transtria_calculate_ea_direction( $ind_dir, $out_dir ){
	
	if( ( $ind_dir == 1 || $ind_dir == 4 ) && ( $out_dir == 1 || $out_dir == 4 ) ){
		return 1;
	} else if ( ( $ind_dir == 2 || $ind_dir == 3 ) || ( $out_dir == 2 || $out_dir == 3 ) ){
		return 2;
	} else {
		return 3;
	}

}

function cc_transtria_calculate_ea_direction_for_studygrouping( $study_group_id ){

	global $wpdb;
	//get all calc_ea_direction for tihs study group
	$direction_sql = 
		"
		SELECT info_id, calc_ea_direction
		FROM $wpdb->transtria_analysis_intermediate
		WHERE StudyGroupingID = $study_group_id
		"		
		;
		
	$form_rows = $wpdb->get_results( $direction_sql, OBJECT_K );
	
	$all_directions = array();

	//put all study designs in single array to evaluate
	foreach( $form_rows as $info_id => $values ){
		array_push( $all_directions, $values->calc_ea_direction );
	}
	
	//now the tricky algorithm
	$num_directions = count( $all_directions ); 
	$instance_directions = array_count_values ( $all_directions );
	
	//if > 50% of directions == 1, return 1
	if( ( $instance_directions[1] / $num_directions ) > 0.5 ){
		return 1;
	} else if ( ( ( $instance_directions[1] / $num_directions ) == 0.5 ) && ( ( $instance_directions[2] / $num_directions ) == 0.5 ) ||
		( ( $instance_directions[3] / $num_directions ) > 0.5 ) ){
		return 2;
	} else if ( ( $instance_directions[2] / $num_directions ) > 0.5 ){
		return 3;
	}
	
	//otherwise, we have a data error
	return "data error";

}

/** Returns a study design for all analysis in a group
 *
 * @param int. Study Grouping
 * @return int? string?
 */
function calculate_study_design_for_analysis( $studygrouping_id ){

	global $wpdb;
	
	//get all study ids (in list) in this group
	$study_list = get_study_id_list_in_study_group( $studygrouping_id );
	
	//get all study designs for this list of study ids
	$design_sql = 
		"
		SELECT StudyID, StudyDesignID
		FROM $wpdb->transtria_studies
		WHERE StudyID in ($study_list)
		ORDER BY StudyID
		"		
		;
		
	$form_rows = $wpdb->get_results( $design_sql, OBJECT_K );
	
	$new_design = 0; //set to mean drop down to change value?
	$all_designs = array();
	
	//put all study designs in single array to evaluate
	foreach( $form_rows as $study_id => $values ){
		array_push( $all_designs, $values->StudyDesignID );
	}
	
	//var_dump( $all_designs );
	//Evaluate against StudyDesign algorithm
	//The algorithm numbers are DIFFERENT here than in Laura's notes, because of the way they are assigned in the codetbl already. //99=StudyDesign
	$one_array = array( "1", "2", "3", "4", "5", "6", "7", "8", "12" );
	if( in_array( $one_array, $all_designs ) ){
		return 1;
	}
	
	else if( in_array( "11", $all_designs ) ){
		return 0;
	}
	
	else if( in_array( "null", $all_designs ) ){
		return 0;
	}
	
	//else if all values are the same and are 9
	else if( count( array_unique( $all_designs ) ) === "1" && end( $all_designs ) == "9" ) {
		return 2;
	}
	
	return $new_design;
	
}

/** Returns a domestic/international setting for all analysis in a group
 *
 * @param int. Study Grouping
 * @return int? string?
 */
function calculate_domestic_intl_for_analysis( $studygrouping_id ){

	global $wpdb;
	//get all study ids for this group
	$study_list = get_study_id_list_in_study_group( $studygrouping_id );

	//get domestic/intl settings for this list of study ids (all three are Y/N in db)
	$domestic_sql = 
		"
		SELECT StudyID, domestic_setting, international_setting, domeesticintlsetting_notreported
		FROM $wpdb->transtria_studies
		WHERE StudyID in ($study_list)
		ORDER BY StudyID
		"		
		;
		
	$form_rows = $wpdb->get_results( $domestic_sql, OBJECT_K );
	
	$all_domestics = array();
	$all_intls = array();
	$all_notreported = array();
	
	//put all study designs in single array to evaluate
	foreach( $form_rows as $study_id => $values ){
		//if "Not reported" ISN'T checked
		if( $values->domeesticintlsetting_notreported != "Y" ){
			array_push( $all_domestics, $values->domestic_setting );
			array_push( $all_intls, $values->international_setting );
		} else {
			array_push( $all_notreported, $values->domeesticintlsetting_notreported );
		}
	}
	
	//it's getting all algorithmic in here
	if( ( in_array( "Y", $all_domestics ) ) && ( in_array( "Y", $all_domestics ) ) ){
		return 3;
	} else if( count( array_unique( $all_domestics ) ) === 1 && end( $all_domestics ) == "Y" ) { //all domestic
		return 1;
	} else if( count( array_unique( $all_intls ) ) === 1 && end( $all_intls ) == "Y" ) { //all domestic
		return 2;
	} 
	
	//else, test for all not reported
	else if( !empty( $all_notreported ) && empty( $all_domestics ) && empty( $all_intls ) ){
		return 999;
	}
	
	return 0;

}

/**
 * Returns the Measure - Outcome Type pairs for a Study Grouping. Outcome Type (seq) must be the SAME for Measure types (seq) across a SG.
 * 	There *should* only be one measure per ea tab (seq) and IS only one Outcome Type per.
 *
 * @param int. Study Grouping ID.
 * @return array. Key->val pairs for (string) Measure => (string) Outcome Type
 */
function calculate_outcome_types_studygrouping( $study_group_id ){

	global $wpdb;
	
	//get all study ids (in list) in this group
	$study_list = get_study_id_list_in_study_group( $study_group_id );
	
	//Get Measures and outcome types for each
	$all_ims = get_all_ims_for_study_group( $study_group_id );
	$measures_outcome_types = array();
	
	foreach( $all_ims as $one_im ){
		$this_measure = $one_im["measure"];
		$this_outcome_type = $one_im["outcome_type"];
		
		//do we have this measure in the final array?
		if( !empty( $measures_outcome_types[ $this_measure ] ) ) {
			//we have the measure.  Do the outcome types match?
			if( $measures_outcome_types[ $this_measure ] != $this_outcome_type ){
				//mismatch.  note it.
				$measures_outcome_types[ $this_measure ] = "multiple outcome types for same measure";
			}
			
		} else {
			//add to the array
			$measures_outcome_types[ $this_measure ] = $this_outcome_type;
		}
		
	
	}

	//var_dump( $measures_outcome_types );
	return $measures_outcome_types;

}


/**
 * Returns the duration for all studies/seq in a list
 *
 * @param array. Array of strings indicating study id_seq_uniqueid
 * @return string
 */
function calculate_duration_for_analysis_duplicates( $info_id_list, $studygroup_ea_data ){

	//get array( study_id => array( seq# => unique_num, seq# => unique_num ...
	$parsed_id_array = parse_study_seq_id_list( $info_id_list );
	$temp_durations = array();
	
	//go through each seq (ea tab) w/in a study id, calculate overall duration based on algorithm: highest duration wins
	foreach( $parsed_id_array as $s_id => $seq_vals ){
		$seq_vals = current( $seq_vals );
		
		foreach( $seq_vals as $seq => $unique_num ){
			array_push( $temp_durations, $studygroup_ea_data[ $s_id ][ $seq ][ "outcome_duration" ] );
		}
		
	}
	
	//var_dump( $temp_durations );
	//if we have the highest duration value present, set $duration and return. continue for next longest, etc
	if( in_array( "more than 12 months", $temp_durations ) ){ return 3; }
	else if( in_array( "6-12 months", $temp_durations ) ){ return 2; }
	else if( in_array( "less than 6 months", $temp_durations ) ) { return 1; }
	else if( in_array( "Not applicable", $temp_durations ) ) { return 999; }
	else { return "no data"; }
	
}

function calculate_duration_for_analysis_single( $duration_string ){

	switch ( $duration_string ) {
		case "more than 12 months":
			return 3;
			break;
		case "6-12 months":
			return 2;
			break;
		case "less than 6 months":
			return 1;
			break;
		case "Not applicable":
			return 999;
			break;
		default:
			return "no data";
			break;
	
	}

}



/** 
 * Turns string list of format study_seq_id into multivariable array by study id
 *
 * @param array. Array of strings in study_seq_number format
 * @return array. Multivariable array sorted by...study id?
 */
function parse_study_seq_id_list( $info_id_list ){

	//something
	$parsed_array = array();
	$temp_array = array();
	
	$info_id_list_exploded = explode( ", ", $info_id_list );
	foreach( $info_id_list_exploded as $info ){
	
		$parsed_one = explode( "_", $info );
		
		$parsed_array[ $parsed_one[0] ] = array();
		$temp_array[ $parsed_one[1] ] = $parsed_one[2]; //seq => unique_number
		
		//$parsed_array[ $parsed_one[0] ]["seq"] = $parsed_one[1];
		//$parsed_array[ $parsed_one[0] ]["unique_num"] = $parsed_one[2];
	
		array_push( $parsed_array[ $parsed_one[0] ], $temp_array );
	}

	unset( $temp_array );
	//var_dump( $parsed_array );
	return $parsed_array;

}


/**
 * Calculates "effectiveness" rating, General
 *
 * @param int/string, int/string, int/string, int/string. Study Design, Intervention Duration, Net Effects or Association, Outcome Type.
 *
 */
function calc_general_effectiveness_analysis( $study_design, $duration, $net_effect, $type ){
	
	//first, check that study design != 0 (means not set!)
	if( $study_design == 0 ){
		return "no study design";
	}
	
	// check for string values where there should be an int (means that net_effects, type, duration are not right )
	if( !is_int( (int)$net_effect ) ){
		return "data error: net effect";
	} else if( !is_int( (int)$type ) ){
		return "data error: outcome type";
	} else if( !is_int( (int)$duration ) ){
		return "data error: duration";
	} else if( !is_int( (int)$study_design ) ){ //for good measure (in case of error string adding later)
		return "data error: study design";
	}
	
	//if outcome_type is != 1 or 2, return
	if( $type != "1" && $type != "2" ){
		//var_dump( $type );
		return "outcome type != 1 or 2";
	}
	
	//Algorithmeat!
	if( ( $study_design == 1 ) && ( ( $duration == 2 ) ||( $duration == 3 ) ) && ( $net_effect == 1 ) ){
		return 1;
	} else if( ( $study_design == 1 ) && ( $duration == 1 ) && ( $net_effect == 1 ) ){
		return 2;
	} else if( ( $study_design == 1 ) && ( ( $net_effect == 2 ) || ( $net_effect == 3 ) ) ){
		return 3;
	} else if( ( $study_design == 2 ) && ( $net_effect == 1 ) ){
		return 4;
	} else if( ( $study_design == 2 ) && ( $net_effect == 2 ) ){
		return 5;
	} else if( ( $study_design == 2 ) && ( $net_effect == 3 ) ){
		return 6;
	} else {
		return 999;
	}
	
}
