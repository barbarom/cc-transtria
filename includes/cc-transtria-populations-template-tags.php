<?php 
/**
 * CC Transtria Populations Tab Template Tags
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
function cc_transtria_render_populations_tab(){
	?>
		POPULATIONS TAB FUNCTION
	<?php
	
	cc_transtria_render_populations_header();
}


/**
 * Renders the header of the populations tab (before any sub tabs)
 *
 */
function cc_transtria_render_populations_header(){

	?>
	<div id="population_tabs">
		<label>Sample size available?:</label>
		<span id="sample_size_available">
			<input type="radio" value="Y" name="sample_size_available">	Yes
			<input type="radio" value="N" name="sample_size_available">	No
		</span>
		<br>


		
		
		
		
		
		
		
		
		
	</div>
		
	<?php

}