<?php 
/**
 * CC Transtria Analysis Template Tags
 *
 * @package   CC Transtria Extras
 * @author    CARES staff
 * @license   GPL-2.0+
 * @copyright 2015 CommmunityCommons.org
 */

/**
 * Output logic for the analysis page. 
 *
 * @since   1.0.0
 * @return 	outputs html
 */
function cc_transtria_render_analysis_page(){

	//$studies_data = cc_transtria_get_singleton_dropdown_options( );
	$study_group_ids = cc_transtria_get_study_groupings();
	
	//var_dump( $studies_data );
	?>
		<div class="analysis_messages">
			<span class="usr-msg"></span>
			<span class="spinny"></span>
		</div>
		
		<div id="analysis_choices">
			1. <select id="StudyGroupingIDList" style="">
				<option value="-1"> -- Select Study Group -- </option>
			<?php
				foreach( $study_group_ids as $key => $val ){
					
					echo "<option value='" . (int)$val['EPNP_ID'] . "'>" . $val['EPNP_ID'] . "</option>";
					
				}
			?>
			</select> Select Study Grouping
			<br />
			2. <a id="run_intermediate_analysis" class="button"> SET UNIQUE IDS**</a>
			<br />
			3. <a id="run_analysis" class="button"> SET ANALYSIS IDS (from UNIQUE IDS)**</a>
			<br />
			4. <a id="get_vars_by_group" class="button"> GET/DISPLAY ALL VARS</a>
			<br />
			5. <a id="run_second_analysis" class="button"> RUN ANALYSIS w/ NEW VARS, GET VARS</a>
			
			<h4><em>**Note: will overwrite existing variables and computations</em></h4>
			
		</div>
		
		<label class="analysis_tab_label active" data-whichanalysistab="intermediate_vars_content" for="intermediate_tab">Intermediate Data</label>
		<label class="analysis_tab_label" data-whichanalysistab="analysis_vars_content" for="analysis_tab">Analysis Data</label>
		
		<div id="analysis_content">
			<input id="secret_study_group" hidden="hidden" >
			<div id="intermediate_vars_content" class="single_analysis_content">
				<h3 id="intermediate_vars_header_text">Intermediate Variables</h3>
				
				<h4>Indicator-Measure Dyad(s):</h4>
				<a id="hide_im_table" class="button" data-whichtable="intermediate_vars_im" data-whichlabel="I-M DYADS">HIDE I-M DYADS</a>
				<table id="intermediate_vars_im">
					<tr class="no_remove">
						<!--<th>Study ID</th>-->
						<th>Unique ID (Study ID _ seq _ unique ID)</th>
						<th>Indicator</th>
						<th>Measure</th>
						<th>Subpop?</th>
						<th>Subpop!</th>
						<th>Eval pop</th>
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				
				</table>
				
				<h4>Study Design</h4>
				<a id="hide_design_table" class="button" data-whichtable="intermediate_vars_design" data-whichlabel="STUDY DESIGN">HIDE STUDY DESIGN</a>
				<table id="intermediate_vars_design">
					<tr class="no_remove">
						<th>Study ID</th>
						<th>Study Design</th>
						<th>Study Design, Other</th>	
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				
				</table>
				
				<h4>Intervention Duration</h4>
				<a id="hide_duration_table" class="button" data-whichtable="intermediate_vars_duration" data-whichlabel="INTERVENTION DURATION">HIDE INTERVENTION DURATION</a>
				<table id="intermediate_vars_duration">
					<tr class="no_remove">
						<th>Unique ID</th>
						<th>Indicator</th>
						<th>Measure</th>		
						<th>Intervention Duration</th>			
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				
				</table>
				
				<h4>Effect or Association Direction:</h4>
				<a id="hide_direction_table"  data-whichtable="intermediate_vars_direction" data-whichlabel="I-M DIRECTIONS"class="button">HIDE I-M DIRECTIONS</a>
				<a id="show_direction_algorithm" class="button alignright" data-whichalgorithm="intermediate_direction_algorithm">SHOW ALGORITHM DETAILS</a>
				<div id="intermediate_direction_algorithm" class="show_algorithm">
					<h4>Effect or Association Direction</h4>
					<p>
						if( $significant == "N" ){<br />
							&nbsp;$ea_direction = "3";<br />
						} else { <br />
							&nbsp;if( !empty( $ind_directions[ $ind_index ] ) ){ //if we HAVE a direction, else let them know <br />
								&nbsp;&nbsp;$ind_dir = $ind_directions[ $ind_index ];<br />
								&nbsp;&nbsp;$ea_direction = cc_transtria_calculate_ea_direction( $indicator_direction, $outcome_direction );<br />
							&nbsp;} else {<br />
								&nbsp;&nbsp;$ind_dir = "no ind. direction set";<br />
								&nbsp;&nbsp;$ea_direction = "no ind. direction set";<br />
							&nbsp;}<br />
						}<br />
					</p>
				</div>
				<table id="intermediate_vars_direction">
					<tr class="no_remove">
						<th>Unique ID</th>
						<th>Indicator</th>
						<th>Measure</th>		
						<th>Outcome Type</th>		
						<th>Effect or Association Direction</th>		
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				
				</table>
				
				<h4>Result Evaluation Population</h4>
				<a id="hide_result_evaluation_pop_table" class="button" data-whichtable="intermediate_vars_result_evaluation_pop" data-whichlabel="RESULT EVALUATION POPULATION">HIDE RESULT EVALUATION POPULATION</a>
				<table id="intermediate_vars_result_evaluation_pop">
					<tr class="no_remove">
						<th>Study ID</th>
						<th>Result Evaluation Population</th>
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				
				</table>
				
				<h4>Result Subpopulation</h4>
				<a id="hide_result_evaluation_pop_table" class="button" data-whichtable="intermediate_vars_result_subpop" data-whichlabel="RESULT SUBPOPULATION">HIDE RESULT SUBPOPULATION</a>
				<table id="intermediate_vars_result_subpop">
					<tr class="no_remove">
						<th>Study ID</th>
						<th>Result Subopulation</th>
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				
				</table>
				
				<h4>Domestic</h4>
				<table id="intermediate_vars_domestic">
					<tr class="no_remove">
						<th>Study ID</th>
						<th>Domestic</th>
						<th>Domestic/Intl not reported</th>
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				
				</table>
				
				<h4>International</h4>
				<table id="intermediate_vars_intl">
					<tr class="no_remove">
						<th>Study ID</th>
						<th>International</th>
						<th>Domestic/Intl not reported</th>
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				
				</table>
				
				<h4>Strategies</h4>
				<table id="intermediate_vars_strategies">
					<tr class="no_remove">
						<th>Unique ID</th>
						<th>Indicator</th>
						<th>Measure</th>
						<th>Strategy</th>
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				
				</table>
				
				<h4>Intervention Components</h4>
				<a id="hide_component_table" class="button" data-whichtable="intermediate_vars_components" data-whichlabel="INTERVENTION COMPONENTS">HIDE INTERVENTION COMPONENTS</a>
				<table id="intermediate_vars_components">
					<tr class="no_remove">
						<th>Study ID</th>
						<th>Intervention Components</th>
						<th>Not Reported</th>		
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				
				</table>
				
				<h4>Complexity</h4>
				<table id="intermediate_vars_complexity">
					<tr class="no_remove">
						<th>Study ID</th>
						<th>Complexity</th>
						<th>Not Reported</th>		
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				
				</table>
				
				<!-- insert participation/potention exposure here - WHERE FROM? -->	
				
				<!-- insert Representativeness here - WHERE FROM? -->	
				
				
				<h4>Intervention Purpose</h4>
				<table id="intermediate_vars_purpose">
					<tr class="no_remove">
						<th>Study ID</th>
						<th>Intervention Purpose</th>
						<th>Not Reported</th>		
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				
				</table>
				
				<h4>Intervention Summary</h4>
				<table id="intermediate_vars_summary">
					<tr class="no_remove">
						<th>Study ID</th>
						<th>Intervention Summary</th>
						<th>Not Reported</th>		
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				
				</table>
				
				<h4>Setting Type</h4>
				<table id="intermediate_vars_settingtype">
					<tr class="no_remove">
						<th>Study ID</th>
						<th>Setting Type</th>
						<th>Other Setting Type</th>
						<th>Not Reported</th>		
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				
				</table>
				<h4>PSE Components</h4>
				<table id="intermediate_vars_pse">
					<tr class="no_remove">
						<th>Study ID</th>
						<th>PSE Components</th>
						<th>Not Reported</th>		
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				
				</table>
				<h4>Support</h4>
				<table id="intermediate_vars_support">
					<tr class="no_remove">
						<th>Study ID</th>
						<th>Support</th>
						<th>Not Reported</th>		
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				
				</table>
				<h4>Opposition</h4>
				<table id="intermediate_vars_opposition">
					<tr class="no_remove">
						<th>Study ID</th>
						<th>Opposition</th>
						<th>Not Reported</th>		
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				
				</table>
				
				<h4>Plan for Sustainability</h4>
				<table id="intermediate_vars_sustainability">
					<tr class="no_remove">
						<th>Study ID</th>
						<th>Plan for Sustainability</th>
						<th>Not Reported</th>		
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				
				</table>
				
				
				
			</div>
			
			<div id="analysis_vars_content" class="single_analysis_content">
			
				<h3 id="analysis_vars_header_text">Analysis Variables</h3>
				
				<h4>Indicator-Measure Dyad:</h4>
				<a id="hide_analysis_im_table" class="button" data-whichlabel="ANALYSIS IDs" data-whichtable="analysis_vars_im">HIDE ANALYSIS IDs</a>
				<table id="analysis_vars_im">
					<tr class="no_remove">
						<th>Analysis ID (SG _ unique ID)</th>
						<th>Indicator</th>
						<th>Measure</th>
						<th>Unique IDs ( >1 = duplicate)</th>
						<th>Net Effects</th>
						<th>Outcome Type</th>
						<th>Effectiveness</th>
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				
				</table>
				
				<h4 id="study_design_label">Study Designs:</h4>
				<h5>Study Design</h5>
				<select class="analysis_study_design">
					<option value="-1"> -- Select Study Design -- </option>
					<option value="1"> 1 = Intervention Evaluation </option>
					<option value="2"> 2 = Associational Study </option>
				</select>
				<br />
				<h5>Study Design High-Risk</h5>
				<select class="analysis_study_design_hr">Study Design High-Risk
					<option value="-1"> -- Select Study Design (HR) -- </option>
					<option value="1"> 1 = Intervention Evaluation </option>
					<option value="2"> 2 = Associational Study </option>
				</select>
				<span>
					<a id="save_analysis_studydesign" class="button alignright analysis_save" data-whichvars="analysis_study_design" data-whichsave="save_studygroup_vars" data-whichmsg="studydesign_msg">SAVE STUDY DESIGNS</a>
					<div id="studydesign_msg" class="save_analysis_msg"></div>
				</span>
				
				
				<h4>Net Effects or Associations:</h4>
				<a id="hide_analysis_effect_table" class="button" data-whichlabel="NET EFFECTS" data-whichtable="analysis_vars_effect">HIDE NET EFFECTS</a>
				<a id="show_effect_algorithm" class="button alignright" data-whichalgorithm="analysis_effect_algorithm">SHOW ALGORITHM DETAILS</a>
				<div id="analysis_effect_algorithm" class="show_algorithm">
					<h4>Effect or Association Direction</h4>
					<p>
						IF there are no duplicates for an I-M dyad {<br />
							&nbsp;Net Effect = I-M effect/association direction;<br />
						} ELSE { <br />
							&nbsp;DROPDOWN to save direction value to analysis_id<br />
							&nbsp;TODO: implement intermediate algorithm to auto-generate if duplicates<br />
						}<br />
					</p>
				</div>
				
				<table id="analysis_vars_effect">
					<tr class="no_remove">
						<th>Analysis ID (Study ID _ seq _ unique ID)</th>
						<th>Indicator</th>
						<th>Measure</th>		
						<th>Net Effect or Association</th>		
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				
				</table>
				<span>
					<a id="save_analysis_effects" class="button alignright analysis_save" data-whichvars="net_effects" data-whichsave="save_analysis_vars" data-whichmsg="net_effects_msg">SAVE EFFECTS</a>
					<div id="net_effects_msg" class="save_analysis_msg"></div>
				</span>
				<br />
				
				<h4>Populations:</h4>
				<a id="hide_analysis_population_table" class="button" data-whichlabel="POPULATIONS" data-whichtable="analysis_vars_population">HIDE POPULATIONS</a>
				<table id="analysis_vars_population">
					<tr class="no_remove">
						<th>Analysis ID (Study ID _ seq _ unique ID)</th>
						<th>Unique IDs - HR</th>
						<th>Evaluation Population</th>
						<th>Result Subpopulation YN?</th>
						<th>Result Subpopulation</th>		
						<th>Population or Subpopulation</th>		
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				
				</table>
				
				<h4>Effectiveness, High Risk Populations:</h4>
				<a id="hide_analysis_hrpops_table" class="button">HIDE HR POPS</a>
				<table id="analysis_vars_effectiveness_hr">
					<tr class="no_remove">
						<th>Analysis ID</th>
						<th>Population or Subpopulation</th>
						<th>Indicator</th>
						<th>Measure</th>		
						<th>Effectiveness, HR Pops</th>	
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				
				</table>
				
				<h4>Domestic/International:</h4> 
				<table id="analysis_vars_domestic">
					<tr class="no_remove">
						<th>Analysis ID</th>
						<th>Value</th>
						<th>Description</th>	
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				
				</table>

				<h4>Multi-Component:</h4> 
				<table id="analysis_vars_multi_component">
					<tr class="no_remove">
						<th>Analysis ID</th>
						<th>Value</th>
						<th>Description</th>
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				
				</table>
				
				<h4>Complex:</h4> 
				<table id="analysis_vars_complex">
					<tr class="no_remove">
						<th>Analysis ID</th>
						<th>Value</th>
						<th>Description</th>				
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				
				</table>
				
				<h4>Participation or Exposure:</h4> 
				<table id="analysis_vars_participation">
					<tr class="no_remove">
						<th>Analysis ID</th>
						<th>Value</th>
						<th>Description</th>
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				
				</table>
				
				<h4>High-risk African American population:</h4> 
				<table id="analysis_vars_hr_black">
					<tr class="no_remove">
						<th>Analysis ID</th>
						<th>Value</th>
						<th>Description</th>
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				</table>
				
				<h4>High-risk Asian population:</h4> 
				<table id="analysis_vars_hr_asian">
					<tr class="no_remove">
						<th>Analysis ID</th>
						<th>Value</th>
						<th>Description</th>
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				</table>
				
				<h4>High-risk Native American/ Alaskan Native population:</h4> 
				<table id="analysis_vars_hr_nativeamerican">
					<tr class="no_remove">
						<th>Analysis ID</th>
						<th>Value</th>
						<th>Description</th>
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				</table>
				
				<h4>High-risk Native Hawaiian/ Pacific Islander population:</h4> 
				<table id="analysis_vars_hr_pacificislander">
					<tr class="no_remove">
						<th>Analysis ID</th>
						<th>Value</th>
						<th>Description</th>
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				</table>
				
				<h4>High-risk Hispanic/ Latino population:</h4> 
				<table id="analysis_vars_hr_hispanic">
					<tr class="no_remove">
						<th>Analysis ID</th>
						<th>Value</th>
						<th>Description</th>
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				</table>
				
				<h4>High-risk Lower Income population:</h4> 
				<table id="analysis_vars_hr_lowincome">
					<tr class="no_remove">
						<th>Analysis ID</th>
						<th>Value</th>
						<th>Description</th>
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				</table>
				
				<h4>Potential Population Reach:</h4> 
				<table id="analysis_vars_popreach">
					<tr class="no_remove">
						<th>Analysis ID</th>
						<th>Value</th>
						<th>Description</th>
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				</table>
				
				<h4>Potential High-risk Population Reach:</h4> 
				<table id="analysis_vars_hr_popreach">
					<tr class="no_remove">
						<th>Analysis ID</th>
						<th>Value</th>
						<th>Description</th>
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				</table>
				
				<h4>State:</h4> 
				<!--<table id="analysis_vars_state">
					<tr class="no_remove">
						<th>Analysis ID</th>
						<th>Value</th>
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				</table>-->
				<select class="state">
					<option value="-1"> -- Select State -- </option>
					<option value="1">1 - Fully complete</option>
					<option value="2">2 - Partially complete</option>
					<option value="999">999 - Insufficient Information</option>
				</select>
				<span>
					<a id="save_analysis_state" class="button alignright analysis_save" data-whichvars="state" data-whichsave="save_studygroup_vars" data-whichmsg="state_msg">SAVE STATE</a>
					<div id="state_msg" class="save_analysis_msg"></div>
				</span>
				<!--<span>
					<a id="save_analysis_state" class="button alignright analysis_save" data-whichvars="state" data-whichsave="save_analysis_vars" data-whichmsg="state_msg">SAVE STATE</a>
					<div id="state_msg" class="save_analysis_msg"></div>
				</span>-->
				<br />
				
				<h4>Quality:</h4> 
				<!--<table id="analysis_vars_quality">
					<tr class="no_remove">
						<th>Analysis ID</th>
						<th>Value</th>
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				</table>-->
				<select class="quality">
					<option value="-1"> -- Select Quality -- </option>
					<option value="1">1 - High</option>
					<option value="2">2 - Low</option>
					<option value="999">999 - Insufficient Information</option>
				</select>
				<span>
					<a id="save_analysis_quality" class="button alignright analysis_save" data-whichvars="quality" data-whichsave="save_studygroup_vars" data-whichmsg="quality_msg">SAVE QUALITY</a>
					<div id="quality_msg" class="save_analysis_msg"></div>
				</span>
				<!--<span>
					<a id="save_analysis_quality" class="button alignright analysis_save" data-whichvars="quality" data-whichsave="save_analysis_vars" data-whichmsg="quality_msg">SAVE QUALITY</a>
					<div id="quality_msg" class="save_analysis_msg"></div>
				</span>-->
				<br />
				
				<h4>Inclusiveness:</h4> 
				<!--<table id="analysis_vars_inclusiveness">
					<tr class="no_remove">
						<th>Analysis ID</th>
						<th>Value</th>
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				</table>-->
				<select class="inclusiveness">
					<option value="-1"> -- Select Inclusiveness -- </option>
					<option value="1">1 - Full</option>
					<option value="2">2 - Partial</option>
					<option value="3">3 - No</option>
					<option value="999">999 - Insufficient Information</option>
				</select>
				<span>
					<a id="save_analysis_inclusiveness" class="button alignright analysis_save" data-whichvars="inclusiveness" data-whichsave="save_studygroup_vars" data-whichmsg="inclusiveness_msg">SAVE INCLUSIVENESSS</a>
					<div id="inclusiveness_msg" class="save_analysis_msg"></div>
				</span>
				<!--<span>
					<a id="save_analysis_inclusiveness" class="button alignright analysis_save" data-whichvars="inclusiveness" data-whichsave="save_analysis_vars" data-whichmsg="inclusiveness_msg">SAVE INCLUSIVENESSS</a>
					<div id="inclusiveness_msg" class="save_analysis_msg"></div>
				</span>-->
				<br />
				
				<h4>Access:</h4> 
				<!--<table id="analysis_vars_access">
					<tr class="no_remove">
						<th>Analysis ID</th>
						<th>Value</th>
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				</table>-->
				<select class="access">
					<option value="-1"> -- Select Access -- </option>
					<option value="1">1 - Strong</option>
					<option value="2">2 - Weak</option>
					<option value="999">999 - Insufficient Information</option>
				</select>
				<span>
					<a id="save_analysis_access" class="button alignright analysis_save" data-whichvars="access" data-whichsave="save_studygroup_vars" data-whichmsg="access_msg">SAVE ACCESS</a>
					<div id="access_msg" class="save_analysis_msg"></div>
				</span>
				<!--<span>
					<a id="save_analysis_access" class="button alignright analysis_save" data-whichvars="access" data-whichsave="save_analysis_vars" data-whichmsg="access_msg">SAVE ACCESS</a>
					<div id="access_msg" class="save_analysis_msg"></div>
				</span>-->
				<br />
				
				<h4>Size:</h4> 
				<!--<table id="analysis_vars_size">
					<tr class="no_remove">
						<th>Analysis ID</th>
						<th>Value</th>
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				</table>-->
				<select class="size">
					<option value="-1"> -- Select Size -- </option>
					<option value="1">1 - Large</option>
					<option value="2">2 - Small</option>
					<option value="999">999 - Insufficient Information</option>
				</select>
				<span>
					<a id="save_analysis_size" class="button alignright analysis_save" data-whichvars="size" data-whichsave="save_studygroup_vars" data-whichmsg="size_msg">SAVE SIZE</a>
					<div id="size_msg" class="save_analysis_msg"></div>
				</span>
				<!--<span>
					<a id="save_analysis_size" class="button alignright analysis_save" data-whichvars="size" data-whichsave="save_analysis_vars" data-whichmsg="size_msg">SAVE SIZE</a>
					<div id="size_msg" class="save_analysis_msg"></div>
				</span>-->
				<br />
				
			</div>
		
		</div>
	<?php
}