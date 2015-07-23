<?php 
/**
 * CC Transtria Extras
 *
 * @package   CC Transtria Extras
 * @author    CARES staff
 * @license   GPL-2.0+
 * @copyright 2015 CommmunityCommons.org
 */

/**
 * Are we on the transtria extras tab?
 *
 * @since   1.0.0
 * @return  boolean
 */
function cc_transtria_is_component() {
    if ( bp_is_groups_component() && bp_is_current_action( cc_transtria_get_slug() ) )
        return true;

    return false;
}

/**
 * Is this the transtria group?
 *
 * @since    1.0.0
 * @return   boolean
 */
function cc_transtria_is_transtria_group( $group_id = 0 ){
    if ( ! $group_id ) {
        $group_id = bp_get_current_group_id();
    }
    return ( $group_id == cc_transtria_get_group_id() );
}

/**
 * Get the group id based on the context
 *
 * @since   1.0.0
 * @return  integer
 */
function cc_transtria_get_group_id(){
    switch ( get_home_url() ) {
        case 'http://localhost/wordpress':
            $group_id = 597;  //Mike's machine
            break;
		case 'http://localhost/cc_local':
            $group_id = 690;  //Mel's compy
            break;
        case 'http://dev.communitycommons.org':
            $group_id = 592; //TODO
            break;
        default:
            $group_id = 594;  //TODO
            break;
    }
    return $group_id;
}

/**
 * Get various slugs
 * These are gathered here so when, inevitably, we have to change them, it'll be simple
 *
 * @since   1.0.0
 * @return  string
 */
function cc_transtria_get_slug(){
    return 'study-form';
}
function cc_transtria_get_assignments_slug(){
    return 'assignments';
}
function cc_transtria_get_quick_survey_summary_slug(){
    return 'quick-summary';
}
function cc_transtria_get_analysis_slug(){
    return 'analysis';
}


/**
 * Get URIs for the various pieces of this tab
 * 
 * @return string URL
 */
function cc_transtria_get_home_permalink( $group_id = false ) {
    $group_id = ( $group_id ) ? $group_id : bp_get_current_group_id() ;
    $permalink = bp_get_group_permalink( groups_get_group( array( 'group_id' => $group_id ) ) ) .  cc_transtria_get_slug() . '/';
    return apply_filters( "cc_transtria_home_permalink", $permalink, $group_id);
}
function cc_transtria_get_assignments_permalink( $page = 1, $group_id = false ) {
    $permalink = cc_transtria_get_home_permalink( $group_id ) . cc_transtria_get_assignments_slug() . '/' . $page . '/';
    return apply_filters( "cc_transtria_assignments_permalink", $permalink, $group_id);
}
function cc_transtria_get_analysis_permalink( $section = false, $metro_id = false ) {
    $permalink = cc_transtria_get_home_permalink( $group_id ) . cc_transtria_get_analysis_slug() . '/' . $page . '/';
    return apply_filters( "cc_transtria_analysis_permalink", $permalink, $group_id);

    // If we've specified a section, build it, else assume health.
    // Expects 'revenue' or 'health'
    //$section_string = ( $section == 'revenue' ) ? cc_transtria_get_analysis_revenue_slug() . '/' : cc_transtria_get_analysis_health_slug() . '/';

    // $permalink = cc_transtria_get_home_permalink() . cc_transtria_get_analysis_slug() . '/' . $metro_id_string . $section_string;
    // return apply_filters( "cc_transtria_analysis_permalink", $permalink, $section, $metro_id);
}



/**
 * Where are we?
 * Checks for the various screens
 *
 * @since   1.0.0
 * @return  string
 */
