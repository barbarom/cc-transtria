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
	<form class="form-inline">
		<div class="form-group">
			
			<div class="input-group">
			<label for="studyid" class="sr-only">Study ID</label>
				<select id="studyid" class="form-control selectwidthauto">
				  <option>1</option>
				  <option>2</option>
				  <option>3</option>
				  <option>4</option>
				  <option>5</option>
				</select>
				<span class="input-group-btn">
					<button type="button" class="btn btn-primary">Primary</button>
				</span>
			</div>
		</div>
	  
	</form>
	<?php
}