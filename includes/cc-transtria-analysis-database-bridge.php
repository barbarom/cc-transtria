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
function get_dyads_by_study( $study_id ){

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
	
	//unset
	unset( $these_indicators, $these_measures, $allowed_inds, $flat_allowed_inds);
	
	return array( 'indicators' => $all_indicators, 'measures' => $all_measures );

}

/**
 * Gets all Indicator-Measure dyads for EACH ea tab (seq) in a given study GROUP from table.  No calcs.
 *
 * @param int. Study ID.
 * @return array?
 */
function get_dyads_for_study_group( $study_group_id ){

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
		
	}
	
	
	unset( $form_rows, $study_ids );
	return $all_dyads;
	
}

/**
 * Returns all Indicator-Measure dyads for EACH ea tab (seq) in a given study GROUP from table.  No calcs.
 *
 * @param int. Study ID.
 * @return array?
 */
function get_all_ims_for_study_group( $study_group_id ){

	global $wpdb;

	//get all study id dyads for this group
	$im_sql = $wpdb->prepare( 
		"
		SELECT      info_id, indicator_value, indicator, measure
		FROM        $wpdb->transtria_analysis_intermediate
		WHERE		StudyGroupingID = %d 
		",
		$study_group_id
	); 
	
	$form_rows = $wpdb->get_results( $im_sql, ARRAY_A );

	return $form_rows;
	
}

/**
 * Returns all UNIQUE Indicator-Measure dyads for EACH ea tab (seq) in a given study GROUP from table.  No dups.
 *
 * @param int. Study ID.
 * @return array?
 */
