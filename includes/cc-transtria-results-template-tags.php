<?php 
/**
 * CC Transtria Results Tab Template Tags
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
function cc_transtria_render_results_tab( $field_data ){

	$results_singletons = $field_data['dd_singleton_options']; //just making it easier for dev, although a waste of memory.  TODO: don't waste memory
	$num_ea_tabs = $field_data['num_ea_tabs'];
	
	//options for dropdowns
	$dd_multiple_options_ea = $field_data['dd_multiple_options_ea'];
	//data for ea tabs
	//$ea_tab_data = $field_data['ea_tab_data'];
	//var_dump( $num_ea_tabs );
	
?>

	<table id="results_table" class="nobottomborder">
		<tr>
			<td colspan="4" class="inner_table_header"><strong>Results</strong></td>
		</tr>

		<tr>
			<td><label>Evaluation Type:</label></td> 
			<td colspan="3"><span>
				<select id="evaluation_type" multiple="multiple" class="multiselect general-multiselect">
				<?php 
					foreach( $results_singletons['evaluation_type'] as $k => $v ){
						echo '<option value="' . $k . '"';
						echo '>' . $v->descr . '</option>';
					
					} ?>
				</select>
			</td>
		</tr>
		
		<tr class="not-reported">
			<td class="not-reported"><label>Evaluation Type not reported</label></td>
			<td colspan="3"><input id="evaluationtype_notreported" class="studies_table" type="checkbox"></td>
		</tr>

		<tr>
			<td><label>Evaluation Methods:</label></td>
			<td><span>
				<select id="evaluation_methods" multiple="multiple" class="multiselect general-multiselect">
				<?php 
					foreach( $results_singletons['evaluation_methods'] as $k => $v ){
						echo '<option value="' . $k . '"';
						echo '>' . $v->descr . '</option>';
					
					} ?>
				</select>
			</span></td>
			<td><label>Other evaluation methods:</label></td>
			<td><input type="text" id="otherevaluationmethods" class="studies_table"></input></td>
		</tr>
		<tr class="not-reported">
			<td class="not-reported"><label>Evaluation Methods not reported</label></td>
			<td colspan="3"><input id="evaluationmethods_notreported" class="studies_table" type="checkbox"></td>
		</tr>

		<tr>
			<td><label>Statistical Analysis and Results Description</label></td>
			<td colspan="3"><textarea id="stat_analysis_results_descr" class="studies_table" style="width:98%"></textarea></td>
		</tr>

		<tr class="not-reported">
			<td class="not-reported"><label>Statistical Analysis/Results Desc. not reported</label></td>
			<td colspan="3"><input id="statisticalanalysis_notreported" class="studies_table" type="checkbox"></td>
		</tr>


		<tr>
			<td><label>Confounders/Mediators/Moderators:</label></td>
			<td colspan="3"><span id="confounders-holder">
				<input type="radio" value="Y" name="confounders" class="studies_table" data-notreported_id="confounders_notreported">Yes
				<input type="radio" value="N" name="confounders" class="studies_table" data-notreported_id="confounders_notreported">No
			</span></td>
		</tr>

		<tr class="not-reported">
			<td class="not-reported"><label>Confounders/Mediators/Moderators not reported</label></td>
			<td colspan="3"><input id="confounders_notreported" class="studies_table not_reported_clear" type="checkbox"></td>
		</tr>

		<tr id="confounders_type">
			<td><label>Type</label></td>
			<td colspan="3"><textarea id="confounders_textarea" class="studies_table" style="width:98%;"></textarea></td>
		</tr>

		<tr>
		<td><label>Analysis Limitations:</label></td>
		<td colspan="3"><textarea id="analysis_limitations" class="studies_table" style="width:98%;"></textarea></td>
			</tr> 

		<tr class="not-reported">
			<td class="not-reported"><label>Analysis Limitations not reported</label></td>
			<td colspan="3"><input id="analysislimitations_notreported" class="studies_table" type="checkbox"></td>
		</tr>

		<tr class="notopborder nobottomborder placeholder-tr">&nbsp;</tr>
		<tr>
			<td colspan="4" align="right" class="notopborder">
				<span><a id="add_effect_association_row" class="button add_ea_button alignright">Add Effect/Association</a></span>
				<span class="spinny alignright"></span>
			</td>
		</tr>
		<tr>
			<td colspan="4" class="notopborder">
				<div id="effect_association_tabs">
					<ul>
					<?php 				
					//if we have ea tabs in db already, render lis
						for( $i=1; $i <= $num_ea_tabs; $i++ ){ ?>
							<li id="ea-tab-<?php echo $i; ?>" class="ea_tab">
								<label class="ea_tab_label <?php if( $i == 1 ){ echo 'active'; }?>" data-whichea="<?php echo $i; ?>" for="ea-tab-<?php echo $i; ?>">EA TAB <?php echo $i; ?></label>
							</li>
						<?php } ?>
					
					</ul>
				  
					<?php 
					//should we render one in the background as the copy-from tab? (it won't ever be shown, just be there for js copying-ness)?  Is this the best way?
					cc_transtria_render_ea_tabs( 'template', $dd_multiple_options_ea ); //dummy tab!
					
					//if we have ea tabs in db already, render them now
					for( $i=1; $i <= $num_ea_tabs; $i++ ){ 
						cc_transtria_render_ea_tabs( $i, $dd_multiple_options_ea );
						
					} ?>
				  
				</div>
			</td>
		</tr>

		<tr class="notopborder nobottomborder placeholder-tr">&nbsp;</tr>
		
		<tr>
			<td colspan="4" class="inner_table_header"><strong>Cost</strong></td>
		</tr>

		<tr>
			<td><label>Staff and volunteer costs:</label></td>
			<td><input id="staff_volunteer_cost_text" class="studies_table"></input></td>
			<td><label>Staff and volunteer costs value:</label></td>
			<td><input id="staff_volunteer_cost_value" class="studies_table"></input></td>
		</tr>

		<tr class="not-reported">
			<td class="not-reported"><label>Staff and Volunteer Costs not reported</label></td>
			<td colspan="3"><input id="staffvolunteercosts_notreported" class="studies_table" type="checkbox"></td>
		</tr>


		<tr>
			<td><label>Space and Infrastructure costs:</label></td>
			<td><input id="space_infrastructure_cost_text" class="studies_table"></input></td>
			<td><label>Space and Infrastructure costs value:</label></td>
			<td><input id="space_infrastructure_cost_value" class="studies_table"></input></td>
		</tr>

		<tr class="not-reported">
			<td class="not-reported"><label>Space and Infrastructure Costs not reported</label></td>
			<td colspan="3"><input id="spacecosts_notreported" class="studies_table" type="checkbox"></td>
		</tr>


		<tr>
			<td><label>Equipment and material costs:</label></td>
			<td><input id="equipment_material_cost_text" class="studies_table"></input></td>
			<td><label>Equipment and material costs value:</label></td>
			<td><input id="equipment_material_cost_value" class="studies_table"></input></td>
		</tr>

		<tr class="not-reported">
			<td class="not-reported"><label>Equipment and material costs not reported</label></td>
			<td colspan="3"><input id="equipmentcosts_notreported" class="studies_table" type="checkbox"></td>
		</tr>

		<tr class="notopborder nobottomborder placeholder-tr">&nbsp;</tr>
		
		<tr>
		   <td colspan="4" class="inner_table_header"><strong>Maintenance/Sustainability</strong></td>
		</tr>

		<tr>
			<td><label>Was the outcome maintained?:</label></td>
			<td><span id="outcome_maintained_flag">
				<input type="radio" value="Y" name="outcome_maintained_flag" class="studies_table" data-notreported_id="outcomemaintained_notreported">Yes
				<input type="radio" value="N" name="outcome_maintained_flag" class="studies_table" data-notreported_id="outcomemaintained_notreported">No
			</span></td>
			<td><label>If yes, explain:</label></td>
			<td><textarea id="explain_maintenance" class="studies_table"></textarea></td>
		</tr>

		<tr class="not-reported">
			<td class="not-reported"><label>Outcome Maintained not reported</label></td>
			<td colspan="3"><input id="outcomemaintained_notreported" class="studies_table not_reported_clear" type="checkbox"></td>
		</tr>

		<tr>
			<td><label>Was there a plan for sustainability?:</label></td>
			<td><span id="sustainability_plan_flag-holder">
				<input type="radio" value="Y" name="sustainability_plan_flag" class="studies_table" data-notreported_id="sustainabilityplan_notreported">Yes
				<input type="radio" value="N" name="sustainability_plan_flag" class="studies_table" data-notreported_id="sustainabilityplan_notreported">No
			</span></td>
			<td><label>If yes, explain:</label></td>
			<td><textarea id="explain_sustainability" class="studies_table"></textarea></td>
		</tr>

		<tr class="not-reported">
			<td class="not-reported"><label>Sustainability plan not reported</label></td>
			<td colspan="3"><input id="sustainabilityplan_notreported" class="studies_table not_reported_clear" type="checkbox"></td>
		</tr>

		<tr>
			<td colspan="3"></td>
			<td align="right">Abstraction Complete?:
				<input id="abstraction_complete" class="studies_table" type="checkbox"></input>
			</td>
		</tr>

		<tr>
			<td colspan="3"></td>
			<td align="right">Validation Complete?:
				<input id="validation_complete" class="studies_table" type="checkbox"></input>
			</td>
		</tr>

		<tr class="result-user-message">
			<td class="abstractor-stop-time-reminder">Please remember to enter the Abstractor stop time on the Basic Info page!</td>
		</tr>
		<tr class="result-user-message">     
			<td class="validator-stop-time-reminder">Please remember to enter the Validator stop time on the Basic Info page!</td>
		</tr>
		
		<tr></tr>
		<tr>
			<td colspan="3" class="notopborder"></td>
			<td class="submit_form notopborder">
				<a class="button save_study alignright">SAVE STUDY</a>
			</td>
		</tr>

	</table>

<?php

}

/**
 * Function to render EA tabs
 * NOTE: seq in effect_association table goes from 1-99.
 *
 * @param array, array. Number and names of EA tabs, data for ea tabs
 * 
 */
