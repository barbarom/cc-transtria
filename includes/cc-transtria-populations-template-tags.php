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

	$dd_singleton_options = $field_data['dd_singleton_options'];
	$dd_multiple_options_pops = $field_data['dd_multiple_options_pops'];
	
	$which_pops = cc_transtria_get_basic_pops_types();
	//TODO: incorporate Meta (what ese tabs have been added in this study?)
	
	
	//var_dump( $dd_multiple_options_pops ); //ok!
	//var_dump( cc_transtria_get_multiple_dropdown_ids_populations( ) ); //default all pops
	

	?>
	<div id="population_tabs">
		<label>Sample size available?:</label>
		<span id="sample_size_available">
			<input type="radio" value="Y" name="sample_size_available">	Yes
			<input type="radio" value="N" name="sample_size_available">	No
		</span>
		<br>
		<label>Is Sample size an estimate?:</label>
		<span id="sample_estimate">
			<input type="radio" value="Y" name="sample_estimate">Yes
			<input type="radio" value="N" name="sample_estimate">No
		</span>
		<br />
		<label>Unit of Analysis</label>
		<select id="unit_of_analysis" class="multiselect" multiple="multiple">				
			<?php //$dd_singleton_options are indexed by the div id - "abstractor", for example
				foreach( $dd_singleton_options['unit_of_analysis'] as $k => $v ){
				echo '<option value="' . $k . '">' . $v->descr . '</option>';
			
			} ?>
		</select>
		<br />
		<div class="not-reported">
			<label>Unit of Analysis Not Reported</label>
			<input id="unitanalysis_notreported" type="checkbox">
		</div>
		<br>

		<div id="sub_pops_tabs">
		<?php //onto the subtabs!
		//TODO: load in correct number of ese tabs for THIS study
		foreach( $which_pops as $pop ){ 
			
			//set up tab name based on which population
			switch( $which_pop ){
				case 'tp':
					$subtitle = "Target Pop";
					break;
				case 'ipe':
					$subtitle = "Intervention Pop - EXPOSED";
					break;
				case 'ipu':
					$subtitle = "Intervention Pop - UNEXPOSED";
					break;
				case 'esu':
					$subtitle = "Evaluation Sample - UNEXPOSED";
					break;
				case 'ese':
				default: //because the rest of the tabs will be ese1, ese2...
					$subtitle = "Evaluation Sample - EXPOSED";
					break;
			}
			?>
			
			<div id="<?php echo $pop; ?>-tab" class="subpops_tab">
				<label class="subpops_tab_label<?php if( $pop == 'tp' ) { echo ' active'; } ?>" for="<?php echo $pop; ?>-tab" data-whichpop="<?php echo $pop; ?>"><?php echo $pop; ?></label>
			</div>
			
			<?php
		} ?>
		
		<div id="add-ese-tab" class="alignright">
			<label class="ese_add_tab_label" for="ass-ese-tab">Add ESE tab</label>
		</div>
		
		</div> <br />
		
		<?php
		
		//second foreach for content
		foreach( $which_pops as $pop ){ 
		
				cc_transtria_render_subpopulations_tab( $field_data, $pop );
			
			?>
			
			<?php
		
		} ?>
		
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
		default: //because the rest of the tabs will be ese1, ese2...
			$subtitle = "Evaluation Sample - EXPOSED";
			break;
	}
			
			
