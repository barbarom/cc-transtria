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
function cc_transtria_render_populations_tab( $field_data ){

	//$dd_singleton_options = $field_data['dd_singleton_options'];
	//var_dump ($dd_singleton_options);
	cc_transtria_render_populations_header( $field_data );
}


/**
 * Renders the header of the populations tab (before any sub tabs)
 *
 */
function cc_transtria_render_populations_header( $field_data ){

	$dd_singleton_options = $field_data['dd_singleton_options'];
	$dd_multiple_options_pops = $field_data['dd_multiple_options_pops'];
	
	var_dump( $dd_multiple_options_pops ); //ok!
	//var_dump( cc_transtria_get_multiple_dropdown_ids_populations( ) ); //default all pops
	

	?>
	<div id="population_tabs">
		<label>Sample size available?:</label>
		<span id="sample_size_available">
			<input type="radio" value="Y" name="sample_size_available">	Yes
			<input type="radio" value="N" name="sample_size_available">	No
		</span>
		<br>
		<label>Is Sample size an estimate?:</label>
		<span id="sample_estimate">
			<input type="radio" value="Y" name="sample_estimate">Yes
			<input type="radio" value="N" name="sample_estimate">No
		</span>
		<br />
		<label>Unit of Analysis</label>
		<select id="unit_of_analysis">
			<option value="">---Select---</option>						
			<?php //$dd_singleton_options are indexed by the div id - "abstractor", for example
				foreach( $dd_singleton_options['unit_of_analysis'] as $k => $v ){
				echo '<option value="' . $k . '">' . $v->descr . '</option>';
			
			} ?>
		</select>
		<br />
		<div class="not-reported">
			<label>Unit of Analysis Not Reported</label>
			<input id="unitanalysis_notreported" type="checkbox">
		</div>
		<br>


		
		
		
		
		
		
		
		
		
	</div>
		
	<?php

}