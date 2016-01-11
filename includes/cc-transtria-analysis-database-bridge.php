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
		
	$form_rows = $wpdb->get_results( $question_sql, ARRAY_A );
	
	return $form_rows;

}

/** Returns all study ids in study group as LIST
 *
 * @param int. Study Grouping ID.
 * @return string. Comma-delimited list of study ids
 */

function get_study_id_list_in_study_group( $study_group_id ){

	global $wpdb;
	
	//get all study ids for this group
	$study_ids = get_study_ids_in_study_group( $study_group_id );
	$study_list = "";
	
	//make comma-delimited list for mysql query
	foreach( $study_ids as $study ){
		//var_dump( $study );
		if( $study_list == ""){ //first one
			$study_list = $study["StudyID"];
		} else {
			$study_list .= "," . $study["StudyID"];
		}
	}
	
	return $study_list;
}

/** 
 * Returns population data, sorted by Study ID, for a given study group
 *
 * @param int. Study Grouping ID.
 * @return array.  Array of population data, indexed by study id 
 */
function get_pop_data_study_group( $study_group_id ){

	global $wpdb;
	
	//get list of studies here for query
	$study_list = get_study_ids_in_study_group( $study_group_id );
	//to hold all the pop data, sorted by study
	$all_pop_data = array();
	
	foreach( $study_list as $study_id ){
		$this_study_id = $study_id[ "StudyID"];
		$pop_sql = 
			"
			SELECT PopulationType, isGeneralPopulation, generalpopulation_notreported, GenderCode, gender_notreported,
				PctBlack, PctAsian, PctPacificIslander, PctNativeAmerican, PctOtherRace, PctHispanic, PctLowerIncome, racepercentages_notreported
			FROM $wpdb->transtria_population
			WHERE StudyID = $this_study_id
			ORDER BY PopulationType
			"		
			;
			
		$form_rows = $wpdb->get_results( $pop_sql, OBJECT_K );
		
		$all_pop_data[ $this_study_id ] = $form_rows;
	}
	
	return $all_pop_data;
}

/** 
 * Returns population subpopulation data(Population Tab=>Subpop tab=>subpopulation), sorted by Study ID, for a given study group
 *
 * @param int. Study Grouping ID.
 * @return array.  Array of population data, indexed by study id 
 */
function get_pops_subpop_data_study_group( $study_group_id ){

	global $wpdb;
	$subpops_lookup = pop_subpop_codetypeid_lookup();
	$subpops_list = implode(",", array_keys( $subpops_lookup ) ); //list for query
	
	//get list of studies here for query
	$study_list = get_study_ids_in_study_group( $study_group_id );
	//to hold all the pop data, sorted by study
	$all_pop_data = array();
	
	foreach( $study_list as $study_id ){
		$this_study_id = $study_id[ "StudyID"];
		$pop_sql = 
			"
			SELECT $wpdb->transtria_code_results.ID, $wpdb->transtria_code_results.codetypeID, $wpdb->transtria_code_results.result, 
			$wpdb->transtria_codetbl.descr 
			FROM $wpdb->transtria_code_results, $wpdb->transtria_codetbl 
			WHERE $wpdb->transtria_codetbl.codetypeID = $wpdb->transtria_code_results.codetypeID 
			AND $wpdb->transtria_code_results.result = $wpdb->transtria_codetbl.value 
			AND $wpdb->transtria_code_results.ID =  $this_study_id 
			AND $wpdb->transtria_code_results.codetypeID IN ( $subpops_list )
			AND $wpdb->transtria_codetbl.codetypeID IN ( $subpops_list )
			"
			;
			
		$form_rows = $wpdb->get_results( $pop_sql, OBJECT );
		
		//var_dump( $form_rows );
		
		$reindexed_form_rows = array();
		
		//reindex with strings instead of codetypeIDs
	
		foreach( $form_rows as $index => $row ){ 
			
			//var_dump( $row );
			if( !empty( $row ) ){
				$new_index = $subpops_lookup[ $row->codetypeID ];
				//var_dump( $new_index );
				if( $reindexed_form_rows[ $new_index ] == null ){
					$reindexed_form_rows[ $new_index ] = array();
				}
				array_push( $reindexed_form_rows[ $new_index ], $row);
			
			}
		}
		
		$all_pop_data[ $this_study_id ] = $reindexed_form_rows;
	}
	
	return $all_pop_data;
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
	
	//var_dump( $study_id );
	
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
		//var_dump( $these_indicators );
		$these_measures = get_measures_for_studyid_seq( $study_id, $i );
	
		//append these indicators to all_indicators by seq
		$all_indicators[ $i ] = $these_indicators;
		$all_measures[ $i ] = $these_measures;
	
	}
	
	//unset
	//unset( $these_indicators, $these_measures, $allowed_inds, $flat_allowed_inds);
	
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
	$strategies_lookup = get_codetbl_by_codetype( 98 ); 
	
	$all_dyads = array();
	
	//get all study id dyads for this group
	foreach( $study_ids as $study_id ){
		$s_id = $study_id["StudyID"];
		$ea_sql = $wpdb->prepare( 
			"
			SELECT      *
			FROM        $wpdb->transtria_analysis_intermediate
			WHERE		StudyID = %d 
			",
			$s_id
		); 
		
		$form_rows = $wpdb->get_results( $ea_sql, ARRAY_A );
		
		foreach ( $form_rows as $index => $one_dyad ){
		
			$one_dyad['result_eval_unserial'] = unserialize( $one_dyad['result_evaluation_population'] );
			$one_dyad['result_subpop_unserial'] = unserialize( $one_dyad['result_subpopulation'] );
			
			
			//strategies will be harder
			//	unserialize the values; for each value, get text in codetbl
			$indexed_strats = array();
			$unserial_strats = unserialize( $one_dyad['indicator_strategies'] );
			
			foreach( $unserial_strats as $i => $strat_val ){
				//go through and create indexed strategies with value/description pairs
				$indexed_strats[ $strat_val ] = $strategies_lookup[ $strat_val ];
			
			}
		
			$one_dyad['indicator_strategies_unserial'] = $indexed_strats;
			
			$form_rows[ $index ] = $one_dyad;
		
		}
		
		$all_dyads[ $s_id ] = $form_rows;
		
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
		SELECT      info_id, indicator_value, indicator, measure, outcome_type, multi_component, complexity, exposure_frequency, rate_of_participation,
			ipe_pctblack, ipe_pctasian, ipe_pctnativeamerican, ipe_pctpacificislander, ipe_pcthispanic, ipe_pctlowerincome, ipe_representativeness,
			sustainability, pse_components
		FROM        $wpdb->transtria_analysis_intermediate
		WHERE		StudyGroupingID = %d 
		",
		$study_group_id
	); 
	
	$form_rows = $wpdb->get_results( $im_sql, ARRAY_A );

	return $form_rows;
	
}

/**
 * NOT USED RIGHT NOW: Returns all UNIQUE Indicator-Measure dyads for EACH ea tab (seq) in a given study GROUP from table.  No dups.
 *
 * @param int. Study ID.
 * @return array?
 */
