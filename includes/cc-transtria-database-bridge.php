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
function cc_transtria_get_all_data_one_study( $study_id = null ){

	$meta_data = cc_transtria_get_study_metadata( $study_id );
	$single_data = cc_transtria_get_single_study_data( $study_id );

	$pops_data_single = cc_transtria_get_pops_study_data_single( $study_id );
	$pops_data_multiple = cc_transtria_get_pops_study_data_multiple( $study_id );
	
	$ea_data = cc_transtria_get_ea_tab_data_for_study( $study_id );

}

/**
 * Returns metadata given a study id (num ese tabs, num ea tabs)
 *
 * @param int. Study ID.
 * @return array
 */
function cc_transtria_get_study_metadata( $study_id = null ){

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
function cc_transtria_get_single_study_data( $study_id = null ){
	
	global $wpdb;
	
	//TODO, use wp->prepare
	$question_sql = 
		"
		SELECT * 
		FROM $wpdb->transtria_studies
		WHERE `StudyID` = $study_id
		";
		
	$form_rows = $wpdb->get_results( $question_sql, ARRAY_A ); //TODO: is ARRAY the best type to use here?
	
	//go through each field and make sure it lines up with div ids
	$reindexed_form_rows = cc_transtria_match_div_ids_to_studies_columns( current( $form_rows ) );
	
	//return current($form_rows);
	return $reindexed_form_rows;

}

/**
 * Saves single study data to studies tables
 * 
 * @param array. Associative array of db_label => incoming value
 * @return string. Error message?
 */
function cc_transtria_save_to_studies_table( $studies_data, $study_id, $new_study = false ){
	
	global $wpdb;
	
	//TODO: if this works, combine things
	if( $new_study = false ){
	
		$studies_where = array(
			'StudyID' => $study_id 
		);
		
		
		//if we have [board] values set by the form, update the table
		// wpdb->update is perfect for this. Wow. Ref: https://codex.wordpress.org/Class_Reference/wpdb#UPDATE_rows
		if ( !empty ( $studies_data ) ) {
			$num_study_rows_updated = $wpdb->update( $wpdb->transtria_studies, $studies_data, $studies_where, $format = null, $where_format = null );
		}
		
		if( $num_study_rows_updated === false ){
			return "Error: new study data could not be added to db";
		} else {
			return $num_study_rows_updated; //should be 1
		}
		
	} else {
		//insert
		//Hmmm...maybe just study id and then update, since not all fields are being used...
		$result = $wpdb->insert( 
			$wpdb->transtria_studies, 
			array( 
				'StudyID' => (int)$study_id
			),
			array( 
				'%d'
			) 
		);
		
		if( $result === false ){
			return "Error: new study could not be initialized in db";
		}
		
		$studies_where = array(
			'StudyID' => $study_id 
		);
		$dummy_studies = array(
			'abstractor' => "04"
			);
		
		
		if ( !empty ( $studies_data ) ) {
			$num_study_rows_updated = $wpdb->update( $wpdb->transtria_studies, $studies_data, $studies_where, $format = null, $where_format = null );
		}
		
		if( $num_study_rows_updated === false ){
			var_dump( $studies_data );
			return "Error: new study data could not be added to db for new study";
		} else {
			return $num_study_rows_updated; //should be 1
		}
	}

}


/**
 * Returns array of all multiple data for a study
 *
 * @param int. Study ID.
 * @return array of arrays. Codetype div names => selected values
 *
 */
function cc_transtria_get_study_data_multiple( $study_id = null ){


	//get the selected values (results) for this study from code_results (then translate to human language from code_tbl!)
	$results = cc_transtria_get_all_code_results_by_study_id( $study_id );
	
	//cycle through array keys are replace with lookup table keys.
	$results = cc_transtria_match_div_ids_to_multiple_columns( $results );
 
 //var_dump( $results );
	return $results;
}

/**
 * Returns array of string->values for population data in populations table
 *
 * @since    1.0.0
 * @return 	array
 */
function cc_transtria_get_pops_study_data_single( $study_id = null ){

	global $wpdb;
	
	//how many ese tabs do we have?
	$meta_sql = $wpdb->prepare( 
		"
		SELECT      value
		FROM        $wpdb->transtria_metadata
		WHERE		StudyID = %s 
		AND 		variablename = 'ese tabCount'
		",
		$study_id
	); 
	
	$ese_tab_count = $wpdb->get_results( $meta_sql, ARRAY_A );
	$ese_tab_count = intval( $ese_tab_count[0]['value'] ); //e.g., if = 2, look for ese0, ese1
	
	$which_pops = cc_transtria_get_basic_pops_types();
	
	//because ese tabs are zero-indexed (meaning: just one addtnl ese tab = ese0):
	for( $i=0; $i < $ese_tab_count; $i++){
		//append ese tab name to $which_pops
		$current_ese_tab = 'ese' . $i;
		array_push( $which_pops, $current_ese_tab );
	}
	
	$all_pops_tabs = []; //instantiate empty array to hold all the pops data
	
	foreach( $which_pops as $which_pop ){
		//there are multiple rows in the pops table per study
		$populations_sql = $wpdb->prepare( 
			"
			SELECT      *
			FROM        $wpdb->transtria_population
			WHERE		StudyID = %s 
			AND 		PopulationType = %s
			",
			$study_id,
			$which_pop
		); 
		
		//run query
		$form_rows = $wpdb->get_results( $populations_sql, ARRAY_A );
		
		//var_dump( $form_rows );
		
		//put label of form rows in div id form
		$new_form_rows = cc_transtria_match_div_ids_to_pops_columns_single( $which_pop, current( $form_rows) );
		
		//add to master array
		$all_pops_tabs[ $which_pop ] = $new_form_rows;
		//$all_pops_tabs[ $which_pop ] = 't';
		
	}
	
	//go through each field and make sure it lines up with div ids
	//$reindexed_form_rows = cc_transtria_match_div_ids_to_pops_columns_single( current( $form_rows ) );
	
	return $all_pops_tabs;


}


//TODO: are we using this one at all??
/**
 * Returns array of string->values for population data in code_results table (drop downs)
 *
 * @since    1.0.0
 * @return 	array
 */
function cc_transtria_get_pops_study_data_multiple( $study_id = null ){

	$what_pops = cc_transtria_get_all_pops_type_for_study( $study_id );
	
	//get text ids for pops stuff
	$pops_ids = array_flip( cc_transtria_get_multiple_dropdown_ids_populations() ); //db => div_ids
	$pops_keys_only = array_keys( $pops_ids ); //we only need the db number right now?
	
	//get lookup codes
	$lookup_codes = cc_transtria_get_codes_by_names( $pops_keys_only );
	
	//get the selected values (results) for this study from code_results (then translate to human language from code_tbl!)
	$results = cc_transtria_get_all_code_results_by_study_id( $study_id );
	
	return $results;



}

/**
 * Gets all EA tab data given a study id
 *
 * @param int, int. Study ID, number of ea tabs (from meta table if not provided)
 * @return array. String list of EA tab names
 */
function cc_transtria_get_ea_tab_data_for_study( $study_id, $num_ea_tabs = null ){

	global $wpdb;
	
	if( is_null( $num_ea_tabs ) ){
	
		//how many ea tabs do we have?
		$meta_sql = $wpdb->prepare( 
			"
			SELECT      value
			FROM        $wpdb->transtria_metadata
			WHERE		StudyID = %s 
			AND 		variablename = 'ea tabCount'
			",
			$study_id
		); 
		
		$ea_tab_count = $wpdb->get_results( $meta_sql, ARRAY_A );
		
		$num_ea_tabs = intval( $ea_tab_count[0]['value'] ); 
	
	} 
	
	$which_ea_tabs = [];
	
	//how many ea tabs do we have?
	$ea_sql = $wpdb->prepare( 
		"
		SELECT      *
		FROM        $wpdb->transtria_effect_association
		WHERE		StudyID = %d 
		AND 		seq <= %d
		",
		$study_id,
		$num_ea_tabs
	); 
	
	$form_rows = $wpdb->get_results( $ea_sql, ARRAY_A );
	
	$all_ea_tabs = []; //instantiate empty array to hold all the pops data
	
	//cycle through all ea #s and do some stuff (Mel's brain is tired right now)
	for( $i = 1; $i <= $num_ea_tabs; $i++ ){
		//put label of form rows in div id form
		$label = 'ea_' . $i;
		$new_form_rows = cc_transtria_match_div_ids_to_ea_columns_single( $label, $form_rows[$i - 1] ); //0-indexed form_rows..
	
		//add to master array
		$all_ea_tabs[ $i ] = $new_form_rows;
		
	}
	
	//var_dump( $all_ea_tabs );
	
	return $all_ea_tabs;

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
		order by StudyID
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
	
	$question_sql = 
		"
		SELECT `rec-number`, `titles_title`
		FROM $wpdb->transtria_phase2
		order by `titles_title`
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

/**
 * Returns citation info for a given endnote id
 *
 * @param int. Endnote ID
 * @return array.
 */
function cc_transtria_get_endnote_citation_info( $endnoteid ){

	global $wpdb;
	
	$endnote_sql = $wpdb->prepare( 
		"
		SELECT      *
		FROM        $wpdb->transtria_phase2
		WHERE		`rec-number` = %d 
		",
		intval($endnoteid)
	); 
		
	$form_rows = $wpdb->get_row( $endnote_sql, OBJECT );
	
	//TODO: can we just use get_results instead of this mess?  Probably..
	//declare our array to hold study vals
	//$endnote_array = [];

	
	return $form_rows;





}

//TODO: are we using this?
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
function cc_transtria_get_multiple_dropdown_options_populations( $study_id = null ){

	$dd_ids = cc_transtria_get_multiple_dropdown_ids_populations( 'all', $study_id );
	
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
	
	//Now, perform lookup.  Just once, since all ea tabs will have same data in their dropdowns.  #efficiency
	foreach( $dd_ids as $div_id => $lookup_name ){
		
		$dd_options[ $div_id ] = cc_transtria_get_options_from_db( $lookup_name );
	
	}
	
	return $dd_options;

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

/**
 * Gets all population types given a study id
 *
 * @param int. Study ID
 * @return array. String list of populations types
 */
function cc_transtria_get_all_pops_type_for_study( $study_id ){

	global $wpdb;
	
	$which_pops = cc_transtria_get_basic_pops_types();
	
	if ( empty( $study_id ) ){
		return $which_pops;
	
	} else {

		//how many ese tabs do we have?
		$meta_sql = $wpdb->prepare( 
			"
			SELECT      value
			FROM        $wpdb->transtria_metadata
			WHERE		StudyID = %s 
			AND 		variablename = 'ese tabCount'
			",
			$study_id
		); 
		
		$ese_tab_count = $wpdb->get_results( $meta_sql, ARRAY_A );
		$ese_tab_count = intval( $ese_tab_count[0]['value'] ); //e.g., if = 2, look for ese0, ese1
		
		//var_dump( $ese_tab_count );
		
		//because ese tabs are zero-indexed (meaning: just one addtnl ese tab = ese0):
		for( $i=0; $i < $ese_tab_count; $i++){
			//append ese tab name to $which_pops
			$current_ese_tab = 'ese' . $i;
			//push to allpops array?
			array_push( $which_pops, $current_ese_tab );
		}

		return $which_pops;

	}

}

/**
 * Gets all EA tabs given a study id
 *
 * @param int. Study ID
 * @return array. String list of EA tab names
 */
function cc_transtria_get_num_ea_tabs_for_study( $study_id ){

	global $wpdb;
	
	$which_ea_tabs = [];
	
	//how many ea tabs do we have?
	$meta_sql = $wpdb->prepare( 
		"
		SELECT      value
		FROM        $wpdb->transtria_metadata
		WHERE		StudyID = %s 
		AND 		variablename = 'ea tabCount'
		",
		$study_id
	); 
	
	$ea_tab_count = $wpdb->get_results( $meta_sql, ARRAY_A );
	$ea_tab_count = intval( $ea_tab_count[0]['value'] ); 

	return $ea_tab_count;

}

/**
 * Returns codetypeID of lookups, given array of string names
 *
 * @param array?
 * @return Array?
*/

function cc_transtria_get_codes_by_names( $incoming_names ){

	global $wpdb;
	
	//turn array into comma-delimited string
	
	$incoming_string_list = implode("','", $incoming_names );
	
	//remove " from beginning and end and replace w '
	//$incoming_string_list = substr( $incoming_string_list, 0, -1 );
	$incoming_string_list = "'" . $incoming_string_list . "'";
	trim($incoming_string_list, '\"'); 
	
	//can we do this in one db call (instead of one db call per name)?
	$codeid_sql = 
			"
			SELECT      codetype, codetypeID
			FROM        $wpdb->transtria_codetype
			WHERE		codetype IN ( $incoming_string_list )
			"
	;
	
	$code_ids = $wpdb->get_results( $codeid_sql, ARRAY_A );
	
	return $code_ids;

}

/**
 * Get all code_results values (2-char string) given a study id
 *
 * @param int. Study ID
 * @return array
 *
 */
function cc_transtria_get_all_code_results_by_study_id( $study_id = null ){

	global $wpdb;
	
	//ok, this works, but can we join w/codetype table to get names?
	/*$results_sql = $wpdb->prepare( 
		"
		SELECT      t1.codetypeID, t1.result
		FROM        $wpdb->transtria_code_results t1
		WHERE		ID = %s 
		",
		$study_id
	); */
	
	$results_sql = $wpdb->prepare(
		"
		SELECT t2.codetype, t1.result 
		FROM $wpdb->transtria_code_results t1,
			$wpdb->transtria_codetype t2
		WHERE t1.codetypeID = t2.codetypeID
		AND t1.ID = %s
		order by t2.codetype
		",
		$study_id
	);
	
	$results = $wpdb->get_results( $results_sql, ARRAY_A );
	
	//now, we need to put these in a coherent format. All items with the same codetypeid should be in their own sub-array. 
	$out = array();
	
	foreach ( $results as $result ) {
		
		//result['codetypeid'] will be the key 
		//$key = $result['codetypeID'];
		$key = $result['codetype'];
		$r = & $out;
		
		//foreach ( explode( ".", $key ) as $key ) {
		foreach ( explode( ",", $key ) as $key ) {
			//var_dump( $key );
			if ( isset( $r[$key] ) ) {
				//test for array already
				if( is_array( $r[$key] ) ){
					//push the new value
					array_push( $r[$key], $result['result'] );
				} else {
					//make an array w the new value added
					$old_r_key = $r[$key];
					$r[$key] = array( $old_r_key, $result['result'] );
				}
				
			} else {
				//there is no key existing, make new one
				$r[$key] = $result['result']; 
			}
			$r = & $r[$key];
		}
				
	}
	
	//order the array by codetype id key (for sanity and checking things)
	ksort( $out );
	
	//var_dump( $out ); //works!
	return $out;
	
}






/**
 * Gets the last study id in table and increments it by one.
 *
 * @since   1.0.0
 * @return 	array
 */
function cc_transtria_get_next_study_id( ){
	global $wpdb;
	 
	$results = $wpdb->get_col( 
		"
		SELECT max( StudyID )
		FROM $wpdb->transtria_studies
		"
	);
	
	$int_results = (int)$results[0] + 1;//todo: is this the best way?
	
	return $int_results;

}

//TODO: code table things....