function cc_transtria_on_main_screen(){
    // There should be no action variables if on the main tab
    if ( cc_transtria_is_component() && ! ( bp_action_variables() )  ){
        return true;
    } else {
        return false;
    }
}
function cc_transtria_on_assignments_screen(){
    if ( cc_transtria_is_component() && bp_is_action_variable( cc_transtria_get_assignments_slug(), 0 ) ){
        return true;
    } else {
        return false;
    }
}
function cc_transtria_on_analysis_screen( $section = null ){
    // If we're checking for a specific subsection, check for it.
    // if ( $section && in_array( $section, array(  cc_transtria_get_analysis_slug(), cc_transtria_get_analysis_revenue_slug() ) ) ) {

        // if ( cc_transtria_is_component() && bp_is_action_variable( cc_transtria_get_analysis_slug(), 0 ) && bp_is_action_variable( $section, 2 ) ){
            // return true;
        // } else {
            // return false;
        // }

    // }

   if ( cc_transtria_is_component() && bp_is_action_variable( cc_transtria_get_analysis_slug(), 0 ) ){
        return true;
    } else {
        return false;
    }
}
function cc_transtria_on_survey_quick_summary_screen(){
    if ( cc_transtria_is_component() && bp_is_action_variable( cc_transtria_get_quick_survey_summary_slug(), 0 ) ){
        return true;
    } else {
        return false;
    }
}
function cc_transtria_on_analysis_complete_report_screen(){
   if ( cc_transtria_is_component() && bp_is_action_variable( 'all', 3 ) ){
        return true;
    } else {
        return false;
    }
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
		'abstractor' => 'abstractor',
		'validator' => 'abstractor',
		'searchtooltype' => 'SearchToolType',
		'searchtoolname' => 'SearchToolName',
		"fundingsource" => "FundingSource",
		"domesticfundingsources" => "domesticfundingsources",
		"fundingpurpose" => "fundingpurpose",
		'StudyDesign' => 'StudyDesignID',
		"validity_threats"=> "ValidityThreats",
		"unit_of_analysis" => "UnitOfAnalysis",
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
	
	return $dd_ids;

}

/**
 * Returns array of div ids -> code_tbl lookup names for multiple dropdown fields in form for Populations tabs.
 *
 * @return array. String div id in form => String lookup name in db (code_tbl)
 *
 */
