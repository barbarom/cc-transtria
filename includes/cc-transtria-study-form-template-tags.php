<?php 
/**
 * CC Transtria Study Form Template Tags
 *
 * @package   CC Transtria Extras Extras
 * @author    CARES staff
 * @license   GPL-2.0+
 * @copyright 2015 CommmunityCommons.org
 */

//TODO: move to functions, if kept.
/** 
 * What fiscal year is it?
 *
 * @return int 4-digit Year
 */

/**
 * Output logic for the form. includes the wrapper pieces.
 * Question building is handled separately
 *
 * @since   1.0.0
 * @return 	outputs html
 */
function cc_transtria_render_form(){
	?>

	<form>
		<div class="mb">			
			<label for="studyid"><strong>Study ID</strong></label>
			<select id="studyid">
			  <option>1</option>
			  <option>2</option>
			  <option>3</option>
			  <option>4</option>
			  <option>5</option>
			</select>
			<span>
				<button type="button">Submit</button>
			</span>		
		</div>
		<div id="study_basic_info">
			<p><strong>Study Grouping ID:</strong></p>
		
			<p>Title: Are physical education-related state policies and schools' physical education requirement related to children's physical activity and obesity?<br />
			Author: Kim, J.<br />
			Dates: Jun 2012</p>
		</div>
		<div role="navigation" id="object-nav" class="item-list-tabs no-ajax clear">
			<ul>

				
				<li class="current selected" id="nav-basic-info-groups-li"><a href="#basic_info" id="nav-basic-info" data-toggle="tab">Basic Info</a></li>
				<li id="nav-population-groups-li"><a href="#population" id="nav-population" data-toggle="tab">Population</a></li>
				<li id="members-intevention_partnership-li"><a href="#intevention_partnership" id="intevention_partnership" data-toggle="tab">Intervention/Partnerships</a></li>
				<li id="nav-results-groups-li"><a href="#results" id="nav-results" data-toggle="tab">Results</a></li>

				
			</ul>
		</div>
		<ul class="nav nav-tabs">
			<li class="active"><a href="#basic_info" data-toggle="tab">Basic Info</a></li>
			<li><a href="#population" data-toggle="tab">Population</a></li>
			<li><a href="#intevention_partnership" data-toggle="tab">Intervention/Partnerships</a></li>
			<li><a href="#results" data-toggle="tab">Results</a></li>
		</ul>
		<!-- Tab panes -->
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane fade in active" id="basic_info">1</div>
			<div role="tabpanel" class="tab-pane fade" id="population">2</div>
			<div role="tabpanel" class="tab-pane fade" id="intevention_partnership">3</div>
			<div role="tabpanel" class="tab-pane fade" id="results">4</div>			
		</div>		
	</form>
	<?php
}