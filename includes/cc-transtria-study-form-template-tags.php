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
	

	
<div class="tabs">
    
   <div class="tab">
       <input type="radio" id="tab-1" name="tab-group-1" class="noshow" checked>
       <label for="tab-1">Basic Info</label>
       
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
			<table>			
				<tr>
					<td colspan="3" class="inner_table_header">
						STUDY
					</td>
				</tr>	
				<tr>
					<td style="width:33%;">
						EndNote ID:
					</td>
					<td style="width:33%;">
						<select>
							<option value="">---Select---</option>
						</select>
					</td>
					<td style="width:33%;">
						<input id="ed" type="text" readonly />					
					</td>					
				</tr>
				<tr>
					<td style="width:33%;">
						Original PubMed ID (Accession Num):
					</td>
					<td style="width:33%;">
						<input type="text" readonly />
					</td>
					<td style="width:33%;">
											
					</td>					
				</tr>
				<tr>
					<td style="width:33%;">
						PubMed ID:
					</td>
					<td style="width:33%;">
						<input type="text" />
					</td>
					<td style="width:33%;">
									
					</td>					
				</tr>				
				<tr>
					<td style="width:33%;">
						<span style="margin-left:20px;font-style:italic;">PubMedID not reported:</span>
					</td>
					<td style="width:33%;">
						<input type="checkbox" />
					</td>
					<td style="width:33%;">
										
					</td>					
				</tr>
				<tr>
					<td style="width:33%;">
						Original Search tool type:
					</td>
					<td style="width:33%;">
						<input type="text" readonly />
					</td>
					<td style="width:33%;">
										
					</td>					
				</tr>
				<tr>
					<td style="width:33%;">
						Search tool type:
					</td>
					<td style="width:33%;">
						<select>
							<option value="">---Select---</option>
						</select>
					</td>
					<td style="width:33%;">
										
					</td>					
				</tr>
				<tr>
					<td style="width:33%;">
						Original Search tool name:
					</td>
					<td style="width:33%;">
						<input type="text" readonly />
					</td>
					<td style="width:33%;">
											
					</td>					
				</tr>
				<tr>
					<td style="width:33%;">
						Search tool name:
					</td>
					<td style="width:33%;">
						<select>
							<option value="">---Select---</option>
						</select>
					</td>
					<td style="width:33%;">
						Other search tool: <input id="ed" type="text" />					
					</td>					
				</tr>				
			</table>
			<table>			
				<tr>
					<td colspan="3" class="inner_table_header">
						FUNDING
					</td>
				</tr>
				<tr>
					<td style="width:33%;">
						Grant or Contract #:
					</td>
					<td style="width:33%;">
						<input type="text" />
					</td>
					<td style="width:33%;">
									
					</td>					
				</tr>				
				<tr>
					<td style="width:33%;">
						<span style="margin-left:20px;font-style:italic;">Grant or Contract # not reported:</span>
					</td>
					<td style="width:33%;">
						<input type="checkbox" />
					</td>
					<td style="width:33%;">
										
					</td>					
				</tr>
				<tr>
					<td style="width:33%;">
						Amount of funding:
					</td>
					<td style="width:33%;">
						<input type="text" />
					</td>
					<td style="width:33%;">
									
					</td>					
				</tr>				
				<tr>
					<td style="width:33%;">
						<span style="margin-left:20px;font-style:italic;">Amount of funding not reported:</span>
					</td>
					<td style="width:33%;">
						<input type="checkbox" />
					</td>
					<td style="width:33%;">
										
					</td>					
				</tr>
				<tr>
					<td style="width:33%;">
						Source of funding:
					</td>
					<td style="width:33%;">
						<select>
							<option value="">---Select---</option>
						</select>					
						
					</td>
					<td style="width:33%;">
						Other funding source: <input type="text" />			
					</td>					
				</tr>				
				<tr>
					<td style="width:33%;">
						<span style="margin-left:20px;font-style:italic;">Source of funding not reported:</span>
					</td>
					<td style="width:33%;">
						<input type="checkbox" />
					</td>
					<td style="width:33%;">
										
					</td>					
				</tr>
				<tr>
					<td style="width:33%;">
						Domestic/International Funding Source:
					</td>
					<td style="width:33%;">
						Domestic: <input type="checkbox" /> International: <input type="checkbox" />
					</td>
					<td style="width:33%;">
									
					</td>					
				</tr>				
				<tr>
					<td style="width:33%;">
						Domestic funding source type:
					</td>
					<td style="width:33%;">
						<select>
							<option value="">---Select---</option>
						</select>	
					</td>
					<td style="width:33%;">
									
					</td>					
				</tr>				
				<tr>
					<td style="width:33%;">
						<span style="margin-left:20px;font-style:italic;">Domestic funding source type not reported:</span>
					</td>
					<td style="width:33%;">
						<input type="checkbox" />
					</td>
					<td style="width:33%;">
										
					</td>					
				</tr>
				<tr>
					<td style="width:33%;">
						Funding purpose:
					</td>
					<td style="width:33%;">
						<input type="text" />
					</td>
					<td style="width:33%;">
									
					</td>					
				</tr>				
				<tr>
					<td style="width:33%;">
						<span style="margin-left:20px;font-style:italic;">Funding purpose not reported:</span>
					</td>
					<td style="width:33%;">
						<input type="checkbox" />
					</td>
					<td style="width:33%;">
										
					</td>					
				</tr>				
			</table>
			<table>			
				<tr>
					<td colspan="3" class="inner_table_header">
						DESIGN
					</td>
				</tr>
				<tr>
					<td>
						Study Design:
					</td>
					<td>
						<select>
							<option value="">---Select---</option>
						</select>
					</td>
					<td>
						Other study design: <input type="text" readonly />
					</td>
				</tr>				
				<tr>
					<td style="width:33%;">
						Design Limitations:
					</td>
					<td colspan="2">
						<textarea style="width:97%"></textarea>
					</td>					
				</tr>				
				<tr>
					<td style="width:33%;">
						<span style="margin-left:20px;font-style:italic;">Design limitations not reported:</span>
					</td>
					<td style="width:33%;">
						<input type="checkbox" />
					</td>
					<td style="width:33%;">
										
					</td>					
				</tr>	
				<tr>
					<td style="width:33%;">
						Data Collection:
					</td>
					<td colspan="2">
						<textarea style="width:97%"></textarea>	
					</td>
				
				</tr>	
				<tr>
					<td style="width:33%;">
						Threat to internal validity?:
					</td>
					<td colspan="2">
						Yes <input class="radio" type="radio" name="threat" value="Yes" >  No <input class="radio" type="radio" name="threat" value="No" >
					</td>					
				</tr>
				<tr>
					<td style="width:33%;">
						Select type(s) of threats to internal validity:
					</td>
					<td style="width:33%;">
						<select>
							<option value="">---Select---</option>
						</select>
					</td>
					<td style="width:33%;">
								
					</td>					
				</tr>				
				<tr>
					<td style="width:33%;">
						<span style="margin-left:20px;font-style:italic;">Threat to internal validity not reported:</span>
					</td>
					<td style="width:33%;">
						<input type="checkbox" />
					</td>
					<td style="width:33%;">
										
					</td>					
				</tr>				
			</table>
       </div> 
   </div>
    
   <div class="tab">
       <input type="radio" id="tab-2" name="tab-group-1" class="noshow">
       <label for="tab-2">Population</label>
       
       <div class="content">
           stuff 2
       </div> 
   </div>
    
    <div class="tab">
       <input type="radio" id="tab-3" name="tab-group-1" class="noshow">
       <label for="tab-3">Intervention/Partnerships</label>
     
       <div class="content">
           stuff 3
       </div> 
   </div>

    <div class="tab">
       <input type="radio" id="tab-4" name="tab-group-1" class="noshow">
       <label for="tab-4">Results</label>
     
       <div class="content">
           stuff 4
       </div> 
   </div>   
</div>	
	
	

	
	</form>

	<?php
}