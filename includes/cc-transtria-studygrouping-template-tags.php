<?php 
/**
 * CC Transtria Study Grouping Template Tags
 *
 * @package   CC Transtria Extras
 * @author    CARES staff
 * @license   GPL-2.0+
 * @copyright 2015 CommmunityCommons.org
 */

/**
 * Output logic for the study grouping page. includes the wrapper pieces.
 *
 * @since   1.0.0
 * @return 	outputs html
 */
function cc_transtria_render_studygrouping_page(){

	$study_group_ids = cc_transtria_get_study_groupings();
	
	?>
		<div class="studygrouping_messages">
			<span class="usr-msg"></span>
			<span class="spinny"></span>
		</div>
		
		<div id="studygrouping_choices">
			<select id="StudyGroupingIDList" style="">
				<option value="-1"> -- Select Study Group -- </option>
			<?php
				foreach( $study_group_ids as $key => $val ){
					
					echo "<option value='" . (int)$val['EPNP_ID'] . "'>" . $val['EPNP_ID'] . "</option>";
					
				}
			?>
			</select>
			
			<a id="get_studygroup_data" class="button">GET STUDY GROUP DATA</a>
		
		</div>
	
		<h3>Study Group Variables</h3>
		
		
		
	<?php
}