function get_unique_ims_for_study_group( $study_group_id ){

	global $wpdb;
	
	//get studies in this group
	$study_ids = get_study_ids_in_study_group( $study_group_id );
	
	//$all_ims = array();
	$unique_ims = array();
	
	//get all study id dyads for this group
	$im_sql = $wpdb->prepare( 
		"
		SELECT      info_id, indicator_value, indicator, measure, calc_ea_direction
		FROM        $wpdb->transtria_analysis_intermediate
		WHERE		StudyGroupingID = %d 
		ORDER BY	measure, indicator
		",
		$study_group_id
	); 
	
	$im_rows = $wpdb->get_results( $im_sql, ARRAY_A );
	
	//var_dump( $im_rows );
	
	$analysis_id_count = 1;
	$previous_measure = ""; //for scope
	$previous_indicator_val = "";
	$next_measure = ""; //for scope
	$next_indicator_val = "";
	$info_id_list = ""; //init our info id list
	
	//iterate through im rows
	foreach( $im_rows as $one_intermediate_im ){
	
		//previous iteration's value:
		$previous_measure = $next_measure;
		$previous_indicator_val = $next_indicator_val;
		
		//next values to check against
		$next_measure = $one_intermediate_im["measure"];
		$next_indicator_val = $one_intermediate_im["indicator_value"];
		$next_indicator = $one_intermediate_im["indicator"];
		$next_info_id = $one_intermediate_im["info_id"];
		$effects = $one_intermediate_im["calc_ea_direction"];
		$me = "measures";
		$ind = "indicators";
		
		//get array of [ info_id => measure ] pairs that are NON-duplicative.  However, Include all info_ids of duplicates w/in that unique array.
		$temp_array_measure = array_column ( $unique_ims, "measure", "indicator_value" );
		$temp_array_measure_info_id = array_column ( $unique_ims, "measure", "org_info_id" );
		$temp_array_indicator_info_id = array_column ( $unique_ims, "indicator_value", "org_info_id" );
		//var_dump( $temp_array_measure );
		
		//if previous vals == next vals, DUPLICATE and we need to add the info_id to the current analysis array list (unless both empty, means we're at start)
		if( ( $previous_measure == $next_measure ) && ( $previous_indicator_val == $next_indicator_val ) ){
			//&& ( ( $previous_measure != "" ) || ( $next_measure != "" ) ) ){
			//update duplicative entry in unique_ims: cros-reference $temp_array_indicator_info_id and $temp_array_measure_info_id to get info_id 
			$info_id_list_measures = array_keys( $temp_array_measure_info_id, $next_measure );
			$info_id_list_indicators = array_keys( $temp_array_indicator_info_id, $next_indicator_val );

			//what info_id has this measure and indicator?
			$intersect_ids = array_intersect( $info_id_list_measures, $info_id_list_indicators );
			
			$org_info_id = current( $intersect_ids ); //should only be one!  TODO: shore this method up
			
			//find entry in unique_ims, update "info_ids" w new list
			foreach( $unique_ims as $im_index => $im_values ){
				if( $org_info_id == $im_values["org_info_id"] ){
					//append to existing list
					$info_id_list = $im_values["info_id_list"] . ', ' . $next_info_id;
					
					//update original entry w new info_id list
					$unique_ims[ $im_index ]["info_id_list"] = $info_id_list;
				
				}
			}
		} 
		

		//memory_get_peak_usage();
	
		//first, see if im w measure value exists in temp measure array
		if( in_array( $next_measure, $temp_array_measure ) ){
		
			$found = false;
			//var_dump( $temp_array_measure );
			//if in array, see if we have the indicator_val in the unique array at all of the unique ids (info_id) for the measure
			foreach( $temp_array_measure as $i => $measure ){
				//check this unique id (info id) in the unique array. Is it the indicator_val?
				if( $i == $next_indicator_val ){
					//we've found it!  Don't add to unique_ims, but add the info_id
					$found = true;
				}
			}
			
			//if we still haven't found the next_indicator for this measure, add to unique array
			if( $found == false ){
				$new_index = $study_group_id . "_" . $analysis_id_count;
				$unique_ims[ $new_index ] = array( 
						"measure" => $next_measure, 
						"indicator_value" => $next_indicator_val,
						"indicator" => $next_indicator,
						"org_info_id" => $next_info_id,
						"info_id_list" => $next_info_id,
						"net_effects" => $effects
					);
					
				//update our counter
				$analysis_id_count++;
			
			}
		
		} else {
			//we don't even have the measure present, so add this indicator/measure to unique_ims array
			$new_index = $study_group_id . "_" . $analysis_id_count;
			$unique_ims[ $new_index ] = array( 
					"measure" => $next_measure, 
					"indicator_value" => $next_indicator_val,
					"indicator" => $next_indicator,
					"org_info_id" => $next_info_id,
					"info_id_list" => $next_info_id,
					"net_effects" => $effects
				);
				
			//update our counter
			$analysis_id_count++;
		}
				
	}
	
	//var_dump( $unique_ims );
	return $unique_ims;
	
}

 
/**
 * Returns Analysis vars from table (not calcs)
 * 
 * @param int. Study Group ID
 * @return array. Array of columns from analysis table.
 */
function get_analysis_vars_for_group( $study_group_id ){

	global $wpdb;
	
	//get all analysis vars for this group
	$analysis_sql = $wpdb->prepare( 
		"
		SELECT      *
		FROM        $wpdb->transtria_analysis
		WHERE		StudyGroupingID = %d 
		",
		$study_group_id
	); 
	
	$form_rows = $wpdb->get_results( $analysis_sql, ARRAY_A );
	
	//TODO: decide where this goes...
	//translate study design vals (since this is analysis, it's not in lookups)
	foreach( $form_rows as $index => $row ){
		//var_dump( $row );
		switch( $row["study_design"] ){
			case 1:
				$row["study_design_label"] = "Intervention Evaluation";
				break;
			case 2:
				$row["study_design_label"] = "Associational Study";
				break;
			default:
				$row["study_design_label"] = "";
				break;
		}
		
		//put the study label back into total array
		$form_rows[ $index ] = $row;
	}
	
	return $form_rows;

}

/**
 * Saves analysis vars to analysis table
 *
 * @param array. Array indexed by column name
 * 
 */
