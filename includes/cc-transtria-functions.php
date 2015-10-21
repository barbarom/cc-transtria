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
            $group_id = 5321; //TODO
            break;
        case 'http://www.communitycommons.org':
            $group_id = 697; //TODO
            break;
        case 'http://staging.communitycommons.org':
            $group_id = 587; //TODO
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
function cc_transtria_get_assignments_permalink( $group_id = false ) {
    $permalink = cc_transtria_get_home_permalink( $group_id ) . cc_transtria_get_assignments_slug() . '/';
    return apply_filters( "cc_transtria_assignments_permalink", $permalink, $group_id);
}
function cc_transtria_get_analysis_permalink( $section = false, $group_id = false ) {
    $permalink = cc_transtria_get_home_permalink( $group_id ) . cc_transtria_get_analysis_slug() . '/';
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
		"domesticfundingsources" => "DomesticFundingSourceType",
		"fundingpurpose" => "fundingpurpose",
		'StudyDesign' => 'StudyDesign',
		"validity_threats"=> "ValidityThreats",
		"unit_of_analysis" => "UnitOfAnalysis",
		"ipe_exposure_frequency" => "ExposureFrequency",  //only on IPE tab.  TODO: confirm!
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
 * @param string, int. Population string (if !'all'), Study ID.
 * @return array. String div id in form => String lookup name in db (code_tbl)
 *
 */
function cc_transtria_get_multiple_dropdown_ids_populations( $which_pop = 'all', $study_id = null ){

	//get basic field and lookup names
	$dd_base_ids = array(
		"_geographic_scale" => "GeographicScale", //"%s_geographic_scale" % _prefix, "%s.GeographicScale"  %s is the pop prepend (ese0, tp, ipe...)
		"_gender" => "Gender",
		'_ability_status' => "AbilityStatus",
		"_sub_populations" => "SubPopulations",
		"_youth_populations" => "YouthPopulations",
		"_professional_populations" => 'ProfessionalPopulations',
		
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
		"ipe_exposure_frequency" => "ExposureFrequency"
	);
	
	//which pops are we interested in? Usually all, but ese is going to be special
	if( $which_pop == 'all' ){
	
		//var_dump( $study_id);
		//if there's an incoming study id, get the extra ese pops.
		if( !empty( $study_id ) ){
			$pops_types = cc_transtria_get_all_pops_type_for_study( $study_id );
		} else {
			$pops_types = cc_transtria_get_basic_pops_types();
		}
	
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
		"ea_duration" => "Duration", //"%s_duration" % _prefix, "%s_duration"
		"ea_result_statistical_model" => "StatisticalModel", //"%s_result_statistical_model" % _prefix, "statistical_model"
		"ea_result_evaluation_population" => "ea_1 Results Populations", //"%s_result_evaluation_population" % _prefix, "%s Results Populations"
		"ea_result_reference_population" => "Results Reference Populations", //"%s_result_evaluation_population" % _prefix, "%s Results Populations"
		"ea_result_subpopulations" => "ea_1 Results SubPopulations", //"%s_result_subpopulations" % _prefix, "%s Results SubPopulations"
		"ea_result_indicator_direction"=> "Results Indicator Direction", //"%s_result_indicator_direction" % _prefix, "indicator_direction"
		"ea_result_outcome_direction" => "Results Outcome Direction", //"%s_result_outcome_direction" % _prefix, "outcome_direction"
		"ea_result_strategy" => "Strategies", //"%s_result_strategy" % _prefix, "result_strategy"
		"ea_result_outcome_type" => "OutcomeType", //"%s_result_outcome_type" % _prefix, '%s_result_outcome_type_other'
		//yes, this is a typo.  No we're not changing it now, it's in the db.
		"ea_result_outcome_accessed" => "OutcomesAccessed", //yeah, this is a legacy typo #stupid
		"ea_result_measures" => "Measures", //"%s_result_measures" % _prefix, "%s Measures"
		"ea_result_indicator" => "Indicator", //"%s_result_indicator" % _prefix, "%s Indicator"
		"ea_result_statistical_measure" => "StatisticalMeasure" //"%s_result_statistical_measure" % _prefix, "statistical_measure"
		
	);
	
	return $dd_ids;

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
 * function to relate div ids and study id columns for single studies table data (all singleton fields/dropdowns in form, excluding Populations and EA tabs)
 *
 * @param array
 * @return array
 */
function cc_transtria_match_div_ids_to_studies_columns( $study_labels = null, $to_db = false ){

	//we can use array_flip if we need to!
	
	//TODO: determine if the commented-out ones are used...
	//db => field name
	$db_to_div_array = array(
		'StudyID' => 'studyid',
		'abstractor' => 'abstractor',
		'abstractorstarttime' => 'abstractorstarttime',
		'abstractorstoptime' => 'abstractorstoptime',
		'validator' => 'validator',
		'validatorstarttime' => 'validatorstarttime',
		'validatorstoptime' => 'validatorstoptime',
		//evidence_review_phase  //Is this used?
		'PubMedID' => 'PubMedID',
		'othersearchtool' => 'othersearchtool',
		'grantcontractnumber' => 'grantcontractnumber',
		'fundingamount' => 'fundingamount',
		'fundingsource' => 'fundingsource',
		'otherfunding' => 'otherfunding',
		'DomesticFundingSourceType' => 'DomesticFundingSourceType',
		'InternationalFundingSourceType' => 'InternationalFundingSourceType',
		//domeesticintlsetting_notreport //used?
		'domesticfundingsources' => 'domesticfundingsources',
		'fundingpurpose' => 'fundingpurpose',
		'StudyDesignID' => 'StudyDesign',
		'otherStudyDesign' => 'otherstudydesign',
		//StudyType //is this used in the form?
		'design_limitations' => 'design_limitations',
		'designlimitations_notreported' => 'designlimitations_notreported',
		'data_collection' => 'data_collection',
		'validitythreatflag' => 'validitythreatflag',
		//'validitythreat' => 'validity_threats', //multi
		'PubMedID_notreported' => 'PubMedID_notreported',
		'grantcontractnumber_notreported' => 'grantcontractnumber_notreported',
		'fundingamount_notreported' => 'fundingamount_notreported',
		'validitythreat_notreported' => 'validitythreat_notreported',
		'fundingsource_notreported' => 'fundingsource_notreported',
		'domesticfundingsources_notreported' => 'domesticfundingsources_notreported',
		'fundingpurpose_notreported' => 'fundingpurpose_notreported',
		
		//basic_info_verification //is this used?
		'sample_size_available' => 'sample_size_available',
		'sample_estimate' => 'sample_estimate',
		
		'unitanalysis_notreported' => 'unitanalysis_notreported', 
		//representativeness
		'domestic_setting' => 'DomesticSetting',
		'international_setting' => 'InternationalSetting',
		'domesticintlsetting_notreported' => 'domesticintlsetting_notreported',
		//setting_types //is this used? Mel thinks this is multi, so no
		'other_setting_type' => 'other_setting_type',
		//partner_discipline //is this used? Mel thinks this is multi, so no
		'other_partner_discipline' => 'other_partner_discipline',
		'lead_agencies' => 'lead_agencies',
		'lead_agency_role' => 'lead_agency_role',
		'theory_framework_flag' => 'theory_framework_flag',
		//theory_framework_type //is this used? Mel thinks this is multi, so no
		'other_theory_framework' => 'other_theory_framework',
		'intervention_purpose' => 'intervention_purpose',
		'intervention_summary' => 'intervention_summary',
		
		'statesettings_notreported' => 'statesettings_notreported',
		'settingtype_notreported' => 'settingtype_notreported',
		'partnerdiscipline_notreported' => 'partnerdiscipline_notreported',
		'leadagencies_notreported' => 'leadagencies_notreported',
		'leadagencyrole_notreported' => 'leadagencyrole_notreported',
		'theoryframework_notreported' => 'theoryframework_notreported',
		'theoryframeworktype_notreported' => 'theoryframeworktype_notreported',
		'interventionpurpose_notreported' => 'interventionpurpose_notreported',
		'interventionsummary_notreported' => 'interventionsummary_notreported',
		'interventioncomponents_notreported' => 'interventioncomponents_notreported',
		'strategies_notreported' => 'strategies_notreported',
		'psecomponents_notreported' => 'psecomponents_notreported',
		'complexity_notreported' => 'complexity_notreported',
		//duration_notreported //on ea tabs
		'indicators_notreported' => 'indicators_notreported',
		'other_intervention_location' => 'other_intervention_location',
		'locationintervention_notreported' => 'locationintervention_notreported',
		
		'alloutcomesassessed_notreported' => 'alloutcomesassessed_notreported',

		
		//intervention_component //is this used? Mel thinks this is multi, so no
		//complexity //is this used? Mel thinks this is multi, so no
		//duration //this is now part of the EA tabs
		//intervention_location //is this used? Mel thinks this is multi, so no
		//intervention_type //?
		//other_intervention_type //?
		//stage
		//state
		//quality
		//inclusiveness
		'replication_flag' => 'replication',
		'replication_descr' => 'replication_descr',
		
		'replication_notreported' => 'replication_notreported',
		'support_notreported' => 'support_notreported',
		'opposition_notreported' => 'opposition_notreported',
		'fidelity_notreported' => 'fidelity_notreported',
		'evidencebased_notreported' => 'evidencebased_notreported',
		'support' => 'support',
		'opposition' => 'opposition',
		'evidence_based' => 'evidence_based',
		'fidelity' => 'fidelity',
		'implementation_limitations' => 'implementation_limitations',
		'implementationlimitations_notreported' => 'implementationlimitations_notreported',
		'lessons_learned' => 'lessons_learned',
		'lessons_learned_descr' => 'lessons_learned_descr',
		'lessonslearned_notreported' => 'lessonslearned_notreported',
		//intervention_verification  //used?
		//evaluation_type //is this used? Mel thinks this is multi, so no
		//evaluation_method //is this used? Mel thinks this is multi, so no
		//outcome_type //nope, in EA tab
		// outcome_type_other //nope, in EA tab
		//statistic_methods  //nope, in EA tab
		//'staff_volunteer_costs' => 'staff_volunteer_cost_value',  //using?
		//'space_infrastructure_costs' => 'space_infrastructure_cost_value',  //using?
		//'equipment_material_costs' => 'equipment_material_cost_value',  //using?
		'staff_volunteer_cost_text' => 'staff_volunteer_cost_text',
		'space_infrastructure_cost_text' => 'space_infrastructure_cost_text',
		'equipment_material_cost_text' => 'equipment_material_cost_text',
		'staff_volunteer_cost_value' => 'staff_volunteer_cost_value',
		'space_infrastructure_cost_value' => 'space_infrastructure_cost_value',
		'equipment_material_cost_value' => 'equipment_material_cost_value',
		'outcome_maintained_flag' => 'outcome_maintained_flag',
		'staffvolunteercosts_notreported' => 'staffvolunteercosts_notreported',
		'spacecosts_notreported' => 'spacecosts_notreported',
		'equipmentcosts_notreported' => 'equipmentcosts_notreported',
		'sustainability_flag' => 'sustainability_plan_flag',
		'outcomemaintained_notreported' => 'outcomemaintained_notreported',
		'sustainabilityplan_notreported' => 'sustainabilityplan_notreported',
		'explain_sustainability' => 'explain_sustainability',
		'explain_maintenance' => 'explain_maintenance',
		//outcome //using?
		//accessibility
		//general_applicability
		//applicability_to_HR_populations
		
		
		
		'otherevaluationmethods' => 'otherevaluationmethods',
		'evaluationmethods_notreported' => 'evaluationmethods_notreported',
		'evaluationtype_notreported' => 'evaluationtype_notreported',
		'confounders' => 'confounders',
		'confounders_textarea' => 'confounders_textarea',
		'confounders_notreported' => 'confounders_notreported',
		'analysis_limitations' => 'analysis_limitations',
		'analysislimitations_notreported' => 'analysislimitations_notreported',
		'stat_analysis_results_descr' => 'stat_analysis_results_descr',
		'statisticalanalysis_notreported' => 'statisticalanalysis_notreported',

		//representativeness_notreported //on pops tabs?
		'EndNoteID' => 'EndNoteID',
		'Phase' => 'Phase', //??
		'StudyGroupingID' => 'StudyGroupingID',
		
		'abstraction_complete' => 'abstraction_complete',
		'validation_complete' => 'validation_complete',
		//results_verification //no longer a thing?
		
		//other intervention indicators
		'other_intervention_indicators' => 'other_intervention_indicators',
		'other_intervention_indicators2' => 'other_intervention_indicators2',
		'other_intervention_indicators3' => 'other_intervention_indicators3',
		'other_intervention_indicators4' => 'other_intervention_indicators4',
		'other_intervention_indicators5' => 'other_intervention_indicators5',
		'other_intervention_indicators6' => 'other_intervention_indicators6',
		'other_intervention_indicators7' => 'other_intervention_indicators7',
		'other_intervention_indicators8' => 'other_intervention_indicators8',
		'other_intervention_indicators9' => 'other_intervention_indicators9',
		'other_intervention_indicators10' => 'other_intervention_indicators10',
		'other_intervention_outcomes_assessed' => 'other_intervention_outcomes_assessed',
		'other_intervention_outcomes_assessed2' => 'other_intervention_outcomes_assessed2',
		'other_intervention_outcomes_assessed3' => 'other_intervention_outcomes_assessed3',
		'other_intervention_outcomes_assessed4' => 'other_intervention_outcomes_assessed4',
		'other_intervention_outcomes_assessed5' => 'other_intervention_outcomes_assessed5',
		'other_intervention_outcomes_assessed6' => 'other_intervention_outcomes_assessed6',
		'other_intervention_outcomes_assessed7' => 'other_intervention_outcomes_assessed7',
		'other_intervention_outcomes_assessed8' => 'other_intervention_outcomes_assessed8',
		'other_intervention_outcomes_assessed9' => 'other_intervention_outcomes_assessed9',
		'other_intervention_outcomes_assessed10' => 'other_intervention_outcomes_assessed10',
	
	);
	
	
	if( !empty( $study_labels ) ){
		if( $to_db == false ) {
		
			//because in_array is looking at values and we need to get db values
			$flipped_array = array_flip( $db_to_div_array );
			$new_study_labels;
			//we have an incoming array whose labels need to be changed
			foreach( $study_labels as $label => $value ){
				if( in_array( $label, $flipped_array ) ) {
				//var_dump( $label );
					$new_label = $db_to_div_array[ $label ];
					$new_study_labels[ $new_label ] = $value;
				} else {
					$new_study_labels[ $label ] = $value; //same ol
				}
			
			}
			return $new_study_labels;
		} else {
			//array_search - Searches the array for a given value and returns the corresponding key if successful
			//$flipped_array = $db_to_div_array;
			$new_study_labels;
			//we have an incoming array whose labels need to be changed
			foreach( $study_labels as $label => $value ){
			
				$db_key = array_search( $label, $db_to_div_array );
				
				if( $db_key !== false ){
					$new_study_labels[ $db_key ] = $value;
				} else {
					$new_study_labels[ $label ] = 0; //same ol
				}
			
			}
			return $new_study_labels;
			
		}
		
	} else {
		//we're just returning the above lookup table
		return $db_to_div_array;
	}
	
	

}

/**
 * function to relate div ids and study id columns
 *
 * @param array
 * @return array
 */
function cc_transtria_match_div_ids_to_multiple_columns( $study_labels = null, $to_db = false ){

	//we can use array_flip if we need to!
	$all_pops_strings = array(
		'tp', 'ipe', 'ipu', 'ese0', 'ese1', 
		'ese2', 'ese3', 'ese4', 'ese5', 'ese6', 
		'ese7', 'ese8', 'ese9', 'ese', 'esu'
	);
	
	//TODO: determine if the commented-out ones are used...
	//db => field ids
	$db_to_div_array = array(
		'SearchToolType' => 'searchtooltype',
		'SearchToolName' => 'searchtoolname',
		'FundingSource' => 'fundingsource', 
		'UnitOfAnalysis' => 'unit_of_analysis',
		'US States' => 'state_setting', 
		'SettingType' => 'setting_type', 
		'Partner discipline' => 'partner_discipline', 
		'TheoryFramework' => 'theory_framework_type', 
		'Intervention Components' => 'intervention_component', 
		'Strategies' => 'strategies', 
		'PSEcomponents' => 'pse_components', 
		'Complexity' => 'complexity', 
		'InterventionLocation' => 'intervention_location', 
		'Indicator' => 'intervention_indicators', 
		'OutcomesAccessed' => 'intervention_outcomes_assessed', 
		'EvaluationType' => 'evaluation_type', 
		'EvaluationMethod' => 'evaluation_methods',
		'ValidityThreats' => 'validity_threats'
		
	);
	
	
	//array to hold pops columns
	$all_pops_columns = array();
	
	foreach( $all_pops_strings as $pop ){
	
		$tmp_array = array(
			$pop. '.GeographicScale' => $pop . '_geographic_scale',
			$pop . '.AbilityStatus' => $pop . '_ability_status',
			$pop . '.SubPopulations' => $pop . '_sub_populations',
			$pop . '.YouthPopulations' => $pop . '_youth_populations',
			$pop . '.ProfessionalPopulations' => $pop . '_professional_populations'
		);
		
		$all_pops_columns = array_merge ( $all_pops_columns, $tmp_array );
	
	}
	
	//array to hold special pops columns
	
	$special_pops_columns = array(
		//'ese.RepresentativeSubpopulations' => 'ese_representative_subpopulations',  //TODO: check w Transtria - is this a thing anymore??
		'ese.RepresentativeSubpopulations' => 'ese_representative_subpopulations',
		'ipe.RepresentativeSubpopulations' => 'ipe_representative_subpopulations',
		'ipe.HighRiskSubpopulations' => 'ipe_hr_subpopulations',
		'ese.HighRiskSubpopulations' => 'ese_hr_subpopulations',

		'ese.HighRiskSubpopulations' => 'ese_hr_subpopulations',
		'ese0.HighRiskSubpopulations' => 'ese0_hr_subpopulations',
		'ese1.HighRiskSubpopulations' => 'ese1_hr_subpopulations',
		'ese2.HighRiskSubpopulations' => 'ese2_hr_subpopulations',
		'ese3.HighRiskSubpopulations' => 'ese3_hr_subpopulations',
		'ese4.HighRiskSubpopulations' => 'ese4_hr_subpopulations',
		'ese5.HighRiskSubpopulations' => 'ese5_hr_subpopulations',
		'ese6.HighRiskSubpopulations' => 'ese6_hr_subpopulations',
		'ese7.HighRiskSubpopulations' => 'ese7_hr_subpopulations',
		'ese8.HighRiskSubpopulations' => 'ese8_hr_subpopulations',
		'ese9.HighRiskSubpopulations' => 'ese9_hr_subpopulations'
	);
	
	
	$all_ea_columns = array();
	
	for( $i = 1; $i <= 100; $i++ ){
		
		$which_ea = 'ea_' . $i;
		
		$tmp_array = array(
			$which_ea . ' Results Populations' => $which_ea . '_result_evaluation_population',
			$which_ea . ' Results SubPopulations' => $which_ea . '_result_subpopulations',
			$which_ea . ' OutcomesAccessed' => $which_ea . '_result_outcome_accessed',
			$which_ea . ' Measures' => $which_ea . '_result_measures',
			$which_ea . ' Indicator' => $which_ea . '_result_indicator'
			
		);
		
		$all_ea_columns = array_merge ( $all_ea_columns, $tmp_array );
	
	}
	
	//merge all arrays into master list
	$db_to_div_array = array_merge( $db_to_div_array, $all_pops_columns);
	$db_to_div_array = array_merge( $db_to_div_array, $special_pops_columns);
	$db_to_div_array = array_merge( $db_to_div_array, $all_ea_columns);
	
	
	
	
	$flipped_array = array_flip( $db_to_div_array );
	
	if( !empty( $study_labels ) ){
	
		if( $to_db == false ) {
			$new_study_labels = array();
			//we have an incoming array whose labels need to be changed
			foreach( $study_labels as $label => $value ){

				if( in_array( $label, $flipped_array ) ) {
					//var_dump( $label );
					$new_label = $db_to_div_array[ $label ];
					$new_study_labels[ $new_label ] = $value;
				} else {
					$new_study_labels[ $label ] = $value; //same ol
				}
			
			}
			return $new_study_labels;	
		} else {
			//array_search - Searches the array for a given value and returns the corresponding key if successful
			//$flipped_array = $db_to_div_array;
			$new_study_labels;
		//	return $study_labels;
			//we have an incoming array whose labels need to be changed
			foreach( $study_labels as $label => $value ){
			
				$db_key = array_search( $label, $db_to_div_array );
				
				if( $db_key !== false ){
					$new_study_labels[ $db_key ] = $value;
				} else {
					$new_study_labels[ $label ] = 0; //same ol
				}
			
			}
			return $new_study_labels;
		
		}
		
	} else {
		//we're just returning the above array IF !$to_db (if there are no study_data for this section, return nothing)
		if( $to_db ){
			return $db_to_div_array;
		} else {
			return 0;
		}
	}
	
}

/**
 * function to relate div ids and study id columns
 *
 * @param string, array, bool, bool. Which population, array of incoming study labels => values, whether we are going INTO the db (else form).
 * @return array
 */
function cc_transtria_match_div_ids_to_pops_columns_single( $which_pop, $study_labels = null, $to_db = false){

	//we can use array_flip if we need to!
	
	//TODO: determine if the commented-out ones are used...
	//db => field ids
	$db_to_div_array = array(
		'StudyID' => 'studyid',
		'PopulationType' => 'population_type',
		'reported' => $which_pop . '_reported',
		'PopulationSize' => $which_pop . '_population_size',
		'populationsize_notreported' => $which_pop . '_populationsize_notreported',
		'GeographicScaleCode' => $which_pop . '_geographic_scale',
		'geographicscale_notreported' => $which_pop . '_geographicscale_notreported',
		'EligibilityCriteriaCode' => $which_pop . '_eligibility_criteria',
		'eligibilitycriteria_notreported' => $which_pop . '_eligibilitycriteria_notreported',
		'isGeneralPopulation' => $which_pop . '_general_population',
		'Representativeness' => $which_pop . '_representativeness',
		'representativeness_notreported' => $which_pop . '_representativeness_notreported',
		'Oversampling' => $which_pop . '_oversampling',
		'oversampling_notreported' => $which_pop . '_oversampling_notreported',
		'ApplicabilityHRPopulations' => $which_pop . '_applicability_hr_pops',
		
		//TODO: is this even IN the db?
		'applicabilityhrpops_notreported' => $which_pop . '_applicabilityhrpops_notreported',
		
		'generalpopulation_notreported' => $which_pop . '_generalpopulation_notreported',
		'GenderCode' => $which_pop . '_gender',
		'PctGenderMale' => $which_pop . '_gender_pctmale',
		'PctGenderFemale' => $which_pop . '_gender_pctfemale',
		'gender_notreported' => $which_pop . '_gender_notreported',
		'MinimumAge' => $which_pop . '_min_age',
		'minimumage_notreported' => $which_pop . '_minimumage_notreported',
		'MaximumAge' => $which_pop . '_max_age',
		'maximumage_notreported' => $which_pop . '_maximumage_notreported',
		'ParticipationRate' => $which_pop . '_participation_rate',
		'rateofparticipation_notreported' => $which_pop . '_rateofparticipation_notreported',
		'ExposureFrequency' => $which_pop . '_exposure_frequency',
		'freqofexposure_notreported' => $which_pop . '_freqofexposure_notreported',
		'AbilityStatusCode' => $which_pop . '_ability_status', //hmm, pretty sure this is multi and not actually in this table
		//'PctAbilityStatus' => 'PctAbilityStatus', //TODO: is this even a thing?
		'PctCognitionDisability' => $which_pop . '_cognition_disability_pct',
		'PctGettingAlongDisability' => $which_pop . '_getting_along_disability_pct',
		'PctLifeActivitiesDisability' => $which_pop . '_life_activities_disability_pct',
		'PctMobilityDisability' => $which_pop . '_mobility_disability_pct',
		'PctSelfCareDisability' => $which_pop . '_self_care_disability_pct',
		'PctParticipationDisability' => $which_pop . '_participation_disability_pct',
		'abilitystatus_notreported' => $which_pop . '_abilitystatus_notreported',
		'subpopulations_notreported' => $which_pop . '_subpopulations_notreported',
		'youthpopulations_notreported' => $which_pop . '_youthpopulations_notreported',
		'professionalpopulations_notreported' => $which_pop . '_professionalpopulations_notreported',
		'other_populations' => $which_pop . '_other_populations',
		//other_population // ?
		'other_population_description' => $which_pop . '_other_population_description',
		'PctBlack' => $which_pop . '_african_american_pct',
		'PctWhite' => $which_pop . '_white_pct',
		'PctAsian' => $which_pop . '_asian_pct',
		'PctPacificIslander' => $which_pop . '_pacific_islander_pct',
		'PctNativeAmerican' => $which_pop . '_native_american_pct',
		'PctOtherRace' => $which_pop . '_other_race_pct',
		'racepercentages_notreported' => $which_pop . '_racepercentages_notreported',
		'PctHispanic' => $which_pop . '_hispanic_pct',
		'percenthispanic_notreported' => $which_pop . '_percenthispanic_notreported',
		'PctLowerIncome' => $which_pop . '_lower_income_pct',
		'percentlowerincome_notreported' => $which_pop . '_percentlowerincome_notreported',
		'PctNoEnglish' => $which_pop . '_non_english_speakers_pct',
		//population_verification //deprecated: used to be a checkbox at the end of the pops tabs
		'percentnonenglish_notreported' => $which_pop . '_percentnonenglish_notreported'

	);
	
	$flipped_array = array_flip( $db_to_div_array );
	
	if( !empty( $study_labels ) ){
	
		if( $to_db == false ) {
			$new_study_labels;
			//we have an incoming array whose labels need to be changed
			foreach( $study_labels as $label => $value ){

				if( in_array( $label, $flipped_array ) ) {
					//var_dump( $label );
					$new_label = $db_to_div_array[ $label ];
					$new_study_labels[ $new_label ] = $value;
				} else {
					$new_study_labels[ $label ] = $value; //same ol
				}
			
			}
			return $new_study_labels;	
		} else {
			//array_search - Searches the array for a given value and returns the corresponding key if successful
			//$flipped_array = $db_to_div_array;
			$new_study_labels;
		//	return $study_labels;
			//we have an incoming array whose labels need to be changed
			foreach( $study_labels as $label => $value ){
			
				$db_key = array_search( $label, $db_to_div_array );
				
				if( $db_key !== false ){
					$new_study_labels[ $db_key ] = $value;
				} else {
					$new_study_labels[ $label ] = 0; //same ol
				}
			
			}
			return $new_study_labels;
		
		}
		
	} else {
		//we're just returning the above array
		return $db_to_div_array;
	}
	
}

/**
 * function to relate div ids and study id columns for SINGLE-select dropdowns
 *
 * @param array
 * @return array
 */
function cc_transtria_match_div_ids_to_ea_columns_single( $which_ea, $study_labels = null, $to_db = false ){

	//we can use array_flip if we need to!
	
	//NOTE: $which ea should be in 'ea_#' format
	//db => field ids
	$db_to_div_array = array(
		//'StudyID' => 'studyid',
		'seq' => 'seq',
		'results' => $which_ea . '_results', //not used
		'result_numeric' => $which_ea . '_result_numeric',
		'duration' => $which_ea . '_duration',
		'duration_notreported' => $which_ea . '_duration_notreported',
		'result_type' => $which_ea . '_result_type',
		'results_variables' => $which_ea . '_results_variables',
		'statistical_model' => $which_ea . '_result_statistical_model',
		'result_reference_population' => $which_ea . '_result_reference_population',
		'result_reference_population_notreported' => $which_ea . '_result_reference_population_notreported',
		'result_subpopulation' => $which_ea . '_result_subpopulationYN',
		'result_subpopulation_text' => 'result_subpopulation_text', //?
		'indicator_direction' => $which_ea . '_result_indicator_direction',
		'outcome_direction' => $which_ea . '_result_outcome_direction',
		'effect_or_association_direction' => $which_ea . '_result_effect_association_direction',
		'result_strategy' => $which_ea . '_result_strategy',
		'outcome_type' => $which_ea . '_result_outcome_type',
		'outcome_type_other' => $which_ea . '_result_outcome_type_other',
		//outcome_accessed //is multi
		'outcome_accessed_other' => $which_ea . '_result_outcome_accessed_other',
		'measures_other' => $which_ea . '_result_measures_other',
		//indicator //multi
		'statistical_measure' => $which_ea . '_result_statistical_measure',
		//statistical_measure_p_value
		'statistical_measure_CI1' => $which_ea . '_statistical_measure_ci_value1',
		'statistical_measure_CI2' => $which_ea . '_statistical_measure_ci_value2',
		'significant' => $which_ea . '_result_significant',
		'approaching_significant' => $which_ea . '_result_approaching_significant',
		//these aren't used, but don't want them to trip up the rest of the form, so prepending w/ current ea#
		'effect_association_type1' => $which_ea . '_effect_association_type1',
		'effect_association_type2' => $which_ea . 'effect_association_type2',
		'effect_association_value' => $which_ea . 'effect_association_value',
		'final_effect_association_value' => $which_ea . 'final_effect_association_value',
		'indicator' => $which_ea . 'indicator',
		'outcome_accessed' => $which_ea . 'outcome_accessed',
		'result_population' => $which_ea . 'result_population',
		'result_subpopulation_text' => $which_ea . 'result_subpopulation_text',
		'statistical_measure_p_value' => $which_ea . 'statistical_measure_p_value',
		'other_indicators' => $which_ea . '_other_indicators',
		'other_outcomes' => $which_ea . '_other_outcomes',
		'leisure_time_measures' => $which_ea . '_leisure_time_measures',
		'active_transportation_measures' => $which_ea . '_active_transportation_measures',
		'fitness_scores_measures' => $which_ea . '_fitness_scores_measures',
		'consumption_vitamins_measures' => $which_ea . '_consumption_vitamins_measures',
		'consumption_minerals_measures' => $which_ea . '_consumption_minerals_measures',
		'consumption_fruits_measures' => $which_ea . '_consumption_fruits_measures',
		'consumption_vegetables_measures' => $which_ea . '_consumption_vegetables_measures'
		//'indicator_strategies_directions' => $which_ea . '_indicator_strategies_directions'

	);
	
	$flipped_array = array_flip( $db_to_div_array );
	
	if( $to_db == false ){ 
		//we're loading the form
		if( !empty( $study_labels ) ){
			$new_study_labels;
			//we have an incoming array whose labels need to be changed
			foreach( $study_labels as $label => $value ){
				
				if( in_array( $label, $flipped_array ) ) {
					$new_label = $db_to_div_array[ $label ];
					$new_study_labels[ $new_label ] = $value;
				} else {
					$new_study_labels[ $label ] = $value; //same ol
				}
			
			}
			return $new_study_labels;	
			
		} else {
			//we're just returning the above array
			return $db_to_div_array;
		}
		
	} else {
		//we're saving to db	
		if( !empty( $study_labels ) ){
			$new_study_labels;
			//we have an incoming array whose labels need to be changed
			foreach( $study_labels as $label => $value ){
				
				if( in_array( $label, $db_to_div_array ) ) {
					//return $label;
					$new_label = $flipped_array[ $label ];
					
					$new_study_labels[ $new_label ] = $value;
				} else {
					$new_study_labels[ $label ] = $value; //same ol
				}
			
			}
			return $new_study_labels;	
			
		} else {
			//we're just returning the above array
			return $db_to_div_array;
		}
	}
}

/**
 * Returns list of values (array of 2-character entries) of measures that require 10 extra text boxes upon selection
 *
 * @param string. Short or long array option
 * @return array
 *
 */
function cc_transtria_measures_w_extra_text( $long = true ){

	$measure_values = array(
		33 => array( 
			'short_name' => 'leisure_time', 
			'long_name' => 'Leisure Time Physical Activity'
		),			
		38 => array( 
			'short_name' => 'active_transportation', 
			'long_name' => 'Active Transportation'
		),
		55 => array( 
			'short_name' => 'fitness_scores', 
			'long_name' => 'Fitness Scores'
		),
		75 => array( 
			'short_name' => 'consumption_vitamins', 
			'long_name' => 'Consumption of Vitamins'
		),
		76 => array( 
			'short_name' => 'consumption_minerals', 
			'long_name' => 'Consumption of Minerals'
		),
		77 => array( 
			'short_name' => 'consumption_fruits', 
			'long_name' => 'Consumption of Fruits'
		),
		78 => array( 
			'short_name' => 'consumption_vegetables', 
			'long_name' => 'Consumption of Vegetables'
		)
	);
	
	if( $long ){
		return $measure_values;
	} else { //only return index
		return array_keys( $measure_values );
	}

}
 


/**
 * Which sub populations tabs are there?
 *
 * @return array.  Array of strings.
 */
function cc_transtria_get_basic_pops_types(){

	return array( 'tp', 'ipe', 'ipu', 'ese', 'esu' );

}