function cc_transtria_render_ea_tabs( $num_ea_tab, $dd_multiple_options_ea ){


?>
	<div id="effect_association_tab_<?php echo $num_ea_tab; ?>" class="one_ea_tab <?php if( $num_ea_tab != "1" ){ echo 'noshow'; } ?>" data-which_tab_num="<?php echo $num_ea_tab; ?>">
		<table>
			<tbody>
				<tr>
					<td colspan="3" class="notopborder"></td>
					<td class="notopborder">
						 <select class="ea_copy_tab"></select>
						 <a class="button ea_copy_tab_button">Copy Tab</a>
					</td>
				</tr>

				<tr>
					<td class="minwidth200"><label>Results (numeric)</label></td>
					<td colspan="3"><input id="ea_<?php echo $num_ea_tab; ?>_result_numeric" class="ea_table" regex="^-?\d{1,6}(\.\d{1,4})?$"></input></td>
				</tr>

				<tr>
					<td><label>Duration</label></td>
					<td colspan="3"><select id="ea_<?php echo $num_ea_tab; ?>_duration" class="ea_table">
						<option value="">---Select---</option>
						<?php //$dd_multiple_options_ea are indexed by the general id
						foreach( $dd_multiple_options_ea['ea_duration'] as $k => $v ){
							echo '<option value="' . $k . '">' . $v->descr . '</option>';
						
						} ?>
					</select></td>
				</tr>

				<tr class="not-reported">
					<td class="not-reported"><label>Duration not reported</label></td>
					<td colspan="3"><input id="ea_<?php echo $num_ea_tab; ?>_duration_notreported" class="ea_table" type="checkbox"></input></td>
				</tr>

				<tr>
					<td><label>Result Type</label></td>
					<td colspan="3"><span id="ea_<?php echo $num_ea_tab; ?>_result_type" data-ea-count="<?php echo $num_ea_tab; ?>">
						<input type="radio" class="ea_table" value="C" name="ea_<?php echo $num_ea_tab; ?>_result_type">Crude
						<input type="radio" class="ea_table" value="A" name="ea_<?php echo $num_ea_tab; ?>_result_type">Adjusted
					</span></td>
				</tr>

				<tr id="ea_<?php echo $num_ea_tab; ?>_results_variables_tr" class="noshow">
					<td><label>Variables</label></td>
					<td colspan="3"><textarea id="ea_<?php echo $num_ea_tab; ?>_results_variables" class="ea_table" style="width:98%"></textarea></td>
				</tr>

				<tr>
					<td><label>Statistical analysis model:</label></td>
					<td colspan="3">
						<select id="ea_<?php echo $num_ea_tab; ?>_result_statistical_model" class="ea_table">
							<option value="">---Select---</option>
							<?php //$dd_multiple_options_ea are indexed by the general id
							foreach( $dd_multiple_options_ea['ea_result_statistical_model'] as $k => $v ){
								echo '<option value="' . $k . '">' . $v->descr . '</option>';
							
							} ?>
						
						</select></td>
				</tr>

				<tr>
					<td><label>Result evaluation population:</label></td>
					<td colspan="3"><select id="ea_<?php echo $num_ea_tab; ?>_result_evaluation_population" class="ea_multiselect multiselect code_results_table" multiple="multiple">
						<?php //$dd_multiple_options_ea are indexed by the general id
						foreach( $dd_multiple_options_ea['ea_result_evaluation_population'] as $k => $v ){
							echo '<option value="' . $k . '">' . $v->descr . '</option>';
						
						} ?>
					</select></td>
				</tr>

				<tr>
					<td><label>Result Subpopulation:</label>
					</td>
					<td><span id="ea_<?php echo $num_ea_tab; ?>_result_subpopulationYN">
						<input type="radio" class="ea_table" value="Y" name="ea_<?php echo $num_ea_tab; ?>_result_subpopulationYN">Yes
						<input type="radio" class="ea_table" value="N" name="ea_<?php echo $num_ea_tab; ?>_result_subpopulationYN">No
					</span>

					</td>
					<td><label>Result Subpopulation:</label>
					<td>
						<select id="ea_<?php echo $num_ea_tab; ?>_result_subpopulations" class="ea_multiselect multiselect code_results_table" multiple="multiple">
							<?php //$dd_multiple_options_ea are indexed by the general id
							foreach( $dd_multiple_options_ea['ea_result_subpopulations'] as $k => $v ){
								echo '<option value="' . $k . '">' . $v->descr . '</option>';
							
							} ?>
						</select>
					</td>
				</tr>

				<tr>
					<td><label>Indicator Direction:</label></td>
					<td colspan="3"><select id="ea_<?php echo $num_ea_tab; ?>_result_indicator_direction" class="ea_table">
						<option value="">---Select---</option>
						<?php //$dd_multiple_options_ea are indexed by the general id
						foreach( $dd_multiple_options_ea['ea_result_indicator_direction'] as $k => $v ){
							echo '<option value="' . $k . '">' . $v->descr . '</option>';
						
						} ?>
					
					</select></td>
				</tr>

				<tr>
					<td><label>Outcome Direction:</label></td>
					<td colspan="3"><select id="ea_<?php echo $num_ea_tab; ?>_result_outcome_direction" class="ea_table">
						<option value="">---Select---</option>
						<?php //$dd_multiple_options_ea are indexed by the general id
						foreach( $dd_multiple_options_ea['ea_result_outcome_direction'] as $k => $v ){
							echo '<option value="' . $k . '">' . $v->descr . '</option>';
						
						} ?>
						
					</select></td>
				</tr>

				<tr>
					<td><label>Effect/Association direction:</label></td>
					<td colspan="3"><input id="ea_<?php echo $num_ea_tab; ?>_result_effect_association_direction" class="ea_table" readonly></input></td>
				</tr>

				<tr>
					<td><label>Result Strategy:</label></td>
					<td colspan="3"><select id="ea_<?php echo $num_ea_tab; ?>_result_strategy" class="ea_table">
						<option value="">---Select---</option>
						<?php //$dd_multiple_options_ea are indexed by the general id
						foreach( $dd_multiple_options_ea['ea_result_strategy'] as $k => $v ){
							echo '<option value="' . $k . '">' . $v->descr . '</option>';
						
						} ?>
					
					</select>
					</td>
				</tr>

				<tr>
					<td><label>Outcome type:</label></td>
					<td><select id="ea_<?php echo $num_ea_tab; ?>_result_outcome_type" class="ea_table">
						<option value="">---Select---</option>
						<?php //$dd_multiple_options_ea are indexed by the general id
						foreach( $dd_multiple_options_ea['ea_result_outcome_type'] as $k => $v ){
							echo '<option value="' . $k . '">' . $v->descr . '</option>';
						
						} ?>
					</select></td>
					<td><label>Other:</label>
					</td>
					<td>
						<input id="ea_<?php echo $num_ea_tab; ?>_result_outcome_type_other" class="ea_table" type="text"></input>
					</td>
				</tr>

				<tr>
					<td><label>Outcome Assessed:</label></td>
					<td>
						<select id="ea_<?php echo $num_ea_tab; ?>_result_outcome_accessed" class="ea_multiselect multiselect code_results_table" multiple="multiple">
						<?php //$dd_multiple_options_ea are indexed by the general id
						foreach( $dd_multiple_options_ea['ea_result_outcome_accessed'] as $k => $v ){
							echo '<option value="' . $k . '">' . $v->descr . '</option>';
						
						} ?>
						</select></td>
					<td><label>Other:</label>
					</td>
					<td>
						<input id="ea_<?php echo $num_ea_tab; ?>_result_outcome_accessed_other" class="ea_table" type="text"></input>
					</td>
				</tr>

				<tr>
					<td><label>Measures:</label></td>
					<td><select id="ea_<?php echo $num_ea_tab; ?>_result_measures" class="ea_multiselect multiselect code_results_table" multiple="multiple">
						<?php //$dd_multiple_options_ea are indexed by the general id
						foreach( $dd_multiple_options_ea['ea_result_measures'] as $k => $v ){
							echo '<option value="' . $k . '">' . $v->descr . '</option>';
						
						} ?>
					</select></td>
					<td><label>Other:</label>
					</td>
					<td>
						<input id="ea_<?php echo $num_ea_tab; ?>_result_measures_other" class="ea_table" type="text"></input>
					</td>
				</tr>

				<tr>
					<td><label>Indicator</label></td>
					<td colspan="3"><select id="ea_<?php echo $num_ea_tab; ?>_result_indicator" class="ea_multiselect multiselect code_results_table" multiple="multiple">
						<?php //$dd_multiple_options_ea are indexed by the general id
						foreach( $dd_multiple_options_ea['ea_result_indicator'] as $k => $v ){
							echo '<option value="' . $k . '">' . $v->descr . '</option>';
						
						} ?>					
					
					</select></td>
				</tr>

				<tr>
					<td><label>Method of Accessing Significance:</label></td>
					<td><select id="ea_<?php echo $num_ea_tab; ?>_result_statistical_measure" class="ea_table">
						<option value="">---Select---</option>
						<?php //$dd_multiple_options_ea are indexed by the general id
						foreach( $dd_multiple_options_ea['ea_result_statistical_measure'] as $k => $v ){
							echo '<option value="' . $k . '">' . $v->descr . '</option>';
						
						} ?>
					
					</select></td>

					<td>
						<label id="ea_<?php echo $num_ea_tab; ?>_ci_label">CI Range:</label>
					</td>
					<td>
						<input type="text" id="ea_<?php echo $num_ea_tab; ?>_statistical_measure_p_value" style="display:none"></input>
						<div id="statistical_measure_p_value">
							<input id="ea_<?php echo $num_ea_tab; ?>_statistical_measure_ci_value1" class="ea_table" size="7" maxlength="7" ></input> to
							<input id="ea_<?php echo $num_ea_tab; ?>_statistical_measure_ci_value2" class="ea_table" size="7" maxlength="7" ></input>
						</div>
					</td>
				</tr>

				<tr>
					<td><label>Significant?:</label></td>
					<td><span id="ea_<?php echo $num_ea_tab; ?>_result_significant">
						<input type="radio" class="ea_table" value="Y" name="ea_<?php echo $num_ea_tab; ?>_result_significant">Yes
						<input type="radio" class="ea_table" value="N" name="ea_<?php echo $num_ea_tab; ?>_result_significant">No
					</span></td>
				</tr>
				
			</tbody>
		</table>
	</div>
 
	<?php

}