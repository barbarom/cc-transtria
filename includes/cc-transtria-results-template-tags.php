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
?>

		  <table>
		<tr>
			<td colspan="4" class="inner_table_header"><strong>Results</strong></td>
		</tr>

		<tr>
			<td><label>Evaluation Type:</label></td> 
			<td><span>
				<select id="evaluation_type" multiple="multiple" class="multiselect general-multiselect">
				<?php 
					foreach( $results_singletons['evaluation_type'] as $k => $v ){
						echo '<option value="' . $k . '"';
						echo '>' . $v->descr . '</option>';
					
					} ?>
				</select>
			</input></td>
		</tr>
		<tr class="not-reported">
			<td class="not-reported"><label>Evaluation Type not reported</label></td>
			<td><input id="evaluationtype_notreported" type="checkbox"></td>
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
			<td><input type="text" id="otherevaluationmethods"></input></td>
		</tr>
		<tr class="not-reported">
			<td class="not-reported"><label>Evaluation Methods not reported</label></td>
			<td><input id="evaluationmethods_notreported" type="checkbox"></td>
		</tr>

		<tr>
			<td><label>Statistical Analysis and Results Description</label></td>
			<td colspan="3"><textarea id="stat_analysis_results_descr" style="width:98%"></textarea></td>
		</tr>

		<tr class="not-reported">
			<td class="not-reported"><label>Statistical Analysis/Results Desc. not reported</label></td>
			<td><input id="statisticalanalysis_notreported" type="checkbox"></td>
		</tr>


		 <tr>
		  <td><label>Confounders/Mediators/Moderators:</label></td>
		  <td><span id="confounders"></span></td>
		 </tr>

		 <tr class="not-reported">
		  <td class="not-reported"><label>Confounders/Mediators/Moderators not reported</label></td>
		  <td><input id="confounders_notreported" type="checkbox"></td>
		 </tr>


		 <tr id="confounders_type">
		  <td><label>Type</label></td>
		  <td colspan="3"><textarea id="confounders_textarea" style="width:98%;"></textarea></td>
		 </tr>
		 
		 <tr>
		  <td><label>Analysis Limitations:</label></td>
		  <td colspan="3"><textarea id="analysis_limitations" style="width:98%;"></textarea></td>
		 </tr> 

		 <tr class="not-reported">
		  <td class="not-reported"><label>Analysis Limitations not reported</label></td>
		  <td><input id="analysislimitations_notreported" type="checkbox"></td>
		 </tr>



		<!--
		 <tr>
		  <td><label>Outcome type:</label></td>
		  <td><span id="outcome_type"></span></td>
		  <td><label>Other:</label></td>
		  <td><input id="other_outcome_type"></input></td>
		 </tr>

		 <tr>
		  <td><label>Statistical method:</label></td>
		  <td><span id="statistical_method"></span></td>
		 </tr>

		-->

		 <tr>
		   <td colspan="4" align="right">
			  <button id="add_effect_association_row" 
					  onclick="addEffectAssociationTab()">Add Effect/Association</button>
		   </td>
		 </tr>
		 <tr>
		   <td colspan="4">
			   <div id="effect_association_tabs">
				  <ul></ul>
			   </div>
		   </td>
		 </tr>

		 <tr>
		   <td colspan="4" class="inner_table_header"><strong>Cost</strong></td>
		 </tr>

		 <tr>
		  <td><label>Staff and volunteer costs:</label></td>
		  <td><input id="staff_volunteer_cost_text"></input></td>
		  <td><label>Staff and volunteer costs value:</label></td>
		  <td><input id="staff_volunteer_cost_value"></input></td>
		 </tr>

		 <tr class="not-reported">
		  <td class="not-reported"><label>Staff and Volunteer Costs not reported</label></td>
		  <td><input id="staffvolunteercosts_notreported" type="checkbox"></td>
		 </tr>


		 <tr>
		  <td><label>Space and Infrastructure costs:</label></td>
		  <td><input id="space_infrastructure_cost_text"></input></td>
		  <td><label>Space and Infrastructure costs value:</label></td>
		  <td><input id="space_infrastructure_cost_value"></input></td>
		 </tr>

		 <tr class="not-reported">
		  <td class="not-reported"><label>Space and Infrastructure Costs not reported</label></td>
		  <td><input id="spacecosts_notreported" type="checkbox"></td>
		 </tr>


		 <tr>
		  <td><label>Equipment and material costs:</label></td>
		  <td><input id="equipment_material_cost_text"></input></td>
		  <td><label>Equipment and material costs value:</label></td>
		  <td><input id="equipment_material_cost_value"></input></td>
		 </tr>

		 <tr class="not-reported">
		  <td class="not-reported"><label>Equipment and material costs not reported</label></td>
		  <td><input id="equipmentcosts_notreported" type="checkbox"></td>
		 </tr>


		 <tr>
		   <td colspan="4" class="inner_table_header"><strong>Maintenance/Sustainability</strong></td>
		 </tr>

		 <tr>
		  <td><label>Was the outcome maintained?:</label></td>
		  <td><span id="outcome_maintained_flag"></span></td>
		  <td><label>If yes, explain:</label></td>
		  <td><input type="text" id="explain_maintenance"></input></td>
		 </tr>

		 <tr class="not-reported">
		  <td class="not-reported"><label>Outcome Maintained not reported</label></td>
		  <td><input id="outcomemaintained_notreported" type="checkbox"></td>
		 </tr>


		 <tr>
		  <td><label>was there a plan for sustainability?:</label></td>
		  <td><span id="sustainability_plan_flag"></span></td>
		  <td><label>If yes, explain:</label></td>
		  <td><input type="text" id="explain_sustainability"></input></td>
		 </tr>

		 <tr class="not-reported">
		  <td class="not-reported"><label>Sustainability plan not reported</label></td>
		  <td><input id="sustainabilityplan_notreported" type="checkbox"></td>
		 </tr>








		   <tr>
			 <td></td>
			 <td></td>
			 <td></td>
			 <td align="right">Abstraction Complete?:
			   <input id="abstraction_complete" type="checkbox"></input>
			 </td>
		   </tr>

		   <tr>
			 <td></td>
			 <td></td>
			 <td></td>
			 <td align="right">Validation Complete?:
			   <input id="validation_complete" type="checkbox"></input>
			 </td>
		   </tr>

		  <tr class="result-user-message">
			 <td class="abstractor-stop-time-reminder">Please remember to enter the Abstractor stop time on the Basic Info page!</td>
		  </tr>
		  <tr class="result-user-message">     
			 <td class="validator-stop-time-reminder">Please remember to enter the Validator stop time on the Basic Info page!</td>
		  </tr>


		</table>



<?php
}