<?php 
/**
 * CC Transtria Assignments Template Tags
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
function cc_transtria_render_assignments_form(){

	$study_group_ids = cc_transtria_get_study_groupings();
	

	?>
		ASSIGNMENTS PAGE
		
		
		
		
		
		
		
		
		
		
	<select id="StudyGroupingIDAssignment" style="">
	<?php
		foreach( $study_group_ids as $key => $val ){
			echo "<option value='" . (int)$val['EPNP_ID'] . "'>" . $val['EPNP_ID'] . "</option>";
			
		}
	?>
	</select>

	<label class="table-label">View Studies Completed and In Progress; Assign Study Groupings</label>

	<div class="filters">
		<h3>Filter and Search</h3>
		<label>Filter by Phase</label>
		<button id="phase1_filter">Show Phase 1 only</button>
		<button id="phase2_filter">Show Phase 2 only</button>
		<button id="phaseall_filter">Show All Phases</button>

		<div class="search">
			<label>Search Text in Title, Author</label>
			<input id="search_text" type="text"></input>
			<button class="search_button">Search (just Author, Title at the moment)</button>
			<button class="clear_search">Clear Search</button>
			<div class="no-results">No Results Found</div>
		</div>

		<div class="search-strategies">
			<label>Filter by Strategy</label>
			<span id="assignment-strategy"></span>
			<button class="clear_strategy">Clear Strategy Filter</button>
		</div>


	</div> 

	
	<table id="assignment-table" class="tablesorter">
		<thead>
			<tr>
				<th>Study Grouping ID<br>&nbsp;&nbsp;&#x25BC;</th>
				<th>Study ID<br>&nbsp;&nbsp;&#x25BC;</th>
				<th class="th-endnote">EndNote - rec number<br>&nbsp;&nbsp;&#x25BC;</th>
				<th>Phase<br>&nbsp;&nbsp;&#x25BC;</th>
				<th>Author<br>&nbsp;&nbsp;&#x25BC;</th>
				<th>Date<br>&nbsp;&nbsp;&#x25BC;</th>
				<th class="th-title">Title<br>&nbsp;&nbsp;&#x25BC;</th>

				<th>Abstraction Complete?<br>&nbsp;&nbsp;&#x25BC;</th>
				<th>Study Grouping Assigned<br>&nbsp;&nbsp;&#x25BC;</th>
				<th>Study Validation Complete<br>&nbsp;&nbsp;&#x25BC;</th>

				<th>Ready for Analysis?<br>&nbsp;&nbsp;&#x25BC;</th>
				<th>Study Grouping Complete<br>&nbsp;&nbsp;&#x25BC;</th>
			</tr>
		</thead>

		<tbody>



<!-- populated with ajax/json data in dynamic_components.js -->
		</tbody>
	</table>


	<div class="assign-save-button">
		<button onclick="save_assignment_data()">Save Assignments</button>
	</div>

	<div id="get-next-endnote">
		<label class="table-label">Get Next Study</label>

		<br />
		<select id="next_phase1"><label>Phase 1</label></select>
		<button id="phase1_submit">Start Next Phase 1 Study</button>
		<br />
		<select id="next_phase2"><label>Phase 2</label></select>
		<button id="phase2_submit">Start Next Phase 2 Study</button>


	</div>



	<?php
}