function save_vars_to_analysis_table( $analysis_vars ){

	global $wpdb;
	
	//sort the incoming array by info_id...hooowwwww
	$vars_by_id = array();
	
	foreach( $analysis_vars as $var_type => $ids_and_vals ){
	
		//check to see if this 
		var_dump( $var_type );
		foreach( $ids_and_vals as $info_id => $actual_val ){
			//append this to the vars_by_id table
			$vars_by_id[ $info_id ][ $var_type ] = $actual_val;		
		
		}
		
	}
	
	//cycle through each id and construct a sql query?
	foreach( $vars_by_id as $info_id => $labels_and_vals ){
		
		$data = array();
		//parse the columns/vars and values into $data array
		foreach( $labels_and_vals as $label => $val ){
			
			$data[ $label ] = $val;		
		
		}
		
		$where = array( 
			'info_id' => $info_id
		);

		$result[ $info_id ] = $wpdb->update( $wpdb->transtria_analysis, $data, $where, $format = null, $where_format = null );
	
	}
	
	return $result;
}


/**
 * Returns study-level data for intermediate vars
 *
 * @param int. Study Grouping ID.
 * @return array.  Array of info by study ids
 */
function get_study_level_for_intermediate( $study_group_id ){

	global $wpdb;
	
	//get studies in this group
	$study_ids = get_study_ids_in_study_group( $study_group_id );
	
	$studies_data = array();
	$studies_multi_data = array();
	
	//get all study data
	foreach( $study_ids as $s_id ){
	
		$this_s_id = current( $s_id );
		
		//get study-level vars for intermediate tab
		$study_sql = $wpdb->prepare( 
			"
			SELECT      StudyDesignID, otherStudyDesign, intervention_purpose, intervention_summary, support, opposition, other_setting_type
			FROM        $wpdb->transtria_studies
			WHERE		StudyID = %d 
			",
			$this_s_id
		); 
		
		$form_rows = $wpdb->get_row( $study_sql, ARRAY_A );
 
		$studies_data[ $this_s_id ] = $form_rows;
		
		//get multi data
		//intervention components: codetypeID = 37
		$studies_multi_data["intervention_components"] = get_code_results_by_study_codetype( $this_s_id, "Intervention Components" );
		//complexity: codetypeID = 6
		$studies_multi_data["complexity"] = get_code_results_by_study_codetype( $this_s_id, "Complexity" );
		//Setting Type: codetypeID = 90
		$studies_multi_data["setting_type"] = get_code_results_by_study_codetype( $this_s_id, "SettingType" );
		//PSE components: codetypeID = 61
		$studies_multi_data["pse_components"] = get_code_results_by_study_codetype( $this_s_id, "PSEcomponents" );
		
		//add multi data to all study data array
		$studies_data[ $this_s_id ][ "multi" ] = $studies_multi_data;
		$studies_multi_data = array();
		
	}
	
	//var_dump( $studies_data );
	
	//array_push( $all_dyads, current( $s_id ) );
	$study_design_lookup = get_lookup_for_study_design();
	
	//loop through studies data and replace study_design value with actual words
	foreach( $studies_data as $index => $one_study ){
		//var_dump( $index );
		//$one_study = current( $one_study );
		$this_value = $one_study[ "StudyDesignID" ];
		//var_dump( $study_design_lookup[ $this_value ] );
		$one_study[ "StudyDesignValue" ] = $study_design_lookup[ $this_value ]->descr;	
		$studies_data[ $index ] = $one_study;
	}
	//var_dump( $studies_data );
	
	return $studies_data;


}

/**
 * Sets all Indicator-Measure dyads for EACH ea tab (seq) in a given study GROUP; updates intermediate_analysis table
 *
 * @param int. Study ID.
 * @return array?
 */
