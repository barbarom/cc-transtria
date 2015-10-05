<?php 
/**
 * CC Transtria Populations Tab Template Tags
 *
 * @package   CC Transtria Extras
 * @author    CARES staff
 * @license   GPL-2.0+
 * @copyright 2015 CommmunityCommons.org
 */



/**
 * Output logic for the form. includes the wrapper pieces.
 * Question building is handled separately
 *
 * @since   1.0.0
 * @return 	outputs html
 */
function cc_transtria_render_populations_tab( $field_data ){

	//$dd_singleton_options = $field_data['dd_singleton_options'];
	//var_dump ($dd_singleton_options);
	cc_transtria_render_populations_header( $field_data );
	
	
}


/**
 * Renders the header of the populations tab (before any sub tabs)
 *
 */
function cc_transtria_render_populations_header( $field_data ){

	//are we loading in a study?
	if( !empty( $_GET["study_id"] ) ) 
		$this_study_id = $_GET["study_id"];
	
	$dd_singleton_options = $field_data['dd_singleton_options'];
	$dd_multiple_options_pops = $field_data['dd_multiple_options_pops'];
	
	//which populations do we have?
	$which_pops = cc_transtria_get_all_pops_type_for_study( $this_study_id );
	//TODO: incorporate Meta (what ese tabs have been added in this study?)
	
	
	//var_dump( $dd_multiple_options_pops ); //ok!
	//var_dump( cc_transtria_get_multiple_dropdown_ids_populations( ) ); //default all pops
	

	?>
	<div id="population_tabs">
		<label class="general_pops_label">Sample size available?:</label>
		<span id="sample_size_available">
			<input type="radio" value="Y" class="studies_table" name="sample_size_available">Yes
			<input type="radio" value="N" class="studies_table" name="sample_size_available">No
		</span>
		<br>
		<label class="general_pops_label">Is Sample size an estimate?:</label>
		<span id="sample_estimate">
			<input type="radio" value="Y" class="studies_table" name="sample_estimate">Yes
			<input type="radio" value="N" class="studies_table" name="sample_estimate">No
		</span>
		<br />
		<label class="general_pops_label">Unit of Analysis</label>
		<select id="unit_of_analysis" class="general-multiselect multiselect" multiple="multiple">				
			<?php //$dd_singleton_options are indexed by the div id - "abstractor", for example
				foreach( $dd_singleton_options['unit_of_analysis'] as $k => $v ){
				echo '<option value="' . $k . '">' . $v->descr . '</option>';
			
			} ?>
		</select>
		<br />
		<div class="not-reported">
			<label class="general_pops_label">Unit of Analysis Not Reported</label>
			<input id="unitanalysis_notreported" class="studies_table" type="checkbox">
		</div>
		<br>

		<div id="sub_pops_tabs">
		<?php //onto the subtabs!
		//TODO: load in correct number of ese tabs for THIS study
		foreach( $which_pops as $pop ){ 
		?>
			
			<div id="<?php echo $pop; ?>-tab" class="subpops_tab">
				<label class="subpops_tab_label<?php if( $pop == 'tp' ) { echo ' active'; } ?>" for="<?php echo $pop; ?>-tab" data-whichpop="<?php echo $pop; ?>"><?php echo $pop; ?></label>
			</div>
			
			<?php
		} ?>
		
		<div id="add-ese-tab" class="alignright">
			<label class="ese_add_tab_label" for="add-ese-tab">Add ESE tab</label>
		</div>
		
		</div> <br />
		
		<?php
		
		//second foreach for content
		foreach( $which_pops as $pop ){ 
		
				cc_transtria_render_subpopulations_tab( $field_data, $pop );
			
			?>
			
			<?php
		
		} ?>
		
		
		<a class="button save_study alignright">SAVE STUDY</a>
				
	</div>
		
	<?php

}

