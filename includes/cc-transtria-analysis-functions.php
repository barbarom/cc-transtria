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

/**
 * Calculats the EA direction across unique ids in a given Analysis ID/Study grouping
 */
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
	
	//var_dump( $num_directions );
	
	//if > 50% of directions == 1, return 1
	if( $num_directions > 0 ){
		if( ( $instance_directions[1] / $num_directions ) > 0.5 ){
			return 1;
		} else if ( ( ( $instance_directions[1] / $num_directions ) == 0.5 ) && ( ( $instance_directions[2] / $num_directions ) == 0.5 ) ||
			( ( $instance_directions[3] / $num_directions ) > 0.5 ) ){
			return 2;
		} else if ( ( $instance_directions[2] / $num_directions ) > 0.5 ){
			return 3;
		}
		
	
	}
	//otherwise, we have a data error
	return "data error: ea dir for SG";

}

/**
 * Calculated the EA direction across unique ids in a given Analysis ID/Study grouping
 */
function calculate_net_effect_for_info_id_list( $info_id_list ){

	//var_dump( $info_id_list );
	//TODO: modularize this
	if( strpos( $info_id_list, "," ) === false ){
		$info_implode = "'" . $info_id_list . "'";
	
	} else {
		$info_explode = explode(", ", $info_id_list );
		$info_implode = implode( "', '", $info_explode ); //adding quotes for mysql happy times
		//pre and post-pend
		$info_implode = "'" . $info_implode . "'";
	} 
	
	global $wpdb;
	
	//get all calc_ea_direction for tihs study group
	$direction_sql = 
		"
		SELECT info_id, calc_ea_direction
		FROM $wpdb->transtria_analysis_intermediate
		WHERE info_id IN ( $info_implode )
		"		
		;
		
	$form_rows = $wpdb->get_results( $direction_sql, OBJECT_K );
	
	$all_directions = array();

	//var_dump( $info_id_list );
	//var_dump( $direction_sql );
	
	//put all study designs in single array to evaluate
	foreach( $form_rows as $info_id => $values ){
		array_push( $all_directions, $values->calc_ea_direction );
	}
	
	//now the tricky algorithm
	$num_directions = count( $all_directions ); 
	$instance_directions = array_count_values ( $all_directions );
	
	//var_dump( $instance_directions );
	
	//if > 50% of directions == 1, return 1
	if( $num_directions > 0 ){
		if( ( $instance_directions[1] / $num_directions ) > 0.5 ){
			return 1;
		} else if ( ( ( $instance_directions[1] / $num_directions ) == 0.5 ) && ( ( $instance_directions[2] / $num_directions ) == 0.5 ) ||
			( ( $instance_directions[3] / $num_directions ) > 0.5 ) ){
			return 2;
		} else if ( ( $instance_directions[2] / $num_directions ) > 0.5 ){
			return 3;
		}
	}
	
	//otherwise, we have a data error
	return "data error";

}

/** Returns a study design for all studies in a group
 *
 * @param int. Study Grouping
 * @return int? string?
 */
function calculate_study_design_for_studygrouping( $studygrouping_id ){

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
	
	//Evaluate against StudyDesign algorithm
	//The algorithm numbers are DIFFERENT here than in Laura's notes, because of the way they are assigned in the codetbl already. //99=StudyDesign
	$one_array = array( "1", "2", "3", "4", "5", "6", "7", "8", "12" );
	
	//if study design for ANY studies == a number in the $one_array, return 1
	$check_intersect = array_intersect( $one_array, $all_designs );
	
	if( !empty( $check_intersect ) ){
		return 1;
	}
	
	//else if study design is "other", return 0 (which means Assign!)
	else if( in_array( "11", $all_designs ) ){
		return 0;
	}
	
	//else if study design is NULL, return 0 (which means Assign!)
	else if( in_array( "null", $all_designs ) ){
		return 0;
	}
	
	//else if all values are the same and are 9 (cross-sectional), return 2
	else if( count( array_unique( $all_designs ) ) === "1" && end( $all_designs ) == "9" ) {
		return 2;
	}
	
	return $new_design;
	
}


/** Returns a study design for all studies in info_id_list
 *
 * @param int. Study Grouping
 * @return int? string?
 */
function calculate_study_design_for_info_id_list( $info_id_list ){

	global $wpdb;
	
	//get all study ids (in list) in this info_id group
	$study_list = parse_studyids_from_infoids( $info_id_list );
	
	if( count( $study_list ) > 1 ){
		$study_id_list = implode(", ", $study_list ); 
	} else {
		$study_id_list = current( $study_list );
	}
	
	//TODO: modularize this
	if( strpos( $study_id_list, "," ) === false ){
		$info_implode = "'" . $study_id_list . "'";
	
	} else {
		$info_explode = explode(", ", $study_id_list );
		$info_implode = implode( "', '", $info_explode ); //adding quotes for mysql happy times
		//pre and post-pend
		$info_implode = "'" . $info_implode . "'";
	} 
	
	//get all study designs for this list of study ids
	$design_sql = 
		"
		SELECT StudyID, StudyDesignID
		FROM $wpdb->transtria_studies
		WHERE StudyID in ($info_implode)
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
	
	$check_intersect = array_intersect( $one_array, $all_designs );
	
	if( !empty( $check_intersect ) ){
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
		SELECT StudyID, domestic_setting, international_setting, domesticintlsetting_notreported
		FROM $wpdb->transtria_studies
		WHERE StudyID in ($study_list)
		ORDER BY StudyID
		"		
		;
		
	$form_rows = $wpdb->get_results( $domestic_sql, OBJECT_K );
	
	$all_domestics = array();
	$all_intls = array();
	$all_notreported = array();
	
	//put domestic international in a few arrays, to evaluate per the algorithm
	foreach( $form_rows as $study_id => $values ){
		//if "Not reported" ISN'T checked
		if( $values->domesticintlsetting_notreported != "Y" ){
			array_push( $all_domestics, $values->domestic_setting );
			array_push( $all_intls, $values->international_setting );
		} else {
			array_push( $all_notreported, $values->domesticintlsetting_notreported );
		}
	}
	
	//it's getting all algorithmic in here
	// if domestic setting = 1 and intl setting =1 for any study in group, return 3
	if( ( in_array( "Y", $all_domestics ) ) && ( in_array( "Y", $all_intls ) ) ){
		return 3;
	} else if( count( array_unique( $all_domestics ) ) === 1 && end( $all_domestics ) == "Y" ) { 
		//else if all domestic settings = 1, return 1
		return 1;
	} else if( count( array_unique( $all_intls ) ) === 1 && end( $all_intls ) == "Y" ) { 
		//else if all intl settings == 1, return 2
		return 2;
	} 
	
	//else, test for all not reported
	else if( !empty( $all_notreported ) && empty( $all_domestics ) && empty( $all_intls ) ){
		return 999;
	}
	
	return 0;

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
	else if( in_array( "not reported", $temp_durations ) ) { return "not reported"; }
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
		case "not reported":
			return "not reported";
			break;
		default:
			return "no data";
			break;
	
	}

}

/*** CALCULATIONS BASED ON IM-SPECIFCI DATA  ****/
/**
 * Returns the Measure - Outcome Type pairs for a Study Grouping. Outcome Type (seq) must be the SAME for Measure types (seq) across a SG.
 * 	There *should* only be one measure per ea tab (seq) and IS only one Outcome Type per.
 *
 * @param array. IM data.
 * @return array. Key->val pairs for (string) Measure => (string) Outcome Type
 */