function set_dyads_for_study_group( $study_group_id ){

	global $wpdb;
	
	//get studies in this group
	$study_ids = get_study_ids_in_study_group( $study_group_id );
	$all_dyads = array();
	$values_string = "";  //for the impending massive INSERT INTO statement..
	$count = 1;
	
	//get all indicators/measures for each study
	foreach( $study_ids as $study_id ){
	
		//Get other seq-related study data for analysis data (EA tab)
		$ea_data = get_ea_analysis_data( $study_id[0] );
		$study_data = get_study_analysis_data( $study_id[0] );
		
		//Part I: remove all IM dyads w/ this study id form intermediate table
		$intermediate_del_row = $wpdb->delete( 
			$wpdb->transtria_analysis_intermediate, 
			array( 
				'StudyID' => (int)$study_id[0]
			)
		);
		
		
		//what's our highest index (for unique id int)?  //TODO: is there a better way for this?  OR does it even matter?
		$index_sql = 
			"
			SELECT unique_id
			FROM $wpdb->transtria_analysis_intermediate
			ORDER BY unique_id DESC LIMIT 0, 1
			"
			;
		
		$highest_index = $wpdb->get_var( $index_sql );
		if( empty( $highest_index ) ){
			$count = 1;
		} else {
			$count = $highest_index;
		}
		$info_id_count = 1; //reset info id count w each study
	
		//get number of ea tabs
		$num_ea = cc_transtria_get_num_ea_tabs_for_study( $study_id );
		$this_im = get_dyads_by_study( (int) $study_id[0] ); //array index = seq number (ea tab number)
		
		//var_dump( $this_im );
		//return false;
		
		$info_id = ""; //TODO: this...how, what?
		$outcome_direction = "";
		$outcome_type = "";
		$outcome_duration = "";
		$ea_direction = "";
		
		$study_design = $study_data["StudyDesignID"];
		
		if( $num_ea > 0 ){ //if we even HAVE ea tabs
			for( $i=1; $i <= $num_ea; $i++ ){ //$i = seq
			
				//start VALUES string
				$values_start_string = "(" . $count . ", , " . (int) $study_id[0] . ", " . $i . ", ";
				//end VALUES string
				$values_end_string = " )";
				
				//set ea-level data (from $ea_data)
				$outcome_direction = $ea_data[ $i ]["outcome_direction"];
				$outcome_type = $ea_data[ $i ]["outcome_type"];
				$outcome_duration = $ea_data[ $i ]["outcome_duration"];
				$significant = $ea_data[ $i ]["significant"];
				$ind_strategies_dir = $ea_data[ $i ]["indicators_strategies_directions"];
				$ind_directions = $ea_data[ $i ]["indicator_directions"];
				$ind_dir = "";
				//var_dump( $ind_directions );
				
				
				
				//go through each measure - should be one, might not be
				foreach( $this_im[ "measures" ][$i] as $single_measure ){
					
					//for each measure, cycle through all indicators
					foreach( $this_im[ "indicators" ][$i] as $ind_index => $single_ind ){
					
						//if we have something in the VALUES string already, prepend with comma
						if( $values_string != "" ){ //No longer using the values string, but might try to incorporate later for efficiency..
							$values_string .= ",";
						}
						
						//info id = study id _ seq _ incremental value (starting w/ 1)
						$info_id = $study_id[0] . "_" . $i . "_" . $info_id_count;
						
						//calc ea_direction
						if( $significant == "N" ){
							$ea_direction = "3";
						} else {
							if( !empty( $ind_directions[ $ind_index ] ) ){ //if we HAVE a direction, else let them know 
								//TODO: this  22Oct 2015
								//TODO: test for outcome direction similarly
								$ind_dir = $ind_directions[ $ind_index ];
								$ea_direction = cc_transtria_calculate_ea_direction( $indicator_direction, $outcome_direction );
							} else {
								$ind_dir = "no ind. direction set";
								$ea_direction = "no ind. direction set";
							}
						}

						//TODO: optimize this...for each...study? Can we make this wpdb statement more dynamical?
						$wpdb->query( $wpdb->prepare( 
							"
								INSERT INTO $wpdb->transtria_analysis_intermediate
								( unique_id, info_id, StudyID, StudyGroupingID, StudyDesignID, ea_seq_id, indicator_value, indicator, indicator_direction, measure, outcome_direction, outcome_type, outcome_duration, significant, calc_ea_direction )
								VALUES ( %d, %s, %d, %d, %s, %d, %s, %s, %s, %s, %s, %s, %s, %s, %s )
							", 
							$count,
							$info_id, 
							$study_id[0],
							$study_group_id,
							$study_design,
							$i,
							$ind_index, 
							$single_ind,
							$ind_dir,
							$single_measure,
							$outcome_direction,
							$outcome_type,
							$outcome_duration,
							$significant,
							$ea_direction
						) );
						
						
						//HERE, construct the VALUES statements!
						$values_string .= $values_start_string;
						$values_string .= $single_ind . ", " . $single_measure;
						$values_string .= $values_end_string;
						
						//update the count
						$count++;
						$info_id_count++;
					
					}
				
				}
				
			}
		}
	}
	
	var_dump( $values_string );	
	return $study_ids; 
}



