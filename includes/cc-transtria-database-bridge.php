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
	
	//Now, perform lookup.


}

/**
 * Gets an array of MULTIPLE lookups for POPULATIONS  and does those lookups
 *
 * @return Array.  Array of arrays of options for all multiple POPULATION dropwdown fields (dropdowns that exist in Populations tabs in form).
 *
 */
function cc_transtria_get_multiple_dropdown_options_populations(){

	$dd_ids = cc_transtria_get_multiple_dropdown_ids_populations();
	
	//Now, perform lookup.


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



/******** LOOKUP TABLES ARE FUN *********/

/**
 * Returns array of div ids -> code_tbl lookup names for singleton dropdown fields in form.
 *
 * @return array. String div id in form => String lookup name in db (code_tbl)
 *
 */
function cc_transtria_get_singleton_dropdown_ids(){

	$dd_ids = array(
		'searchtooltype' => 'SearchToolType',
		'searchtoolname' => 'SearchToolName',
		"fundingsource" => "FundingSource",
		"domesticfundingsources" => "domesticfundingsources",
		"fundingpurpose" => "fundingpurpose",
		'StudyDesign' => 'StudyDesignID',
		"validity_threats"=> "ValidityThreats",
		"ipe_exposure_frequency" => "ipe.ExposureFrequency",  //only on IPE tab.  TODO: confirm!
		"state_setting" => 'US States',
		"setting_type" => "SettingType",
		"partner_discipline" => "Partner discipline",
		"theory_framework_type" => "TheoryFramework",
		"intervention_component" => "Intervention Components",
		"strategies" => "Strategies",
		"pse_components" => "PSEcomponents",
		"complexity" => "Complexity",
		"intervention_location" => "InterventionLocation",
		"intervention_indicators" => "Indicator",
		"intervention_outcomes_assessed" => "OutcomesAccessed",
		"evaluation_type" => "EvaluationType",
		"evaluation_methods" => "EvaluationMethod"
		


	);



}

/**
 * Returns array of div ids -> code_tbl lookup names for multiple dropdown fields in form for Populations tabs.
 *
 * @return array. String div id in form => String lookup name in db (code_tbl)
 *
 */
function cc_transtria_get_multiple_dropdown_ids_populations(){

	//get basic field and lookup names
	$dd_ids = array(
		"_geographic_scale" => "GeographicScale", //"%s_geographic_scale" % _prefix, "%s.GeographicScale"  %s is the pop prepend (ese0, tp, ipe...)
		"_gender" => "GenderCode",
		//"ipe_exposure_frequency" => "ipe.ExposureFrequency",  //TODO: special case...\
		"_sub_populations" => "SubPopulations",
		"_youth_populations" => "YouthPopulations",
		"_professional_populations" => 'ProfessionalPopulations',
		//TODO: figure this mess out.
		//ESE and IPE only, but there are multiple ESE tabs, awesoooome
		"_representative_subpopulations" => "RepresentativeSubpopulations",
		//ESE and IPE only (all ESE tabs)
		"_subpopulations" => "HighRiskSubpopulations"
		
		
	
	);
	
	//

}

/**
 * Returns array of div ids -> code_tbl lookup names for multiple dropdown fields in form for EA tabs.
 *
 * @return array. String div id in form => String lookup name in db (code_tbl)
 *
 */
function cc_transtria_get_multiple_dropdown_ids_ea(){

	$dd_ids = array(
		"_duration" => "_duration", //"%s_duration" % _prefix, "%s_duration"
		"_result_evaluation_population" => " Results Populations", //"%s_result_evaluation_population" % _prefix, "%s Results Populations"
		"_result_subpopulations" => " Results SubPopulations", //"%s_result_subpopulations" % _prefix, "%s Results SubPopulations"
		"_result_indicator_direction"=> "indicator_direction", //"%s_result_indicator_direction" % _prefix, "indicator_direction"
		"_result_outcome_direction" => "outcome_direction", //"%s_result_outcome_direction" % _prefix, "outcome_direction"
		"_result_strategy" => "result_strategy", //"%s_result_strategy" % _prefix, "result_strategy"
		"_result_outcome_type" => '%s_result_outcome_type_other', //"%s_result_outcome_type" % _prefix, '%s_result_outcome_type_other'
		//yes, this is a typo.  No we're not changing it now, it's in the db.
		"_result_outcome_accessed" => " OutcomesAccessed", //"%s_result_outcome_accessed" % _prefix, "%s OutcomesAccessed" 
		"_result_measures" => " Measures", //"%s_result_measures" % _prefix, "%s Measures"
		"_result_indicator" => " Indicator", //"%s_result_indicator" % _prefix, "%s Indicator"
		"_result_statistical_model" => "statistical_model", //"%s_result_statistical_model" % _prefix, "statistical_model"
		"_result_statistical_measure" => "statistical_measure" //"%s_result_statistical_measure" % _prefix, "statistical_measure"
		


	);

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