function cc_transtria_get_multiple_dropdown_ids_populations( $which_pop = 'all'){

	//get basic field and lookup names
	$dd_base_ids = array(
		"_geographic_scale" => "GeographicScale", //"%s_geographic_scale" % _prefix, "%s.GeographicScale"  %s is the pop prepend (ese0, tp, ipe...)
		"_gender" => "Gender",
		'_ability_status' => "AbilityStatus",
		"_sub_populations" => "SubPopulations",
		"_youth_populations" => "YouthPopulations",
		"_professional_populations" => 'ProfessionalPopulations'
	);
	
	//Special cases for certain pops.
	$dd_actual_ids = array(
		//ESE and IPE only, but there are multiple ESE tabs, awesoooome
		"ese_representative_subpopulations" => "RepresentativeSubpopulations",
		"ipe_representative_subpopulations" => "RepresentativeSubpopulations",
		//ESE and IPE only (all ESE tabs)
		"ese_hr_subpopulations" => "ese.HighRiskSubpopulations",
		"ipe_hr_subpopulations" => "ipe.HighRiskSubpopulations",
		//special!
		"ipe_exposure_frequency" => "ipe.ExposureFrequency"
	);
	
	//which pops are we interested in? Usually all, but ese is going to be special
	if( $which_pop == 'all' ){
		$pops_types = cc_transtria_get_basic_pops_types();
	
		foreach( $dd_base_ids as $div_id => $lookup_name ){
			
			//Now, cycle through population types and build array for all pops
			foreach( $pops_types as $pop_type ){
				//build actual id
				$actual_div_id = $pop_type . $div_id;
				//build actual lookup name
				if( $lookup_name != "Gender" ) { //because legacy db $hit
					$actual_lookup_name = $pop_type . '.' . $lookup_name;
				} else {
					$actual_lookup_name = "Gender";
				}
				
				//add to master array of lookup things
				$dd_actual_ids[ $actual_div_id ] = $actual_lookup_name;			
				
			}
		}
	} else if ( $which_pop == 'ese' ) { //prepping for ese tab madness!
		//cheating!
		$dd_actual_ids = array(
			"ese_representative_subpopulations" => "RepresentativeSubpopulations",
			"ese_subpopulations" => "ese.HighRiskSubpopulations",
			"ese_geographic_scale" => "ese.GeographicScale", //"%s_geographic_scale" % _prefix, "%s.GeographicScale"  %s is the pop prepend (ese0, tp, ipe...)
			"ese_gender" => "Gender",
			"ese_sub_populations" => "ese.SubPopulations",
			"ese_youth_populations" => "ese.YouthPopulations",
			"ese_professional_populations" => "ese.ProfessionalPopulations"
		);
	
	}
	
	return $dd_actual_ids;	
	
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

/**
 * Relates populations table column names to div ids on form
 *
 * @param string. Population type - e.g., 'ese', 'ipu', 'tp', 'ese1'
 */
 //TODO: make this general for incoming population
function cc_transtria_relate_div_ids_and_pop_columns( $which_pop ){

	//Populations table name => div id
	$lookups = array(
		$which_pop . '_reported' => 'reported',
		$which_pop . '_population_size' => 'PopulationSize',
		$which_pop . '_populationsize_notreported' => 'populationsize_notreported',
		$which_pop . '_geographic_scale' => $whichPop . '.GeographicScale', //multi
		$which_pop . '_geographicscale_notreported' => 'geographicscale_notreported', 
		$which_pop . '_eligibility_criteria' => 'EligibilityCriteriaCode',
		$which_pop . '_eligibilitycriteria_notreported' => 'eligibilitycriteria_notreported',
		$which_pop . '_general_population' => 'isGeneralPopulation',
		$which_pop . '_generalpopulation_notreported' => 'generalpopulation_notreported',
		$which_pop . '_gender' => 'Gender',//multi/lookup 
		$which_pop . '_gender_pctmale' => 'PctGenderMale',
		$which_pop . '_gender_pctfemale' => 'PctGenderFemale',
		$which_pop . '_gender_notreported' => 'gender_notreported',
		$which_pop . '_min_age' => 'MinimumAge',
		$which_pop . '_minimumage_notreported' => 'minimumage_notreported',
		$which_pop . '_max_age' => 'MaximumAge',
		$which_pop . '_maximumage_notreported' => 'maximumage_notreported',
		$which_pop . '_ability_status' => $whichPop . '.AbilityStatus', //multi
		$which_pop . '_cognition_disability_pct' => 'PctCognitionDisability',
		$which_pop . '_getting_along_disability_pct' => 'PctGettingAlongDisability',
		$which_pop . '_life_activities_disability_pct' => 'PctLifeActivitiesDisability',
		$which_pop . '_mobility_disability_pct' => 'PctMobilityDisability',
		$which_pop . '_self_care_disability_pct' => 'PctSelfCareDisability',
		$which_pop . '_participation_disability_pct' => 'PctParticipationDisability',
		$which_pop . '_abilitystatus_notreported' => 'abilitystatus_notreported',
		$which_pop . '_sub_populations' => $whichPop . '.SubPopulations',
		$which_pop . '_subpopulations_notreported' => 'subpopulations_notreported',
		$which_pop . '_youth_populations' => $which_pop . '.YouthPopulations',
		$which_pop . '_youthpopulations_notreported' => 'youthpopulations_notreported',
		$which_pop . '_professional_populations' => $which_pop . '.ProfessionalPopulations',
		$which_pop . '_professionalpopulations_notreported' => 'professionalpopulations_notreported',
		$which_pop . '_other_populations' => 'other_populations',
		$which_pop . '_other_population_description' => 'other_population_description',
		$which_pop . '_african_american_pct' => 'PctBlack',
		$which_pop . '_white_pct' => 'PctWhite',
		$which_pop . '_asian_pct' => 'PctAsian',
		$which_pop . '_pacific_islander_pct' => 'PctPacificIslander',
		$which_pop . '_native_american_pct' => 'PctNativeAmerican',
		$which_pop . '_other_race_pct' => 'PctOtherRace',
		$which_pop . '_racepercentages_notreported' => 'racepercentages_notreported',
		$which_pop . '_hispanic_pct' => 'PctHispanic',
		$which_pop . '_percenthispanic_notreported' => 'percenthispanic_notreported',
		$which_pop . '_lower_income_pct' => 'PctLowerIncome',
		$which_pop . '_percentlowerincome_notreported' => 'percentlowerincome_notreported',
		$which_pop . '_non_english_speakers_pct' => 'PctNoEnglish',
		$which_pop . '_percentnonenglish_notreported' => 'percentnonenglish_notreported',
		//not all pops
		$which_pop . '_representativeness' => 'Representativeness',
		$which_pop . '_representativeness_notreported' => 'representativeness_notreported',
		$which_pop . '_applicability_hr_pops' => 'ApplicabilityHRPopulations',
		//$which_pop . '_applicabilityhrpops_notreported' => 'ApplicabilityHRPopulations', //hmm, this one doesn't exist?
		$which_pop . '_hr_subpopulations' => $which_pop . '.HighRiskSubpopulations', //multi.  Won't exist for other than ese, ipe
		$which_pop . '_exposure_frequency' => 'ExposureFrequency', //multi
		$which_pop . '_freqofexposure_notreported' => 'freqofexposure_notreported', //multi
		
		
	);



}


/**
 * Which sub populations tabs are there?
 *
 * @return array.  Array of strings.
 */
function cc_transtria_get_basic_pops_types(){

	return array( 'tp', 'ipe', 'ipu', 'ese', 'esu' );

}