/**
 * Sets Analysis IDs for this group. Method: set analysis id for UNIQUE I-M dyads in study group (no dups!); update analysis table
 *
 * @param int. Study Group ID.
 * @return bool?
 */
function set_unique_analysis_ids_for_group( $study_group_id ){

	global $wpdb;
	
	//get all dyads for this group
	$all_ims = get_unique_ims_for_study_group( $study_group_id );
	
	//TODO: talk to Transtria and review this decision, they may need Analysis IDs to be SET (unless deleted or created, not recreated)
	
	//for now, remove all analysis rows of this study group from analysis table
	$analysis_del_row = $wpdb->delete( 
			$wpdb->transtria_analysis, 
			array( 
				'StudyGroupingID' => (int)$study_group_id 
			)
		);
	
	
	//var_dump( $all_ims );
	$placeholder = 0;
	foreach( $all_ims as $analysis_index => $one_im ){
	
		var_dump( $one_im );
		$measure = $one_im[ "measure" ];
		$indicator = $one_im[ "indicator" ];
		$indicator_val = $one_im[ "indicator_value" ];
		$info_id_list = $one_im[ "info_id_list" ];
		
		//set ea_direction, if no duplicates
		if( strpos( $info_id_list, "," ) === false ){
			$ea_direction = $one_im[ "net_effects" ];
			$duplicate_im = "N";
		} else {
			$ea_direction = "";
			$duplicate_im = "Y";
		}
		
		//add these to analysis table
		//var_dump( $analysis_index );
		//var_dump( $one_im );
	
		$spartacus = $wpdb->prepare( 
			"
				INSERT INTO $wpdb->transtria_analysis
				( info_id, StudyGroupingID, study_design, indicator_value, indicator, measure, info_id_list, duplicate_ims, net_effects )
				VALUES ( %s, %d, %d, %s, %s, %s, %s, %s, %s )
			", 
			$analysis_index,
			$study_group_id,
			$placeholder,
			$indicator_val, 
			$indicator,
			$measure,
			$info_id_list,
			$duplicate_im,
			$ea_direction
		);
		
		$wpdb->query( $spartacus );
	
	}
	
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
		//var_dump( $indicators );
		if( array_key_exists( $indicators["result"], $indicator_list ) ){
			//if this indicator shows on the intervention/partnership tab, allow it! Bonus: indicators list has string descr of ind!
			//also, we need to account for 'other' - if 'Other' is checked, get 'other_indicator' value from studies table
			if( $indicators["result"] == "Other" ){
				//get 'other_intervention_indicators' from studies table
				$other_ind = get_single_field_value_study_table( $study_id, 'other_intervention_indicators' );
				$new_code_table_inds[ (string) $indicators["result"] ] = "Other: " . $other_ind;
			} else {
				$new_code_table_inds[ (string) $indicators["result"] ] = $indicator_list[ $indicators["result"] ];
			}
		
		}
		
	}
	
	//var_dump( $new_code_table_inds );
	
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
	
	//combine and return both inds lists (no array_merge, it reindexes int indeces)
	//$both_sets_indicators = array_merge( $new_code_table_inds, $new_ea_table_inds );
	$both_sets_indicators = $new_code_table_inds + $new_ea_table_inds;
	
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

/**
 * Returns Analysis-specific data from EA table
 *
 * @param int. Study ID.
 * @return array. Array of all analysis fields for all seq (ea tabs)
 */
