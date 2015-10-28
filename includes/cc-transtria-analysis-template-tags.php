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
			<select id="StudyGroupingIDList" style="">
				<option value="-1"> -- Select Study Group -- </option>
			<?php
				foreach( $study_group_ids as $key => $val ){
					
					echo "<option value='" . (int)$val['EPNP_ID'] . "'>" . $val['EPNP_ID'] . "</option>";
					
				}
			?>
			</select>
			
			<a id="get_vars_by_group" class="button">GET ALL VARS FOR STUDY GROUP</a>
			<a id="run_intermediate_analysis" class="button">RUN INTERMEDIATE ANALYSIS FOR STUDY GROUP</a>
			<a id="run_analysis" class="button">RUN ANALYSIS FOR STUDY GROUP</a>
		
		</div>
		
		<label class="analysis_tab_label active" data-whichanalysistab="intermediate_vars_content" for="intermediate_tab">Intermediate Data</label>
		<label class="analysis_tab_label" data-whichanalysistab="analysis_vars_content" for="analysis_tab">Analysis Data</label>
		
		<div id="analysis_content">
			<div id="intermediate_vars_content" class="single_analysis_content">
				<h3>Intermediate Variables</h3>
				
				<h4>Indicator-Measure Dyad(s):</h4>
				<a id="hide_im_table" class="button">HIDE I-M DYADS</a>
				<table id="intermediate_vars_im">
					<tr class="no_remove">
						<th>Study ID</th>
						<th>Unique ID (Study ID _ seq _ unique ID)</th>
						<th>Indicator</th>
						<th>Measure</th>		
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				
				</table>
				
				<h4>Study Design</h4>
				<a id="hide_design_table" class="button">HIDE STUDY DESIGN</a>
				<table id="intermediate_vars_design">
					<tr class="no_remove">
						<th>Study ID</th>
						<th>Study Design</th>
						<th>Study Design, Other</th>		
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				
				</table>
				
				<h4>Effect or Association Direction:</h4>
				<a id="hide_direction_table" class="button">HIDE I-M DIRECTIONS</a>
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
						<th>Unique ID (Study ID _ seq _ unique ID)</th>
						<th>Indicator</th>
						<th>Measure</th>		
						<th>Outcome Type</th>		
						<th>Effect or Association Direction</th>		
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				
				</table>
				
			</div>
			
			<div id="analysis_vars_content" class="single_analysis_content">
			
				<h3>Analysis Variables</h3>
				
				<h4>Indicator-Measure Dyad(s):</h4>
				<a id="hide_analysis_im_table" class="button">HIDE I-M DYADS</a>
				<table id="analysis_vars_im">
					<tr class="no_remove">
						<th>Study Grouping ID</th>
						<th>Analysis ID (SG _ unique ID)</th>
						<th>Indicator</th>
						<th>Measure</th>	
						<th>Duplicate IDs</th>	
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				
				</table>
				
				<select id="analysis_study_design">Study Design
					<option value="-1"> -- Select Study Design -- </option>
					<option value="-1"> 1 = Intervention Evaluation </option>
					<option value="-1"> 2 = Associational Study </option>
				</select>
				
				<h4>Net Effects or Associations:</h4>
				<a id="hide_analysis_effect_table" class="button">HIDE I-M DIRECTIONS</a>
				<table id="analysis_vars_effect">
					<tr class="no_remove">
						<th>Analysis ID (Study ID _ seq _ unique ID)</th>
						<th>Indicator</th>
						<th>Measure</th>		
						<th>Net Effect or Association</th>		
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				
				</table>
				
				<h4>Effectiveness:</h4>
				<a id="hide_analysis_effectiveness_table" class="button">HIDE EFFECTIVENESS</a>
				<table id="analysis_vars_effectiveness">
					<tr class="no_remove">
						<th>Population or Subpopulation</th>
						<th>Indicator</th>
						<th>Measure</th>		
						<th>Effectiveness</th>		
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				
				</table>
				
				<h4>Effectiveness, High Risk Populations:</h4>
				<a id="hide_analysis_hrpops_table" class="button">HIDE HR POPS</a>
				<table id="analysis_vars_hrpops">
					<tr class="no_remove">
						<th>Population or Subpopulation</th>
						<th>Indicator</th>
						<th>Measure</th>		
						<th>Effectiveness, HR Pops</th>		
					</tr>
					<tr id="data_parent" class="no_remove"></tr>
				
				</table>
				
			</div>
		
		</div>
	<?php
}