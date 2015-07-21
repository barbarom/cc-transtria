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

	//get data related to the form
	$dd_singeton_options = cc_transtria_get_singleton_dropdown_options(); //all options for singleton dropdowns
	
	//TODO: think about whether we want to populate these in php or in js..
	
	?>

	<form>
		<div class="bottom_margin">			
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
	

	
	<div class="tabs">
		
	   <div class="tab primary_tab">
		   <input type="radio" id="tab-1" name="tab-group-1" checked>
		   <label for="tab-1" class="primary_tab_label">Basic Info</label>
		   
		   <div class="content">
				<table style="width:100%;">
					<tr>
						<td colspan="2" style="text-align:center;padding:20px;">
							<button>SHOW ENDNOTE CITATION DATA</button>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="inner_table_header">
							GENERAL
						</td>
					</tr>
					<tr>
						<td>
							<table>
								<tr>
									<td>
										Abstractor:
									</td>
									<td>
										<select>
											<option value="">---Select---</option>
											
											<?php //$dd_singleton_options are indexed by the div id - "abstractor", for example
												foreach( $dd_singeton_options['abstractor'] as $k => $v ){
												echo '<option value="' . $k . '">' . $v->descr . '</option>';
											
											} ?>
										</select>
									</td>
								</tr>
								<tr>
									<td>
										Start Date/Time:
									</td>
									<td>
										<input id="abstractorstarttime" type="text" >
									</td>
								</tr>
								<tr>
									<td>
										Stop Date/Time:
									</td>
									<td>
										<input id="abstractorstoptime" type="text" />
									</td>
								</tr>					
							</table>
						</td>
						<td>
							<table>
								<tr>
									<td>
										Validator:
									</td>
									<td>
										<select>
											<option value="">---Select---</option>
										</select>
									</td>
								</tr>
								<tr>
									<td>
										Start Date/Time:
									</td>
									<td>
										<input id="validatorstarttime" type="text" />
									</td>
								</tr>
								<tr>
									<td>
										Stop Date/Time:
									</td>
									<td>
										<input id="validatorstoptime" type="text" />
									</td>
								</tr>					
							</table>			
						</td>			
					</tr>
				</table>  
		   </div> 
	   </div>
		
	   <div class="tab primary_tab">
		   <input type="radio" id="tab-2" name="tab-group-1">
		   <label for="tab-2" class="primary_tab_label">Population</label>
		   
		   <div class="content">
			   <?php 
				//render pops tab in all its glory
				cc_transtria_render_populations_tab(); ?>
		   </div> 
	   </div>
		
		<div class="tab primary_tab">
		   <input type="radio" id="tab-3" name="tab-group-1">
		   <label for="tab-3" class="primary_tab_label">Intervention/Partnerships</label>
		 
		   <div class="content">
			   stuff 3
		   </div> 
	   </div>

		<div class="tab primary_tab">
		   <input type="radio" id="tab-4" name="tab-group-1">
		   <label for="tab-4" class="primary_tab_label">Results</label>
		 
		   <div class="content">
			   stuff 4
		   </div> 
	   </div>   
	</div>	
	
	

	
	</form>
	<script type='text/javascript'>
 
		jQuery('#abstractorstarttime').datetimepicker();
		jQuery('#abstractorstoptime').datetimepicker();
		jQuery('#validatorstarttime').datetimepicker();
		jQuery('#validatorstoptime').datetimepicker();
		
	</script>
	<?php
}