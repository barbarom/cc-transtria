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

	$studies_data = cc_transtria_get_singleton_dropdown_options( );
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
			
			<a id="get_studies_by_group" class="button">GET ANALYSIS FOR STUDY GROUP</a>
			<a id="run_analysis" class="button">RE-RUN ANALYSIS FOR STUDY GROUP</a>
		
		</div>
	
		<h3>Intermediate Variables</h3>
		
		<h4>Indicator-Measure Dyad(s):</h4>
		<table id="intermediate_vars">
			<tr class="no_remove">
				<th>Study ID</th>
				<th>Unique ID (StudyID_seq_uniqueID</th>
				<th>Indicator</th>
				<th>Measure</th>		
			</tr>
			<tr id="data_parent" class="no_remove"></tr>
		
		</table>
		
		
	<?php
}