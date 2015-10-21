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
 * Output logic for the analysis page. includes the wrapper pieces.
 * Question building is handled separately
 *
 * @since   1.0.0
 * @return 	outputs html
 */
function cc_transtria_render_analysis_form(){

	$studies_data = cc_transtria_get_singleton_dropdown_options( );
	$study_group_ids = cc_transtria_get_study_groupings();
	
	//var_dump( $studies_data );
	?>
		<div class="analysis_messages">
			<span class="usr-msg"></span>
			<span class="spinny"></span>
		</div>
		
		<select id="StudyGroupingIDList" style="">
		<?php
			foreach( $study_group_ids as $key => $val ){
				echo "<option value='" . (int)$val['EPNP_ID'] . "'>" . $val['EPNP_ID'] . "</option>";
				
			}
		?>
		</select>
		
		<a id="get_studies_by_group" class="button">GET THE THINGS</a>
		<a id="run_analysis" class="button">RE-RUN ANALYSIS, DISPLAY NEW THINGS</a>
	
		<br />
	
		ANALYSIS PAGE
	<?php
}