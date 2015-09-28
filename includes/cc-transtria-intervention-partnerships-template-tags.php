<?php 
/**
 * CC Transtria Intervention Parterships Tab Template Tags
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
function cc_transtria_render_intervention_partnerships_tab( $field_data ){

	$intervention_singletons = $field_data['dd_singleton_options']; //just making it easier for dev, although a waste of memory.  TODO: don't waste memory

?>

	<table>
		<tr>
			<td colspan="4" class="inner_table_header"><strong>Intervention Setting</strong></td>
		</tr>
		<tr>
			<td><label>Domestic/International Setting:</label></td> 
			<td colspan="3">
				<input type="checkbox" id="DomesticSetting" class="studies_table">Domestic</input>
				<input type="checkbox" id="InternationalSetting" class="studies_table">International</input>
			</td>
		</tr>

		<tr class="not-reported">
			<td class="not-reported"><label>Domestic/Intl Setting Not Reported</label></td>
			<td colspan="3"><input id="domeesticintlsetting_notreported" class="studies_table" type="checkbox"></td>
		</tr>


		<tr>
			<td><label>State Settings:</label></td>
			<td colspan="3"><span>
				<select id="state_setting" multiple="multiple" class="multiselect">
					<?php 
					foreach( $intervention_singletons['state_setting'] as $k => $v ){
						echo '<option value="' . $k . '"';
						echo '>' . $v->descr . '</option>';
					
					} ?>
				</select>
			</span></td>
		</tr>

		<tr class="not-reported">
			<td class="not-reported"><label>State Settings not reported</label></td>
			<td colspan="3"><input id="statesettings_notreported" class="studies_table" type="checkbox"></input></td>
		</tr>

		<tr>
			<td><label>Setting Type:</label></td>
			<td><span>
				<select id="setting_type" multiple="multiple" class="multiselect general-multiselect">
					<?php //populate the dropdown
					foreach( $intervention_singletons['setting_type'] as $k => $v ){
						echo '<option value="' . $k . '"';
						echo '>' . $v->descr . '</option>';
					} ?>
				</select>
			</span></td>
			<td><label>Other:</label></td>
			<td><input id="other_setting_type" class="studies_table"></input></td>
		</tr>

		<tr class="not-reported">
			<td class="not-reported"><label>Setting Type not reported</label></td>
			<td colspan="3"><input id="settingtype_notreported" class="studies_table" type="checkbox"></td>
		</tr>

		<tr class="notopborder nobottomborder placeholder-tr">&nbsp;</tr>
		
		<tr>
			<td colspan="4" class="inner_table_header"><strong>Partnerships</strong></td>
		</tr>

		<tr>
			<td><label>Partner discipline:</label></td>
			<td><span>
				<select id="partner_discipline" multiple="multiple" class="multiselect general-multiselect">
					<?php //populate the dropdown
					foreach( $intervention_singletons['partner_discipline'] as $k => $v ){
						echo '<option value="' . $k . '"';
						echo '>' . $v->descr . '</option>';
					} ?>
				</select>
			</span></td>
			<td><label>Other:</label></td>
			<td colspan="2"><input type="text" id="other_partner_discipline" class="studies_table"></input></td>
		 </tr>
		 
		<tr class="not-reported">
			<td class="not-reported"><label>Partner discipline not reported</label></td>
			<td colspan="3"><input id="partnerdiscipline_notreported" class="studies_table" type="checkbox"></td>
		 </tr>

		 
		 <tr>
			<td><label>Name of lead agencies:</label></td>
			<td colspan="3"><textarea id="lead_agencies" class="studies_table"></textarea></td>
		 </tr>

		 <tr class="not-reported">
			<td class="not-reported"><label>Name of lead agencies not reported</label></td>
			<td colspan="3"><input id="leadagencies_notreported" class="studies_table" type="checkbox"></td>
		 </tr>


		 <tr>
			<td><label>Lead agency role:</label></td>
			<td colspan="3"><textarea id="lead_agency_role" class="studies_table"></textarea></td>
		 </tr>

		 <tr class="not-reported">
			<td class="not-reported"><label>Lead agency role not reported</label></td>
			<td colspan="3"><input id="leadagencyrole_notreported" class="studies_table" type="checkbox"></td>
		 </tr>

		<tr class="notopborder nobottomborder placeholder-tr">&nbsp;</tr>
		
		 <tr>
			<td colspan="4" class="inner_table_header"><strong>Framework</strong></td>
		 </tr>

		 <tr>
			<td><label>Theory/Framework:</label></td>
			<td><span id="theory_framework_flag">
				<input type="radio" value="Y" name="theory_framework_flag" class="studies_table" data-notreported_id="theoryframework_notreported">Yes
				<input type="radio" value="N" name="theory_framework_flag" class="studies_table" data-notreported_id="theoryframework_notreported">No
			</span></td>
		 </tr>

		 <tr class="not-reported">
			<td class="not-reported"><label>Theory/Framework not reported</label></td>
			<td>
				<input id="theoryframework_notreported" class="studies_table not_reported_clear" type="checkbox">
			</td>
		 </tr>

		 <tr>
			<td><label>Theory/Framework type:</label></td>
			<td colspan="1"><span>
				<select id="theory_framework_type" multiple="multiple" class="multiselect general-multiselect">
					<?php //populate the dropdown
					foreach( $intervention_singletons['theory_framework_type'] as $k => $v ){
						echo '<option value="' . $k . '"';
						echo '>' . $v->descr . '</option>';
					} ?>
				</select>
			</span></td>
			<td><label>Other:</label></td>
			<td><input type="text" id="other_theory_framework" class="studies_table"></input></td>
		 </tr>

		 <tr class="not-reported">
			<td class="not-reported"><label>Theory/Framework Type not reported</label></td>
			<td colspan="3"><input id="theoryframeworktype_notreported" class="studies_table" type="checkbox"></td>
		 </tr>
		 
		<tr class="notopborder nobottomborder placeholder-tr">&nbsp;</tr>

		 <tr>
			<td colspan="4" class="inner_table_header"><strong>Intervention</strong></td>
		 </tr>

		 <tr>
			<td><label>Intervention purpose:</label></td>
			<td colspan="3"><textarea id="intervention_purpose" class="studies_table"></textarea></td>
		 </tr>
		  
		 <tr class="not-reported">
			<td class="not-reported"><label>Intervention Purpose not reported</label></td>
			<td colspan="3"><input id="interventionpurpose_notreported" class="studies_table" type="checkbox"></td>
		 </tr>

		 <tr>
			<td><label>Intervention summary:</label></td>
			<td colspan="3"><textarea id="intervention_summary" class="studies_table"></textarea></td>
		 </tr>

		 <tr class="not-reported">
			<td class="not-reported"><label>Intervention Summary not reported</label></td>
			<td colspan="3"><input id="interventionsummary_notreported" class="studies_table" type="checkbox"></td>
		 </tr>

		 <tr>
			<td><label>Intervention Components:</label></td>
			<td colspan="3"><span>
				<select id="intervention_component" multiple="multiple" class="multiselect general-multiselect">
					<?php //populate the dropdown
					foreach( $intervention_singletons['intervention_component'] as $k => $v ){
						echo '<option value="' . $k . '"';
						echo '>' . $v->descr . '</option>';
					} ?>
				</select>
			</span></td>

		 </tr>

		 <tr class="not-reported">
			<td class="not-reported"><label>Intervention Components not reported</label></td>
			<td colspan="3"><input id="interventioncomponents_notreported" class="studies_table" type="checkbox"></td>
		 </tr>

		 <tr>
			<td><label>Select strategies:</label></td>
			<td colspan="3"><span>
				<select id="strategies" multiple="multiple" class="multiselect general-multiselect">
					<?php //populate the dropdown
					foreach( $intervention_singletons['strategies'] as $k => $v ){
						echo '<option value="' . $k . '"';
						echo '>' . $v->descr . '</option>';
					} ?>
				</select>
			</span></td>
		 </tr>

		 <tr class="not-reported">
			<td class="not-reported"><label>Strategies not reported</label></td>
			<td colspan="3"><input id="strategies_notreported" class="studies_table" type="checkbox"></td>
		 </tr>

		 <tr>
			<td><label>PSE components:</label></td>
			<td colspan="3"><span>
				<select id="pse_components" multiple="multiple" class="multiselect general-multiselect">
					<?php //populate the dropdown
					foreach( $intervention_singletons['pse_components'] as $k => $v ){
						echo '<option value="' . $k . '"';
						echo '>' . $v->descr . '</option>';
					} ?>
				</select>
			</span></td>
		 </tr>

		 <tr class="not-reported">
			<td class="not-reported"><label>PSE components not reported</label></td>
			<td colspan="3"><input id="psecomponents_notreported" class="studies_table" type="checkbox"></td>
		 </tr>

		 <tr>
			<td><label>Complexity:</label></td>
			<td colspan="3"><span>
				<select id="complexity" multiple="multiple" class="multiselect general-multiselect">
					<?php //populate the dropdown
					foreach( $intervention_singletons['complexity'] as $k => $v ){
						echo '<option value="' . $k . '"';
						echo '>' . $v->descr . '</option>';
					} ?>
				</select>
			</span></td>
		 </tr>

		 <tr class="not-reported">
			<td class="not-reported"><label>Complexity not reported</label></td>
			<td colspan="3"><input id="complexity_notreported" class="studies_table" type="checkbox"></td>
		 </tr>

		 <tr>
			<td><label>Location of intervention:</label></td>
			<td><span>
				<select id="intervention_location" multiple="multiple" class="multiselect general-multiselect">
					<?php //populate the dropdown
					foreach( $intervention_singletons['intervention_location'] as $k => $v ){
						echo '<option value="' . $k . '"';
						echo '>' . $v->descr . '</option>';
					} ?>
				</select>
			</span></td>
			<td><label>Other:</label></td>
			<td><input type="text" id="other_intervention_location" class="studies_table"></input></td> 

		</tr>

		<tr class="not-reported">
			<td class="not-reported"><label>Location of intervention not reported</label></td>
			<td colspan="3"><input id="locationintervention_notreported" class="studies_table" type="checkbox"></td>
		</tr>

		<tr>
			<td><label>Indicator(s):</label></td>
			<td><span>
				<select id="intervention_indicators" multiple="multiple" class="multiselect general-multiselect">
					<?php //populate the dropdown
					foreach( $intervention_singletons['intervention_indicators'] as $k => $v ){
						echo '<option value="' . $k . '"';
						echo '>' . $v->descr . '</option>';
					} ?>
				</select>
			</span></td>
		</tr>
		
		<tr class="additional_indicators">
			<td></td>
			<td><label>Other Intervention Indicator 1:</label></td>
			<td><input type="text" id="other_intervention_indicators" class="studies_table other_indicator" data-which_other="1"></input></td>
			<td><a class="show_indicator_field button <?php if( $field_data['num_other_indicators'] > 1 ){ echo 'noshow'; } ?>">+</a></td>
		</tr>
		
		<?php //if we have other intervention indicators saved, load them
		if( $field_data['num_other_indicators'] > 1 ){
			for( $i = 2; $i <=10; $i++ ){
				if( $field_data['num_other_indicators'] < $i ){
					break;
				} else {
					//draw shell for other indicators
					echo '<tr class="additional_indicators"><td></td><td><label>Other Intervention Indicator ' . $i . ':</label></td>';
					echo '<td><input type="text" id="other_intervention_indicators' . $i .'" class="studies_table other_indicator" data-which_other="' . $i . '"></input></td>';
					echo '<td><a class="show_indicator_field button ';
					if( $field_data['num_other_indicators'] != $i ) { echo 'noshow'; }
					echo '">+</a></td></tr>';
									
				}
			
			} 
		} ?>

		<tr class="not-reported">
		<td class="not-reported"><label>Indicators not reported</label></td>
		<td colspan="3"><input id="indicators_notreported" class="studies_table" type="checkbox"></td>
		</tr>

		<tr class="intervention_indicators_display">
			<td colspan="4"><label>Indicators Selected:</label></td>
		</tr>

		<tr>
			<td><label>All outcomes assessed:</label></td>
			<td colspan="3"><span>
				<select id="intervention_outcomes_assessed" multiple="multiple" class="multiselect general-multiselect">
					<?php //populate the dropdown
					foreach( $intervention_singletons['intervention_outcomes_assessed'] as $k => $v ){
						echo '<option value="' . $k . '"';
						echo '>' . $v->descr . '</option>';
					} ?>
				</select>
			</span></td>
			<!--<td><label>Other:</label></td>
			<td><input type="text" id="other_intervention_outcomes_assessed" class="studies_table"></input></td>-->
		</tr>
		
		<tr class="additional_outcomes">
			<td></td>
			<td><label>Other Outcomes Assessed 1:</label></td>
			<td><input type="text" id="other_intervention_outcomes_assessed" class="studies_table other_outcome" data-which_other="1"></input></td>
			<td><a class="show_outcomes_field button <?php if( $field_data['num_other_outcomes'] > 1 ){ echo 'noshow'; } ?>">+</a></td>
		</tr>
		
		<?php //if we have other intervention indicators saved, load them
		if( $field_data['num_other_outcomes'] > 1 ){
			for( $i = 2; $i <=10; $i++ ){
				if( $field_data['num_other_outcomes'] < $i ){
					break;
				} else {
					//draw shell for other indicators
					echo '<tr class="additional_outcomes"><td></td><td><label>Other Outcomes Assessed ' . $i . ':</label></td>';
					echo '<td><input type="text" id="other_intervention_outcomes_assessed' . $i .'" class="studies_table other_outcome" data-which_other="' . $i . '"></input></td>';
					echo '<td><a class="show_outcomes_field button ';
					if( $field_data['num_other_outcomes'] != $i ) { echo 'noshow'; }
					echo '">+</a></td></tr>';
									
				}
			
			} 
		} ?>


		<tr class="not-reported">
			<td class="not-reported"><label>All outcomes assessed not reported</label></td>
			<td colspan="3"><input id="alloutcomesassessed_notreported" class="studies_table" type="checkbox"></td>
		</tr>


		<tr>
			<td><label>Replication:</label></td>
			<td><span id="replication">
				<input type="radio" value="Y" name="replication" class="studies_table" data-notreported_id="replication_notreported">Yes
				<input type="radio" value="N" name="replication" class="studies_table" data-notreported_id="replication_notreported">No
			</span></td>
			<td><label>Description:</label></td>
			<td><textarea id="replication_descr" class="studies_table"></textarea></td>
		</tr>

		<tr class="not-reported">
			<td class="not-reported"><label>Replication not reported</label></td>
			<td colspan="3"><input id="replication_notreported" class="studies_table not_reported_clear" type="checkbox"></td>
		</tr>


		<tr>
			<td><label>Support:</label></td>
			<td colspan="3"><textarea id="support" class="studies_table" style="width:100%"></textarea></td>
		</tr>

		<tr class="not-reported">
			<td class="not-reported"><label>Support not reported</label></td>
			<td colspan="3"><input id="support_notreported" class="studies_table" type="checkbox"></td>
		</tr>


		<tr>
			<td><label>Opposition:</label></td>
			<td colspan="3"><textarea id="opposition" class="studies_table" style="width:100%"></textarea></td>
		</tr>

		<tr class="not-reported">
			<td class="not-reported"><label>Opposition not reported</label></td>
			<td colspan="3"><input id="opposition_notreported" class="studies_table" type="checkbox"></td>
		</tr>


		<tr>
			<td><label>Evidence-based:</label></td>
			<td><span id="evidence_based">
				<input type="radio" value="Y" name="evidence_based" class="studies_table" data-notreported_id="evidencebased_notreported">Yes
				<input type="radio" value="N" name="evidence_based" class="studies_table" data-notreported_id="evidencebased_notreported">No
			</span></td>
		</tr>

		<tr class="not-reported">
			<td class="not-reported"><label>Evidence-based not reported</label></td>
			<td colspan="3"><input id="evidencebased_notreported" class="studies_table not_reported_clear" type="checkbox"></td>
		</tr>


		<tr>
			<td><label>Fidelity:</label></td>
			<td><span id="fidelity">
				<input type="radio" value="Y" name="fidelity" class="studies_table" data-notreported_id="fidelity_notreported">Yes
				<input type="radio" value="N" name="fidelity" class="studies_table" data-notreported_id="fidelity_notreported">No
			</span></td>
		</tr>

		<tr class="not-reported">
			<td class="not-reported"><label>Fidelity not reported</label></td>
			<td colspan="3"><input id="fidelity_notreported" class="studies_table not_reported_clear" type="checkbox"></td>
		</tr>


		<tr>
			<td><label>Implementation Limitations:</label></td>
			<td colspan="3"><textarea id="implementation_limitations" class="studies_table" style="width:100%"></textarea></td>
		</tr>

		<tr class="not-reported">
			<td class="not-reported"><label>Implementation Limitations not reported</label></td>
			<td colspan="3"><input id="implementationlimitations_notreported" class="studies_table" type="checkbox"></td>
		</tr>


		<tr>
			<td><label>Lessons Learned:</label></td>
			<td><span id="lessons_learned">
				<input type="radio" value="Y" name="lessons_learned" class="studies_table" data-notreported_id="lessonslearned_notreported">Yes
				<input type="radio" value="N" name="lessons_learned" class="studies_table" data-notreported_id="lessonslearned_notreported">No
			</span></td>
		</tr>

		<tr class="not-reported">
			<td class="not-reported"><label>Lessons Learned not reported</label></td>
			<td colspan="3"><input id="lessonslearned_notreported" class="studies_table not_reported_clear" type="checkbox"></td>
		</tr>


		<tr>
			<td><label>Lessons Learned Description:</label></td>
			<td colspan="3"><textarea id="lessons_learned_descr" class="studies_table" style="width:100%"></textarea></td>
		</tr>

		<tr></tr>
		<tr>
			<td colspan="3"></td>
			<td class="submit_form">
				<a class="button save_study alignright">SAVE STUDY</a>
			</td>
		</tr>
	</table>
	
<?php	
}