function calculate_outcome_types_ims( $all_ims ){

	global $wpdb;
	
	//Get Measures and outcome types for each
	//$all_ims = get_all_ims_for_study_group( $study_group_id );
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
 * Returns the multi-component for the im data provided
 *
 * @param array. IM data.
 * @return int.
*/ 
function calculate_multi_component_ims( $all_ims ){

	global $wpdb;
	
	//Get Measures and outcome types for each
	//$all_ims = get_all_ims_for_study_group( $study_group_id );
	$multi_components = array();
	
	//populate array of mcs; return 1 is ANY are one
	foreach( $all_ims as $one_im ){
		$this_mc = $one_im["multi_component"];
		
		if( ( $this_mc == 1 ) || ( $this_mc == "1" ) ){
			return 1;
		}
		
		array_push( $multi_components, $this_mc );

	}

	//if no mcs = 1, go through rest of algorithm
	if( count( array_unique( $multi_components ) ) === 1 && ( end( $multi_components ) == "1" || ( end( $multi_components ) == 1 ) ) ){
		return 0;
	} else if( count( array_unique( $multi_components ) ) === 1 && ( end( $multi_components ) == "999" || ( end( $multi_components ) == 999 ) ) ){
		return 999;
	} 
	
	return 0;

}

/**
 * Returns the complexity for the im data provided
 *
 * @param array. IM data for all IMs in a study group.
 * @return int.
*/ 
function calculate_complexity_ims( $all_ims ){

	$complexities = array();
	
	//populate array of mcs; return 1 is ANY are one
	foreach( $all_ims as $one_im ){
		$this_mc = $one_im["complexity"];
		
		if( ( $this_mc == 1 ) || ( $this_mc == "1" ) ){
			return 1; //If complex = 1 (for any Unique ID in grouping), then complexity = 1
		}
		
		array_push( $complexities, $this_mc );

	}

	//if no mcs = 1, go through rest of algorithm
	if( count( array_unique( $complexities ) ) === 1 && ( end( $complexities ) == "1" || ( end( $complexities ) == 1 ) ) ){
		return 0; //Else If complex = 0 (for ALL Unique IDs in grouping), then complex = 0
	} else if( count( array_unique( $complexities ) ) === 1 && ( end( $complexities ) == "999" || ( end( $complexities ) == 999 ) ) ){
		return 999; //Else If complex = 999 or NULL (for ALL Unique IDs in grouping), then complex = 999
	} 
	
	return 0;

}

/**
 * Returns the Participation or Exposure for the study grouping
 *
 * @param array. IM data for all IMs in a study group.
 * @return int.
*/ 
function calculate_participation_exposure_ims( $all_ims ){

	$participations = array();
	$exposures = array();
	
	//populate array of mcs; return 1 is ANY are one
	foreach( $all_ims as $one_im ){
		$this_part = $one_im["rate_of_participation"];
		$this_exposure = $one_im["exposure_frequency"];
		
		//1 = High, if Participation = 1 OR (Participation = 999 AND Potential Exposure = 1) (for any Unique ID in grouping)
		if( ( $this_part == 1 ) || ( $this_part == "1" ) ){ 
			return 1;
		} else if( ( ( $this_part == 999 ) || ( $this_part == "999" ) ) && ( ( $this_exposure == 1 ) || ( $this_exposure == "1" ) ) ){ 
			return 1;
		}
		
		array_push( $participations, $this_part );
		array_push( $exposures, $this_exposure );

	}

	//2 = Low, if Participation = 2 OR (Participation = 999 AND Potential Exposure = 2) (for ALL Unique IDs in grouping) 
	if( count( array_unique( $participations ) ) === 1 && ( end( $participations ) == "2" || ( end( $participations ) == 2 ) ) ){
		return 2; 
	} else if( ( count( array_unique( $participations ) ) === 1 && ( end( $participations ) == "999" || ( end( $participations ) == 999 ) ) ) &&
		( count( array_unique( $exposures ) ) === 1 && ( end( $exposures ) == "2" || ( end( $exposures ) == 2 ) ) ) ){
		return 2; 
	} 
	
	//else, return 999 (participation AND exposure == 999)
	return 999;

}

/**
 * Returns the HR African American/Black values for incoming IM data
 *
 * @param array. IM data for all IMs in a study group.
 * @return int.
*/ 
function calculate_hr_black_ims( $all_ims ){

	$ipe_data_highest = floatval( 999 );
	
	//populate ipe_data_highest with highest percents;
	foreach( $all_ims as $one_im ){
		$this_pct_string = $one_im["ipe_pctblack"];
		$this_pct = floatval ( $this_pct_string );
		
		//var_dump( $this_pct );
		
		//1 = High, if IPE “pctblack” = 100 (for any Unique ID in grouping)
		if( (int)$this_pct == 100 ){ 
			return 1; //return 1 if IPE “pctblack” = 100 (for any Unique ID in grouping)
		} 
		
		//otherwise, if current percent > ipe_data_highest, put higher val in ipe_data_highest (unless 999)
		//if we HAVE a percent, that trumps 999
		if( ( (int)$ipe_data_highest == 999 ) && ( ( $this_pct != 0 ) || ( $this_pct != "0" ) || ( $this_pct != "NULL" ) || ( $this_pct != NULL ) ) ) {
			$ipe_data_highest = $this_pct;
		}			
		if( ( $this_pct > $ipe_data_highest ) && ( ( $this_pct != 999 ) || ( $this_pct != "999" ) ) ){
			$ipe_data_highest = $this_pct;
		}

	}
	
	//var_dump( $ipe_data_highest );

	//calculate population score based on highest percentage
	if( ( ( $ipe_data_highest >= 50 ) || ( $ipe_data_highest >= "50" ) ) && ( ( $ipe_data_highest < 100 ) || ( $ipe_data_highest < "100" ) ) ){ //2 = Moderate, else if IPE “pctblack” < 100 and >= 50, (for any Unique ID in grouping)
		return 2; 
	} else if( ( ( $ipe_data_highest >= 13.2 ) || ( $ipe_data_highest >= "13.2" ) ) && ( ( $ipe_data_highest < 50 ) || ( $ipe_data_highest < "50" ) ) ){ //3 = Low, else if IPE “pctblack” < 50 and >= 13.2
		return 3; 
	} else if( ( $ipe_data_highest < 13.2 ) || ( $ipe_data_highest < "13.2" ) ) { //4 = No, else if IPE “pctblack” < 13.2
		return 4;
	} else { //999 = Insufficient information, else if IPE “pctblack” = not reported (for ALL Unique IDs in grouping)
		return 999;
	}
	
	//else, if we are still in this function:
	return 999;

}

/**
 * Returns the HR Asian values for incoming IM data
 *
 * @param array. IM data for all IMs in a study group.
 * @return int.
*/ 
function calculate_hr_asian_ims( $all_ims ){

	$ipe_data_highest = floatval( 999 ); //default is "not reported" = 999
	
	//populate ipe_data_highest with highest percents;
	foreach( $all_ims as $one_im ){
		$this_pct_string = $one_im["ipe_pctasian"];
		$this_pct = floatval ( $this_pct_string );
		
		//var_dump( $this_pct );
		
		//1 = High, if IPE pctasian = 100 (for any Unique ID in grouping)
		if( ( $this_pct == 100 ) || ( $this_pct == "100" ) ){ 
			return 1; //return 1 if IPE pctasian = 100 (for any Unique ID in grouping)
		} 
		
		//otherwise, if current percent > ipe_data_highest, put higher val in ipe_data_highest (unless 999)
		//if we HAVE a percent, that trumps 999
		if( ( ( $ipe_data_highest == "999" ) || ( $ipe_data_highest == 999 ) ) && ( ( $this_pct != 0 ) || ( $this_pct != "0" ) || ( $this_pct != "NULL" ) || ( $this_pct != NULL ) ) ) {
			$ipe_data_highest = $this_pct;
		}			
		if( ( $this_pct > $ipe_data_highest ) && ( ( $this_pct != 999 ) || ( $this_pct != "999" ) ) ){
			$ipe_data_highest = $this_pct;
		}

	}
	
	//var_dump( $ipe_data_highest );

	//calculate population score based on highest percentage
	if( ( ( $ipe_data_highest >= 50 ) || ( $ipe_data_highest >= "50" ) ) && ( ( $ipe_data_highest < 100 ) || ( $ipe_data_highest < "100" ) ) ){ //2 = Moderate, else if IPE pctasian < 100 and >= 50, (for any Unique ID in grouping)
		return 2; 
	} else if( ( ( $ipe_data_highest >= 5.3 ) || ( $ipe_data_highest >= "5.3" ) ) && ( ( $ipe_data_highest < 50 ) || ( $ipe_data_highest < "50" ) ) ){ //3 = Low, else if IPE pctasian < 50 and >= 5.3
		return 3; 
	} else if( ( $ipe_data_highest < 5.3 ) || ( $ipe_data_highest < "5.3" ) ) { //4 = No, else if IPE pctasian < 5.3
		return 4;
	} else { //999 = Insufficient information, else if IPE pctasian = not reported (for ALL Unique IDs in grouping)
		return 999;
	}
	
	//else, if we are still in this function:
	return 999;

}

/**
 * Returns the HR Native American values for incoming IM data
 *
 * @param array. IM data for all IMs in a study group.
 * @return int.
*/ 
function calculate_hr_nativeamerican_ims( $all_ims ){

	$ipe_data_highest = floatval( 999 ); //default is "not reported" = 999
	
	//populate ipe_data_highest with highest percents;
	foreach( $all_ims as $one_im ){
		$this_pct_string = $one_im["ipe_pctnativeamerican"];
		$this_pct = floatval ( $this_pct_string );
		
		//var_dump( $this_pct );
		
		//1 = High, if IPE pctnativeamerican = 100 (for any Unique ID in grouping)
		if( ( $this_pct == 100 ) || ( $this_pct == "100" ) ){ 
			return 1; //return 1 if IPE pctnativeamerican = 100 (for any Unique ID in grouping)
		} 
		
		//otherwise, if current percent > ipe_data_highest, put higher val in ipe_data_highest (unless 999)
		//if we HAVE a percent, that trumps 999
		if( ( ( $ipe_data_highest == "999" ) || ( $ipe_data_highest == 999 ) ) && ( ( $this_pct != 0 ) || ( $this_pct != "0" ) || ( $this_pct != "NULL" ) || ( $this_pct != NULL ) ) ) {
			$ipe_data_highest = $this_pct;
		}			
		if( ( $this_pct > $ipe_data_highest ) && ( ( $this_pct != 999 ) || ( $this_pct != "999" ) ) ){
			$ipe_data_highest = $this_pct;
		}

	}
	
	//var_dump( $ipe_data_highest );

	//calculate population score based on highest percentage
	if( ( ( $ipe_data_highest >= 50 ) || ( $ipe_data_highest >= "50" ) ) && ( ( $ipe_data_highest < 100 ) || ( $ipe_data_highest < "100" ) ) ){ //2 = Moderate, else if IPE pctnativeamerican < 100 and >= 50, (for any Unique ID in grouping)
		return 2; 
	} else if( ( ( $ipe_data_highest >= 1.2 ) || ( $ipe_data_highest >= "1.2" ) ) && ( ( $ipe_data_highest < 50 ) || ( $ipe_data_highest < "50" ) ) ){ //3 = Low, else if IPE pctnativeamerican < 50 and >= 1.2
		return 3; 
	} else if( ( $ipe_data_highest < 1.2 ) || ( $ipe_data_highest < "1.2" ) ) { //4 = No, else if IPE pctnativeamerican < 1.2
		return 4;
	} else { //999 = Insufficient information, else if IPE pctnativeamerican = not reported (for ALL Unique IDs in grouping)
		return 999;
	}
	
	//else, if we are still in this function:
	return 999;

}

/**
 * Returns the HR Pacific Islander values for incoming IM data
 *
 * @param array. IM data for all IMs in a study group.
 * @return int.
*/ 
function calculate_hr_pacificislander_ims( $all_ims ){

	$ipe_data_highest = floatval( 999 ); //default is "not reported" = 999
	
	//populate ipe_data_highest with highest percents;
	foreach( $all_ims as $one_im ){
		$this_pct_string = $one_im["ipe_pctpacificislander"];
		$this_pct = floatval ( $this_pct_string );
		
		//var_dump( $this_pct );
		
		//1 = High, if IPE “pctpacificislander” = 100 (for any Unique ID in grouping)
		if( ( $this_pct == 100 ) || ( $this_pct == "100" ) ){ 
			return 1; //return 1 if IPE “pctpacificislander” = 100 (for any Unique ID in grouping)
		} 
		
		//otherwise, if current percent > ipe_data_highest, put higher val in ipe_data_highest (unless 999)
		//if we HAVE a percent, that trumps 999
		if( ( ( $ipe_data_highest == "999" ) || ( $ipe_data_highest == 999 ) ) && ( ( $this_pct != 0 ) || ( $this_pct != "0" ) || ( $this_pct != "NULL" ) || ( $this_pct != NULL ) ) ) {
			$ipe_data_highest = $this_pct;
		}			
		if( ( $this_pct > $ipe_data_highest ) && ( ( $this_pct != 999 ) || ( $this_pct != "999" ) ) ){
			$ipe_data_highest = $this_pct;
		}

	}
	
	//var_dump( $ipe_data_highest );

	//calculate population score based on highest percentage
	if( ( ( $ipe_data_highest >= 50 ) || ( $ipe_data_highest >= "50" ) ) && ( ( $ipe_data_highest < 100 ) || ( $ipe_data_highest < "100" ) ) ){ //2 = Moderate, else if IPE “pctpacificislander” < 100 and >= 50, (for any Unique ID in grouping)
		return 2; 
	} else if( ( ( $ipe_data_highest >= 0.2 ) || ( $ipe_data_highest >= "0.2" ) ) && ( ( $ipe_data_highest < 50 ) || ( $ipe_data_highest < "50" ) ) ){ //3 = Low, else if IPE “pctpacificislander” < 50 and >= 0.2
		return 3; 
	} else if( ( $ipe_data_highest < 0.2 ) || ( $ipe_data_highest < "0.2" ) ) { //4 = No, else if IPE “pctpacificislander” < 0.2
		return 4;
	} else { //999 = Insufficient information, else if IPE “pctpacificislander” = not reported (for ALL Unique IDs in grouping)
		return 999;
	}
	
	//else, if we are still in this function:
	return 999;

}

/**
 * Returns the HR Hispanic values for incoming IM data
 *
 * @param array. IM data for all IMs in a study group.
 * @return int.
*/ 
function calculate_hr_hispanic_ims( $all_ims ){

	$ipe_data_highest = floatval( 999 ); //default is "not reported" = 999
	
	//populate ipe_data_highest with highest percents;
	foreach( $all_ims as $one_im ){
		$this_pct_string = $one_im["ipe_pcthispanic"];
		$this_pct = floatval ( $this_pct_string );
		
		//var_dump( $this_pct );
		
		//1 = High, if IPE “pcthispanic” = 100 (for any Unique ID in grouping)
		if( ( $this_pct == 100 ) || ( $this_pct == "100" ) ){ 
			return 1; //return 1 if IPE “pcthispanic” = 100 (for any Unique ID in grouping)
		} 
		
		//otherwise, if current percent > ipe_data_highest, put higher val in ipe_data_highest (unless 999)
		//if we HAVE a percent, that trumps 999
		if( ( ( $ipe_data_highest == "999" ) || ( $ipe_data_highest == 999 ) ) && ( ( $this_pct != 0 ) || ( $this_pct != "0" ) || ( $this_pct != "NULL" ) || ( $this_pct != NULL ) ) ) {
			$ipe_data_highest = $this_pct;
		}			
		if( ( $this_pct > $ipe_data_highest ) && ( ( $this_pct != 999 ) || ( $this_pct != "999" ) ) ){
			$ipe_data_highest = $this_pct;
		}

	}
	
	//var_dump( $ipe_data_highest );

	//calculate population score based on highest percentage
	if( ( ( $ipe_data_highest >= 50 ) || ( $ipe_data_highest >= "50" ) ) && ( ( $ipe_data_highest < 100 ) || ( $ipe_data_highest < "100" ) ) ){ //2 = Moderate, else if IPE “pcthispanic” < 100 and >= 50, (for any Unique ID in grouping)
		return 2; 
	} else if( ( ( $ipe_data_highest >= 17.1 ) || ( $ipe_data_highest >= "17.1" ) ) && ( ( $ipe_data_highest < 50 ) || ( $ipe_data_highest < "50" ) ) ){ //3 = Low, else if IPE “pcthispanic” < 50 and >= 17.1
		return 3; 
	} else if( ( $ipe_data_highest < 17.1 ) || ( $ipe_data_highest < "17.1" ) ) { //4 = No, else if IPE “pcthispanic” < 17.1
		return 4;
	} else { //999 = Insufficient information, else if IPE “pcthispanic” = not reported (for ALL Unique IDs in grouping)
		return 999;
	}
	
	//else, if we are still in this function:
	return 999;

}

/**
 * Returns the HR Hispanic values for incoming IM data
 *
 * @param array. IM data for all IMs in a study group.
 * @return int.
*/ 
function calculate_hr_lowincome_ims( $all_ims ){

	$ipe_data_highest = floatval( 999 ); //default is "not reported" = 999
	
	//populate ipe_data_highest with highest percents;
	foreach( $all_ims as $one_im ){
		$this_pct_string = $one_im["ipe_pctlowerincome"];
		$this_pct = floatval ( $this_pct_string );
		
		//var_dump( $this_pct );
		
		//1 = High, if IPE pctlowerincome = 100 (for any Unique ID in grouping)
		if( ( $this_pct == 100 ) || ( $this_pct == "100" ) ){ 
			return 1; //return 1 if IPE pctlowerincome = 100 (for any Unique ID in grouping)
		} 
		
		//otherwise, if current percent > ipe_data_highest, put higher val in ipe_data_highest (unless 999)
		//if we HAVE a percent, that trumps 999
		if( ( ( $ipe_data_highest == "999" ) || ( $ipe_data_highest == 999 ) ) && ( ( $this_pct != 0 ) || ( $this_pct != "0" ) || ( $this_pct != "NULL" ) || ( $this_pct != NULL ) ) ) {
			$ipe_data_highest = $this_pct;
		}			
		if( ( $this_pct > $ipe_data_highest ) && ( ( $this_pct != 999 ) || ( $this_pct != "999" ) ) ){
			$ipe_data_highest = $this_pct;
		}

	}
	
	//var_dump( $ipe_data_highest );

	//calculate population score based on highest percentage
	if( ( ( $ipe_data_highest >= 50 ) || ( $ipe_data_highest >= "50" ) ) && ( ( $ipe_data_highest < 100 ) || ( $ipe_data_highest < "100" ) ) ){ //2 = Moderate, else if IPE pctlowerincome < 100 and >= 50, (for any Unique ID in grouping)
		return 2; 
	} else if( ( ( $ipe_data_highest >= 14.5 ) || ( $ipe_data_highest >= "14.5" ) ) && ( ( $ipe_data_highest < 50 ) || ( $ipe_data_highest < "50" ) ) ){ //3 = Low, else if IPE pctlowerincome < 50 and >= 14.5
		return 3; 
	} else if( ( $ipe_data_highest < 14.5 ) || ( $ipe_data_highest < "14.5" ) ) { //4 = No, else if IPE pctlowerincome < 14.5
		return 4;
	} else { //999 = Insufficient information, else if IPE pctlowerincome = not reported (for ALL Unique IDs in grouping)
		return 999;
	}
	
	//else, if we are still in this function:
	return 999;

}

/**
 * Returns the Representativeness values for incoming IM data
 *
 * @param array. IM data for all IMs in a study group.
 * @return int.
*/ 
function calculate_representativeness_ims( $all_ims ){

	$reps = array(); //array to hold ALL IM values, if 1 does not occur
	
	//get representativeness values; analyze
	foreach( $all_ims as $one_im ){
		$this_rep = $one_im["ipe_representativeness"];
		
		//If representativeness = 1 (for any Unique ID in grouping), then representativeness = 1, yes
		if( ( $this_rep == 1 ) || ( $this_rep == "1" ) ){ 
			return 1; //return 1 if IPE “pcthispanic” = 100 (for any Unique ID in grouping)
		} 
		
		//otherwise, we will calc after all have been iterated over
		array_push( $reps, $this_rep );

	}
	
	//calculate representativenss, if !1:  Else If representativeness = 2 (for ALL Unique IDs in grouping), then representativeness = 0, no
	if( count( array_unique( $reps ) ) === 1 && ( end( $reps ) == "2" || ( end( $reps ) == 2 ) ) ){
		return 2; 
	} else if( ( count( array_unique( $reps ) ) === 1 ) && ( end( $reps ) == "999" || ( end( $reps ) == 999 ) ) ){
		return 999; 
	} 
	
	//else, return 0 (combo of 2s and 999s, TODO: Laura, what do we return in this case?)
	return 0;

}

/**
 * Returns the Stage value for incoming IM data
 *
 * @param array. IM data for all IMs in a study group.
 * @return int.
*/ 
function calculate_stage_ims( $all_ims ){

	$sustainabilities = array(); //array to hold ALL IM values, if 1 does not occur
	$pse_components = array(); //array to hold ALL IM values, if 1 does not occur
	$policy_only_flag = false; //if true for JUST ONE IM, it's totally true for analysis
	
	//get all values for sustainability and pse_components for these IMs
	foreach( $all_ims as $one_im ){
		
		$this_sustain = (int)$one_im["sustainability"];
		
		//arg, can be multiple (serialized)
		$this_pse = unserialize( $one_im["pse_components"] );
		
		//policy only check
		if( in_array( "Policy change", $this_pse ) && !( in_array( "Environment change", $this_pse ) ) && !( in_array( "Practice/System change", $this_pse ) ) ){
			$policy_only_flag = true;
		}
		
		foreach( $this_pse as $one_pse ){
			array_push( $pse_components, $one_pse );
		}
	
		//populate array for sustainability
		array_push( $sustainabilities, $this_sustain );
		

	}
	
	//calculate stage
	//999 = Insufficient information, if PSE component = 999 AND Plan for sustainability = 999 (for ALL Unique IDs in grouping)
	if( ( count( array_unique( $pse_components ) ) === 1 && ( (int)end( $pse_components ) == 999 ) ) && 
		( count( array_unique( $sustainabilities ) ) === 1 && ( (int)end( $sustainabilities ) == 999 ) ) ){
		return 999; 
	} 
	//3 = Enforcement/ maintenance, else if Plan for Sustainability = 1 (for any Unique ID in grouping)
	else if( in_array( 1, $sustainabilities ) ){
		return 3; 
	} 
	//2 = Implementation, else if PSE Component practice change = 1 OR PSE Component environmental change = 1 (for any Unique ID in grouping)
	else if( in_array( "Practice/System change", $pse_components ) || in_array( "Environment change", $pse_components ) ){
		return 2; 
	}
	//1 = Adoption, else if PSE Component policy change = 1 AND PSE Component practice change = 2 (not in there 
	//	AND PSE Component environmental change = (not in there) (for any Unique ID in grouping)
	else if( $policy_only_flag == true ){
		return 1; 
	}
	
	
	//else, return 0 (combo of 2s and 999s, TODO: Laura, what do we return in this case?)
	return 0;

}


/******* SECONDARY ANALYSIS CALCS (from input data on Analysis tab) ***/

/**
 * Returns the Potential Population Reach value for incoming IM data
 *
 * @param int, int. Analysis participation/potential exposure value, analysis representativeness value
 * @return int.
*/ 
function calculate_pop_potential_reach_ims( $participation_exposure, $representativeness ){

	//return 999 = Insufficient information, if Participation/ Potential Exposure = 999 OR Representativeness = 999
	if( (int)$participation_exposure == 999 || (int)$representativeness == 999 ){
		return 999;
	} else if( ( (int)$participation_exposure == 1 && (int)$representativeness == 1 ) ) { //return 1 = High, else if Participation/ Potential Exposure = 1 AND Representativeness = 1
		return 1;
	} else if( ( (int)$participation_exposure == 1 || (int)$participation_exposure == 2 ) && 
		( (int)$representativeness == 1 || (int)$representativeness == 2 ) ) {
		return 2;
	} 
	
	//else, return 0 (combo of 2s and 999s, TODO: Laura, what do we return in this case?)
	return 0;

}

/**
 * Returns the Potential HR Population Reach value for incoming IM data
 *
 * @param int, int, int, int, int, int, int, int. Analysis participation/potential exposure value, analysis representativeness value, hr_black, hr_asian, hr_nativeamerican, hr_pacificislander, hr_hispanic, hr_lowerincome
 * @return int.
*/ 
function calculate_hr_pop_potential_reach_ims( $representativeness, $hr_black_calc, $hr_asian_calc, $hr_nativeamerican_calc, $hr_pacificislander_calc, $hr_hispanic_calc, $hr_lowincome_calc ){

	//999 = Insufficient information, if <ALL above high-risk racial or ethnic population>  = 999 OR Representativeness = 999
	if( (int)$representativeness == 999 && (int)$hr_black_calc == 999 && (int)$hr_asian_calc == 999 && (int)$hr_nativeamerican_calc == 999 && 
		(int)$hr_pacificislander_calc == 999 && (int)$hr_hispanic_calc == 999 && (int)$hr_lowincome_calc == 999 ){
		return 999;
	} else if( ( (int)$representativeness == 1 ) && ( (int)$hr_black_calc == 1 || (int)$hr_asian_calc == 1 || (int)$hr_nativeamerican_calc == 1 || 
		(int)$hr_pacificislander_calc == 1 || (int)$hr_hispanic_calc == 1 || (int)$hr_lowincome_calc == 1 ) ){ //1 = High, else if <any above high-risk racial or ethnic population> = 1 AND Representativeness = 1
		return 1;
	} else if( (int)$hr_black_calc == 1 || (int)$hr_black_calc == 2 || (int)$hr_asian_calc == 1 || (int)$hr_asian_calc == 2 || (int)$hr_nativeamerican_calc == 1 || (int)$hr_nativeamerican_calc == 2 || 
		(int)$hr_pacificislander_calc == 1 || (int)$hr_pacificislander_calc == 2 || (int)$hr_hispanic_calc == 1 || (int)$hr_hispanic_calc == 2 || (int)$hr_lowincome_calc == 1 || (int)$hr_lowincome_calc == 2 ){
		return 2;
	} else if( (int)$hr_black_calc == 3 || (int)$hr_asian_calc == 3 || (int)$hr_nativeamerican_calc == 3 || 
		(int)$hr_pacificislander_calc == 3 || (int)$hr_hispanic_calc == 3 || (int)$hr_lowincome_calc == 3 ){ //1 = High, else if <any above high-risk racial or ethnic population> = 1 AND Representativeness = 1
		return 3;
	} else if( (int)$hr_black_calc == 4 || (int)$hr_asian_calc == 4 || (int)$hr_nativeamerican_calc == 4 || 
		(int)$hr_pacificislander_calc == 4 || (int)$hr_hispanic_calc == 4 || (int)$hr_lowincome_calc == 4 ){ //1 = High, else if <any above high-risk racial or ethnic population> = 1 AND Representativeness = 1
		return 4;
	} 
	
	//else, return 0 (combo of 2s and 999s, TODO: Laura, what do we return in this case?)
	return 0;

}

/**
 * Returns the Applicability for HR pops value for incoming IM data
 *
 * @param array. IM data for all IMs in a study group.
 * @return int.
*/ 
function calculate_applicability_hr_pops_ims( $all_ims ){

	$applicabilities = array(); //array to hold ALL IM values, if 1 does not occur
	
	//get applicability_hr_pops values; put into array for algorthming
	foreach( $all_ims as $one_im ){
		$this_appl = (int)$one_im["applicability_hr_pops"];
		
		//If applicability = 1 (for any Unique ID in grouping), then applicability = 1, yes
		if( $this_appl == 1 ){ 
			return 1;
		}
		//otherwise, we will calc after all have been iterated over
		array_push( $applicabilities, $this_appl );

	}
	
	//Else If applicability = 2 (for ALL Unique IDs in grouping), then applicability = 0, no
	if( count( array_unique( $applicabilities ) ) === 1 && ( end( $applicabilities ) == 2 ) ){
		return 0; 
	} 
	//Else If applicability = 999 or NULL/0 (for ALL Unique IDs in grouping), then applicability = 999, insufficient information
	else if( ( count( array_unique( $applicabilities ) ) === 1 ) && ( end( $applicabilities ) == 999 ) ){
		return 999; 
	}
	
	//else, return 0 (combo of 2s and 999s, TODO: Laura, what do we return in this case?)
	return 0;

}

/** 
 * Returns inclusiveness measure
 *
 * @param int, int, int. Stage, State, Quality
 * @return int.
 */
function calculate_implementation( $stage, $state, $quality ) {

	//999 = Insufficient information, if Stage = 999 OR State = 999  OR Quality = 999 
	if( ( $stage == 999 ) || ( $state == 999 ) || ( $quality == 999 ) ){
		return 999;
	} 
	//1 = High, else if Stage = 2 or 3 AND State = 1 AND Quality = 1
	else if( ( ( $stage == 2 ) || ( $stage == 3 ) ) && ( $state == 1 ) && ( $quality == 1 ) ){
		return 1;
	} 
	//2 = Weak, else if Stage = 1 OR State = 2 OR Quality = 2
	else if( ( $stage == 1 ) || ( $state == 2 ) || ( $quality == 2 ) ){
		return 2;
	} 
	
	return 999; //should never happen, but if it does there's insufficient info SOMEwhere, amiright?

}

/** 
 * Returns inclusiveness-inclusiveness measure
 *
 * @param int, int, int. Stage, State, Quality
 * @return int.
 */
function calculate_implementation_inclusiveness( $stage, $state, $quality, $inclusiveness ) {

	//999 = Insufficient information, if Stage = 999 OR State = 999 OR Quality = 999 OR Inclusiveness = 999 
	if( ( $stage == 999 ) || ( $state == 999 ) || ( $quality == 999 ) || ( $inclusiveness == 999 ) ){
		return 999;
	} 
	//1 = High, else if Stage = 2 or 3 AND State = 1 AND Quality = 1 AND Inclusiveness = 1 or 2
	else if( ( ( $stage == 2 ) || ( $stage == 3 ) ) && ( $state == 1 ) && ( $quality == 1 ) && ( ( $inclusiveness == 1 ) || ( $inclusiveness == 2 ) ) ){
		return 1;
	} 
	//2 = Weak, else if Stage = 1 OR State = 2 OR Quality = 2 OR Inclusiveness = 3
	else if( ( $stage == 1 ) || ( $state == 2 ) || ( $quality == 2 ) || ( $inclusiveness == 2 ) ){
		return 2;
	} 
	
	return 999; //should never happen, but if it does there's insufficient info SOMEwhere, amiright?

}

/** 
 * Returns scale measure
 *
 * @param int, int, int. Stage, State, Quality
 * @return int.
 */
function calculate_scale( $access, $size ) {

	//999 = Insufficient information, if Access = 999  OR Size = 999 
	if( ( $access == 999 ) || ( $size == 999 ) ){
		return 999;
	} 
	//1 = Large, else if Access = 1 AND Size = 1
	else if( ( $access == 1 ) && ( $size == 1 ) ){
		return 1;
	} 
	//2 = Small, else if Access = 2 OR Size = 2
	else if( ( $access == 2 ) || ( $size == 2 ) ){
		return 2;
	} 
	
	return 999; //should never happen, but if it does there's insufficient info SOMEwhere, amiright?

}

/** 
 * Returns hr scale measure
 *
 * @param int, int, int. Stage, State, Quality
 * @return int.
 */
function calculate_hr_scale( $access, $size, $applicability ) {

	//999 = Insufficient information, if Access = 999 OR Size = 999 OR Applicability = 999
	if( ( $access == 999 ) || ( $size == 999 ) || ( $applicability == 999 ) ){
		return 999;
	} 
	//1 = Large, else if Access = 1 AND Size = 1 AND Applicability = 1
	else if( ( $access == 1 ) && ( $size == 1 ) && ( $applicability == 1 ) ){
		return 1;
	} 
	//2 = Small, else if (Access = 2 OR Size = 2) AND Applicability = 1
	else if( ( ( $access == 2 ) || ( $size == 2 ) ) && ( $applicability == 1 ) ){
		return 2;
	} 
	
	return 999; //should never happen, but if it does there's insufficient info SOMEwhere, amiright?

}

/** 
 * Returns dose measure
 *
 * @param int, int. Implementation, Scale.
 * @return int.
 */
function calculate_dose( $implementation, $scale ){

	//999 = Insufficient information, if Implementation = 999 OR Scale = 999
	if( ( $implementation == 999 ) || ( $scale == 999 ) ){
		return 999;
	} 
	//1 = High, else if Implementation = 1 AND Scale = 1
	else if( ( $implementation == 1 ) && ( $scale == 1 ) ){
		return 1;
	} 
	//2 = Low, else if Implementation = 2 OR Scale = 2
	else if( ( $implementation == 2 ) || ( $scale == 2 ) ){
		return 2;
	} 
	
	return 999; //should never happen, but if it does there's insufficient info SOMEwhere, amiright?
	
}

/** 
 * Returns Population Impact measure
 *
 * @param int, int. Implementation, Scale.
 * @return int.
 */
function calculate_population_impact( $effectiveness, $pop_reach, $dose ){

	//1 = High, if Effectiveness = 1 AND Population Reach = 1 AND Dose = 1
	if( ( $effectiveness == 1 ) && ( $pop_reach == 1 ) && ( $dose == 1 ) ){
		return 1;
	} 
	//2 = Low, else if Effectiveness = 1 or 2 AND Population Reach = 1 or 2 AND Dose = 1 or 2
	else if( (( $effectiveness == 1 ) || ( $effectiveness == 2 )) && (( $pop_reach == 1 ) || ( $pop_reach == 2 )) && (( $dose == 1 ) || ( $dose == 2 )) ){
		return 2;
	} 
	//3 = No, else if (Effectiveness = 2 AND Population Reach = 2 AND Dose = 2) OR Effectiveness = 3
	else if( (( $effectiveness == 2 ) && ( $pop_reach == 2 ) && ( $dose == 2 )) || ( $effectiveness == 3 ) ){
		return 3;
	} 
	
	return 999; //should never happen, but if it does there's insufficient info SOMEwhere, amiright?
	
}

/** 
 * Returns HR Population Impact measure
 *
 * @param int, int. Implementation, Scale.
 * @return int.
 */
function calculate_hr_population_impact( $effectiveness_hr, $pop_reach, $dose ){

	//1 = High, if Effectiveness for High-risk Populations = 1 AND Population Reach = 1 AND Dose = 1
	if( ( $effectiveness_hr == 1 ) && ( $pop_reach == 1 ) && ( $dose == 1 ) ){
		return 1;
	} 
	//2 = Low, else if Effectiveness for High-risk Populations = 1 or 2 AND Population Reach = 1 or 2 AND Dose = 1 or 2
	else if( (( $effectiveness_hr == 1 ) || ( $effectiveness_hr == 2 )) && (( $pop_reach == 1 ) || ( $pop_reach == 2 )) && (( $dose == 1 ) || ( $dose == 2 )) ){
		return 2;
	} 
	//3 = No, else if (Effectiveness for High-risk Populations = 2 AND Population Reach = 2 AND Dose = 2) OR Effectiveness for High-risk Populations = 3
	else if( (( $effectiveness_hr == 2 ) && ( $pop_reach == 2 ) && ( $dose == 2 )) || ( $effectiveness_hr == 3 ) ){
		return 3;
	} 
	
	return 999; //should never happen, but if it does there's insufficient info SOMEwhere, amiright?
	
}





/**
 * Gets, combines and returns strategies given an info id list (go through each unique id here and combine strategies!)
 *
 * @param string. Comma-space delimited list of UniqueIDs
 * @return string. Strategies
 *
 */
function calculate_strategies_for_info_id_list( $info_id_list ){

	global $wpdb;

	//add quotes for mysql IN clause
		//TODO: modularize this!!  Also in calc_study_design by info id.
	if( strpos( $info_id_list, "," ) === false ){
		$info_implode = "'" . $info_id_list . "'";
	
	} else {
		$info_explode = explode(", ", $info_id_list );
		$info_implode = implode( "', '", $info_explode ); //adding quotes for mysql happy times
		//pre and post-pend
		$info_implode = "'" . $info_implode . "'";
	} 

	//upload codetbl for strategies (1 db call, vs many) //"Strategies" = 98
	$strategy_lookup = get_codetbl_by_codetype( 98 );
	
	$all_strategies = array();
	//get all serialized strategies for this list of study ids
	$strat_sql = 
		"
		SELECT indicator_strategies
		FROM $wpdb->transtria_analysis_intermediate
		WHERE info_id in ($info_implode)
		ORDER BY info_id
		"		
		;
		
	$form_rows = $wpdb->get_results( $strat_sql, ARRAY_N );
	
	//TODO: unserialize strategies
	foreach( $form_rows as $i => $v ){
		//var_dump( $i );
		//first, check to see if default text is there
		$uncereal = unserialize( current( $v ) ); //to array of index => strategy values (numbers to be looked up)
		//var_dump( $uncereal );
		if( $uncereal !== false ){ //will get rid of 'ind. not selected' or whatever
			foreach( $uncereal as $in => $va ){
				//lookup each value in strategy lookup table
				$this_strategy = $strategy_lookup[ $va ]->descr;
				//append to all strategies array
				$all_strategies[ $va ] = $this_strategy;
				
			}
		
		} 
	
	}
	
	//if we have no strategies in list, return 999.
	if( empty( $all_strategies ) ){
		$all_strategies[] = 999;
		$all_strategies = serialize( $all_strategies );
	} else {
		$all_strategies = serialize( $all_strategies );
	}
	
	return $all_strategies;


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

/**
 * Calculates Population or SubPopulation for an I-M
 *
 * @param
 * @return population_calc, list of unique_ids (if duplicate = "Y") that are evaluated (info_id_list_hr)?
 */
function calculate_pop_subpop_analysis( $pop_data_by_study_id, $info_id_list, $evalpop, $subpopYN, $subpop, $this_study_group ){

	//set vars for THIS im
	
	//parse info list to show what Studies (study_ids) we need to look at //TODO: modularize this (see func below)
	$study_id_list = array();
	$info_id_list_by_study = array();
	$exploded_info_ids = explode( ", ", $info_id_list );
	foreach( $exploded_info_ids as $this_info_id ){
		$underscore_pos = strpos( $this_info_id, "_" );
		$this_study_id = substr( $this_info_id, 0, $underscore_pos );
		//push to study id list
		array_push( $study_id_list, $this_study_id );
		//push this info id to list by study (for info_id_list_hr-ing later)
		if( $info_id_list_by_study[$this_study_id] == null ){
			$info_id_list_by_study[$this_study_id] = array();
		}
		array_push( $info_id_list_by_study[$this_study_id], $this_info_id ); 
	}
	
	//get unique vals for study ids
	$new_study_ids = array_unique( $study_id_list );
	
	//var_dump( $info_id_list );
	//var_dump( $subpop );
	//TODO, clean this up when no incoming $subpop (check for subpop!!, etc), etc
	$unpacked_subpop = unserialize( $subpop );
	$unpacked_evalpop = unserialize( $evalpop );
	if( $unpacked_subpop != false ){
		$this_subpop = current( $unpacked_subpop );//-> value is db value; => descr is string d
	} else {
		$this_subpop = array();
	}
	if( $unpacked_evalpop != false ){
		$this_evalpop = current( $unpacked_evalpop );//-> value is db value; => descr is string d
		$this_evalpop_string = evalpop_lookup( $this_evalpop["value"] );
	} else {
		$this_evalpop = array();
		$this_evalpop_string = "";
	}
	
	//returns "tp", "ese0", etc (the PopulationType in the pops data)
	
	
	
	$this_pop_data = array(); //to hold current study's incoming single pop data
	$eval_pop_data_parsed = array(); //to hold all studies' parsed pop data (with eval pop and IPE data only!); indexed by column name
	$ipe_pop_data_parsed = array(); //to hold all studies' parsed pop data (with eval pop and IPE data only!); indexed by column name
	
	//subpopulation of Populations tabs, indexed by study
	$sub_pop_data = get_pops_subpop_data_study_group( $this_study_group ); //indexed by study id (Youth value == 1)
	

	
	//Gather population data across studies?  Maybe?
	//	Need to get percentages for eval pop AND IPE
	foreach( $new_study_ids as $one_study ){
		
		//all study-form population data
		$this_pop_data = $pop_data_by_study_id[ $one_study ];
		
		//this result eval pop data //$this_evalpop_string
		$eval_pop_data = $this_pop_data[ $this_evalpop_string ];
		$ipe_pop_data = $this_pop_data[ "ipe" ];
		
		
		//are race percentages even reported??
		$eval_pop_data_parsed["racepercentages_notreported"][$one_study] = $eval_pop_data->racepercentages_notreported;
		$ipe_pop_data_parsed["racepercentages_notreported"][$one_study] = $ipe_pop_data->racepercentages_notreported;
		
		//if eval pop race percentages not reported, don't continue to populate; "Y" = not reported
		if( $eval_pop_data_parsed["racepercentages_notreported"][$one_study] == "N" ){
			$eval_pop_racereported = true; //set flag
		} else {
			$eval_pop_racereported = false;  //set flag
		}
		
			
		//get percentages, subpopulation values for this study's eval pop
		$eval_pop_data_parsed["PctBlack"][$one_study] = $eval_pop_data->PctBlack;
		$eval_pop_data_parsed["PctAsian"][$one_study] = $eval_pop_data->PctAsian;
		$eval_pop_data_parsed["PctPacificIslander"][$one_study] = $eval_pop_data->PctPacificIslander;
		$eval_pop_data_parsed["PctNativeAmerican"][$one_study] = $eval_pop_data->PctNativeAmerican;
		$eval_pop_data_parsed["PctOtherRace"][$one_study] = $eval_pop_data->PctOtherRace;
		$eval_pop_data_parsed["PctHispanic"][$one_study] = $eval_pop_data->PctHispanic;
		$eval_pop_data_parsed["PctLowerIncome"][$one_study] = $eval_pop_data->PctLowerIncome;
		$eval_pop_data_parsed["GenderCode"][$one_study] = $eval_pop_data->GenderCode;
		$eval_pop_data_parsed["gender_notreported"][$one_study] = $eval_pop_data->gender_notreported;
		$eval_pop_data_parsed["isGeneralPopulation"][$one_study] = $eval_pop_data->isGeneralPopulation;
		$eval_pop_data_parsed["generalpopulation_notreported"][$one_study] = $eval_pop_data->generalpopulation_notreported;
	
		//if ipe pop race percentages not reported, flag
		if( $ipe_pop_data_parsed["racepercentages_notreported"][$one_study] == "N" ){
		
			$ipe_pop_racereported = true; //set flag
			
		} else {
			$ipe_pop_racereported = false;  //set flag
		}
		
		//get percentages, subpopulation values for this study's IPE pop
		$ipe_pop_data_parsed["PctBlack"][$one_study] = $ipe_pop_data->PctBlack;
		$ipe_pop_data_parsed["PctAsian"][$one_study] = $ipe_pop_data->PctAsian;
		$ipe_pop_data_parsed["PctPacificIslander"][$one_study] = $ipe_pop_data->PctPacificIslander;
		$ipe_pop_data_parsed["PctNativeAmerican"][$one_study] = $ipe_pop_data->PctNativeAmerican;
		$ipe_pop_data_parsed["PctOtherRace"][$one_study] = $ipe_pop_data->PctOtherRace;
		$ipe_pop_data_parsed["PctHispanic"][$one_study] = $ipe_pop_data->PctHispanic;
		$ipe_pop_data_parsed["PctLowerIncome"][$one_study] = $ipe_pop_data->PctLowerIncome;
		$ipe_pop_data_parsed["GenderCode"][$one_study] = $ipe_pop_data->GenderCode;
		$ipe_pop_data_parsed["gender_notreported"][$one_study] = $ipe_pop_data->gender_notreported;
		$ipe_pop_data_parsed["isGeneralPopulation"][$one_study] = $ipe_pop_data->isGeneralPopulation;
		$ipe_pop_data_parsed["generalpopulation_notreported"][$one_study] = $ipe_pop_data->generalpopulation_notreported;
		
	}
	
	
	
	/***** ALGORITHM *****/
	/*This should do what?
	//	if subpop id'd (African American, Girls, etc), evaluate from that.
	//	Else, need to evaluate the race percentages for the Result Evaluation Population ("TP", "ESE0", etc)
	//	In order, if race percentages for ANY of the Unique IDs (I-M dyads that make up the current Analysis ID) == 100, go with that.
	//	However, if they have companion Unique ID != 100 on that race %, exclude these Unique IDs for HR-specific props/calc (duration, study design, net effects, outcome type.
	*/
	
	$return_data = array(
		'which_pop' => "", //subpop, eval pop, ipe
		'population_calc' => "", //what's the digit?
		'info_id_list_hr' => "" //(if subpop, this will be incoming info_if_list)
		);
	$study_list_hr = array();
	$we_found_it = false;
	
	
	
	//var_dump( $ipe_pop_data_parsed );
	//var_dump( $this_subpop["value"] );
	
	
	//1. African-American = 5
	if( (int)$this_subpop["value"] == 5 ){
		$return_data['which_pop'] = "subpop";
		$return_data['population_calc'] = 5;
		$return_data['info_id_list_hr'] = $info_id_list;
		$we_found_it = true;
	} else if( $eval_pop_racereported ){ //any of the studies' ptcblack data
		foreach( $eval_pop_data_parsed["PctBlack"] as $which_study => $racepct ){
			if( $racepct == 100 ){ //yes!
				$return_data['population_calc'] = 5;
				array_push( $study_list_hr, $which_study ); //add to the hr list
				$we_found_it = true;
			}
		}
		$info_id_list_hr = parse_hr_info_list( $study_list_hr, $info_id_list_by_study );
		//return the things
		$return_data['which_pop'] = $this_evalpop_string;
		$return_data['info_id_list_hr'] = $info_id_list_hr;
	
	} else if( $ipe_pop_racereported ){ //look to ipe
		foreach( $ipe_pop_data_parsed["PctBlack"] as $which_study => $racepct ){
			if( $racepct == 100 ){ //yes!
				$return_data['population_calc'] = 5;
				array_push( $study_list_hr, $which_study ); //add to the hr list
				$we_found_it = true;
			}
		}
		//var_dump( $info_id_list_by_study );
		$info_id_list_hr = parse_hr_info_list( $study_list_hr, $info_id_list_by_study );
		//return the things
		$return_data['which_pop'] = "ipe";
		$return_data['info_id_list_hr'] = $info_id_list_hr;
	
	}
	
	if( $we_found_it == true ){
		return $return_data;
	}
	
	//2. Asian-American = 6
	if( (int)$this_subpop["value"] == 6 ){
		$return_data['which_pop'] = "subpop";
		$return_data['population_calc'] = 6;
		$return_data['info_id_list_hr'] = $info_id_list;
		$we_found_it = true;
	} else if( $eval_pop_racereported ){ //any of the studies' ptcblack data
		foreach( $eval_pop_data_parsed["PctAsian"] as $which_study => $racepct ){
			if( $racepct == 100 ){ //yes!
				$return_data['population_calc'] = 6;
				array_push( $study_list_hr, $which_study ); //add to the hr list
				$we_found_it = true;
			}
		}
		$info_id_list_hr = parse_hr_info_list( $study_list_hr, $info_id_list_by_study );
		//return the things
		$return_data['which_pop'] = $this_evalpop_string;
		$return_data['info_id_list_hr'] = $info_id_list_hr;
	
	} else if( $ipe_pop_racereported ){ //look to ipe
		foreach( $ipe_pop_data_parsed["PctAsian"] as $which_study => $racepct ){
			if( $racepct == 100 ){ //yes!
				$return_data['population_calc'] = 6;
				array_push( $study_list_hr, $which_study ); //add to the hr list
				$we_found_it = true;
			}
		}
		$info_id_list_hr = parse_hr_info_list( $study_list_hr, $info_id_list_by_study );
		//return the things
		$return_data['which_pop'] = "ipe";
		$return_data['info_id_list_hr'] = $info_id_list_hr;
	
	}
	
	if( $we_found_it == true ){
		return $return_data;
	}

	//3. Native American/ Alaskan Native = 7 (11 in db)
	if( (int)$this_subpop["value"] == 11 ){
		$return_data['which_pop'] = "subpop";
		$return_data['population_calc'] = 7;
		$return_data['info_id_list_hr'] = $info_id_list;
		$we_found_it = true;
	} else if( $eval_pop_racereported ){ //any of the studies' ptcblack data
		foreach( $eval_pop_data_parsed["PctNativeAmerican"] as $which_study => $racepct ){
			if( $racepct == 100 ){ //yes!
				$return_data['population_calc'] = 7;
				array_push( $study_list_hr, $which_study ); //add to the hr list
				$we_found_it = true;
			}
		}
		$info_id_list_hr = parse_hr_info_list( $study_list_hr, $info_id_list_by_study );
		//return the things
		$return_data['which_pop'] = $this_evalpop_string;
		$return_data['info_id_list_hr'] = $info_id_list_hr;
	
	} else if( $ipe_pop_racereported ){ //look to ipe
		foreach( $ipe_pop_data_parsed["PctNativeAmerican"] as $which_study => $racepct ){
			if( $racepct == 100 ){ //yes!
				$return_data['population_calc'] = 7;
				array_push( $study_list_hr, $which_study ); //add to the hr list
				$we_found_it = true;
			}
		}
		$info_id_list_hr = parse_hr_info_list( $study_list_hr, $info_id_list_by_study );
		//return the things
		$return_data['which_pop'] = "ipe";
		$return_data['info_id_list_hr'] = $info_id_list_hr;
	
	}
	
	if( $we_found_it == true ){
		return $return_data;
	}
	
	//4. Native Hawaiian/Pacific Islander = 8 (8 in db)
	if( (int)$this_subpop["value"] == 8 ){
		$return_data['which_pop'] = "subpop";
		$return_data['population_calc'] = 8;
		$return_data['info_id_list_hr'] = $info_id_list;
		$we_found_it = true;
	} else if( $eval_pop_racereported ){ //any of the studies' ptcblack data
		foreach( $eval_pop_data_parsed["PctPacificIslander"] as $which_study => $racepct ){
			if( $racepct == 100 ){ //yes!
				$return_data['population_calc'] = 8;
				array_push( $study_list_hr, $which_study ); //add to the hr list
				$we_found_it = true;
			}
		}
		$info_id_list_hr = parse_hr_info_list( $study_list_hr, $info_id_list_by_study );
		//return the things
		$return_data['which_pop'] = $this_evalpop_string;
		$return_data['info_id_list_hr'] = $info_id_list_hr;
	
	} else if( $ipe_pop_racereported ){ //look to ipe
		foreach( $ipe_pop_data_parsed["PctPacificIslander"] as $which_study => $racepct ){
			if( $racepct == 100 ){ //yes!
				$return_data['population_calc'] = 8;
				array_push( $study_list_hr, $which_study ); //add to the hr list
				$we_found_it = true;
			}
		}
		$info_id_list_hr = parse_hr_info_list( $study_list_hr, $info_id_list_by_study );
		//return the things
		$return_data['which_pop'] = "ipe";
		$return_data['info_id_list_hr'] = $info_id_list_hr;
	
	}
	
	if( $we_found_it == true ){
		return $return_data;
	}
	
	//5. Other Race = 9 (12 in db)
	if( (int)$this_subpop["value"] == 12 ){
		$return_data['which_pop'] = "subpop";
		$return_data['population_calc'] = 9;
		$return_data['info_id_list_hr'] = $info_id_list;
		$we_found_it = true;
	} else if( $eval_pop_racereported ){ //any of the studies' ptcblack data
		foreach( $eval_pop_data_parsed["PctOtherRace"] as $which_study => $racepct ){
			if( $racepct == 100 ){ //yes!
				$return_data['population_calc'] = 9;
				array_push( $study_list_hr, $which_study ); //add to the hr list
				$we_found_it = true;
			}
		}
		$info_id_list_hr = parse_hr_info_list( $study_list_hr, $info_id_list_by_study );
		//return the things
		$return_data['which_pop'] = $this_evalpop_string;
		$return_data['info_id_list_hr'] = $info_id_list_hr;
	
	} else if( $ipe_pop_racereported ){ //look to ipe
		foreach( $ipe_pop_data_parsed["PctOtherRace"] as $which_study => $racepct ){
			if( $racepct == 100 ){ //yes!
				$return_data['population_calc'] = 9;
				array_push( $study_list_hr, $which_study ); //add to the hr list
				$we_found_it = true;
			}
		}
		$info_id_list_hr = parse_hr_info_list( $study_list_hr, $info_id_list_by_study );
		//return the things
		$return_data['which_pop'] = "ipe";
		$return_data['info_id_list_hr'] = $info_id_list_hr;
	
	}
	
	if( $we_found_it == true ){
		return $return_data;
	}
	
	//6. Hispanic/Latino = 10 (9 in db)
	if( (int)$this_subpop["value"] == 9 ){
		$return_data['which_pop'] = "subpop";
		$return_data['population_calc'] = 10;
		$return_data['info_id_list_hr'] = $info_id_list;
		$we_found_it = true;
	} else if( $eval_pop_racereported ){ //any of the studies' ptcblack data
		foreach( $eval_pop_data_parsed["PctHispanic"] as $which_study => $racepct ){
			if( $racepct == 100 ){ //yes!
				$return_data['population_calc'] = 10;
				array_push( $study_list_hr, $which_study ); //add to the hr list
				$we_found_it = true;
			}
		}
		$info_id_list_hr = parse_hr_info_list( $study_list_hr, $info_id_list_by_study );
		//return the things
		$return_data['which_pop'] = $this_evalpop_string;
		$return_data['info_id_list_hr'] = $info_id_list_hr;
	
	} else if( $ipe_pop_racereported ){ //look to ipe
		foreach( $ipe_pop_data_parsed["PctHispanic"] as $which_study => $racepct ){
			if( $racepct == 100 ){ //yes!
				$return_data['population_calc'] = 10;
				array_push( $study_list_hr, $which_study ); //add to the hr list
				$we_found_it = true;
			}
		}
		$info_id_list_hr = parse_hr_info_list( $study_list_hr, $info_id_list_by_study );
		//return the things
		$return_data['which_pop'] = "ipe";
		$return_data['info_id_list_hr'] = $info_id_list_hr;
	
	}
	
	if( $we_found_it == true ){
		return $return_data;
	}
	
	//7. Lower Income = 11 (10 in db)
	if( (int)$this_subpop["value"] == 10 ){
		$return_data['which_pop'] = "subpop";
		$return_data['population_calc'] = 11;
		$return_data['info_id_list_hr'] = $info_id_list;
		$we_found_it = true;
	} else if( $eval_pop_racereported ){ //any of the studies' ptcblack data
		foreach( $eval_pop_data_parsed["PctLowerIncome"] as $which_study => $racepct ){
			if( $racepct == 100 ){ //yes!
				$return_data['population_calc'] = 11;
				array_push( $study_list_hr, $which_study ); //add to the hr list
				$we_found_it = true;
			}
		}
		$info_id_list_hr = parse_hr_info_list( $study_list_hr, $info_id_list_by_study );
		//return the things
		$return_data['which_pop'] = $this_evalpop_string;
		$return_data['info_id_list_hr'] = $info_id_list_hr;
	
	} else if( $ipe_pop_racereported ){ //look to ipe
		foreach( $ipe_pop_data_parsed["PctLowerIncome"] as $which_study => $racepct ){
			if( $racepct == 100 ){ //yes!
				$return_data['population_calc'] = 11;
				array_push( $study_list_hr, $which_study ); //add to the hr list
				$we_found_it = true;
			}
		}
		$info_id_list_hr = parse_hr_info_list( $study_list_hr, $info_id_list_by_study );
		//return the things
		$return_data['which_pop'] = "ipe";
		$return_data['info_id_list_hr'] = $info_id_list_hr;
	
	}
	
	if( $we_found_it == true ){
		return $return_data;
	}
	
	//8. Girls = 1 (1 in db); Gender = "F", subpop = 1 (Youth)
	if( (int)$this_subpop["value"] == 1 ){
		$return_data['which_pop'] = "subpop";
		$return_data['population_calc'] = 1;
		$return_data['info_id_list_hr'] = $info_id_list;
		$we_found_it = true;
	} else if( $eval_pop_data_parsed["gender_notreported"] != "Y" ){ //any of the studies' ptcblack data

		foreach( $eval_pop_data_parsed["GenderCode"] as $which_study => $gender ){
			//what are the pops subpops strings?
			//var_dump( $sub_pop_data );
			$this_pop_subpops = $sub_pop_data[ $which_study ][ $this_evalpop_string ];
			if( !empty( $this_pop_subpops ) ){ //TODO: roll this out to all the subpop things to prevent WARNING
				$pop_subpop_array = array(); //to hold the subpop strings (i.e. "Adults", "Families", "Youth")
				foreach( $this_pop_subpops as $this_pop_subpop ){
					array_push( $pop_subpop_array, $this_pop_subpop->descr );
				}
				
				if( ( trim( $gender ) == "F" ) && in_array( "Youth", $pop_subpop_array ) ){ //if Gender == "F" AND this pop subpop contains "Youth"...
					$return_data['population_calc'] = 1;
					array_push( $study_list_hr, $which_study ); //add to the hr list
					$we_found_it = true;
				}
			}
		}
		$info_id_list_hr = parse_hr_info_list( $study_list_hr, $info_id_list_by_study );
		//return the things
		$return_data['which_pop'] = $this_evalpop_string;
		$return_data['info_id_list_hr'] = $info_id_list_hr;
	
	} else if( $ipe_pop_data_parsed["gender_notreported"] != "Y" ){ //look to ipe
		foreach( $ipe_pop_data_parsed["GenderCode"] as $which_study => $gender ){
			//what are the ipe subpops?
			$this_pop_subpops = $sub_pop_data[ $which_study ][ "ipe" ];
			$pop_subpop_array = array(); //to hold the subpop strings (i.e. "Adults", "Families", "Youth")
			foreach( $this_pop_subpops as $this_pop_subpop ){
				array_push( $pop_subpop_array, $this_pop_subpop->descr );
			}
			//do we have "F" and Youth for IPE?
			if( ( trim( $gender ) == "F" )  && in_array( "Youth", $pop_subpop_array ) ){ //yes!
				$return_data['population_calc'] = 1;
				array_push( $study_list_hr, $which_study ); //add to the hr list
				$we_found_it = true;
			}
		}
		$info_id_list_hr = parse_hr_info_list( $study_list_hr, $info_id_list_by_study );
		//return the things
		$return_data['which_pop'] = "ipe";
		$return_data['info_id_list_hr'] = $info_id_list_hr;
	
	}
	
	if( $we_found_it == true ){
		return $return_data;
	}
	
	//9. Boys = 2 (2 in db); Gender = "M", subpop = 1 (Youth)
	if( (int)$this_subpop["value"] == 2 ){
		$return_data['which_pop'] = "subpop";
		$return_data['population_calc'] = 2;
		$return_data['info_id_list_hr'] = $info_id_list;
		$we_found_it = true;
	} else if( $eval_pop_data_parsed["gender_notreported"] != "Y" ){ //any of the studies' ptcblack data
		foreach( $eval_pop_data_parsed["GenderCode"] as $which_study => $gender ){
			//what are the pops subpops strings?
			$this_pop_subpops = $sub_pop_data[ $which_study ][ $this_evalpop_string ];
			$pop_subpop_array = array(); //to hold the subpop strings (i.e. "Adults", "Families", "Youth")
			if( !empty( $this_pop_subpops ) ){
				foreach( $this_pop_subpops as $this_pop_subpop ){
					array_push( $pop_subpop_array, $this_pop_subpop->descr );
				}
			}
			
			if( ( trim( $gender ) == "M" ) && in_array( "Youth", $pop_subpop_array ) ){ //if Gender == "F" AND this pop subpop contains "Youth"...
				$return_data['population_calc'] = 2;
				array_push( $study_list_hr, $which_study ); //add to the hr list
				$we_found_it = true;
			}
		}
		$info_id_list_hr = parse_hr_info_list( $study_list_hr, $info_id_list_by_study );
		//return the things
		$return_data['which_pop'] = $this_evalpop_string;
		$return_data['info_id_list_hr'] = $info_id_list_hr;
	
	} else if( $ipe_pop_data_parsed["gender_notreported"] != "Y" ){ //look to ipe
		foreach( $ipe_pop_data_parsed["GenderCode"] as $which_study => $gender ){
			//what are the ipe subpops?
			$this_pop_subpops = $sub_pop_data[ $which_study ][ "ipe" ];
			$pop_subpop_array = array(); //to hold the subpop strings (i.e. "Adults", "Families", "Youth")
			foreach( $this_pop_subpops as $this_pop_subpop ){
				array_push( $pop_subpop_array, $this_pop_subpop->descr );
			}
			//do we have "F" and Youth for IPE?
			if( ( trim( $gender ) == "M" )  && in_array( "Youth", $pop_subpop_array ) ){ //yes!
				$return_data['population_calc'] = 2;
				array_push( $study_list_hr, $which_study ); //add to the hr list
				$we_found_it = true;
			}
		}
		$info_id_list_hr = parse_hr_info_list( $study_list_hr, $info_id_list_by_study );
		//return the things
		$return_data['which_pop'] = "ipe";
		$return_data['info_id_list_hr'] = $info_id_list_hr;
	
	}
	
	if( $we_found_it == true ){
		return $return_data;
	}
	
	//10. Women = 3 (3 in db); Gender = "F"
	if( (int)$this_subpop["value"] == 3 ){
		$return_data['which_pop'] = "subpop";
		$return_data['population_calc'] = 3;
		$return_data['info_id_list_hr'] = $info_id_list;
		$we_found_it = true;
	} else if( $eval_pop_data_parsed["gender_notreported"] != "Y" ){ //any of the studies' ptcblack data
		foreach( $eval_pop_data_parsed["GenderCode"] as $which_study => $gender ){
			
			if( trim( $gender ) == "F" ){ //if Gender == "F" 
				$return_data['population_calc'] = 3;
				array_push( $study_list_hr, $which_study ); //add to the hr list
				$we_found_it = true;
			}
		}
		$info_id_list_hr = parse_hr_info_list( $study_list_hr, $info_id_list_by_study );
		//var_dump( "info id list wonem");
		//var_dump( $info_id_list_hr );
		//return the things
		$return_data['which_pop'] = $this_evalpop_string;
		$return_data['info_id_list_hr'] = $info_id_list_hr;
	
	} else if( $ipe_pop_data_parsed["gender_notreported"] != "Y" ){ //look to ipe
		foreach( $ipe_pop_data_parsed["GenderCode"] as $which_study => $gender ){
			
			//do we have "F"
			if( trim( $gender ) == "F" ){ //yes!
				$return_data['population_calc'] = 3;
				array_push( $study_list_hr, $which_study ); //add to the hr list
				$we_found_it = true;
			}
		}
		$info_id_list_hr = parse_hr_info_list( $study_list_hr, $info_id_list_by_study );
		//return the things
		$return_data['which_pop'] = "ipe";
		$return_data['info_id_list_hr'] = $info_id_list_hr;
	
	}
	
	if( $we_found_it == true ){
		return $return_data;
	}
	
	//11. Men = 4 (4 in db); Gender = "M"
	if( (int)$this_subpop["value"] == 4 ){
		$return_data['which_pop'] = "subpop";
		$return_data['population_calc'] = 4;
		$return_data['info_id_list_hr'] = $info_id_list;
		$we_found_it = true;
	} else if( $eval_pop_data_parsed["gender_notreported"] != "Y" ){ //any of the studies' ptcblack data
		foreach( $eval_pop_data_parsed["GenderCode"] as $which_study => $gender ){
			
			if( trim( $gender ) == "M" ){ //if Gender == "F" AND this pop subpop contains "Youth"...
				$return_data['population_calc'] = 4;
				array_push( $study_list_hr, $which_study ); //add to the hr list
				$we_found_it = true;
			}
		}
		$info_id_list_hr = parse_hr_info_list( $study_list_hr, $info_id_list_by_study );
		//return the things
		$return_data['which_pop'] = $this_evalpop_string;
		$return_data['info_id_list_hr'] = $info_id_list_hr;
	
	} else if( $ipe_pop_data_parsed["gender_notreported"] != "Y" ){ //look to ipe
		foreach( $ipe_pop_data_parsed["GenderCode"] as $which_study => $gender ){
			
			//do we have "F"
			if( trim( $gender ) == "M" ){ //it's raining men..
				$return_data['population_calc'] = 4;
				array_push( $study_list_hr, $which_study ); //add to the hr list
				$we_found_it = true;
			}
		}
		$info_id_list_hr = parse_hr_info_list( $study_list_hr, $info_id_list_by_study );
		//return the things
		$return_data['which_pop'] = "ipe";
		$return_data['info_id_list_hr'] = $info_id_list_hr;
	
	}
	
	if( $we_found_it == true ){
		return $return_data;
	}
	
	//12a. Youth = 12; No subpop check - FOR RESULTS EVAL POP ONLY!
	//var_dump( $new_study_ids ); //12 
	//var_dump( $info_id_list_by_study ); //yup
	foreach( $new_study_ids as $this_study ) {
	
		//what are the pops subpops strings?
		$this_pop_subpops = $sub_pop_data[ $this_study ][ $this_evalpop_string ];
		$pop_subpop_array = array(); //to hold the subpop strings (i.e. "Adults", "Families", "Youth")
		if( !empty( $this_pop_subpops ) ){
			foreach( $this_pop_subpops as $this_pop_subpop ){
				array_push( $pop_subpop_array, $this_pop_subpop->descr );
			}
		}
		
		if( in_array( "Youth", $pop_subpop_array ) ){ //if this pop subpop contains "Youth"...
			$return_data['population_calc'] = 12;
			array_push( $study_list_hr, $this_study ); //add to the hr list
			$we_found_it = true;
		//var_dump( $study_list_hr );
			$info_id_list_hr = parse_hr_info_list( $study_list_hr, $info_id_list_by_study );
			//var_dump( $info_id_list_hr );
			//return the things
			$return_data['which_pop'] = $this_evalpop_string;
			$return_data['info_id_list_hr'] = $info_id_list_hr;
		}

	}
	
	if( $we_found_it == true ){
		return $return_data;
	}
	
	//12b. Youth = 12; No subpop check - FOR IPE ONLY!
	foreach( $new_study_ids as $this_study ) {
	
		//what are the pops subpops strings?
		$this_pop_subpops = $sub_pop_data[ $this_study ][ "ipe" ];
		$pop_subpop_array = array(); //to hold the subpop strings (i.e. "Adults", "Families", "Youth")
		foreach( $this_pop_subpops as $this_pop_subpop ){
			array_push( $pop_subpop_array, $this_pop_subpop->descr );
		}
		
		if( in_array( "Youth", $pop_subpop_array ) ){ //if this pop subpop contains "Youth"...
			$return_data['population_calc'] = 12;
			array_push( $study_list_hr, $this_study ); //add to the hr list
			$we_found_it = true;
			$info_id_list_hr = parse_hr_info_list( $study_list_hr, $info_id_list_by_study );
			//return the things
			$return_data['which_pop'] = "ipe";
			$return_data['info_id_list_hr'] = $info_id_list_hr;
		}

	}
	
	if( $we_found_it == true ){
		return $return_data;
	}
	
	//13a. GENERAL = 13; No subpop check - FOR RESULTS EVAL POP ONLY!
	foreach( $new_study_ids as $this_study ) {
	
		if( $eval_pop_data_parsed["isGeneralPopulation"][$this_study] == "Y" ){
		
			$return_data['population_calc'] = 13;
			array_push( $study_list_hr, $this_study ); //add to the hr list
			$we_found_it = true;
			$info_id_list_hr = parse_hr_info_list( $study_list_hr, $info_id_list_by_study );
			//return the things
			$return_data['which_pop'] = $this_evalpop_string;
			$return_data['info_id_list_hr'] = $info_id_list_hr;
		}
	
	}
	
	if( $we_found_it == true ){
		return $return_data;
	}
	
	//13b. GENERAL = 13; No subpop check - FOR IPE ONLY!
	foreach( $new_study_ids as $this_study ) {
	
		if( $ipe_pop_data_parsed["isGeneralPopulation"][$this_study] == "Y" ){
		
			$return_data['population_calc'] = 13;
			array_push( $study_list_hr, $this_study ); //add to the hr list
			$we_found_it = true;
			$info_id_list_hr = parse_hr_info_list( $study_list_hr, $info_id_list_by_study );
			//return the things
			$return_data['which_pop'] ="ipe";
			$return_data['info_id_list_hr'] = $info_id_list_hr;
		}
	
	}
	
	if( $we_found_it == true ){
		return $return_data;
	}
	
	$return_data['population_calc'] = 0;
	$return_data['which_pop'] = 0;
	$return_data['info_id_list_hr'] = $info_id_list_hr;
	
	return $return_data;

}

/**
 * Returns array for subpop value lookup.  This is mostly Mel's lookup for now, probs never called.
 *
 * @return array. Int=>val
 *
 */
function subpop_lookup(){

	//array of subpops/values in db 
	return array(
		1 => "Girls",
		2 => "Boys",
		3 => "Women",
		4 => "Men",
		5 => "African American Participants",
		6 => "Asian Participants",
		7 => "White Participants",
		8 => "Pacific Islander/Native Hawaiian Participants",
		9 => "Hispanic/Latino Participants",
		10 => "Low Income Participants",
		11 => "Native American/Alaskan Native",
		12 => "Other Race",
		13 => "Non-English Speaking"
		);
		
}

/**
 * Returns codetype ids for Population-based subpopulations
 *
 * @return array. CodetypeId => subpop name
 */
function pop_subpop_codetypeid_lookup(){

	return array(
		102 => "tp",
		103 => "ipe",
		104 => "ipu",
		105 => "ese",
		106 => "esu",
		1004 => "ese0",
		1014 => "ese1",
		1024 => "ese2",
		1034 => "ese3",
		1044 => "ese4",
		1054 => "ese5",
		1064 => "ese6",
		1074 => "ese7",
		1084 => "ese8",
		1094 => "ese9"
		);

}

/**
 * Returns array for subpop value lookup.  This is mostly Mel's lookup for now, probs never called.
 *
 * @return array. Int=>val
 *
 */
function evalpop_lookup( $this_eval_pop ){

	//array of subpops/values in db 
	$all_eval_pops = array(
		"TP" => "tp",
		"IE" => "ipe",
		"EU" => "ipu",
		"SE" => "ese",
		"SU" => "esu",
		"E0" => "ese0",
		"E0" => "ese1",
		"E0" => "ese2",
		"E0" => "ese3",
		"E0" => "ese4",
		"E0" => "ese5",
		"E0" => "ese6",
		"E0" => "ese7",
		"E0" => "ese8",
		"E0" => "ese9"
		);
		
	return $all_eval_pops[ $this_eval_pop ];
		
}

/**
 * Returns list of info_ids (unique ids) given a list of info ids by study and a list of studies to be included in list
 *
 * @param array, array.
 * @return string.  Info ids w/ comma separator
 *
 */
function parse_hr_info_list( $study_list_hr, $info_id_list_by_study ){

	if( empty($study_list_hr) || empty( $info_id_list_by_study ) ){
		return "";
	}
	$info_id_list_hr = array();
	foreach( $study_list_hr as $study_id ){
		if( !empty( $info_id_list_by_study[ $study_id ] ) ){
		
			foreach( $info_id_list_by_study[ $study_id ] as $info_id ){
			
				//array_push( $info_id_list_hr, $info_id );
				//var_dump( $info_id );
				$info_id_list_hr[] = $info_id ;
		
			}
		}
	}
	
	//var_dump( $info_id_list_by_study );
	//var_dump( $study_list_hr );
	
	if( count( $info_id_list_hr ) > 1 ){
	//var_dump( $info_id_list_hr );
		$string_info_id_list = implode(", ", $info_id_list_hr ); 
	} else {
		$string_info_id_list = current( $info_id_list_hr );
	}
	return $string_info_id_list;

}

/**
 * Returns list of study ids given list of info_ids
 *
 * @param array. List of info_ids in format: studyid_seq_count
 * @return array. List of Study IDs
 */
function parse_studyids_from_infoids( $info_id_list ){

	//parse info list to show what Studies (study_ids) we need to look at //TODO: modularize this (see func below)
	$study_id_list = array();
	//$info_id_list_by_study = array();
	$exploded_info_ids = explode( ", ", $info_id_list );
	foreach( $exploded_info_ids as $this_info_id ){
		$underscore_pos = strpos( $this_info_id, "_" );
		$this_study_id = substr( $this_info_id, 0, $underscore_pos );
		//push to study id list
		array_push( $study_id_list, $this_study_id );
		
		//TODO: do we need this here or in another helper func?
		//push this info id to list by study (for info_id_list_hr-ing later)
		/*
		if( $info_id_list_by_study[$this_study_id] == null ){
			$info_id_list_by_study[$this_study_id] = array();
		}
		array_push( $info_id_list_by_study[$this_study_id], $this_info_id ); 
		*/
	}
	
	//get unique vals for study ids
	$new_study_ids = array_unique( $study_id_list );

	return $new_study_ids;
}

/**** LOOKUPS **/
function cc_transtria_analysis_val_lookup( $which_type, $value ){

	switch( $which_type ){
	
		case "indicator_value":
			//switch-case the values if they differ (between our db and transtria's desired output val)
			switch( $value ){
				case "A0":
					return 100;
				case "A1":
					return 101;
				case "A2":
					return 102;
				case "A3":
					return 103;
				case "A4":
					return 104;
				case "A5":
					return 105;
				case "A6":
					return 106;
				case "A7":
					return 107;
				case "A8":
					return 108;
				case "A9":
					return 109;
				case "AA":
					return 110;
				case "AB":
					return 111;
				case "AC":
					return 112;
				case "AD":
					return 113;
				case "AE":
					return 114;
				case "AF":
					return 115;
				case "AG":
					return 116;
				case "AH":
					return 117;
				case "AI":
					return 118;
				case "AJ":
					return 119;
				case "AK":
					return 120;
					
				
				//default for $value = transtria's value
				default:
					return $value;
					break;
			
			}
			break;
		
	}

}