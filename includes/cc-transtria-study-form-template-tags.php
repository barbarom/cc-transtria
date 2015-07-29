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

	//are we loading an existing study?
	$this_study_id = $_GET["study_id"];
	
	//$study_data['single'] = cc_transtria_get_single_study_data( $this_study_id );
	//var_dump( $study_data['single'] );
	//$study_data['pops_single'] = cc_transtria_get_pops_study_data_single( $this_study_id );
	//var_dump( $study_data['pops_single'] );
	
	//$pops_ids = array_flip( cc_transtria_get_multiple_dropdown_ids_populations() );
	//var_dump( $pops_ids );
	
	$pops_data_multiple = cc_transtria_get_pops_study_data_multiple( $this_study_id );
	//var_dump( $pops_data_multiple );
	
	
	//get all study ids in system
	$all_study_ids = cc_transtria_get_study_ids();

	//get data related to the form
	$dd_singleton_options = cc_transtria_get_singleton_dropdown_options(); //all options for singleton dropdowns
	//var_dump( $dd_singleton_options );
	
	$dd_multiple_options_pops = cc_transtria_get_multiple_dropdown_options_populations( $this_study_id ); //all options for pops sub tabs
	
	//bundle field options into single array (or whatever, really) to send to other render functions
	$field_data = [];
	$field_data['dd_singleton_options'] = $dd_singleton_options;
	$field_data['dd_multiple_options_pops'] = $dd_multiple_options_pops;
	
	//TODO: think about whether we want to populate these in php or in js..	
	?>

	<form id="study_form">
		<div class="bottom_margin">			
			<label for="studyid"><strong>Study ID</strong></label>
			<select id="studyid">
				<?php //cycle through existing study ids
					foreach( $all_study_ids as $one_study_id ){
					echo '<option value="' . $one_study_id . '">' . $one_study_id . '</option>';
				
				} ?>
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
		   <input type="radio" id="tab-1" name="tab-group-1" class="noshow" checked>
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
										<select id="abstractor" name="abstractor">
											<option value="">---Select---</option>
											
											<?php //$dd_singleton_options are indexed by the div id - "abstractor", for example
												
												foreach( $dd_singleton_options['abstractor'] as $k => $v ){
													echo '<option value="' . $k . '"';
													//if( intval( $abstractor_val ) == intval( $k ) ) echo 'selected';
													echo '>' . $v->descr . '</option>';
												
												} ?>
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
										<select id="validator" name="validator">
											<option value="">---Select---</option>
											<?php										
												//$dd_singleton_options are indexed by the div id - "abstractor", for example
												foreach( $dd_singleton_options['validator'] as $k => $v ){
													echo '<option value="' . $k . '"';
													//if( intval( $selected_val ) == intval( $k ) ) echo 'selected';
													echo '>' . $v->descr . '</option>';
												} ?>											
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
						<td >
							EndNote ID:
						</td>
						<td >
							<select id="EndNoteID">
								<option value="">---Select---</option>
							</select>
						</td>
						<td >
							<input id="ed" type="text" readonly />					
						</td>					
					</tr>
					<tr>
						<td>
							Original PubMed ID (Accession Num):
						</td>
						<td>
							<input id="endnotes_pubmed" type="text" readonly />
						</td>
						<td>
												
						</td>					
					</tr>
					<tr>
						<td>PubMed ID:</td>
						<td>
							<input id="PubMedID" type="text" />
						</td>
						<td></td>					
					</tr>				
					<tr>
						<td><span>PubMedID not reported:</span></td>
						<td>
							<input id="PubMedID_notreported" type="checkbox" />
						</td>
						<td></td>					
					</tr>
					<tr>
						<td>Original Search tool type:</td>
						<td >
							<input type="text" readonly />
						</td>
						<td ></td>					
					</tr>
					<tr>
						<td>Search tool type:</td>
						<td>
							<select id="searchtooltype" class="multiselect"> 
								<?php //$dd_singleton_options are indexed by the div id - "abstractor", for example
									foreach( $dd_singleton_options['searchtooltype'] as $k => $v ){
									echo '<option value="' . $k . '" selected="selected">' . $v->descr . '</option>';
								
								} ?>								
							</select>
						</td>
						<td></td>					
					</tr>
					<tr>
						<td>Original Search tool name:</td>
						<td>
							<input class="endnotes_remotedatabasename" type="text" readonly />
						</td>
						<td></td>					
					</tr>
					<tr>
						<td>Search tool name:</td>
						<td>
							<select id="searchtoolname" name="searchtoolname" class="multiselect">
								<?php //$dd_singleton_options are indexed by the div id - "abstractor", for example
									foreach( $dd_singleton_options['searchtoolname'] as $k => $v ){
									echo '<option value="' . $k . '">' . $v->descr . '</option>';
								
								} ?>								
							</select>
						</td>
						<td >
							Other search tool: <input id="othersearchtool" type="text" />					
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
						<td>Grant or Contract #:
						</td>
						<td>
							<input id="grantcontractnumber" type="text" />
						</td>
						<td></td>					
					</tr>				
					<tr>
						<td><span>Grant or Contract # not reported:</span></td>
						<td>
							<input type="checkbox" />
						</td>
						<td></td>					
					</tr>
					<tr>
						<td>Amount of funding:</td>
						<td>
							<input id="fundingamount" type="text" />
						</td>
						<td></td>					
					</tr>				
					<tr>
						<td><span>Amount of funding not reported:</span></td>
						<td>
							<input id="fundingamount_notreported" type="checkbox" />
						</td>
						<td></td>					
					</tr>
					<tr>
						<td>Source of funding:</td>
						<td>
							<select id="fundingsource" name="fundingsource" class="multiselect">
								<?php //$dd_singleton_options are indexed by the div id - "abstractor", for example
									foreach( $dd_singleton_options['fundingsource'] as $k => $v ){
									echo '<option value="' . $k . '">' . $v->descr . '</option>';
								
								} ?>								
							</select>					
						</td>
						<td>
							Other funding source: <input id="otherfunding" type="text" />			
						</td>					
					</tr>				
					<tr>
						<td><span>Source of funding not reported:</span></td>
						<td>
							<input id="fundingsource_notreported" type="checkbox" />
						</td>
						<td></td>					
					</tr>
					<tr>
						<td>Domestic/International Funding Source:</td>
						<td>
							Domestic: <input id="DomesticFundingSourceType" type="checkbox" /> International: <input id="InternationalFundingSourceType" type="checkbox" />
						</td>
						<td></td>					
					</tr>				
					<tr>
						<td>Domestic funding source type:</td>
						<td >
							<select id="domesticfundingsources" name="domesticfundingsources">
								<option value="">---Select---</option>
								<?php //$dd_singleton_options are indexed by the div id - "abstractor", for example
									foreach( $dd_singleton_options['domesticfundingsources'] as $k => $v ){
									echo '<option value="' . $k . '">' . $v->descr . '</option>';
								
								} ?>								
							</select>	
						</td>
						<td></td>					
					</tr>				
					<tr>
						<td><span>Domestic funding source type not reported:</span></td>
						<td>
							<input id="domesticfundingsources_notreported" type="checkbox" />
						</td>
						<td></td>					
					</tr>
					<tr>
						<td>Funding purpose:</td>
						<td>
							<select id="fundingpurpose" name="fundingpurpose">
								<option value="">---Select---</option>
								<?php //$dd_singleton_options are indexed by the div id - "abstractor", for example
									foreach( $dd_singleton_options['fundingpurpose'] as $k => $v ){
									echo '<option value="' . $k . '">' . $v->descr . '</option>';
								
								} ?>								
							</select>							
						</td>
						<td></td>					
					</tr>				
					<tr>
						<td><span>Funding purpose not reported:</span></td>
						<td>
							<input id="fundingpurpose_notreported" type="checkbox" />
						</td>
						<td></td>					
					</tr>				
				</table>
				<table>			
					<tr>
						<td colspan="3" class="inner_table_header">
							DESIGN
						</td>
					</tr>
					<tr>
						<td>Study Design:</td>
						<td>
							<select id="StudyDesign" name="StudyDesign">
								<option value="">---Select---</option>
								<?php //$dd_singleton_options are indexed by the div id - "abstractor", for example
									foreach( $dd_singleton_options['StudyDesign'] as $k => $v ){
									echo '<option value="' . $k . '">' . $v->descr . '</option>';
								
								} ?>								
							</select>
						</td>
						<td>Other study design: <input id="otherstudydesign" type="text" /></td>
					</tr>				
					<tr>
						<td>Design Limitations:</td>
						<td colspan="2">
							<textarea style="width:97%"></textarea>
						</td>					
					</tr>				
					<tr>
						<td>
							<span>Design limitations not reported:</span>
						</td>
						<td>
							<input id="designlimitations_notreported" type="checkbox" />
						</td>
						<td></td>					
					</tr>	
					<tr>
						<td>Data Collection:</td>
						<td colspan="2">
							<textarea id="data_collection" style="width:97%"></textarea>	
						</td>
					
					</tr>	
					<tr>
						<td>Threat to internal validity?:</td>
						<td colspan="2">
							<span id="validitythreatflag">
								<input type="radio" value="Y" name="validitythreatflag">Yes
								<input type="radio" value="N" name="validitythreatflag">No
							</span>
						</td>					
					</tr>
					<tr>
						<td>
							Select type(s) of threats to internal validity:
						</td>
						<td>
							<select id="validity_threats" name="validity_threats" class="multiselect">
								<?php //$dd_singleton_options are indexed by the div id - "abstractor", for example
									foreach( $dd_singleton_options['validity_threats'] as $k => $v ){
									echo '<option value="' . $k . '">' . $v->descr . '</option>';
								
								} ?>								
							</select>
						</td>
						<td></td>					
					</tr>				
					<tr>
						<td><span>Threat to internal validity not reported:</span></td>
						<td>
							<input id="validitythreat_notreported" type="checkbox" />
						</td>
						<td></td>					
					</tr>				
				</table>
		   </div> 
	   </div>
    
	   <div class="tab primary_tab">
		   <input type="radio" id="tab-2" name="tab-group-1" class="noshow">
		   <label for="tab-2" class="primary_tab_label">Population</label>
		   
		   <div class="content">
			   <?php 
				//render pops tab in all its glory
				cc_transtria_render_populations_tab( $field_data ); ?>
		   </div> 
	   </div>
		
		<div class="tab primary_tab">
		   <input type="radio" id="tab-3" name="tab-group-1" class="noshow">
		   <label for="tab-3" class="primary_tab_label">Intervention/Partnerships</label>
		 
		   <div class="content">
			   <?php 
				
				cc_transtria_render_intervention_partnerships_tab( $field_data ); ?>
		   </div> 
	   </div>

		<div class="tab primary_tab">
		   <input type="radio" id="tab-4" name="tab-group-1" class="noshow">
		   <label for="tab-4" class="primary_tab_label">Results</label>
		 
		   <div class="content">
			   <?php
			   
			   cc_transtria_render_results_tab( $field_data ); ?>
		   </div> 
	   </div>   
	</div>	
	
	

	
	</form>

	<?php
}