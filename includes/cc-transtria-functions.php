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
 * function to relate div ids and study id columns
 *
 * @param array
 * @return array
 */
function cc_transtria_match_div_ids_to_studies_columns( $study_labels = null ){

	//we can use array_flip if we need to!
	
	//TODO: determine if the commented-out ones are used...
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
		'validitythreat' => 'validity_threats',
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
		//representativeness
		'domestic_setting' => 'DomesticSetting',
		'international_setting' => 'InternationalSetting',
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
		'support' => 'support',
		'opposition' => 'opposition',
		'evidence_based' => 'evidence_based',
		'fidelity' => 'fidelity_notreported',
		'implementation_limitations' => 'implementation_limitations',
		'lessons_learned' => 'lessons_learned',
		'lessons_learned_descr' => 'lessons_learned_descr',
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
		
		
		
		'confounders' => 'confounders',
		'confounders_textarea' => 'confounders_textarea',
		'confounders_notreported' => 'confounders_notreported',
		'analysis_limitations' => 'analysis_limitations',
		'analysislimitations_notreporteded' => 'analysislimitations_notreported',
		'stat_analysis_results_descr' => 'stat_analysis_results_descr',
		'statisticalanalysis_notreported' => 'statisticalanalysis_notreported',

		'replication_notreported' => 'replication_notreported',
		'support_notreported' => 'support_notreported',
		'opposition_notreported' => 'opposition_notreported',
		'evidencebased_notreported' => 'evidencebased_notreported',
		'implementationlimitations_notreported' => 'implementationlimitations_notreported',
		'lessonslearned_notreported' => 'lessonslearned_notreported',
		'fidelity_notreported' => 'fidelity_notreported',
		'evaluationtype_notreported' => 'evaluationtype_notreported',
		//unitanalysis_notreported  //using?
		//representativeness_notreported //on pops tabs?
		'EndNoteID' => 'EndNoteID',
		'Phase' => 'Phase', //??
		'StudyGroupingID' => 'StudyGroupingID',
		
		'abstraction_complete' => 'abstraction_complete',
		'validation_complete' => 'validation_complete',
		//results_verification //no longer a thing?
		'otherevaluationmethods' => 'otherevaluationmethods',
		'evaluationmethods_notreported' => 'evaluationmethods_notreported'
	
	);
	
	if( empty( $study_labels ) ){
		$new_study_labels = [];
		//we have an incoming array whose labels need to be changed
		foreach( $study_labels as $label => $value ){
			
			if( in_array( $label, $db_to_div_array ) ) {
				$new_label = $db_to_div_array[ $label ];
				$new_study_labels[ $new_label ] = $value;
			} else {
				$new_study_labels[ $label ] = $value; //same ol
			}
		
		}
		
		
	} else {
		//we're just returning the above array
		return $db_to_div_array;
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
