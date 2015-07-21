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
	
<div id="tabs">
  <ul>
    <li><a href="#tabs-1" class="tabhead">Basic Info</a></li>
    <li><a href="#tabs-2" class="tabhead">Population</a></li>
    <li><a href="#tabs-3" class="tabhead">Intervention/Partnerships</a></li>
	<li><a href="#tabs-4" class="tabhead">Results</a></li>
  </ul>
  <div id="tabs-1">
	<table style="width:100%;">
		<tr>
			<td colspan="2" style="text-align:center;">
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
							</select>
						</td>
					</tr>
					<tr>
						<td>
							Start Date/Time:
						</td>
						<td>
							<input id="abstractorstarttime" type="text" />
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
  <div id="tabs-2">
    <p>Morbi tincidunt, dui sit amet facilisis feugiat, odio metus gravida ante, ut pharetra massa metus id nunc. Duis scelerisque molestie turpis. Sed fringilla, massa eget luctus malesuada, metus eros molestie lectus, ut tempus eros massa ut dolor. Aenean aliquet fringilla sem. Suspendisse sed ligula in ligula suscipit aliquam. Praesent in eros vestibulum mi adipiscing adipiscing. Morbi facilisis. Curabitur ornare consequat nunc. Aenean vel metus. Ut posuere viverra nulla. Aliquam erat volutpat. Pellentesque convallis. Maecenas feugiat, tellus pellentesque pretium posuere, felis lorem euismod felis, eu ornare leo nisi vel felis. Mauris consectetur tortor et purus.</p>
  </div>
  <div id="tabs-3">
    <p>Mauris eleifend est et turpis. Duis id erat. Suspendisse potenti. Aliquam vulputate, pede vel vehicula accumsan, mi neque rutrum erat, eu congue orci lorem eget lorem. Vestibulum non ante. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Fusce sodales. Quisque eu urna vel enim commodo pellentesque. Praesent eu risus hendrerit ligula tempus pretium. Curabitur lorem enim, pretium nec, feugiat nec, luctus a, lacus.</p>
    <p>Duis cursus. Maecenas ligula eros, blandit nec, pharetra at, semper at, magna. Nullam ac lacus. Nulla facilisi. Praesent viverra justo vitae neque. Praesent blandit adipiscing velit. Suspendisse potenti. Donec mattis, pede vel pharetra blandit, magna ligula faucibus eros, id euismod lacus dolor eget odio. Nam scelerisque. Donec non libero sed nulla mattis commodo. Ut sagittis. Donec nisi lectus, feugiat porttitor, tempor ac, tempor vitae, pede. Aenean vehicula velit eu tellus interdum rutrum. Maecenas commodo. Pellentesque nec elit. Fusce in lacus. Vivamus a libero vitae lectus hendrerit hendrerit.</p>
  </div>
  <div id="tabs-4">
    <p>Mauris eleifend est et turpis. Duis id erat. Suspendisse potenti. Aliquam vulputate, pede vel vehicula accumsan, mi neque rutrum erat, eu congue orci lorem eget lorem. Vestibulum non ante. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Fusce sodales. Quisque eu urna vel enim commodo pellentesque. Praesent eu risus hendrerit ligula tempus pretium. Curabitur lorem enim, pretium nec, feugiat nec, luctus a, lacus.</p>
    <p>Duis cursus. Maecenas ligula eros, blandit nec, pharetra at, semper at, magna. Nullam ac lacus. Nulla facilisi. Praesent viverra justo vitae neque. Praesent blandit adipiscing velit. Suspendisse potenti. Donec mattis, pede vel pharetra blandit, magna ligula faucibus eros, id euismod lacus dolor eget odio. Nam scelerisque. Donec non libero sed nulla mattis commodo. Ut sagittis. Donec nisi lectus, feugiat porttitor, tempor ac, tempor vitae, pede. Aenean vehicula velit eu tellus interdum rutrum. Maecenas commodo. Pellentesque nec elit. Fusce in lacus. Vivamus a libero vitae lectus hendrerit hendrerit.</p>
  </div>  
</div>	
	
	
	
	

	
	</form>
	<script type='text/javascript'>
		jQuery('#abstractorstarttime').datetimepicker();
		jQuery('#abstractorstoptime').datetimepicker();
		jQuery('#validatorstarttime').datetimepicker();
		jQuery('#validatorstoptime').datetimepicker();
		//Restores normal scroll function when clicking anywhere but tabs.
		jQuery('html').click(function() {
			jQuery('html, body').css({
				'overflow': 'auto',
				'height': 'auto'
			});	
		});
		//Prevents the page from scrolling when tabs are clicked.
		jQuery( ".tabhead" ).click(function() {
			jQuery('html, body').css({
			'overflow': 'hidden',
			'height': '100%'
			});			
		});  
		
		
	</script>
	<?php
}