function get_unique_ims_for_study_group( $study_group_id ){

	global $wpdb;
	
	//$all_ims = array();
	$unique_ims = array();
	
	//get all study id dyads for this group
	$im_sql = $wpdb->prepare( 
		"
		SELECT      info_id, indicator_value, indicator, measure, calc_ea_direction, outcome_type, outcome_duration,
			result_evaluation_population, result_subpopulationYN, result_subpopulation
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
	
	$effects = cc_transtria_calculate_ea_direction_for_studygrouping( $study_group_id );
		//$effects = $one_intermediate_im["calc_ea_direction"];
		//var_dump( $next_info_id );
		//var_dump( $effects );
		
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
		
		
		$evalpop = $one_intermediate_im["result_evaluation_population"];
		$subpop_YN = $one_intermediate_im["result_subpopulationYN"];
		$subpop = $one_intermediate_im["result_subpopulation"];
		
		$me = "measures";
		$ind = "indicators";
		
		//get array of [ info_id => measure ] pairs that are NON-duplicative.  However, Include all info_ids of duplicates w/in that unique array.
		$temp_array_measure = array_column ( $unique_ims, "measure", "indicator_value" );
		$temp_array_measure_info_id = array_column ( $unique_ims, "measure", "org_info_id" );
		$temp_array_indicator_info_id = array_column ( $unique_ims, "indicator_value", "org_info_id" );
		//var_dump( $temp_array_measure );
		
		//if previous vals == next vals, DUPLICATE and we need to add the info_id to the current analysis array list 
		if( ( $previous_measure == $next_measure ) && ( $previous_indicator_val == $next_indicator_val ) ){
		
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
	
	return $unique_ims;
	
}

//trying above func again given NEW PARAMETERS for evaluating duplicates by Transtria
function get_unique_ims_for_study_group_pop( $study_group_id ){

	global $wpdb;
	
	//get studies in this group
	//$study_ids = get_study_ids_in_study_group( $study_group_id );
	
	$unique_ims = array();
	
	//get all study id dyads for this group
	$im_sql = $wpdb->prepare( 
		"
		SELECT      info_id, indicator_value, indicator, measure_value, measure, calc_ea_direction, outcome_type, outcome_duration,
			result_evaluation_population, result_subpopulationYN, result_subpopulation
		FROM        $wpdb->transtria_analysis_intermediate
		WHERE		StudyGroupingID = %d 
		ORDER BY	measure, indicator
		",
		$study_group_id
	); 
	
	$im_rows = $wpdb->get_results( $im_sql, ARRAY_A );
	
	$analysis_id_count = 1;

	$next_measure = ""; //for scope
	$next_measure_val = ""; 
	$next_indicator_val = "";
	$next_eval_pop = "";
	$next_result_pop = "";
	$next_result_popYN = "";
	
	//net eggects calcd at study grouping level
	$effects = cc_transtria_calculate_ea_direction_for_studygrouping( $study_group_id );
	
	$info_id_list = ""; //init our info id list
	
	$temp_im_list = array(); //to hold temporary im info_id values that match SO FAR in the mathcing process (5 properties to match)
	
	//iterate through all intermediate im rows to get the 5-point I-M dyad!!
	foreach( $im_rows as $one_intermediate_im ){
	
		//var_dump( $temp_im_list  );
		//next values to check against
		$next_measure = $one_intermediate_im["measure"];
		$next_measure_val = $one_intermediate_im["measure_value"];
		$next_indicator_val = $one_intermediate_im["indicator_value"];
		$next_indicator = $one_intermediate_im["indicator"];
		$next_evalpop = $one_intermediate_im["result_evaluation_population"];
		$next_subpop = $one_intermediate_im["result_subpopulation"];
		$next_subpopYN = $one_intermediate_im["result_subpopulationYN"];
		
		$next_info_id = $one_intermediate_im["info_id"];
		//$effects = $one_intermediate_im["calc_ea_direction"];
		
		$me = "measures";
		$ind = "indicators";
		
		//get array of [ info_id => measure ] pairs that are NON-duplicative.  However, Include all info_ids of duplicates w/in that unique array.
		$temp_array_measure_info_id = array_column ( $unique_ims, "measure", "org_info_id" );
		$temp_array_indicator_info_id = array_column ( $unique_ims, "indicator_value", "org_info_id" );
		$temp_array_evalpop_info_id = array_column ( $unique_ims, "result_evaluation_population", "org_info_id" );
		$temp_array_subpopYN_info_id = array_column ( $unique_ims, "result_subpopulationYN", "org_info_id" );
		$temp_array_subpop_info_id = array_column ( $unique_ims, "result_subpopulation", "org_info_id" );
		//var_dump( $temp_array_subpopYN_info_id );
		
		
		//redoing the thinking...
		//Do the current 5 properties match any already in the $unique_ims array? Flags 
		$meas_ind_found = false;
		$evalpop_found = false;
		$subpopYN_found = false;
		$subpop_found = false;
		
		
		//Check for Uniqueness of 5 vars together (pentad). Measure => Indicator => Result Evaluation Population => result Subpopulation YN => Result SubPopulations
		if( in_array( $next_measure, $temp_array_measure_info_id ) ){  // 1/5
			//we have the measure in the unique_im array.  Get all info_ids and iterate through $unique_im for other 4 variables			
			$this_measure_by_info_id = array_keys( $temp_array_measure_info_id, $next_measure ); //array of all unique_im info_ids with THIS measure

			//cycle through these ids to see if we have THIS indicator at THIS measure for THIS info id
			foreach( $this_measure_by_info_id as $meas_info_id ){			
				//if we have THIS measure and indicator together:= 2/5
				if( $temp_array_indicator_info_id[ $meas_info_id ] == $next_indicator_val  ){
					$meas_ind_found = true;
					//update our temp list
					array_push( $temp_im_list, $meas_info_id );
				}
			}
			
			//if no Measure-Indicator pair of these values, put in db
			if( $meas_ind_found == false ){ //not even this I-M dyad in there!
				$new_index = $study_group_id . "_" . $analysis_id_count;
				$unique_ims[ $new_index ] = array( 
						"measure" => $next_measure, 
						"measure_value" => $next_measure_val, 
						"indicator_value" => $next_indicator_val,
						"indicator" => $next_indicator,
						"org_info_id" => $next_info_id,
						"info_id_list" => $next_info_id,
						"net_effects" => $effects,
						"result_evaluation_population" => $next_evalpop,
						"result_subpopulationYN" => $next_subpopYN,
						"result_subpopulation" => $next_subpop
					);
					
				//update our counter
				$analysis_id_count++;
				
			} else { //Indicator and measure are not unique, check for eval pop
				//result evaluation pop = 3/5
				foreach( $temp_im_list as $temp_info_id ){
					//if we have THIS measure, indicator and THIS result evaluation pop together
					if( $temp_array_evalpop_info_id[ $temp_info_id ] == $next_evalpop  ){
						$evalpop_found = true;
					} else {
						//not a match with THIS, so remove it from the temp_id list?
						if( ( $key = array_search( $temp_info_id, $temp_im_list ) ) !== false ) { 
							unset( $temp_im_list[$key] );
						}
					}
					
				}
				
				//check for truthiness of uniqueness of the I-M dyad so far; if !found, put it in unique_ims
				if( $evalpop_found == false ){ //not even this I-M dyad in there!
					$new_index = $study_group_id . "_" . $analysis_id_count;
					$unique_ims[ $new_index ] = array( 
							"measure" => $next_measure, 
							"measure_value" => $next_measure_val, 
							"indicator_value" => $next_indicator_val,
							"indicator" => $next_indicator,
							"org_info_id" => $next_info_id,
							"info_id_list" => $next_info_id,
							"net_effects" => $effects,
							"result_evaluation_population" => $next_evalpop,
							"result_subpopulationYN" => $next_subpopYN,
							"result_subpopulation" => $next_subpop
						);
						
					//update our counter
					$analysis_id_count++;
					
				} else { //Indicator and measure and eval pop are not unique, check for subpop YN
					//subpop YN = 4/5
					foreach( $temp_im_list as $temp_info_id ){
						//if we have THIS measure, indicator, result evaluation pop and THIS subpop YN together
						if( $temp_array_subpopYN_info_id[ $temp_info_id ] == $next_subpopYN  ){
							$subpopYN_found = true;							
						} else {
							//not a match with THIS, so remove it from the temp_id list?
							if( ( $key = array_search( $temp_info_id, $temp_im_list ) ) !== false ) { 
								unset( $temp_im_list[$key] );
							}
						}
					}
					
					if( $subpopYN_found == false ){ //not even this I-M dyad in there!
						$new_index = $study_group_id . "_" . $analysis_id_count;
						$unique_ims[ $new_index ] = array( 
								"measure" => $next_measure, 
								"measure_value" => $next_measure_val, 
								"indicator_value" => $next_indicator_val,
								"indicator" => $next_indicator,
								"org_info_id" => $next_info_id,
								"info_id_list" => $next_info_id,
								"net_effects" => $effects,
								"result_evaluation_population" => $next_evalpop,
								"result_subpopulationYN" => $next_subpopYN,
								"result_subpopulation" => $next_subpop
							);
							
						//update our counter
						$analysis_id_count++;
						
					} else { //Indicator and measure are not unique, check for subpop
						//subpop = 5/5
						foreach( $temp_im_list as $temp_info_id ){
							//if we have THIS measure, indicator, result evaluation pop, subpop YN and THIS subpop together
							if( $temp_array_subpop_info_id[ $temp_info_id ] == $next_subpop  ){
								$subpop_found = true;
							} else {
								//update our temp list - remove this im from it?
								if( ( $key = array_search( $temp_info_id, $temp_im_list ) ) !== false ) { 
									unset( $temp_im_list[$key] );
								}
							}
							
						}
						
						if( $subpop_found == false ){ //not even this I-M dyad in there!
							$new_index = $study_group_id . "_" . $analysis_id_count;
							$unique_ims[ $new_index ] = array( 
									"measure" => $next_measure, 
									"measure_value" => $next_measure_val, 
									"indicator_value" => $next_indicator_val,
									"indicator" => $next_indicator,
									"org_info_id" => $next_info_id,
									"info_id_list" => $next_info_id,
									"net_effects" => $effects,
									"result_evaluation_population" => $next_evalpop,
									"result_subpopulationYN" => $next_subpopYN,
									"result_subpopulation" => $next_subpop
								);
								
							//update our counter
							$analysis_id_count++;
							
						} else {
							//we have a duplicate!  Add this to the I-M info_id_list param..
							foreach( $temp_im_list as $temp_id ) { //should be just one (the original info_id)..
								//$next_info_id at this point is the duplicate and needs to be added to the unique_ims at the ORIGINAL ($temp_id)
								
								
								//what's the original info_id?
								foreach( $unique_ims as $this_info_id => $this_values ){
									if( $this_values["org_info_id"] == $temp_id ){
										//we have the original
										
										//append info_id to existing list
										$info_id_list = $unique_ims[ $this_info_id ]["info_id_list"] . ', ' . $next_info_id;
										
										//update original entry w new info_id list
										$unique_ims[ $this_info_id ]["info_id_list"] = $info_id_list;
										
										break; //we are done here
									}	
								}
							}
						}
					}
					
				}
				
			}
		
		} else {
			//we don't even have the measure present, so add this indicator/measure to unique_ims array
			$new_index = $study_group_id . "_" . $analysis_id_count;
			$unique_ims[ $new_index ] = array( 
					"measure" => $next_measure, 
					"measure_value" => $next_measure_val, 
					"indicator_value" => $next_indicator_val,
					"indicator" => $next_indicator,
					"org_info_id" => $next_info_id,
					"info_id_list" => $next_info_id,
					"net_effects" => $effects,
					"result_evaluation_population" => $next_evalpop,
					"result_subpopulationYN" => $next_subpopYN,
					"result_subpopulation" => $next_subpop
				);
				
			//update our counter
			$analysis_id_count++;
		}
		
				
	}
	
	$temp_im_list = array(); //new temp im list next time we iterate
		
	//var_dump( $unique_ims );
	return $unique_ims;
	
}

/**
 * Returns row from intermediate_analysis table by info_id
 *
 * @param string
 * @return array
 */
function get_single_im_from_intermediate( $info_id ){

	global $wpdb;

	//get all study id dyads for this group
	$im_sql = $wpdb->prepare( 
		"
		SELECT      *
		FROM        $wpdb->transtria_analysis_intermediate
		WHERE		info_id = %s
		",
		$info_id 
	); 
	
	$form_row = $wpdb->get_row( $im_sql, ARRAY_A );

	return $form_row;	



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
	
	//unserialize the serialized
	foreach( $form_rows as $index => $row ){
	
		if( !empty( $row[ "result_evaluation_population" ] ) ){
			
			$new_eval = unserialize( $row[ "result_evaluation_population" ] );
			$form_rows[ $index ][ "result_evaluation_population" ] = $new_eval;		
		
		}
		if( !empty( $row[ "result_subpopulation" ] ) ){
			
			$new_eval = unserialize( $row[ "result_subpopulation" ] );
			$form_rows[ $index ][ "result_subpopulation" ] = $new_eval;		
		
		}
	
	
	}
	
	return $form_rows;

} 

/**
 * Returns Study Grouping vars from SG table (not calcs)
 * 
 * @param int. Study Group ID
 * @return array. Array of columns from analysis table.
 */
function get_studygrouping_vars( $study_group_id ){

	global $wpdb;
	
	//get all analysis vars for this group
	$sg_sql = $wpdb->prepare( 
		"
		SELECT      *
		FROM        $wpdb->transtria_analysis_studygrouping
		WHERE		StudyGroupingID = %d 
		",
		$study_group_id
	); 
	
	$form_row = $wpdb->get_row( $sg_sql, ARRAY_A );
	
	return $form_row;

}

/**
 * Saves analysis vars to analysis table from front-end form
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
		//var_dump( $var_type );
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
 * Saves studygroup-level analysis vars to analysis table from front-end form
 *
 * @param array. Array indexed by column name
 * 
 */
function save_studygroup_vars_to_sg_table( $analysis_vars, $study_group_id ){

	global $wpdb;
	
	$vars_by_id = array();
	
	foreach( $analysis_vars as $var_type => $ids_and_vals ){
	
		switch( $var_type ){
			case "analysis_study_design":
				$var_type = "study_design";
				break;
			case "analysis_study_design_hr":
				$var_type = "study_design_hr";
				break;
			default:
				$var_type = $var_type;
				break;
		
		}
		
		$vars_by_id[ $var_type ] = $ids_and_vals;		
		
	}
		
	$data = array();
	
	//cycle through each id and construct a sql query?
	foreach( $vars_by_id as $label => $val ){
		
		
		//parse the columns/vars and values into $data array
		$data[ $label ] = $val;		
		
		$where = array( 
			'StudyGroupingID' => $study_group_id
		);

		$result = $wpdb->update( $wpdb->transtria_analysis_studygrouping, $data, $where, $format = null, $where_format = null );
	
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
	
		$this_s_id = $s_id["StudyID"];
		
		//get study-level vars for intermediate tab from STUDIES table
		$study_sql = $wpdb->prepare( 
			"
			SELECT      StudyDesignID, otherStudyDesign, intervention_purpose, intervention_summary, support, opposition, other_setting_type, sustainability_flag,
				sustainabilityplan_notreported, interventioncomponents_notreported, complexity_notreported, support_notreported, opposition_notreported,
				interventionpurpose_notreported, interventionsummary_notreported, settingtype_notreported, psecomponents_notreported, domestic_setting, 
				international_setting, domesticintlsetting_notreported
			FROM        $wpdb->transtria_studies
			WHERE		StudyID = %d 
			",
			$this_s_id
		); 
		
		$study_row = $wpdb->get_row( $study_sql, ARRAY_A );
		
		$studies_data[ $this_s_id ] = $study_row;
		
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
		
		//get ipe data (freq of exposure, rate of participation, race percentage data)
		$which_pop = 'ipe';
		$pops_sql = $wpdb->prepare(
			"
			SELECT      ParticipationRate, ExposureFrequency, rateofparticipation_notreported, freqofexposure_notreported, Representativeness, representativeness_notreported,
				racepercentages_notreported, percenthispanic_notreported, percentlowerincome_notreported, applicabilityhrpops_notreported, 
				PctBlack, PctAsian, PctNativeAmerican, PctPacificIslander, PctHispanic, PctLowerIncome, ApplicabilityHRPopulations
			FROM        $wpdb->transtria_population
			WHERE		StudyID = %d 
			AND			PopulationType = %s
			",
			$this_s_id,
			$which_pop		
		);
		
		$pops_row = $wpdb->get_row( $pops_sql, ARRAY_A );
		
		//append pops data to study_data
		$studies_data[ $this_s_id ][ $which_pop ] = $pops_row;
		
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
 * Sets ALL Indicator-Measure dyads for EACH ea tab (seq) in a given study GROUP; updates intermediate_analysis table
 *
 * @param int. Study ID.
 * @return array?
 */
function calc_and_set_dyads_primary_intermediate_analysis( $study_group_id ){

	global $wpdb;
	
	//get studies in this group
	$study_ids = get_study_ids_in_study_group( $study_group_id );
	$all_dyads = array();
	$values_string = "";  //for the impending massive INSERT INTO statement..
	
	
	//get all intermediate study-level variables for this study group
	$study_data = get_study_level_for_intermediate( $study_group_id ); //indexed by study id
	//var_dump( $study_data );
	
	$intermediate_calcs = array();
	$temp_vals = array();
	
		
	//Part I: remove all IM dyads w/ this study grouping id form intermediate table
	$intermediate_del_row = $wpdb->delete( 
		$wpdb->transtria_analysis_intermediate, 
		array( 
			'StudyGroupingID' => (int)$study_group_id
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
		$index_count = 1;
	} else {
		$index_count = $highest_index + 1;
	}
	
	//get all indicators/measures for each study
	foreach( $study_ids as $study_id ){
	
		$new_study_id = $study_id["StudyID"];
		//var_dump( $new_study_id );
	
		//Get other seq-related study data for analysis data (EA tab)
		$ea_data = get_ea_analysis_data( $new_study_id );
		//$study_data = get_single_study_analysis_data( $new_study_id ); //duplicate?
		$this_study_data = $study_data[ $new_study_id ];
		//var_dump( $this_study_data );
	
		
		
		$info_id_count = 1; //reset info id count w each study
	
		//get number of ea tabs
		$num_ea = cc_transtria_get_num_ea_tabs_for_study( $new_study_id );
		$this_im = get_dyads_by_study( (int) $new_study_id ); //array index = seq number (ea tab number)
		
		//var_dump( $this_im );
		//return false;
		
		$info_id = ""; //TODO: this...how, what?
		$outcome_direction = "";
		$outcome_type = "";
		$outcome_duration = "";
		$ea_direction = "";
		
		//$study_design = $study_data["StudyDesignID"];
		$study_design = $this_study_data["StudyDesignID"];
		
		//cycle through the EA tabs
		if( $num_ea > 0 ){ //if we even HAVE ea tabs
			for( $i=1; $i <= $num_ea; $i++ ){ //$i = seq
			
				//var_dump( $i );
			
				//start VALUES string
				$values_start_string = "(" . $index_count . ", , " . (int) $new_study_id . ", " . $i . ", ";
				//end VALUES string
				$values_end_string = " )";
				
				//set ea-level data (from $ea_data)
				$outcome_direction = $ea_data[ $i ]["outcome_direction"];
				$outcome_type = $ea_data[ $i ]["outcome_type"];
				$outcome_duration = $ea_data[ $i ]["outcome_duration"];
				$significant = $ea_data[ $i ]["significant"];
				$ind_strategies_dir = $ea_data[ $i ]["indicators_strategies_directions"];
				$ind_directions = $ea_data[ $i ]["indicator_directions"];
				$ind_strategies = $ea_data[ $i ]["indicator_strategies"];
				$result_eval_pop = $ea_data[ $i ]["result_evaluation_population"];
				$result_sub_pop_yn = $ea_data[ $i ]["result_subpopulationYN"];
				$result_sub_pop = $ea_data[ $i ]["result_subpopulation"];
				$ind_dir = "";
				$ind_strat = "";
				
				//var_dump( $this_im );
				
				
				
				//go through each measure - should be one, might not be
				foreach( $this_im[ "measures" ][$i] as $measure_val => $single_measure ){
					//var_dump( $single_measure );
					
					//for each measure, cycle through all indicators on this EA tab
					foreach( $this_im[ "indicators" ][$i] as $ind_index => $single_ind ){
						//var_dump( $ind_index );
						//var_dump( $ind_directions[ $ind_index ] );
						//var_dump( $single_ind );
					
						//if we have something in the VALUES string already, prepend with comma
						if( $values_string != "" ){ //No longer using the values string, but might try to incorporate later for efficiency..
							$values_string .= ",";
						}
						
						//info id = study id _ seq _ incremental value (starting w/ 1)
						$info_id = $new_study_id . "_" . $i . "_" . $info_id_count;
						
						//calc ea_direction
						if( $significant == "N" ){
							$ea_direction = "3";
							
							//ind direction will go in the db, so capture them anyway
							if( !empty( $ind_directions[ $ind_index ] ) ){ //if we HAVE a direction, else let them know
								$ind_dir = $ind_directions[ $ind_index ];
							} else {
								$ind_dir = "no ind. direction set";
							}
						} else {
							if( !empty( $ind_directions[ $ind_index ] ) ){ //if we HAVE a direction, else let them know 
								
								$ind_dir = $ind_directions[ $ind_index ];
								var_dump( $ind_dir );
								$ea_direction = cc_transtria_calculate_ea_direction( $ind_dir, $outcome_direction );
							} else {
								$ind_dir = "no ind. direction set";
								$ea_direction = "no ind. direction set";
							}
						}
						
						//get strategies associated with this indicator
						if( !empty( $ind_strategies[ $ind_index ] ) ){ //if we HAVE a direction, else let them know 
							//var_dump( $ind_strategies );
							//multiple strategies/indicator means serializing?
							$ind_strat_raw = $ind_strategies[ $ind_index ];
							$ind_strat = serialize( $ind_strat_raw );
						} else {
							$ind_strat = "no ind. strategy set";
						}

						
						//add study-level data to the intermediate table
						
						//intervention components ("multi_component"), complexity, pse components
						if( $this_study_data["interventioncomponents_notreported"] == "Y" ){ //if "interventioncomponents_notreported" checkbox checked, that trumps selections
							$intermediate_calcs["multi_component"] = "999";
						} else if( !empty( $this_study_data["multi"]["intervention_components"] ) ){
							$temp_vals = array();
							foreach( $this_study_data["multi"]["intervention_components"] as $in => $in_val ){
								//var_dump( $in );
								//var_dump( $in_val );
								$in_val = current( $in_val );
								//var_dump( $in_val["value"] );
								array_push( $temp_vals, $in_val["value"] );
							}
							
							//if we only have 1 element
							if( count( $temp_vals ) == 1 ){
								//if the val = 1 or 2, yes Multi
								if( in_array( 1, $temp_vals ) || in_array( "1", $temp_vals ) ||
									in_array( 2, $temp_vals ) || in_array( "2", $temp_vals ) ){
									$intermediate_calcs["multi_component"] = "1";
								} else {
									//val = 3 or 4, no multi
									$intermediate_calcs["multi_component"] = "0";
								}
								//var_dump( $intermediate_calcs );
							} else if( count( $temp_vals ) > 1 ){ //we have more than one value, yes multi
								$intermediate_calcs["multi_component"] = "1";
							} 
						} else { //no intervention components.  999.
							$intermediate_calcs["multi_component"] = "999";
						}
						
						//complexity
						if( $this_study_data["complexity_notreported"] == "Y" ){ //if "complexity_notreported" checkbox checked, that trumps selections
							$intermediate_calcs["complexity"] = 999; //complexity not reported
						} else if( !empty( $this_study_data["multi"]["complexity"] ) ){
							$intermediate_calcs["complexity"] = 1; //at least one checked
						} else {
							$intermediate_calcs["complexity"] = 0; //no complexity checked
						}
						
						
						//sustainability intermediate var calculation
						if( $this_study_data["sustainabilityplan_notreported"] == "Y" ) {
							$intermediate_calcs["sustainability"] = 999;
						} else if( $this_study_data["sustainability_flag"] == "Y" ){
							$intermediate_calcs["sustainability"] = 1;
						} else if( $this_study_data["sustainability_flag"] == "N" ){
							$intermediate_calcs["sustainability"] = 2;
						} else {
							$intermediate_calcs["sustainability"] = 999;
						} 
						
						//pse_componetne intermediate var calculation
						if( $this_study_data["psecomponents_notreported"] == "Y" ){ //if "interventioncomponents_notreported" checkbox checked, that trumps selections
							$intermediate_calcs["pse_components"] = "999";
						} else if( !empty( $this_study_data["multi"]["pse_components"] ) ){
							$temp_vals = array();
							foreach( $this_study_data["multi"]["pse_components"] as $in => $in_val ){
								$in_val = current( $in_val );
								array_push( $temp_vals, $in_val["descr"] ); //get the descr: this is what we'll use in Analysis ID
							}
							
							//serialize array of values
							$intermediate_calcs["pse_components"] = serialize( $temp_vals );
							
						} else { //no psa components.  999.
							$intermediate_calcs["pse_components"] = "999";
						}
						
						
						//ipe - rate of participation
						if( $this_study_data["ipe"]["rateofparticipation_notreported"] == "Y" ){
							$intermediate_calcs["ParticipationRate"] = 999; //rate of participation "not reported" is checked
						} else if( !empty( $this_study_data["ipe"]["ParticipationRate"] ) ){
							if( (int)$this_study_data["ipe"]["ParticipationRate"] >= 75 ){
								$intermediate_calcs["ParticipationRate"] = 1; //1 = High, if Rate of Participation >= 75%
							} else if( (int)$this_study_data["ipe"]["ParticipationRate"] < 75 ){
								$intermediate_calcs["ParticipationRate"] = 2; //2 = Low, if Rate of Participation < 75%
							} else {
								$intermediate_calcs["ParticipationRate"] = 999; //2 = Low, if Rate of Participation < 75%
							}
						}
						
						//ipe - potential exposure
						if( !empty( $this_study_data["ipe"]["ExposureFrequency"] ) ){
							if( (int)$this_study_data["ipe"]["ExposureFrequency"] == 1 ){
								$intermediate_calcs["ExposureFrequency"] = 1;
							} else {
								$intermediate_calcs["ExposureFrequency"] = 2;
							}
						}
						//ipe - representativeness
						if( $this_study_data["ipe"]["representativeness_notreported"] == "Y" ){
							$intermediate_calcs["Representativeness"] = 999; //no complexity checked
						} else if( !empty( $this_study_data["ipe"]["Representativeness"] ) ){
							if( $this_study_data["ipe"]["Representativeness"] == "Y" ){
								$intermediate_calcs["Representativeness"] = 1;
							} else {
								$intermediate_calcs["Representativeness"] = 2;
							}
						}
									
						/*** IPE Population calculations ***/
						//Race percentages
						
						if( $this_study_data["ipe"]["racepercentages_notreported"] == "Y" ){
							//all race percentages are 999
							$intermediate_calcs["ipe_pctblack"] = "999";
							$intermediate_calcs["ipe_pctasian"] = "999";
							$intermediate_calcs["ipe_pctnativeamerican"] = "999";
							$intermediate_calcs["ipe_pctpacificislander"] = "999";
						} else {
							$intermediate_calcs["ipe_pctblack"] = $this_study_data["ipe"]["PctBlack"];
							$intermediate_calcs["ipe_pctasian"] = $this_study_data["ipe"]["PctAsian"];
							$intermediate_calcs["ipe_pctnativeamerican"] = $this_study_data["ipe"]["PctNativeAmerican"];
							$intermediate_calcs["ipe_pctpacificislander"] = $this_study_data["ipe"]["PctPacificIslander"];
						}
						//IPE Hispanic
						if( $this_study_data["ipe"]["percenthispanic_notreported"] == "Y" ){
							//all race percentages are 999
							$intermediate_calcs["ipe_pcthispanic"] = "999";
						} else {
							$intermediate_calcs["ipe_pcthispanic"] = $this_study_data["ipe"]["PctHispanic"];
						}
						//IPE Low income
						if( $this_study_data["ipe"]["percentlowerincome_notreported"] == "Y" ){
							//all race percentages are 999
							$intermediate_calcs["ipe_pctlowerincome"] = "999";
						} else {
							$intermediate_calcs["ipe_pctlowerincome"] = $this_study_data["ipe"]["PctLowerIncome"];
						} 
						
						//ipe applicability to HR pops //applicabilityhrpops_notreported ("N", "Y" or empty string) //ApplicabilityHRPopulations ("Y" or "N" or empty string)
						if( $this_study_data["ipe"]["applicabilityhrpops_notreported"] == "Y" ){
							//all race percentages are 999
							$intermediate_calcs["applicability_hr_pops"] = "999";
						} else if( $this_study_data["ipe"]["ApplicabilityHRPopulations"] == "Y" ) {
							$intermediate_calcs["applicability_hr_pops"] = "1";
						} else if( $this_study_data["ipe"]["ApplicabilityHRPopulations"] == "N" ) {
							$intermediate_calcs["applicability_hr_pops"] = "2";
						} else {
							$intermediate_calcs["applicability_hr_pops"] = "999";
						}
						
									
						//calc the transtria value for indicator
						$indicator_tt_val = cc_transtria_analysis_val_lookup( "indicator_value", $ind_index );
						
						//add this IM-ness to the intermediate table
						$did_it_work = $wpdb->insert( 
							$wpdb->transtria_analysis_intermediate, 
							array( 
								'unique_id' => $index_count, 
								'info_id' => $info_id,
								'StudyID' => $new_study_id,
								'StudyGroupingID' => $study_group_id,
								'StudyDesignID' => $study_design,
								'ea_seq_id' => $i,
								'indicator_value' => $ind_index,
								'indicator_value_tt' => $indicator_tt_val,
								'indicator' => $single_ind,
								'indicator_direction' => $ind_dir,
								'indicator_strategies' => $ind_strat,
								'measure_value' => $measure_val,
								'measure' => $single_measure,
								'outcome_direction' => $outcome_direction,
								'outcome_type' => $outcome_type,
								'outcome_duration' => $outcome_duration,
								'significant' => $significant,
								'calc_ea_direction' => $ea_direction,
								'multi_component' => $intermediate_calcs["multi_component"],
								'complexity' => $intermediate_calcs["complexity"],
								'exposure_frequency' => $intermediate_calcs["ExposureFrequency"],
								'rate_of_participation' => $intermediate_calcs["ParticipationRate"],
								'result_evaluation_population' => $result_eval_pop,
								'result_subpopulationYN' => $result_sub_pop_yn,
								'result_subpopulation' => $result_sub_pop,
								'ipe_pctblack' => $intermediate_calcs["ipe_pctblack"],
								'ipe_pctasian' => $intermediate_calcs["ipe_pctasian"],
								'ipe_pctnativeamerican' => $intermediate_calcs["ipe_pctnativeamerican"],
								'ipe_pctpacificislander' => $intermediate_calcs["ipe_pctpacificislander"],
								'ipe_pcthispanic' => $intermediate_calcs["ipe_pcthispanic"],
								'ipe_pctlowerincome' => $intermediate_calcs["ipe_pctlowerincome"],
								'ipe_representativeness' => $intermediate_calcs["Representativeness"],
								'sustainability' => $intermediate_calcs["sustainability"],
								'pse_components' => $intermediate_calcs["pse_components"],
								'applicability_hr_pops' => $intermediate_calcs["applicability_hr_pops"]
							), 
							array( '%d', '%s', '%d', '%d', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s',
							'%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s',
							'%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%d' ) 
						);
						
						//reset placeholders 
						$intermediate_calcs = array();
						//var_dump( $inter_analysis_sql );
						//$helps = $wpdb->query( $inter_analysis_sql );
						
						if( $did_it_work === false ){
						
							//var_dump( $wpdb->last_query );
						}
						//var_dump( $did_it_work );
						//update the count
						$index_count++;
						$info_id_count++;
						
						//HERE, construct the VALUES statements!
						$values_string .= $values_start_string;
						$values_string .= $single_ind . ", " . $single_measure;
						$values_string .= $values_end_string;
						
						//var_dump( $single_measure ); //text at this point
					}
				
				}
				
			}
		}
	}
	
	//var_dump( $values_string );	
	return $study_ids; 
}

//TODO: UPDATE THIS!
/**
 * Calculates secondary intermediate variables to return to front end.  TODO: add these to table
 *
 * @param int. Study Group IS.
 * @return
 */
function run_secondary_intermediate_analysis( $study_group_id ){

	global $wpdb;
	
	//get all intermediate study-level variables
	$study_data = get_study_level_for_intermediate( $study_group_id );
	$intermediate_calcs = array();
	$temp_vals = array();
		
	//intervention components, complexity, pse components
	foreach( $study_data as $study_id => $values ){
		//var_dump( $values );
		if( !empty( $values["multi"]["intervention_components"] ) ){
			$temp_vals = array();
			foreach( $values["multi"]["intervention_components"] as $in => $in_val ){
				$in_val = current( $in_val );
				array_push( $temp_vals, $in_val["value"] );
			}
			//if we only have 1 element
			if( count( $array ) == 1 ){
				if( in_array( "1", $temp_vals ) || in_array( 1, $temp_vals ) ||
					in_array( 2, $temp_vals ) || in_array( "2", $temp_vals ) ){
					$intermediate_calcs[$study_id]["multi_component"] = "1";
				} else {
					$intermediate_calcs[$study_id]["multi_component"] = "0";
				}
			} else if( count( $array ) > 1 ){ //we have more than one value
				$intermediate_calcs[$study_id]["multi_component"] = "1";
			} 
		} else { //no intervention components.  999.
			$intermediate_calcs[$study_id]["multi_component"] = "999";
		}
		
		//complexity
		if( $values["complexity_notreported"] == "Y" ){
			$intermediate_calcs[$study_id]["complexity"] = 999; //complexity not reported
		} else if( !empty( $values["multi"]["complexity"] ) ){
			$intermediate_calcs[$study_id]["complexity"] = 1; //at least one checked
		} else {
			$intermediate_calcs[$study_id]["complexity"] = 0; //no complexity checked
		}
		//ipe - rate of participation
		if( $values["ipe"]["rateofparticipation_notreported"] == "Y" ){
			$intermediate_calcs[$study_id]["ParticipationRate"] = 999; //no complexity checked
		} else if( !empty( $values["ipe"]["ParticipationRate"] ) ){
			if( (int)$values["ipe"]["ParticipationRate"] >= 75 ){
				$intermediate_calcs[$study_id]["ParticipationRate"] = 1;
			} else {
				$intermediate_calcs[$study_id]["ParticipationRate"] = 0;
			}
		}
		//ipe - potential exposure
		if( !empty( $values["ipe"]["ExposureFrequency"] ) ){
			if( (int)$values["ipe"]["ExposureFrequency"] == 1 ){
				$intermediate_calcs[$study_id]["ExposureFrequency"] = 1;
			} else {
				$intermediate_calcs[$study_id]["ExposureFrequency"] = 2;
			}
		}
		//ipe - representativeness
		if( $values["ipe"]["representativeness_notreported"] == "Y" ){
			$intermediate_calcs[$study_id]["Representativeness"] = 999; //no complexity checked
		} else if( !empty( $values["ipe"]["Representativeness"] ) ){
			if( $values["ipe"]["Representativeness"] == "Y" ){
				$intermediate_calcs[$study_id]["Representativeness"] = 1;
			} else {
				$intermediate_calcs[$study_id]["Representativeness"] = 2;
			}
		}
		
	
	
	}
	
	return $intermediate_calcs;

}

/**
 * Sets Analysis IDs for this group (w/ respective vars). Method: set analysis id for UNIQUE I-M dyads in study group (no dups!); update analysis table w/ calcs
 *	2/3 
 *
 * @param int. Study Group ID.
 * @return bool?
 */
function calc_and_set_unique_analysis_ids_for_group( $study_group_id ){

	global $wpdb;
	
	//get all dyads for this group
	$all_ims = get_unique_ims_for_study_group_pop( $study_group_id );
	
	//get specific info to calculate multi_component, complexity, outcome types, participation or exposure
	$component_complexity_ims = get_all_ims_for_study_group( $study_group_id ); //get specific im data for these calcs, input to calc functions
		
	//remove all analysis rows of this study group from analysis table
	$analysis_del_row = $wpdb->delete( 
			$wpdb->transtria_analysis, 
			array( 
				'StudyGroupingID' => (int)$study_group_id 
			)
		);
	
	
	//var_dump( $all_ims );
	$placeholder = 0;
	
	//get population data for calculating pop results
	$pop_data_by_study_id = get_pop_data_study_group( $study_group_id ); //get this ONCE for all the ims
	
	//calculate study-grouping-level analysis variables (Study Grouping, Domestic/International settings)
	$study_design = calculate_study_design_for_studygrouping( (int)$study_group_id ); //if "0", we will need drop down..
	$domestic_intl = calculate_domestic_intl_for_analysis( (int)$study_group_id );
	
	//calculate IM-level analysis variables (multi_component, complexity, outcome_type, participation or exposure
	$multi_component = calculate_multi_component_ims( $component_complexity_ims );
	$complexities = calculate_complexity_ims( $component_complexity_ims );
	$measures_outcome_types = calculate_outcome_types_ims( $component_complexity_ims ); //get measures => outcome types for study group/analysis
	$participation_exposure = calculate_participation_exposure_ims( $component_complexity_ims ); //calculate participation_exposure
	
	//calculate IM-level analysis variables: population calcs
	$hr_black_calc = calculate_hr_black_ims( $component_complexity_ims ); 
	$hr_asian_calc = calculate_hr_asian_ims( $component_complexity_ims ); 
	$hr_nativeamerican_calc = calculate_hr_nativeamerican_ims( $component_complexity_ims ); 
	$hr_pacificislander_calc = calculate_hr_pacificislander_ims( $component_complexity_ims ); 
	$hr_hispanic_calc = calculate_hr_hispanic_ims( $component_complexity_ims ); 
	$hr_lowincome_calc = calculate_hr_lowincome_ims( $component_complexity_ims ); 
	
	//calculate IM-level analysis variables: representativeness calc
	$representativeness_calc = calculate_representativeness_ims( $component_complexity_ims ); 
	
	//calculate IM-level analysis variables: stage
	$stage = calculate_stage_ims( $component_complexity_ims ); 
	$applicability_hr_pops = calculate_applicability_hr_pops_ims( $component_complexity_ims ); 
	
	//analysis calcs based on analysis-calc'd vars
	$potential_reach = calculate_pop_potential_reach_ims( $participation_exposure, $representativeness_calc );
	$potential_hr_reach = calculate_hr_pop_potential_reach_ims( $representativeness_calc, $hr_black_calc, $hr_asian_calc, $hr_nativeamerican_calc, $hr_pacificislander_calc, $hr_hispanic_calc, $hr_lowincome_calc );

	
	$outcome_types_hr = array(); //will hold analysis_index => outcome type (since type is tied to measure, nothing hr there)
	$result_populations_hr = array(); //will hold analysis_index => population calc result
	
	
	
	/***** UPDATE Analysis table w/vars *****/
	//calculate duration, duplicate, effectiveness_general for each unique im; INSERT INTO analysis table
	foreach( $all_ims as $analysis_index => $one_im ){
		
		$measure = $one_im[ "measure" ];
		$measure_val = $one_im[ "measure_value" ];
		
		$indicator = $one_im[ "indicator" ];
		
		//change indicator val to Laura's table
		$indicator_val = $one_im[ "indicator_value" ];
		$indicator_val_tt = cc_transtria_analysis_val_lookup( "indicator_value", $indicator_val );
		
		$info_id_list = $one_im[ "info_id_list" ];
		$ea_direction = $one_im[ "net_effects" ];
		$evalpop = $one_im[ "result_evaluation_population" ];
		$subpopYN = $one_im[ "result_subpopulationYN" ];
		$subpop = $one_im[ "result_subpopulation" ];
		
		$duration = "";
		
		//outcome type depends on measure
		$type = $measures_outcome_types[ $measure ];
		$outcome_types_hr[ $analysis_index ] = $type; //for later usage
		
		/***** CALCULATE AND UPDATE W POPULATION VARS; calculate HR vars (HR info_id list comes from population calc) *****/
		$result_pop_result = calculate_pop_subpop_analysis( $pop_data_by_study_id, $info_id_list, $evalpop, $subpopYN, $subpop, $study_group_id );
		$info_id_list_hr = $result_pop_result['info_id_list_hr'];
		$result_populations_hr[ $analysis_index ] = $result_pop_result;
		
		//calculate direction, duration
		//Only ONE I-M dyad, no duplicates: set ea_direction, duration
		if( strpos( $info_id_list, "," ) === false ){
			$duplicate_im = "N";
			
			$this_intermediate_im = get_single_im_from_intermediate( $info_id_list );
			//var_dump( $this_intermediate_im );
			//$duration = $this_intermediate_im[ "outcome_duration" ];
			$duration = calculate_duration_for_analysis_single( $this_intermediate_im[ "outcome_duration" ] );
			$strategies = calculate_strategies_for_info_id_list( $info_id_list );
			
		} else {
			$duplicate_im = "Y";
			
			//remove duplicate IM duplicates from list
			$exploded_infos = explode(", ", $info_id_list );
			$new_infos = array_unique( $exploded_infos );
			$info_id_list = implode( ", ", $new_infos );
			
			//get ea data for each study here (to send to calc functions)
			$parsed_id_array = parse_study_seq_id_list( $info_id_list );
			$studygroup_ea_data = array();
			foreach( $parsed_id_array as $s_id => $vals ){
				//get ea data (seq) for this study
				$studygroup_ea_data[ $s_id ] = get_ea_analysis_data( $s_id );
			}
			
			//calculate duration, net_effect, etc, based on info_id_list - modulate
			$duration = calculate_duration_for_analysis_duplicates( $info_id_list, $studygroup_ea_data );
			$strategies = calculate_strategies_for_info_id_list( $info_id_list );
			
		}
		
		
		//parse strategies (serialized) into up-to-5 individual ones
		$uncereal_strats = unserialize( $strategies );
		$strategy_1 = "";
		$strategy_2 = "";
		$strategy_3 = "";
		$strategy_4 = "";
		$strategy_5 = "";
		$strategy_1_text_ = "";
		$strategy_2_text = "";
		$strategy_3_text = "";
		$strategy_4_text = "";
		$strategy_5_text = "";
		$strat_count = 1;
		
		foreach( $uncereal_strats as $strat_i => $strat_v ){
			$text_name = $strat_count . "_text";
			if( $strat_v == 999 ){
				${ 'strategy_' . $strat_count } = 999;
				${ 'strategy_' . $strat_count . '_text'} = "Not applicable";
			} else {
				${ 'strategy_' . $strat_count } = $strat_i;
				${ 'strategy_' . $strat_count . '_text'} = $strat_v;
			}
		
			$strat_count++;
		}
		
		
		//calculate effectiveness for this analysis id
		$effectiveness_gen = calc_general_effectiveness_analysis( $study_design, $duration, $ea_direction, $type );
		
		//add these to analysis table
		$spartacus = $wpdb->prepare(
			"
				INSERT INTO $wpdb->transtria_analysis
				( info_id, StudyGroupingID, domestic_international, indicator_value, indicator_value_tt, indicator, measure_value, measure, 
					info_id_list, info_id_list_hr, duplicate_ims, study_design, net_effects, duration, outcome_type, indicator_strategies, 
					effectiveness_general, multi_component, complexity, participation_exposure, result_evaluation_population, result_subpopulationYN, result_subpopulation, result_population_result,
					strategy_1, strategy_1_text, strategy_2, strategy_2_text, strategy_3, strategy_3_text, strategy_4, strategy_4_text, strategy_5, strategy_5_text,
					hr_black, hr_asian, hr_nativeamerican, hr_pacificislander, hr_hispanic, hr_lowerincome, representativeness, 
					potential_pop_reach, potential_hr_pop_reach, stage, applicability_hr_pops )
				VALUES ( %s, %d, %d, %s, %d, %s, %d, %s, 
					%s, %s, %s, %s, %s, %s, %s, %s, 
					%s, %d, %d, %d, %s, %s, %s, %s, 
					%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, 
					%d, %d, %d, %d, %d, %d, %d, 
					%d, %d, %d, %d )
			", 
			$analysis_index, $study_group_id, $domestic_intl, $indicator_val, $indicator_val_tt, $indicator, $measure_val, $measure,
			$info_id_list, $info_id_list_hr, $duplicate_im, $study_design, $ea_direction, $duration, $type, $strategies,
			$effectiveness_gen, $multi_component, $complexities, $participation_exposure, $evalpop, $subpopYN, $subpop, $result_pop_result["population_calc"],
			$strategy_1, $strategy_1_text, $strategy_2, $strategy_2_text, $strategy_3, $strategy_3_text, $strategy_4, $strategy_4_text, $strategy_5, $strategy_5_text,
			$hr_black_calc, $hr_asian_calc, $hr_nativeamerican_calc, $hr_pacificislander_calc, $hr_hispanic_calc, $hr_lowincome_calc, $representativeness_calc,
			$potential_reach, $potential_hr_reach, $stage, $applicability_hr_pops
		);
		
		$help_me = $wpdb->query( $spartacus );
		
	
	}
	/*****  UPDATE Study Grouping table/vars *****/
	//remove all rows of this study group from studygrouping table
	$sg_del_row = $wpdb->delete( 
			$wpdb->transtria_analysis_studygrouping, 
			array( 
				'StudyGroupingID' => (int)$study_group_id 
			)
		);
	
	//unset some things
	unset( $measures_outcome_types );
	unset( $pop_data_by_study_id );
	
	/***** UPDATE Analysis table w/ HR vars *****/
	//Run secondary analysis, calculating hr variables from info_id_list_hr set above
	foreach( $all_ims as $analysis_index => $one_im ){
		
		//get all info ids HR for this study group (set individually above), as shown in Analysis table
		$which_info_ids = get_info_id_list_hr_studygroup( $study_group_id );
		
		//var_dump( $which_info_ids );
		
		//return 'arkgn';
		$study_design_hr = calculate_study_design_for_info_id_list( $which_info_ids ); //if "0", we will need drop down..
	
		$net_effects_hr = calculate_net_effect_for_info_id_list( $which_info_ids );
	
		//calculate HR variables: study_design_hr, duration_hr, net_effects_hr, outcome_type_hr
		//Only ONE I-M dyad, no duplicates: set ea_direction, duration
		if( strpos( $which_info_ids, "," ) === false ){
			//$duplicate_im_hr = "N";
			
			$this_intermediate_im = get_single_im_from_intermediate( $which_info_ids );
			$duration_hr = calculate_duration_for_analysis_single( $this_intermediate_im[ "outcome_duration" ] );
			
		} else {
		
			//get ea data for each study here (to send to calc functions)
			$parsed_id_array = parse_study_seq_id_list( $which_info_ids );
			$studygroup_ea_data = array();
			foreach( $parsed_id_array as $s_id => $vals ){
				//get ea data (seq) for this study
				$studygroup_ea_data[ $s_id ] = get_ea_analysis_data( $s_id );
			
			}
			
			//calculate duration, net_effect, etc, based on info_id_list - modulate
			$duration_hr = calculate_duration_for_analysis_duplicates( $which_info_ids, $studygroup_ea_data );
			
		}
	
	
		//calculate effectiveness hr and insert into table
		$this_outcome_type = $outcome_types_hr[ $analysis_index ];
		$this_pop = $result_populations_hr[ $analysis_index ]["population_calc"];
		if( ( $this_outcome_type == 1 ) || ( $this_outcome_type == 2 ) ){
			//now make sure we're in the right pops
			if( ( (int)$this_pop > 4 ) && ( (int)$this_pop < 12 ) ){
			
				if( ( $study_design_hr == 1 ) && ( $net_effects_hr == 1 ) && ( ( $duration_hr == 2 ) || ( $duration_hr == 3 ) ) ){
					//effective
					$effectiveness_hr = 1;
				} else if( ( $study_design_hr == 1 ) && ( $net_effects_hr == 1 ) && ( $duration_hr == 1 ) ){
					//somewhat effective
					$effectiveness_hr = 2;
				} else if( ( $study_design_hr == 1 ) && ( ( $net_effects_hr == 2 ) || ( $net_effects_hr == 3 ) ) ){
					//not effective
					$effectiveness_hr = 3;
				} else if( ( $study_design_hr == 2 ) && ( $net_effects_hr == 1 ) ){
					//positive association
					$effectiveness_hr = 4;
				} else if( ( $study_design_hr == 2 ) && ( $net_effects_hr == 2 ) ){
					//no association
					$effectiveness_hr = 5;
				} else if( ( $study_design_hr == 2 ) && ( $net_effects_hr == 3 ) ){
					//negative association
					$effectiveness_hr = 6;
				} else {
					//insufficient information
					$effectiveness_hr = 999;
				}
			
			} else {
				$effectiveness_hr = "population calc: " . $this_pop;
			}
		
		} else {
			$effectiveness_hr = "outcome type: " . $this_outcome_type;
		}
		
		//insert these hr values into analysis table
		$spartacus_two = $wpdb->prepare(
			"
			UPDATE $wpdb->transtria_analysis
			SET study_design_hr = %s,
				net_effects_hr = %s,
				duration_hr = %s,
				effectiveness_hr = %s
			WHERE info_id = %s
			",
			$study_design_hr,
			$net_effects_hr,
			$duration_hr,
			$effectiveness_hr,
			$analysis_index
			
		);
		
		$help_me = $wpdb->query( $spartacus_two );
		
	
	}
	
	
	//Update Studygrouping-level vars: Study Design
	$spartacus_designed = $wpdb->prepare( 
		"
			INSERT INTO $wpdb->transtria_analysis_studygrouping
			( StudyGroupingID, study_design, study_design_hr )
			VALUES ( %d, %s, %s )
		", 
		$study_group_id,
		$study_design, 
		$study_design_hr
	);
	
	$wpdb->query( $spartacus_designed );
	
	
	//unset some things
	unset( $all_ims );
	
	return 1;
	
}


/**
 * Tertiary data analysis, from vars added via front-end Analysis form
 *
 * @param int. Study Grouping ID.
 *
*/
function recalc_analysis_vars_form_data( $study_group_id ){

	global $wpdb; 
	
	//what needs to be updated when the study design, net effect (... )are updated?
	$analysis_vars = get_analysis_vars_for_group( $study_group_id );
	
	//recalc effectiveness
	//get design (Study Grouping level)
	$sg_vars = get_studygrouping_vars( $study_group_id );
	$design = $sg_vars["study_design"];
	
	$duration = 0;
	$effect = 0;
	$type = 0;
	
	
	//get other vars for effectiveness (analysis level)
	foreach( $analysis_vars as $index => $a_vals ){
	
		//var_dump( $index );
		$this_info_id = $a_vals[ "info_id" ];
		
		//pull out pieces of info
		$duration = $a_vals[ "duration" ];
		$effect = $a_vals[ "net_effects" ];
		$type = $a_vals[ "outcome_type" ];
		$result_pop = $a_vals[ "result_population_result" ];
		
		//info for implementation
		$stage = (int)$a_vals[ "stage" ];
		$state = (int)$a_vals[ "state" ];
		$quality = (int)$a_vals[ "quality" ];
		$inclusiveness = (int)$a_vals[ "inclusiveness" ];
		
		//info for scale, hr scale, dose, pop impact, hr pop impact
		$access = (int)$a_vals[ "access" ];
		$size = (int)$a_vals[ "size" ];
		$applicability = (int)$a_vals[ "applicability_hr_pops" ];
		$pop_reach = (int)$a_vals[ "potential_pop_reach" ];
		$hr_pop_reach = (int)$a_vals[ "potential_pop_reach" ];
		
		//get and massage effectiveness data (TODO: make this an int and let Transtria find their own data errors)
		$effectiveness = $a_vals[ "effectiveness_general" ]; 
		//if > 1 char, is error message (poor planning Mel!), else is int
		if( strlen( $effectiveness > 1 ) || strlen( $effectiveness == 0 ) ){ //error
			$effectiveness = 0;
		} else {
			$effectiveness = (int)$effectiveness;
		}
		$effectiveness_hr = $a_vals[ "effectiveness_hr" ]; 
		//if > 1 char, is error message (poor planning Mel!), else is int
		if( strlen( $effectiveness_hr > 1 ) || strlen( $effectiveness_hr == 0 ) ){ //error
			$effectiveness_hr = 0;
		} else {
			$effectiveness_hr = (int)$effectiveness_hr;
		}
		
		
		//HR calc stuff
		$which_info_ids = get_info_id_list_hr_studygroup( $study_group_id );
		$study_design_hr = calculate_study_design_for_info_id_list( $which_info_ids ); //if "0", we will need drop down..
		$net_effects_hr = calculate_net_effect_for_info_id_list( $which_info_ids );
		
		//duration
		if( strpos( $which_info_ids, "," ) === false ){
			$this_intermediate_im = get_single_im_from_intermediate( $which_info_ids );
			$duration_hr = calculate_duration_for_analysis_single( $this_intermediate_im[ "outcome_duration" ] );
			
		} else {
		
			//get ea data for each study here (to send to calc functions)
			$parsed_id_array = parse_study_seq_id_list( $which_info_ids );
			$studygroup_ea_data = array();
			foreach( $parsed_id_array as $s_id => $vals ){
				//get ea data (seq) for this study
				$studygroup_ea_data[ $s_id ] = get_ea_analysis_data( $s_id );
			
			}
			
			//calculate duration, net_effect, etc, based on info_id_list - modulate
			$duration_hr = calculate_duration_for_analysis_duplicates( $which_info_ids, $studygroup_ea_data );
			
		}
		
		
		if( ( $type == 1 ) || ( $type == 2 ) ){
			//now make sure we're in the right pops
			if( ( (int)$result_pop > 4 ) && ( (int)$result_pop < 12 ) ){
			
				if( ( $study_design_hr == 1 ) && ( $net_effects_hr == 1 ) && ( ( $duration_hr == 2 ) || ( $duration_hr == 3 ) ) ){
					//effective
					$effectiveness_hr = 1;
				} else if( ( $study_design_hr == 1 ) && ( $net_effects_hr == 1 ) && ( $duration_hr == 1 ) ){
					//somewhat effective
					$effectiveness_hr = 2;
				} else if( ( $study_design_hr == 1 ) && ( ( $net_effects_hr == 2 ) || ( $net_effects_hr == 3 ) ) ){
					//not effective
					$effectiveness_hr = 3;
				} else if( ( $study_design_hr == 2 ) && ( $net_effects_hr == 1 ) ){
					//positive association
					$effectiveness_hr = 4;
				} else if( ( $study_design_hr == 2 ) && ( $net_effects_hr == 2 ) ){
					//no association
					$effectiveness_hr = 5;
				} else if( ( $study_design_hr == 2 ) && ( $net_effects_hr == 3 ) ){
					//negative association
					$effectiveness_hr = 6;
				} else {
					//insufficient information
					$effectiveness_hr = 999;
				}
			
			} else {
				$effectiveness_hr = "population calc: " . $result_pop;
			}
		
		} else {
			$effectiveness_hr = "outcome type: " . $type;
		}
		
		
		$new_effectiveness = calc_general_effectiveness_analysis( $design, $duration, $effect, $type );
		
		//var_dump( $this_info_id );
		//implementation, implementation inclusiveness (based on analysis data set on screen)
		$implementation = calculate_implementation( $stage, $state, $quality );
		$implementation_inclusiveness = calculate_implementation_inclusiveness( $stage, $state, $quality, $inclusiveness );
		
		$scale = calculate_scale( $access, $size );
		var_dump($access, $size);
		$hr_scale = calculate_hr_scale( $access, $size, $applicability );
		$dose = calculate_dose( $implementation, $scale );
		$population_impact = calculate_population_impact( $effectiveness, $pop_reach, $dose );
		$population_impact_hr = calculate_hr_population_impact( $effectiveness_hr, $hr_pop_reach, $dose );
		
	
		//insert new vals by info_id
		$data = array(
			"effectiveness_general" 		=> $new_effectiveness,
			"effectiveness_hr" 				=> $effectiveness_hr,
			"implementation"				=> $implementation,
			"implementation_inclusiveness"	=> $implementation_inclusiveness,
			"scale"							=> $scale,
			"hr_scale"						=> $hr_scale,
			"dose"							=> $dose,
			"population_impact"				=> $population_impact,
			"hr_population_impact"			=> $population_impact_hr
			);
			
		$where = array( 
			'info_id' => $this_info_id
		);

		$result = $wpdb->update( $wpdb->transtria_analysis, $data, $where, $format = null, $where_format = null );
		
	}
	
	

	return "recalc'd complete: " . $result;
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
	
	//if we've got nothing, return false 
	if( !empty( $codetype_id ) ){
		
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
		
		//var_dump( $code_table_measures );
		//since there should only be one measure, "other measure" will be accounted for ONLY if nothing seleted in drop down
		if( empty( $code_table_measures ) ){
		
			$ea_sql = $wpdb->prepare( 
				"
				SELECT      measures_other
				FROM        $wpdb->transtria_effect_association
				WHERE		StudyID = %d 
				AND 		seq = %d
				",
				$study_id,
				$seq
			); 
		
			//var_dump( $ea_sql);
			$ea_val = $wpdb->get_var( $ea_sql );
			
			//var_dump( $ea_rows );
			$all_measure_numbers_names[ $ea_val ] = $ea_val;
			
		} else {
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
		
		}
		
	} else {
		return false;
	}
	//Get 'other measure' for this ea tab and append to array
	//var_dump( $all_measure_numbers_names ); //should ONLY be one measure, indexed by code result value => descr.
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
		SELECT      seq, outcome_direction, outcome_type, outcome_type_other, significant, duration, duration_notreported, 
			indicator_strategies_directions, result_subpopulation
		FROM        $wpdb->transtria_effect_association
		WHERE		StudyID = %d
		",
		$study_id
	); 
	
	$form_rows = $wpdb->get_results( $ea_sql, ARRAY_A );
	
	//now, we need to do lookups on some of these fields (to translate from lookup table)
	foreach( $form_rows as $one_seq ){
	
		//var_dump( $one_seq );
		
		//hmm, just put in values
		//$new_outcome_direction = get_single_codetypeid_descr_by_value( 64, $one_seq["outcome_direction"] ); //64 = "Results Outcome Direction"
		$new_outcome_direction = $one_seq["outcome_direction"]; //64 = "Results Outcome Direction"
		
		if( $one_seq["duration_notreported"] != "Y" ){
			$new_outcome_duration = get_single_codetypeid_descr_by_value( 9, $one_seq["duration"] ); //9 = "Duration"
		} else {
			$new_outcome_duration = "not reported";
		}
		
		//var_dump ( $one_seq["outcome_type_other"]);
		if( ( $one_seq["outcome_type_other"] == "" ) || empty( $one_seq["outcome_type_other"] ) ){
			//$new_outcome_type = get_single_codetypeid_descr_by_value( 54, $one_seq["outcome_type"] ); //64 = "OutcomeType"
			//we can just return the value here, since it correspond's to Laura's algorithms
			$new_outcome_type = $one_seq["outcome_type"]; 
		} else {
			$new_outcome_type = "Other: " . $one_seq["outcome_type_other"];
		}
		
		//deal with serialized indicator/strategies/directions to get indicator["string"] => Direction
		$unserialized_inds = unserialize( $one_seq["indicator_strategies_directions"] );
		$indicators_directions = array();
		$indicators_strategies = array();
		//var_dump( $unserialized_inds["indicators"] );
		if( !empty( $unserialized_inds ) && ( $unserialized_inds != false ) ){
			foreach( $unserialized_inds["indicators"] as $index => $details ){
			
				//var_dump( $index );
				//var_dump( $details );
				$indicators_directions[ $index ] = $details["direction"];
			}
			foreach( $unserialized_inds["indicators"] as $which_ind => $details ){
			
				//var_dump( $index );
				//var_dump( $details );
				$indicators_strategies[$which_ind] = $details["strategies"];
			}
		} else {
			//TODO: are we looking for old vars here?
		}
		
		//also need to get result subpopulation and result evaluation population (both multi, although eval should only have one val..)
		//Result Evaluation Population = "ea_# Results Populations"; Result Subpopulations = "ea_# Results SubPopulations"
		$result_eval_codetype = "ea_" . $one_seq["seq"] . " Results Populations";
		$result_subpop_codetype = "ea_" . $one_seq["seq"] . " Results SubPopulations";
		$result_eval_pop = get_code_results_by_study_codetype( $study_id, $result_eval_codetype );
		$result_sub_pop = get_code_results_by_study_codetype( $study_id, $result_subpop_codetype );
		
		//var_dump( current( $result_eval_pop ) );
		//var_dump( serialize( current( $result_eval_pop) ) );
		//var_dump( current( $result_sub_pop ) );
		//var_dump( serialize( current( $result_sub_pop) ) );
		
		
		$translated_ea[ $one_seq["seq"] ] = array(
				"outcome_direction" => $new_outcome_direction,
				"outcome_type" => $new_outcome_type,
				"outcome_duration" => $new_outcome_duration,
				"significant" => $one_seq["significant"],
				"indicator_directions" => $indicators_directions,
				"indicator_strategies" => $indicators_strategies,
				"indicators_strategies_directions" => $unserialized_inds,
				"result_subpopulationYN" => $one_seq["result_subpopulation"],
				"result_evaluation_population" => serialize( current( $result_eval_pop ) ),
				"result_subpopulation" => serialize( current( $result_sub_pop ) )
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
function get_single_study_analysis_data( $study_id ){

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
	
	//let's int these
	foreach( $values as $i => $stuff ){
		if( is_int( (int)$i ) ){
			$values[(int)$i] = $stuff;
		
		}
	}

	return $values;
}

/**
 * Returns a comma-separated list of hr info_ids for a given study group
 *
 * @param
 * @return
 */
function get_info_id_list_hr_studygroup( $study_group_id ){

	global $wpdb;
	
	$info_id_hr_sql = $wpdb->prepare(
		"
		SELECT info_id_list_hr
		FROM $wpdb->transtria_analysis
		WHERE StudyGroupingID = %d
		",
		$study_group_id
		);
		
	$lists = $wpdb->get_results( $info_id_hr_sql, ARRAY_N );
	$master_hr_list = ""; //eventually what we want
	
	foreach( $lists as $one_list ){
	
		//var_dump( $one_list );
		if( $master_hr_list == "" ){ //nothing in the lsit yet
			$master_hr_list = current( $one_list );
		} else { //we have things, so prepend w comma-space
			$master_hr_list .= ", " . current( $one_list );
		}

	}

	return $master_hr_list;

}