function cc_transtria_render_subpopulations_tab( $field_data, $which_pop = 'tp'){

	//get dropdown options
	$dd_multiple_options_pops = $field_data['dd_multiple_options_pops'];
	//var_dump( $dd_multiple_options_pops );
	
	switch( $which_pop ){
		case 'tp':
			$subtitle = "Target Population";
			break;
		case 'ipe':
			$subtitle = "Intervention Population - EXPOSED";
			break;
		case 'ipu':
			$subtitle = "Intervention Population - UNEXPOSED";
			break;
		case 'esu':
			$subtitle = "Evaluation Sample - UNEXPOSED";
			break;
		case 'ese':
			$subtitle = "Evaluation Sample - EXPOSED";
			break;
		case 'ese0':
			$subtitle = "Evaluation Sample - EXPOSED: 0";
			break;
		case 'ese1':
			$subtitle = "Evaluation Sample - EXPOSED: 1";
			break;
		case 'ese2':
			$subtitle = "Evaluation Sample - EXPOSED: 2";
			break;
		case 'ese3':
			$subtitle = "Evaluation Sample - EXPOSED: 3";
			break;
		case 'ese4':
			$subtitle = "Evaluation Sample - EXPOSED: 4";
			break;
		case 'ese5':
			$subtitle = "Evaluation Sample - EXPOSED: 5";
			break;
		case 'ese6':
			$subtitle = "Evaluation Sample - EXPOSED: 6";
			break;
		case 'ese7':
			$subtitle = "Evaluation Sample - EXPOSED: 7";
			break;
		case 'ese8':
			$subtitle = "Evaluation Sample - EXPOSED: 8";
			break;
		case 'ese9':
			$subtitle = "Evaluation Sample - EXPOSED: 9";
			break;
		default: //because the rest of the tabs will be ese1, ese2...
	}
			
			
?>
	<div class="subpops_content <?php echo $which_pop; ?>_content <?php if( $which_pop != "tp" ){ echo 'noshow'; } ?>">
		<input class="population_type" value="<?php echo $which_pop; ?>" type="hidden" hidden>
		<table class="population_container">
			<tr>
				<td colspan="4" class="inner_table_header"><strong><?php echo $subtitle; ?></strong></td>
				
				
			</tr>
			<?php //if ($which_pop == 'ese' ){ 
			if ( ( substr( $which_pop, 0, 3 ) == 'ese' ) && ( $which_pop != 'ese' ) ){ ?>
				<tr><td colspan="4"><a id="" class="button remove_ese_tab alignright" data-tabnumber="" >Delete this ES-E tab</a></tr>
			<?php } else if ( $which_pop == 'ese' ) { //create the delete but hide it (for copying purposes) ?>
				<tr><td colspan="4" class="remove_ese_tab_td"><a id="" class="button remove_ese_tab alignright noshow" data-tabnumber="" >Delete this ES-E tab</a></tr>
			<?php } ?>
			<tr>
				<td class="minwidth200"><label>Reported?</label></td>
				<td colspan="3">
					<span id="<?php echo $which_pop; ?>_reported-holder">
						<input type="radio" value="Y" name="<?php echo $which_pop; ?>_reported" class="population_table" >Yes
						<input type="radio" value="N" name="<?php echo $which_pop; ?>_reported" class="population_table" >No
					</span></td>
			</tr>

			<tr>
				<td><label>Population Size:</label></td> 
				<td colspan="3"><input type="text" id="<?php echo $which_pop; ?>_population_size" class="population_table"></input></td>
			</tr>
			<tr class="not-reported">
				<td class="not-reported"><label>Population size not reported</label></td>
				<td colspan="3"><input id="<?php echo $which_pop; ?>_populationsize_notreported" class="population_table" type="checkbox"></td>
			</tr>


			<tr>
				<td><label>Geographic scale:</label></td>
				<td colspan="3">
					<span>
						<select id="<?php echo $which_pop; ?>_geographic_scale" class="general-multiselect multiselect ese_copy_multiselect" multiple="multiple">
							<?php 
								$field_name = $which_pop . '_geographic_scale';
								foreach( $dd_multiple_options_pops[ $field_name ] as $k => $v ){
								echo '<option value="' . $k . '">' . $v->descr . '</option>';
							
							} ?>
						</select>
					</span></td>
			</tr>
			<tr class="not-reported">
				<td class="not-reported"><label>Geographic scale not reported</label></td>
				<td colspan="3"><input id="<?php echo $which_pop; ?>_geographicscale_notreported" class="population_table" type="checkbox"></td>
			</tr>


			<tr>
				<td><label>Eligibility criteria:</label></td>
				<td colspan="3"><span id="<?php echo $which_pop; ?>_eligibility_criteria-holder">
					<input type="radio" value="Y" name="<?php echo $which_pop; ?>_eligibility_criteria" class="population_table" data-notreported_id="<?php echo $which_pop; ?>_eligibilitycriteria_notreported">Yes
					<input type="radio" value="N" name="<?php echo $which_pop; ?>_eligibility_criteria" class="population_table" data-notreported_id="<?php echo $which_pop; ?>_eligibilitycriteria_notreported">No
				</span></td>
			</tr>
			<tr class="not-reported">
				<td class="not-reported"><label>Eligibility criteria not reported</label></td>
				<td colspan="3"><input id="<?php echo $which_pop; ?>_eligibilitycriteria_notreported" class="population_table not_reported_clear" type="checkbox"></td>
			</tr>


			<tr>
				<td><label><?php echo strtoupper( $which_pop ); ?> is general population?:</label></td>
				<td colspan="3"><span id="<?php echo $which_pop; ?>_general_population-holder">
					<input type="radio" value="Y" name="<?php echo $which_pop; ?>_general_population" class="population_table" data-notreported_id="<?php echo $which_pop; ?>_generalpopulation_notreported">Yes
					<input type="radio" value="N" name="<?php echo $which_pop; ?>_general_population" class="population_table" data-notreported_id="<?php echo $which_pop; ?>_generalpopulation_notreported">No
				</span></td>
			</tr>
			<tr class="not-reported">
				<td class="not-reported"><label><?php echo strtoupper( $which_pop ); ?> is general population not reported</label></td>
				<td colspan="3"><input id="<?php echo $which_pop; ?>_generalpopulation_notreported" class="population_table not_reported_clear" type="checkbox"></td>
			</tr>

		<?php 
			$which_pop_start = substr( $which_pop, 0, 3 );
			if( $which_pop_start == 'ese' ){ ?>

			<tr>
				<td><label>Representativeness?</label></td>
				<td colspan="4"><span id="<?php echo $which_pop; ?>_representativeness-holder">
					<input type="radio" value="Y" name="<?php echo $which_pop; ?>_representativeness" class="population_table" data-notreported_id="<?php echo $which_pop; ?>_representativeness_notreported">Yes (no statistical differences from target or intervention-exposed populations reported)
					<br />
					<input type="radio" value="N" name="<?php echo $which_pop; ?>_representativeness" class="population_table" data-notreported_id="<?php echo $which_pop; ?>_representativeness_notreported">No (statistical differences from target or intervention-exposed populations reported)
				</span></td>
			</tr>
			<tr class="not-reported">
				<td class="not-reported"><label>Representativeness not reported</label></td>
				<td colspan="3"><input id="<?php echo $which_pop; ?>_representativeness_notreported" class="population_table not_reported_clear" type="checkbox"></td>
			</tr>


			<tr>
				<td><label>Oversampling?</label></td>
				<td colspan="4"><span id="<?php echo $which_pop; ?>_oversampling-holder">
					<input type="radio" value="Y" name="<?php echo $which_pop; ?>_oversampling" class="population_table" data-notreported_id="<?php echo $which_pop; ?>_oversampling_notreported">Yes
					<input type="radio" value="N" name="<?php echo $which_pop; ?>_oversampling" class="population_table" data-notreported_id="<?php echo $which_pop; ?>_oversampling_notreported">No
				</span></td>
			</tr>
			<tr class="not-reported">
				<td class="not-reported"><label>Oversampling not reported</label></td>
				<td colspan="3"><input id="<?php echo $which_pop; ?>_oversampling_notreported" name="<?php echo $which_pop; ?>_oversampling_notreported" class="population_table not_reported_clear" type="checkbox"></td>
			</tr>


			<tr class="<?php echo $which_pop; ?>_hr_subpopulations">
				<td><label>Identify the HR subpopulations</label></td>
				<td colspan="3"><span>
					<select id="<?php echo $which_pop; ?>_hr_subpopulations" class="general-multiselect multiselect ese_copy_multiselect" multiple="multiple">
						<?php 
							foreach( $dd_multiple_options_pops[ 'ese_hr_subpopulations' ] as $k => $v ){
							echo '<option value="' . $k . '">' . $v->descr . '</option>';
						
						} ?>
					</select>
				</span></td>
			</tr>

		<?php } else if( $which_pop == 'ipe' ) { ?>

			<tr>
				<td><label>Representativeness?</label></td>
				<td colspan="4"><span id="ipe_representativeness-holder">
					<input type="radio" value="Y" name="ipe_representativeness" class="population_table" data-notreported_id="ipe_representativeness_notreported">Yes (no statistical differences from target population reported)
					<br />
					<input type="radio" value="N" name="ipe_representativeness" class="population_table" data-notreported_id="ipe_representativeness_notreported">No (statistical differences from target population reported)
				</span></td>
			</tr>
			<tr class="not-reported">
				<td class="not-reported"><label>Representativeness not reported</label></td>
				<td colspan="3"><input id="ipe_representativeness_notreported" class="population_table not_reported_clear" type="checkbox"></td>
			</tr>
			
			<tr>
				<td><label>Applicability to high-risk populations?</label></td>
				<td colspan="4"><span id="ipe_applicability_hr_pops-holder">
					<input type="radio" value="Y" name="ipe_applicability_hr_pops" class="population_table" data-notreported_id="ipe_applicabilityhrpops_notreported">Yes (intervention specific to high-risk population)
					<br />
					<input type="radio" value="N" name="ipe_applicability_hr_pops" class="population_table" data-notreported_id="ipe_applicabilityhrpops_notreported">No (intervention applies to general population)
				</span></td>
			</tr>
			<tr class="not-reported">
				<td class="not-reported"><label>Applicability to HR populations not reported</label></td>
				<td colspan="3"><input id="ipe_applicabilityhrpops_notreported" class="population_table not_reported_clear" type="checkbox"></td>
			</tr>

			<tr class="ipe_hr_subpopulations">
				<td><label>Identify the HR subpopulations</label></td>
				<td colspan="3"><span>
					<select id="ipe_hr_subpopulations" class="general-multiselect multiselect" multiple="multiple">
						<?php 
							foreach( $dd_multiple_options_pops[ 'ipe_hr_subpopulations' ] as $k => $v ){
							echo '<option value="' . $k . '">' . $v->descr . '</option>';
						
						} ?>
					</select>
				</span></td>
			</tr>

		<?php } ?>

			<tr class="<?php echo $which_pop; ?>_not_general">
				<td><label>Gender:</label></td>
				<td><span>
					<select id="<?php echo $which_pop; ?>_gender" class="population_table">
						<option value="">---Select---</option>
						<?php 
							$field_name = $which_pop . '_gender';
							foreach( $dd_multiple_options_pops[ $field_name ] as $k => $v ){
							echo '<option value="' . $k . '">' . $v->descr . '</option>';
						
						} ?>
					</select>
				</span></td>
				
				<td><label>Pct Male:</label></td>
				<td><input id="<?php echo $which_pop; ?>_gender_pctmale" class="population_table"></input>%</td>
			</tr>
			<tr class="<?php echo $which_pop; ?>_not_general">
				<td></td>
				<td></td>
				<td><label>Pct Female:</label></td>
				<td><input id="<?php echo $which_pop; ?>_gender_pctfemale" class="population_table"></input>%</td>
			</tr>

			<tr class="not-reported <?php echo $which_pop; ?>_not_general">
				<td class="not-reported"><label>Gender not reported</label></td>
				<td colspan="3"><input id="<?php echo $which_pop; ?>_gender_notreported" class="population_table" type="checkbox"></td>
			</tr>
			<br />

			<tr class="<?php echo $which_pop; ?>_not_general">
				<td><label>Minimum age:</label></td>
				<td colspan="3"><input id="<?php echo $which_pop; ?>_min_age" class="population_table"></input></td>
			</tr>
			<tr class="not-reported <?php echo $which_pop; ?>_not_general">
				<td class="not-reported"><label>Minimum age not reported</label></td>
				<td colspan="3"><input id="<?php echo $which_pop; ?>_minimumage_notreported" class="population_table" type="checkbox"></td>
			</tr>

			<tr class="<?php echo $which_pop; ?>_not_general">
				<td><label>Maximum age:</label></td>
				<td colspan="3"><input id="<?php echo $which_pop; ?>_max_age" class="population_table"></input></td>
			</tr>
			<tr class="not-reported <?php echo $which_pop; ?>_not_general">
				<td class="not-reported"><label>Maximum age not reported</label></td>
				<td colspan="3"><input id="<?php echo $which_pop; ?>_maximumage_notreported" class="population_table" type="checkbox"></td>
			</tr>

		<?php if( $which_pop == 'ipe' ) { ?>
		
			<tr class="<?php echo $which_pop; ?>_not_general">
				<td><label>Rate of Participation:</label></td>
				<td colspan="3"><input id="<?php echo $which_pop; ?>_participation_rate" class="population_table"></input></td>
			</tr>
			<tr class="not-reported <?php echo $which_pop; ?>_not_general">
				<td class="not-reported"><label>Rate of Participation not reported</label></td>
				<td colspan="3"><input id="<?php echo $which_pop; ?>_rateofparticipation_notreported" class="population_table" type="checkbox"></td>
			</tr>


			<tr class="<?php echo $which_pop; ?>_not_general">
				<td><label>Frequency of Exposure:</label></td>
				<td colspan="3">
					<select id="<?php echo $which_pop; ?>_exposure_frequency">
						<option value="">---Select---</option>
						<?php 
							foreach( $dd_multiple_options_pops[ 'ipe_exposure_frequency' ] as $k => $v ){
							echo '<option value="' . $k . '">' . $v->descr . '</option>';
						
						} ?>
					</select>
				</td>
			</tr>
			<tr class="not-reported <?php echo $which_pop; ?>_not_general">
				<td class="not-reported"><label>Frequency of Exposure not reported</label></td>
				<td colspan="3"><input id="<?php echo $which_pop; ?>_freqofexposure_notreported" class="population_table" type="checkbox"></td>
			</tr>

		<?php } ?>

			<tr class="<?php echo $which_pop; ?>_not_general">
				<td><label>Ability status:</label></td>
				<td colspan="3"><span>
					<select id="<?php echo $which_pop; ?>_ability_status" multiple="multiple" class="multiselect ese_copy_multiselect">
						<?php 
							$field_name = $which_pop . '_ability_status';
							foreach( $dd_multiple_options_pops[ $field_name ] as $k => $v ){
							echo '<option value="' . $k . '">' . $v->descr . '</option>';
						
						} ?>
					</select>
				</span></td>
			</tr>
			
			<?php ///TODO: this!  Conditionally shows depending on which abilities selected ?>
			<tr class="<?php echo $which_pop; ?>-ability-percent <?php echo $which_pop; ?>_not_general" data-ability-value="1">
				<td><label>Cognition disability percent</label></td>
				<td colspan="3"><input id="<?php echo $which_pop; ?>_cognition_disability_pct" class="population_table"></input>%</td>
			</tr>

			<tr class="<?php echo $which_pop; ?>-ability-percent <?php echo $which_pop; ?>_not_general" data-ability-value="2">
				<td><label>Getting along disability percent</label></td>
				<td colspan="3"><input id="<?php echo $which_pop; ?>_getting_along_disability_pct" class="population_table"></input>%</td>
			</tr>

			<tr class="<?php echo $which_pop; ?>-ability-percent <?php echo $which_pop; ?>_not_general" data-ability-value="3">
				<td><label>Life activities disability percent</label></td>
				<td colspan="3"><input id="<?php echo $which_pop; ?>_life_activities_disability_pct" class="population_table"></input>%</td>
			</tr>

			<tr class="<?php echo $which_pop; ?>-ability-percent <?php echo $which_pop; ?>_not_general" data-ability-value="4">
				<td><label>Mobility disability percent</label></td>
				<td colspan="3"><input id="<?php echo $which_pop; ?>_mobility_disability_pct" class="population_table"></input>%</td>
			</tr>

			<tr class="<?php echo $which_pop; ?>-ability-percent <?php echo $which_pop; ?>_not_general" data-ability-value="5">
				<td><label>Self-care disability percent</label></td>
				<td colspan="3"><input id="<?php echo $which_pop; ?>_self_care_disability_pct" class="population_table"></input>%</td>
			</tr>

			<tr class="<?php echo $which_pop; ?>-ability-percent <?php echo $which_pop; ?>_not_general" data-ability-value="6">
				<td><label>Participation disability percent</label></td>
				<td colspan="3"><input id="<?php echo $which_pop; ?>_participation_disability_pct" class="population_table"></input>%</td>
			</tr>

			<tr class="not-reported <?php echo $which_pop; ?>_not_general">
				<td class="not-reported"><label>Ability status not reported</label></td>
				<td colspan="3"><input id="<?php echo $which_pop; ?>_abilitystatus_notreported" class="population_table" type="checkbox"></td>
			</tr>

			<tr class="<?php echo $which_pop; ?>_not_general">
				<td><label>Subpopulations:</label></td>
				<td colspan="3"><span>
					<select id="<?php echo $which_pop; ?>_sub_populations" class="general-multiselect multiselect ese_copy_multiselect" multiple="multiple">
						<?php 
							$field_name = $which_pop . '_sub_populations';
							foreach( $dd_multiple_options_pops[ $field_name ] as $k => $v ){
							echo '<option value="' . $k . '">' . $v->descr . '</option>';
						
						} ?>
					</select>
				</span></td>
			</tr>
			<tr class="not-reported <?php echo $which_pop; ?>_not_general">
				<td class="not-reported"><label>Subpopulations not reported</label></td>
				<td colspan="3"><input id="<?php echo $which_pop; ?>_subpopulations_notreported" class="population_table" type="checkbox"></td>
			</tr>

			<tr class="<?php echo $which_pop; ?>_not_general">
				<td><label>Youth populations:</label></td>
				<td colspan="3"><span>
					<select id="<?php echo $which_pop; ?>_youth_populations" multiple="multiple" class="general-multiselect multiselect ese_copy_multiselect">
						<?php 
							$field_name = $which_pop . '_youth_populations';
							foreach( $dd_multiple_options_pops[ $field_name ] as $k => $v ){
							echo '<option value="' . $k . '">' . $v->descr . '</option>';
						} ?>
					</select>
				</span></td>
			</tr>
			<tr class="not-reported <?php echo $which_pop; ?>_not_general">
				<td class="not-reported"><label>Youth populations not reported</label></td>
				<td colspan="3"><input id="<?php echo $which_pop; ?>_youthpopulations_notreported" class="population_table" type="checkbox"></td>
			</tr>

			<tr class="<?php echo $which_pop; ?>_not_general">
				<td><label>Professional populations:</label></td>
				<td colspan="3"><span>
					<select id="<?php echo $which_pop; ?>_professional_populations" multiple="multiple" class="general-multiselect multiselect ese_copy_multiselect">
						<?php 
							$field_name = $which_pop . '_professional_populations';
							foreach( $dd_multiple_options_pops[ $field_name ] as $k => $v ){
							echo '<option value="' . $k . '">' . $v->descr . '</option>';
						} ?>
					</select>
				</span></td>
			</tr>
			<tr class="not-reported <?php echo $which_pop; ?>_not_general">
				<td class="not-reported"><label>Professional populations not reported</label></td>
				<td colspan="3"><input id="<?php echo $which_pop; ?>_professionalpopulations_notreported" class="population_table" type="checkbox"></td>
			</tr>

			<tr class="<?php echo $which_pop; ?>_not_general">
				<td class=""><label>Other populations</label></td>
				<td colspan="3"><input id="<?php echo $which_pop; ?>_other_populations" class="population_table other_populations_textenable" type="checkbox"></td>
			</tr>

			<tr class="<?php echo $which_pop; ?>_not_general">
				<td class=""><label>Other population description</label></td>
				<td colspan="3"><textarea id="<?php echo $which_pop; ?>_other_population_description" class="population_table" disabled data-otherpopcheckbox_id="<?php echo $which_pop; ?>_other_populations"></textarea></td>
			</tr>


			<tr class="<?php echo $which_pop; ?>_not_general">
				<td colspan="4">
					<table style="border:5px solid #a6a6a6; margin:1px">
						<tr class="inner_table_header"><th colspan="6">Race Percentages</th></tr>
						<tr>
							<td>Black</td>
							<td>White</td>
							<td>Asian</td>
							<td>Pacific<br>Islander</td>
							<td>Native<br>American</td>
							<td>Other</td>						   
						</tr>
						<tr>
						  <td><input id="<?php echo $which_pop; ?>_african_american_pct" class="percentage population_table"></input>%</td>
						  <td><input id="<?php echo $which_pop; ?>_white_pct" class="percentage population_table"></input>%</td>
						  <td><input id="<?php echo $which_pop; ?>_asian_pct" class="percentage population_table"></input>%</td>						
						  <td><input id="<?php echo $which_pop; ?>_pacific_islander_pct" class="percentage population_table"></input>%</td>
						  <td><input id="<?php echo $which_pop; ?>_native_american_pct" class="percentage population_table"></input>%</td>
						  <td><input id="<?php echo $which_pop; ?>_other_race_pct" class="percentage population_table"></input>%</td>
						</tr>
						<tr class="not-reported <?php echo $which_pop; ?>_not_general">
							
							<td class="not-reported" colspan="6"><label>Race Percentages not reported</label>
							<input id="<?php echo $which_pop; ?>_racepercentages_notreported" class="population_table" type="checkbox"></td>
						</tr>


					</table>
				</td>
			</tr>

			<tr class="<?php echo $which_pop; ?>_not_general">
				<td><label>Percent Hispanic:</label></td>
				<td colspan="3"><input id="<?php echo $which_pop; ?>_hispanic_pct" class="population_table"></input>%</td>
			</tr>
			<tr class="not-reported <?php echo $which_pop; ?>_not_general">
				<td class="not-reported"><label>Percent Hispanic not reported</label></td>
				<td><input id="<?php echo $which_pop; ?>_percenthispanic_notreported" class="population_table" type="checkbox"></td>
			</tr>

			<tr class="<?php echo $which_pop; ?>_not_general">
				<td><label>Percent lower income:</label></td>
				<td colspan="3"><input id="<?php echo $which_pop; ?>_lower_income_pct" class="population_table"></input>%</td>
			</tr>
			<tr class="not-reported <?php echo $which_pop; ?>_not_general">
				<td class="not-reported"><label>Percent lower income not reported</label></td>
				<td colspan="3"><input id="<?php echo $which_pop; ?>_percentlowerincome_notreported" class="population_table" type="checkbox"></td>
			</tr>

			<tr class="<?php echo $which_pop; ?>_not_general">
				<td><label>Percent non-English speakers:</label></td>
				<td colspan="3"><input id="<?php echo $which_pop; ?>_non_english_speakers_pct" class="population_table"></input>%</td>
			</tr>
			<tr class="not-reported <?php echo $which_pop; ?>_not_general">
				<td class="not-reported"><label>Percent non-English speakers not reported</label></td>
				<td colspan="3"><input id="<?php echo $which_pop; ?>_percentnonenglish_notreported" class="population_table" type="checkbox"></td>
			</tr>
			
			<tr></tr>
			
			
		</table>
	
	</div>
	
<?php }