?>
	<div class="subpops_content <?php echo $which_pop; ?>_content <?php if( $which_pop != "tp" ){ echo 'noshow'; } ?>">
		<table>
			<tr>
				<td colspan="4" class="subtitle"><strong><?php echo $subtitle; ?></strong></td>
				<?php if ($which_pop == 'ese' ){ ?>
					<td><button id="" class="remove_tab_button alignright hidden" data-tabnumber="" onclick="remove_extra_ese_tab()">Clear this ES-E tab data</button>
				<?php } ?>
			</tr>

			<tr>
				<td><label>Reported?</label></td>
				<td>
					<span id="<?php echo $which_pop; ?>_reported">
						<input type="radio" value="Y" name="<?php echo $which_pop; ?>_reported">Yes
						<input type="radio" value="N" name="<?php echo $which_pop; ?>_reported">No
					</span></td>
			</tr>

			<tr>
				<td><label>Population Size:</label></td> 
				<td><input type="text" id="<?php echo $which_pop; ?>_population_size"></input></td>
			</tr>
			<tr class="not-reported">
				<td class="not-reported"><label>Population size not reported</label></td>
				<td><input id="<?php echo $which_pop; ?>_populationsize_notreported" type="checkbox"></td>
			</tr>


			<tr>
				<td><label>Geographic scale:</label></td>
				<td>
					<span id="<?php echo $which_pop; ?>_geographic_scale">
						<select class="multiselect" multiple="multiple">
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
				<td><input id="<?php echo $which_pop; ?>_geographicscale_notreported" type="checkbox"></td>
			</tr>


			<tr>
				<td><label>Eligibility criteria:</label></td>
				<td><span id="<?php echo $which_pop; ?>_eligibility_criteria">
					<input type="radio" value="Y" name="<?php echo $which_pop; ?>_eligibility_criteria">Yes
					<input type="radio" value="N" name="<?php echo $which_pop; ?>_eligibility_criteria">No
				</span></td>
			</tr>
			<tr class="not-reported">
				<td class="not-reported"><label>Eligibility criteria not reported</label></td>
				<td><input id="<?php echo $which_pop; ?>_eligibilitycriteria_notreported" type="checkbox"></td>
			</tr>


			<tr>
				<td><label><?php echo strtoupper( $which_pop ); ?> is general population?:</label></td>
				<td><span id="<?php echo $which_pop; ?>_general_population">
					<input type="radio" value="Y" name="<?php echo $which_pop; ?>_general_population">Yes
					<input type="radio" value="N" name="<?php echo $which_pop; ?>_general_population">No
				</span></td>
			</tr>
			<tr class="not-reported">
				<td class="not-reported"><label><?php echo strtoupper( $which_pop ); ?> is general population not reported</label></td>
				<td><input id="<?php echo $which_pop; ?>_generalpopulation_notreported" type="checkbox"></td>
			</tr>

		<?php if( $which_pop == 'ese' ){ ?>

			<tr>
				<td><label>Representativeness?</label></td>
				<td colspan="4"><span id="ese_representativeness">
					<input type="radio" value="Y" name="ese_representativeness">Yes (no statistical differences from target or intervention-exposed populations reported)
					<input type="radio" value="N" name="ese_representativeness">No (statistical differences from target or intervention-exposed populations reported)
				</span></td>
			</tr>
			<tr class="not-reported">
				<td class="not-reported"><label>Representativeness not reported</label></td>
				<td><input id="ese_representativeness_notreported" type="checkbox"></td>
			</tr>


			<tr>
				<td><label>Oversampling?</label></td>
				<td colspan="4"><span id="ese_oversampling">
					<input type="radio" value="Y" name="ese_oversampling">Yes
					<input type="radio" value="N" name="ese_oversampling">No
				</span></td>
			</tr>
			<tr class="not-reported">
				<td class="not-reported"><label>Oversampling not reported</label></td>
				<td><input id="ese_oversampling_notreported" type="checkbox"></td>
			</tr>


			<tr class="<?php echo $which_pop; ?>_hr_subpopulations">
				<td><label>Identify the HR subpopulations</label></td>
				<td><span id="ese_hr_subpopulations">
					<select class="multiselect" multiple="multiple">
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
				<td colspan="4"><span id="ipe_representativeness">
					<input type="radio" value="Y" name="ipe_representativeness">Yes (no statistical differences from target population reported)
					<input type="radio" value="N" name="ipe_representativeness">No (statistical differences from target population reported)
				</span></td>
			</tr>
			<tr class="not-reported">
				<td class="not-reported"><label>Representativeness not reported</label></td>
				<td><input id="ipe_representativeness_notreported" type="checkbox"></td>
			</tr>
			
			<tr>
				<td><label>Applicability to high-risk populations?</label></td>
				<td colspan="4"><span id="ipe_applicability_hr_pops">
					<input type="radio" value="Y" name="ipe_applicability_hr_pops">Yes (intervention specific to high-risk population)
					<input type="radio" value="N" name="ipe_applicability_hr_pops">No (intervention applies to general population)
				</span></td>
			</tr>
			<tr class="not-reported">
				<td class="not-reported"><label>Applicability to HR populations not reported</label></td>
				<td><input id="ipe_applicabilityhrpops_notreported" type="checkbox"></td>
			</tr>

			<tr class="ipe_hr_subpopulations">
				<td><label>Identify the HR subpopulations</label></td>
				<td><span id="ipe_hr_subpopulations">
					<select class="multiselect" multiple="multiple">
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
				<td><span id="<?php echo $which_pop; ?>_gender">
					<select>
						<option value="">---Select---</option>
						<?php 
							$field_name = $which_pop . '_gender';
							foreach( $dd_multiple_options_pops[ $field_name ] as $k => $v ){
							echo '<option value="' . $k . '">' . $v->descr . '</option>';
						
						} ?>
					</select>
				</span></td>
				
				<td><label>Pct Male:</label></td>
				<td><input id="<?php echo $which_pop; ?>_gender_pctmale"></input>%</td>
			</tr>
			<tr class="<?php echo $which_pop; ?>_not_general">
				<td></td>
				<td></td>
				<td><label>Pct Female:</label></td>
				<td><input id="<?php echo $which_pop; ?>_gender_pctfemale"></input>%</td>
			</tr>

			<tr class="not-reported <?php echo $which_pop; ?>_not_general">
				<td class="not-reported"><label>Gender not reported</label></td>
				<td><input id="<?php echo $which_pop; ?>_gender_notreported" type="checkbox"></td>
			</tr>
			<br />

			<tr class="<?php echo $which_pop; ?>_not_general">
				<td><label>Minimum age:</label></td>
				<td><input id="<?php echo $which_pop; ?>_min_age"></input></td>
			</tr>
			<tr class="not-reported <?php echo $which_pop; ?>_not_general">
				<td class="not-reported"><label>Minimum age not reported</label></td>
				<td><input id="<?php echo $which_pop; ?>_minimumage_notreported" type="checkbox"></td>
			</tr>

			<tr class="<?php echo $which_pop; ?>_not_general">
				<td><label>Maximum age:</label></td>
				<td><input id="<?php echo $which_pop; ?>_max_age"></input></td>
			</tr>
			<tr class="not-reported <?php echo $which_pop; ?>_not_general">
				<td class="not-reported"><label>Maximum age not reported</label></td>
				<td><input id="<?php echo $which_pop; ?>_maximumage_notreported" type="checkbox"></td>
			</tr>

		<?php if( $which_pop == 'ipe' ) { ?>
		
			<tr class="<?php echo $which_pop; ?>_not_general">
				<td><label>Rate of Participation:</label></td>
				<td><input id="<?php echo $which_pop; ?>_participation_rate"></input></td>
			</tr>
			<tr class="not-reported <?php echo $which_pop; ?>_not_general">
				<td class="not-reported"><label>Rate of Participation not reported</label></td>
				<td><input id="<?php echo $which_pop; ?>_rateofparticipation_notreported" type="checkbox"></td>
			</tr>


			<tr class="<?php echo $which_pop; ?>_not_general">
				<td><label>Frequency of Exposure:</label></td>
				<td><input id="<?php echo $which_pop; ?>_exposure_frequency">
					<select>
						<option value="">---Select---</option>
						<?php 
							foreach( $dd_multiple_options_pops[ 'ipe_exposure_frequency' ] as $k => $v ){
							echo '<option value="' . $k . '">' . $v->descr . '</option>';
						
						} ?>
					</select>
				</input></td>
			</tr>
			<tr class="not-reported <?php echo $which_pop; ?>_not_general">
				<td class="not-reported"><label>Frequency of Exposure not reported</label></td>
				<td><input id="<?php echo $which_pop; ?>_freqofexposure_notreported" type="checkbox"></td>
			</tr>

		<?php } ?>

			<tr class="<?php echo $which_pop; ?>_not_general">
				<td><label>Ability status:</label></td>
				<td><span id="<?php echo $which_pop; ?>_ability_status">
					<select multiple="multiple" class="multiselect">
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
				<td><input id="<?php echo $which_pop; ?>_cognition_disability_pct"></input>%</td>
			</tr>

			<tr class="<?php echo $which_pop; ?>-ability-percent <?php echo $which_pop; ?>_not_general" data-ability-value="2">
				<td><label>Getting along disability percent</label></td>
				<td><input id="<?php echo $which_pop; ?>_getting_along_disability_pct"></input>%</td>
			</tr>

			<tr class="<?php echo $which_pop; ?>-ability-percent <?php echo $which_pop; ?>_not_general" data-ability-value="3">
				<td><label>Life activities disability percent</label></td>
				<td><input id="<?php echo $which_pop; ?>_life_activities_disability_pct"></input>%</td>
			</tr>

			<tr class="<?php echo $which_pop; ?>-ability-percent <?php echo $which_pop; ?>_not_general" data-ability-value="4">
				<td><label>Mobility disability percent</label></td>
				<td><input id="<?php echo $which_pop; ?>_mobility_disability_pct"></input>%</td>
			</tr>

			<tr class="<?php echo $which_pop; ?>-ability-percent <?php echo $which_pop; ?>_not_general" data-ability-value="5">
				<td><label>Self-care disability percent</label></td>
				<td><input id="<?php echo $which_pop; ?>_self_care_disability_pct"></input>%</td>
			</tr>

			<tr class="<?php echo $which_pop; ?>-ability-percent <?php echo $which_pop; ?>_not_general" data-ability-value="6">
				<td><label>Participation disability percent</label></td>
				<td><input id="<?php echo $which_pop; ?>_participation_disability_pct"></input>%</td>
			</tr>

			<tr class="not-reported <?php echo $which_pop; ?>_not_general">
				<td class="not-reported"><label>Ability status not reported</label></td>
				<td><input id="<?php echo $which_pop; ?>_abilitystatus_notreported" type="checkbox"></td>
			</tr>

			<tr class="<?php echo $which_pop; ?>_not_general">
				<td><label>Subpopulations:</label></td>
				<td><span id="<?php echo $which_pop; ?>_sub_populations">
					<select class="multiselect" multiple="multiple">
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
				<td><input id="<?php echo $which_pop; ?>_subpopulations_notreported" type="checkbox"></td>
			</tr>

			<tr class="<?php echo $which_pop; ?>_not_general">
				<td><label>Youth populations:</label></td>
				<td><span id="<?php echo $which_pop; ?>_youth_populations">
					<select multiple="multiple" class="multiselect">
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
				<td><input id="<?php echo $which_pop; ?>_youthpopulations_notreported" type="checkbox"></td>
			</tr>

			<tr class="<?php echo $which_pop; ?>_not_general">
				<td><label>Professional populations:</label></td>
				<td><span id="<?php echo $which_pop; ?>_professional_populations">
					<select multiple="multiple" class="multiselect">
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
				<td><input id="<?php echo $which_pop; ?>_professionalpopulations_notreported" type="checkbox"></td>
			</tr>

			<tr class="<?php echo $which_pop; ?>_not_general">
				<td class=""><label>Other populations</label></td>
				<td><input id="<?php echo $which_pop; ?>_other_populations" type="checkbox"></td>
			</tr>

			<tr class="<?php echo $which_pop; ?>_not_general">
				<td class=""><label>Other population description</label></td>
				<td colspan="2"><textarea id="<?php echo $which_pop; ?>_other_population_description" style="width:98%"></textarea></td>
			</tr>


			<tr class="<?php echo $which_pop; ?>_not_general">
				<td colspan="4">
					<table style="border:1px solid">
						<tr><th colspan="3">Race Percentages</th></tr>
						<tr><td>Black</td>
						   <td>White</td>
						   <td>Asian</td>
						</tr>
						<tr>
						  <td><input id="<?php echo $which_pop; ?>_african_american_pct"></input>%</td>
						  <td><input id="<?php echo $which_pop; ?>_white_pct"></input>%</td>
						  <td><input id="<?php echo $which_pop; ?>_asian_pct"></input>%</td>
						</tr>
						<tr>
						   <td>Pacific<br>Islander</td>
						   <td>Native<br>American</td>
						   <td>Other</td>
						</tr>
						<tr>
						
						  <td><input id="<?php echo $which_pop; ?>_pacific_islander_pct"></input>%</td>
						  <td><input id="<?php echo $which_pop; ?>_native_american_pct"></input>%</td>
						  <td><input id="<?php echo $which_pop; ?>_other_race_pct"></input>%</td>
						</tr>
						<tr class="not-reported <?php echo $which_pop; ?>_not_general">
							<td></td>
							<td class="not-reported" colspan="3"><label>Race Percentages not reported</label></td>
							<td><input id="<?php echo $which_pop; ?>_racepercentages_notreported" type="checkbox"></td>
						</tr>


					</table>
				</td>
			</tr>

			<tr class="<?php echo $which_pop; ?>_not_general">
				<td><label>Percent Hispanic:</label></td>
				<td><input id="<?php echo $which_pop; ?>_hispanic_pct"></input>%</td>
			</tr>
			<tr class="not-reported <?php echo $which_pop; ?>_not_general">
				<td class="not-reported"><label>Percent Hispanic not reported</label></td>
				<td><input id="<?php echo $which_pop; ?>_percenthispanic_notreported" type="checkbox"></td>
			</tr>

			<tr class="<?php echo $which_pop; ?>_not_general">
				<td><label>Percent lower income:</label></td>
				<td><input id="<?php echo $which_pop; ?>_lower_income_pct"></input>%</td>
			</tr>
			<tr class="not-reported <?php echo $which_pop; ?>_not_general">
				<td class="not-reported"><label>Percent lower income not reported</label></td>
				<td><input id="<?php echo $which_pop; ?>_percentlowerincome_notreported" type="checkbox"></td>
			</tr>

			<tr class="<?php echo $which_pop; ?>_not_general">
				<td><label>Percent non-English speakers:</label></td>
				<td><input id="<?php echo $which_pop; ?>_non_english_speakers_pct"></input>%</td>
			</tr>
			<tr class="not-reported <?php echo $which_pop; ?>_not_general">
				<td class="not-reported"><label>Percent non-English speakers not reported</label></td>
				<td><input id="<?php echo $which_pop; ?>_percentnonenglish_notreported" type="checkbox"></td>
			</tr>
		</table>
	
	</div>
	
<?php }