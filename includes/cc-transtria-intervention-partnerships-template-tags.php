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
?>

		  <table>
		 <tr>
		   <td colspan="4" class="inner_table_header"><strong>Intervention Setting</strong></td>
		 </tr>
		 <tr>
		  <td><label>Domestic/International Setting:</label></td> 
		  <td><input type="checkbox" id="DomesticSetting">Domestic</input>
			  <input type="checkbox" id="InternationalSetting">International</input></td>
		 </tr>

		 <tr class="not-reported">
		  <td class="not-reported"><label>Domestic/Intl Setting Not Reported</label></td>
		  <td><input id="domeesticintlsetting_notreported" type="checkbox"></td>
		 </tr>


		 <tr>
		  <td><label>State Settings:</label></td>
		  <td><span id="state_setting"></span></td>
		 </tr>

		 <tr class="not-reported">
		  <td class="not-reported"><label>State Settings not reported</label></td>
		  <td><input id="statesettings_notreported" type="checkbox"></input></td>
		 </tr>

		 <tr>
		  <td><label>Setting Type:</label></td>
		  <td><span id="setting_type"></span></td>
		  <td><label>Other:</label></td>
		  <td><input id="other_setting_type"></input></td>
		 </tr>

		 <tr class="not-reported">
		  <td class="not-reported"><label>Setting Type not reported</label></td>
		  <td><input id="settingtype_notreported" type="checkbox"></td>
		 </tr>


		 <tr>
		   <td colspan="4" class="inner_table_header"><strong>Partnerships</strong></td>
		 </tr>

		 <tr>
		  <td><label>Partner discipline:</label></td>
		  <td><input type="text" id="partner_discipline"></input></td>
		  <td><label>Other:</label></td>
		  <td colspan="2"><input type="text" id="other_partner_discipline"></input></td>
		 </tr>
		 
		 <tr class="not-reported">
		  <td class="not-reported"><label>Partner discipline not reported</label></td>
		  <td><input id="partnerdiscipline_notreported" type="checkbox"></td>
		 </tr>

		 
		 <tr>
		  <td><label>Name of lead agencies:</label></td>
		  <td colspan="2"><input type="text" id="lead_agencies"></input></td>
		 </tr>

		 <tr class="not-reported">
		  <td class="not-reported"><label>Name of lead agencies not reported</label></td>
		  <td><input id="leadagencies_notreported" type="checkbox"></td>
		 </tr>


		 <tr>
		  <td><label>Lead agency role:</label></td>
		  <td colspan="2"><input type="text" id="lead_agency_role"></input></td>
		 </tr>

		 <tr class="not-reported">
		  <td class="not-reported"><label>Lead agency role not reported</label></td>
		  <td><input id="leadagencyrole_notreported" type="checkbox"></td>
		 </tr>


		 <tr>
		   <td colspan="4" class="inner_table_header"><strong>Framework</strong></td>
		 </tr>

		 <tr>
		   <td><label>Theory/Framework:</label></td>
		   <td><span id="theory_framework_flag"></span></td>
		 </tr>

		 <tr class="not-reported">
		  <td class="not-reported"><label>Theory/Framework not reported</label></td>
		  <td><input id="theoryframework_notreported" type="checkbox"></td>
		 </tr>


		 <tr>
		  <td><label>Theory/Framework type:</label></td>
		  <td><span id="theory_framework_type"></span></td>
		  <td><label>Other:</label></td>
		  <td><input type="text" id="other_theory_framework"></input></td>
		 </tr>

		 <tr class="not-reported">
		  <td class="not-reported"><label>Theory/Framework Type not reported</label></td>
		  <td><input id="theoryframeworktype_notreported" type="checkbox"></td>
		 </tr>


		 <tr>
		   <td colspan="4" class="inner_table_header"><strong>Intervention</strong></td>
		 </tr>

		 <tr>
		  <td><label>Intervention purpose:</label></td>
		  <td colspan="2"><input type="text" id="intervention_purpose"></input></td>
		 </tr>
		  
		 <tr class="not-reported">
		  <td class="not-reported"><label>Intervention Purpose not reported</label></td>
		  <td><input id="interventionpurpose_notreported" type="checkbox"></td>
		 </tr>


		 <tr>
		  <td><label>Intervention summary:</label></td>
		  <td colspan="3"><textarea id="intervention_summary" style="width:100%"></textarea></td>
		 </tr>

		 <tr class="not-reported">
		  <td class="not-reported"><label>Intervention Summary not reported</label></td>
		  <td><input id="interventionsummary_notreported" type="checkbox"></td>
		 </tr>


		 <tr>
		  <td><label>Intervention Components:</label></td>
		  <td><span id="intervention_component"></span></td>

		 </tr>

		 <tr class="not-reported">
		  <td class="not-reported"><label>Intervention Components not reported</label></td>
		  <td><input id="interventioncomponents_notreported" type="checkbox"></td>
		 </tr>


		 <tr>
		  <td><label>Select strategies:</label></td>
		  <td><span id="strategies"></span></td>
		 </tr>

		 <tr class="not-reported">
		  <td class="not-reported"><label>Strategies not reported</label></td>
		  <td><input id="strategies_notreported" type="checkbox"></td>
		 </tr>


		 <tr>
		  <td><label>PSE components:</label></td>
		  <td><span id="pse_components"></span></td>
		 </tr>

		 <tr class="not-reported">
		  <td class="not-reported"><label>PSE components not reported</label></td>
		  <td><input id="psecomponents_notreported" type="checkbox"></td>
		 </tr>


		 <tr>
		  <td><label>Complexity:</label></td>
		  <td><span id="complexity"></span></td>
		 </tr>

		 <tr class="not-reported">
		  <td class="not-reported"><label>Complexity not reported</label></td>
		  <td><input id="complexity_notreported" type="checkbox"></td>
		 </tr>





		 <tr>
		  <td><label>Location of intervention:</label></td>
		  <td><span id="intervention_location"></span></td>
		  <td><label>Other:</label></td>
		  <td><input type="text" id="other_intervention_location"></input></td> 

		</tr>

		 <tr class="not-reported">
		  <td class="not-reported"><label>Location of intervention not reported</label></td>
		  <td><input id="locationintervention_notreported" type="checkbox"></td>
		 </tr>


		 <!-- is this needed anymore?
		 <tr>
		  <td><label>Type(s) of intervention:</label></td>
		  <td><span id="intervention_type"></span></td>
		  <td><label>Other:</label></td>
		  <td><input type="text" id="other_intervention_type"></input></td>
		 </tr>
		 -->

		 <tr>
		  <td><label>Indicator(s):</label></td>
		  <td><span id="intervention_indicators"></span></td>
		  <td><label>Other:</label></td>
		  <td><input type="text" id="other_intervention_indicators"></input></td>
		 </tr>

		 <tr class="not-reported">
		  <td class="not-reported"><label>Indicators not reported</label></td>
		  <td><input id="indicators_notreported" type="checkbox"></td>
		 </tr>

		 <tr class="intervention_indicators_display">
		  <td><label>Indicators Selected:</label></td>
		 </tr>


		 <tr>
		  <td><label>All outcomes assessed:</label></td>
		  <td><span id="intervention_outcomes_assessed"></span></td>
		  <td><label>Other:</label></td>
		  <td><input type="text" id="other_intervention_outcomes_assessed"></input></td>
		 </tr>

		 <tr class="not-reported">
		  <td class="not-reported"><label>All outcomes assessed not reported</label></td>
		  <td><input id="alloutcomesassessed_notreported" type="checkbox"></td>
		 </tr>







		 <tr>
		  <td><label>Replication:</label></td>
		  <td><span id="replication"></span></td>
		  <td><label>Description:</label></td>
		  <td><input type="text" id="replication_descr"></input></td>
		 </tr>

		 <tr class="not-reported">
		  <td class="not-reported"><label>Replication not reported</label></td>
		  <td><input id="replication_notreported" type="checkbox"></td>
		 </tr>


		 <tr>
		  <td><label>Support:</label></td>
		  <td colspan="3"><textarea id="support" style="width:100%"></textarea></td>
		 </tr>

		 <tr class="not-reported">
		  <td class="not-reported"><label>Support not reported</label></td>
		  <td><input id="support_notreported" type="checkbox"></td>
		 </tr>


		 <tr>
		  <td><label>Opposition:</label></td>
		  <td colspan="3"><textarea id="opposition" style="width:100%"></textarea></td>
		 </tr>

		 <tr class="not-reported">
		  <td class="not-reported"><label>Opposition not reported</label></td>
		  <td><input id="opposition_notreported" type="checkbox"></td>
		 </tr>


		 <tr>
		  <td><label>Evidence-based:</label></td>
		  <td><span id="evidence_based"></span></td>
		 </tr>

		 <tr class="not-reported">
		  <td class="not-reported"><label>Evidence-based not reported</label></td>
		  <td><input id="evidencebased_notreported" type="checkbox"></td>
		 </tr>


		 <tr>
		  <td><label>Fidelity:</label></td>
		  <td><span id="fidelity"></span></td>
		 </tr>

		 <tr class="not-reported">
		  <td class="not-reported"><label>Fidelity not reported</label></td>
		  <td><input id="fidelity_notreported" type="checkbox"></td>
		 </tr>


		 <tr>
		  <td><label>Implementation Limitations:</label></td>
		  <td colspan="3"><textarea id="implementation_limitations" style="width:100%"></textarea></td>
		 </tr>

		 <tr class="not-reported">
		  <td class="not-reported"><label>Implementation Limitations not reported</label></td>
		  <td><input id="implementationlimitations_notreported" type="checkbox"></td>
		 </tr>


		 <tr>
		  <td><label>Lessons Learned:</label></td>
		  <td><span id="lessons_learned"></span></td>
		 </tr>

		 <tr class="not-reported">
		  <td class="not-reported"><label>Lessons Learned not reported</label></td>
		  <td><input id="lessonslearned_notreported" type="checkbox"></td>
		 </tr>


		 <tr>
		  <td><label>Lessons Learned Description:</label></td>
		  <td colspan="3"><textarea id="lessons_learned_descr" style="width:100%"></textarea></td>
		 </tr>

		</table>
	
<?php	
}