function get_ea_analysis_data( $study_id ){

	global $wpdb;
	
	//init final array
	$translated_ea = array();
	
	//get raw data from db
	$ea_sql = $wpdb->prepare( 
		"
		SELECT      seq, outcome_direction, outcome_type, outcome_type_other, significant, duration, duration_notreported, indicator_strategies_directions
		FROM        $wpdb->transtria_effect_association
		WHERE		StudyID = %d
		",
		$study_id
	); 
	
	$form_rows = $wpdb->get_results( $ea_sql, ARRAY_A );
	
	//now, we need to do lookups on some of these fields (to translate from lookup table)
	foreach( $form_rows as $one_seq ){
	
		//var_dump( $one_seq );
		
		$new_outcome_direction = get_single_codetypeid_descr_by_value( 64, $one_seq["outcome_direction"] ); //64 = "Results Outcome Direction"
		if( $one_seq["duration_notreported"] != "Y" ){
			$new_outcome_duration = get_single_codetypeid_descr_by_value( 9, $one_seq["duration"] ); //9 = "Duration"
		} else {
			$new_outcome_duration = "not reported";
		}
		
		//var_dump ( $one_seq["outcome_type_other"]);
		if( ( $one_seq["outcome_type_other"] != "" )  || empty( $one_seq["outcome_type_other"] ) ){
			//$new_outcome_type = get_single_codetypeid_descr_by_value( 54, $one_seq["outcome_type"] ); //64 = "OutcomeType"
			//we can just return the value here, since it correspond's to Laura's algorithms
			$new_outcome_type = $one_seq["outcome_type"]; 
		} else {
			$new_outcome_type = "Other: " . $one_seq["outcome_type"];
		}
		
		//deal with serialized indicator/strategies/directions to get indicator["string"]  => Direction
		$unserialized_inds = unserialize( $one_seq["indicator_strategies_directions"] );
		$indicators_directions = array();
		//var_dump( $unserialized_inds );
		if( !empty( $unserialized_inds ) && ( $unserialized_inds != false ) ){
			foreach( $unserialized_inds["indicators"] as $index => $details ){
			
				//var_dump( $index );
				//var_dump( $details );
				$indicators_directions[ $index ] = $details["direction"];
			}
		} else {
			//TODO: are we looking for old vars here?
		}
		
		
		$translated_ea[ $one_seq["seq"] ] = array(
				"outcome_direction" => $new_outcome_direction,
				"outcome_type" => $new_outcome_type,
				"outcome_duration" => $new_outcome_duration,
				"significant" => $one_seq["significant"],
				"indicator_directions" => $indicators_directions,
				"indicators_strategies_directions" => $unserialized_inds
			);
	
	}
	//var_dump( $translated_ea );
	return $translated_ea;

}

/**
 * Returns Analysis-specific data from Studies table
 *
 * @param int. Study ID.
 * @return array. Array of all analysis fields for study
 */
function get_study_analysis_data( $study_id ){

	global $wpdb;
	
	//init final array
	$translated_ea = array();
	
	//get raw data from db
	$study_sql = $wpdb->prepare( 
		"
		SELECT      StudyID, StudyDesignID
		FROM        $wpdb->transtria_studies
		WHERE		StudyID = %d
		",
		$study_id
	); 
	
	$form_row = $wpdb->get_row( $study_sql, ARRAY_A );

	return $form_row;

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
	//var_dump( $coderesult_sql );
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
 * Returns a lookup table for codetypeid
 *
 * @param int. CodetypeID
 * @return array. Indexed values => descr
 */
function get_codetbl_by_codetype( $codetypeID ){

	global $wpdb;
	
	//what is the codetypeID (in codetype table) given a codetype string
	$codetype_sql = $wpdb->prepare( 
		"
		SELECT      value, descr
		FROM        $wpdb->transtria_codetbl
		WHERE		codetypeID = %s 
		ORDER BY 	sequence
		",
		$codetypeID
	); 

	$codetbl_rows = $wpdb->get_results( $codetype_sql, OBJECT_K );

	return $codetbl_rows;

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

/**
 * Returns lookup table for study design
 *
 * @return array. Ints => atrings
 */
function get_lookup_for_study_design(){

	$values = get_codetbl_by_codetype( 99 );

	return $values;
}
