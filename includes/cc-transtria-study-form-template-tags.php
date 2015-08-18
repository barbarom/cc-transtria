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

	//get all study ids in system.  If url param not in system, set this_study_id to null
	$all_study_ids = cc_transtria_get_study_ids();
	if( !in_array( $this_study_id, $all_study_ids ) ){
		$this_study_id = null;
	}
	
	$pops_data_multiple = cc_transtria_get_study_data_multiple( $this_study_id );
	$dd_multiple_options_pops = cc_transtria_get_multiple_dropdown_options_populations( $this_study_id ); //all options for pops sub tabs
	$field_data['num_ea_tabs'] = cc_transtria_get_num_ea_tabs_for_study( $this_study_id );
	//these are separate because of legacy code automatically saving 100 ea tabs even if there AREN't 100. #stupidstupidstupid
	//$field_data['ea_tab_data'] = cc_transtria_get_ea_tab_data_for_study( $this_study_id, $field_data['num_ea_tabs'] );
	$ea_data = cc_transtria_get_ea_tab_data_for_study( $this_study_id );
	//var_dump( $ea_data );
	//var_dump( $pops_data_multiple );
	
	
	//get all endnote ids and titles in system
	$all_endnote_ids = cc_transtria_get_endnote_id_title();
	//var_dump( $all_endnote_ids );

	//get data related to the form
	$dd_singleton_options = cc_transtria_get_singleton_dropdown_options(); //all options for singleton dropdowns
	//var_dump( $dd_singleton_options );
	
	
	$dd_multiple_options_ea = cc_transtria_get_multiple_dropdown_options_ea( ); //all options for pops sub tabs

	//bundle field options into single array (or whatever, really) to send to other render functions
	$field_data = [];
	$field_data['dd_singleton_options'] = $dd_singleton_options;
	$field_data['dd_multiple_options_pops'] = $dd_multiple_options_pops;
	$field_data['dd_multiple_options_ea'] = $dd_multiple_options_ea;
	
	
	//TODO: think about whether we want to populate these in php or in js..	
	?>

	<div class="basic_info_messages">
		<span class="usr-msg"></span>
		<span class="spinny"></span>
	</div>
	
	<form id="study_form">
	
		<input type="hidden" id="this_study_id" value="<?php echo $this_study_id; ?>">
		<div class="bottom_margin">			
			<label for="studyid"><strong>Study ID</strong></label>
			<select id="studyid">
				<option value="-1">-- Select --</option>
				<?php //cycle through existing study ids
					foreach( $all_study_ids as $one_study_id ){
					echo '<option value="' . $one_study_id . '">' . $one_study_id . '</option>';
				
				} ?>
			</select>
			<span>
				<a id="load_this_study" class="button">Load this study!</a>
			</span>		
		</div>
		
		
		<div id="study_top_info">
			<p><strong>Study Grouping ID:</strong>
				<input id="StudyGroupingID" class="no-edit" readonly="">
			</p>
			<div class="title_info">
					Title: <span id="endnote_title"></span><br />
					Author: <span id="endnote_author"></span><br />
					Dates: <span id="endnote_dates"></span> <span id="endnote_dates_year"></span><br />
				</p>
		</div>
		
	

	
	<div class="tabs">
		
	   <div class="tab primary_tab">
		   <input type="radio" id="tab-1" name="tab-group-1" class="noshow" checked>
		   <label for="tab-1" class="primary_tab_label">Basic Info</label>
       
		   <div id="basic_form_content" class="content">
				<table id="citation_table">
					<tr>
						<td class="citation_button" colspan="2">
							<!--<button>SHOW ENDNOTE CITATION DATA</button>-->
							<span><a class="button show_citation_data">SHOW ENDNOTE CITATION DATA</a></span>
							<span class="citation_spinny spinny"></span>
						</td>
					</tr>
					
					<?php cc_render_citation_data(); ?>
							
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
										<select id="abstractor" name="abstractor" class="studies_table">
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
										<input id="abstractorstarttime" class="studies_table" type="text" />
									</td>
								</tr>
								<tr>
									<td>
										Stop Date/Time:
									</td>
									<td>
										<input id="abstractorstoptime" class="studies_table" type="text" />
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
										<select id="validator" name="validator" class="studies_table">
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
										<input id="validatorstarttime" class="studies_table" type="text" />
									</td>
								</tr>
								<tr>
									<td>
										Stop Date/Time:
									</td>
									<td>
										<input id="validatorstoptime" class="studies_table" type="text" />
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
						<td>
							<span><select id="EndNoteID" class="studies_table"> 
								<option value="">---Select---</option>
								<?php										
								//$all_endnote_ids are indexed by the div id - "abstractor", for example
								foreach( $all_endnote_ids as $k => $v ){
									echo '<option value="' . $k . '"';
									echo '>' . $k . ': ' . $v . '</option>';
								} ?>
							</select></span>
							<span class="citation_spinny spinny"></span>
							
						</td>
						<td>
							<em>Phase: </em>
							<strong><span id="endnote_phase"></span></strong>				
						</td>					
					</tr>
					<tr>
						<td>
							Original PubMed ID (Accession Num):
						</td>
						<td>
							<input id="accession-num" type="text" readonly />
						</td>
						<td>
												
						</td>					
					</tr>
					<tr>
						<td>PubMed ID:</td>
						<td>
							<input id="PubMedID" class="studies_table" type="text" />
						</td>
						<td></td>					
					</tr>				
					<tr>
						<td class="not-reported"><label>PubMedID not reported:</label></td>
						<td>
							<input id="PubMedID_notreported" type="checkbox" />
						</td>
						<td></td>					
					</tr>
					<tr>
						<td>Original Search tool type:</td>
						<td >
							<input id="remote-database-provider" type="text" readonly />
						</td>
						<td ></td>					
					</tr>
					<tr>
						<td>Search tool type:</td>
						<td>
							<select id="searchtooltype" multiple="multiple" class="multiselect code_results_table"> 
								<?php //$dd_singleton_options are indexed by the div id - "abstractor", for example
									foreach( $dd_singleton_options['searchtooltype'] as $k => $v ){
									echo '<option value="' . $k . '">' . $v->descr . '</option>';
								
								} ?>								
							</select>
						</td>
						<td></td>					
					</tr>
					<tr>
						<td>Original Search tool name:</td>
						<td>
							<input id="remote-database-name" type="text" readonly />
						</td>
						<td></td>					
					</tr>
					<tr>
						<td>Search tool name:</td>
						<td>
							<select id="searchtoolname" multiple="multiple" name="searchtoolname" class="multiselect general-multiselect code_results_table">
								<?php //$dd_singleton_options are indexed by the div id - "abstractor", for example
									foreach( $dd_singleton_options['searchtoolname'] as $k => $v ){
									echo '<option value="' . $k . '">' . $v->descr . '</option>';
								
								} ?>								
							</select>
						</td>
						<td >
							Other search tool: <input id="othersearchtool" class="studies_table" type="text" />					
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
							<input id="grantcontractnumber" class="studies_table" type="text" />
						</td>
						<td></td>					
					</tr>				
					<tr>
						<td class="not-reported"><label>Grant or Contract # not reported:</label></td>
						<td>
							<input type="checkbox" id="grantcontractnumber_notreported" class="studies_table" />
						</td>
						<td></td>					
					</tr>
					<tr>
						<td>Amount of funding:</td>
						<td>
							<input id="fundingamount" class="studies_table" type="text" />
						</td>
						<td></td>					
					</tr>				
					<tr>
						<td class="not-reported"><label>Amount of funding not reported:</label></td>
						<td>
							<input id="fundingamount_notreported" class="studies_table" type="checkbox" />
						</td>
						<td></td>					
					</tr>
					<tr>
						<td>Source of funding:</td>
						<td>
							<select id="fundingsource" multiple="multiple" name="fundingsource" class="multiselect general-multiselect code_results_table">
								<?php //$dd_singleton_options are indexed by the div id - "abstractor", for example
									foreach( $dd_singleton_options['fundingsource'] as $k => $v ){
									echo '<option value="' . $k . '">' . $v->descr . '</option>';
								
								} ?>								
							</select>					
						</td>
						<td>
							Other funding source: <input id="otherfunding" class="studies_table" type="text" />			
						</td>					
					</tr>				
					<tr>
						<td class="not-reported"><label>Source of funding not reported:</label></td>
						<td>
							<input id="fundingsource_notreported" class="studies_table" type="checkbox" />
						</td>
						<td></td>					
					</tr>
					<tr>
						<td>Domestic/International Funding Source:</td>
						<td>
							Domestic: <input id="DomesticFundingSourceType" class="studies_table" type="checkbox" /> International: <input id="InternationalFundingSourceType" class="studies_table" type="checkbox" />
						</td>
						<td></td>					
					</tr>				
					<tr>
						<td>Domestic funding source type:</td>
						<td >
							<select id="domesticfundingsources"class="studies_table">
								<?php //$dd_singleton_options are indexed by the div id - "abstractor", for example
									foreach( $dd_singleton_options['domesticfundingsources'] as $k => $v ){
									echo '<option value="' . $k . '">' . $v->descr . '</option>';
								
								} ?>								
							</select>	
						</td>
						<td></td>					
					</tr>				
					<tr>
						<td class="not-reported"><label>Domestic funding source type not reported:</label></td>
						<td>
							<input id="domesticfundingsources_notreported" class="studies_table" type="checkbox" />
						</td>
						<td></td>					
					</tr>
					<tr>
						<td>Funding purpose:</td>
						<td>
							<select id="fundingpurpose" class="studies_table">
								<option value="-1">-- Select --</option>
								<?php //$dd_singleton_options are indexed by the div id - "abstractor", for example
									foreach( $dd_singleton_options['fundingpurpose'] as $k => $v ){
									echo '<option value="' . $k . '">' . $v->descr . '</option>';
								
								} ?>								
							</select>							
						</td>
						<td></td>					
					</tr>				
					<tr>
						<td class="not-reported"><label>Funding purpose not reported:</label></td>
						<td>
							<input id="fundingpurpose_notreported" class="studies_table" type="checkbox" />
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
							<select id="StudyDesign" name="StudyDesign" class="studies_table">
								<option value="-1">-- Select --</option>
								<?php //$dd_singleton_options are indexed by the div id - "abstractor", for example
									
									foreach( $dd_singleton_options['StudyDesign'] as $k => $v ){
									echo '<option value="' . $k . '">' . $v->descr . '</option>';
								
								} ?>								
							</select>
						</td>
						<td>Other study design: <input id="otherstudydesign" class="studies_table" type="text" /></td>
					</tr>				
					<tr>
						<td>Design Limitations:</td>
						<td colspan="2">
							<textarea id="design_limitations" class="studies_table" style="width:97%"></textarea>
						</td>					
					</tr>				
					<tr>
						<td class="not-reported">
							<label>Design limitations not reported:</label>
						</td>
						<td>
							<input id="designlimitations_notreported" class="studies_table" type="checkbox" />
						</td>
						<td></td>					
					</tr>	
					<tr>
						<td>Data Collection:</td>
						<td colspan="2">
							<textarea id="data_collection" class="studies_table" style="width:97%"></textarea>	
						</td>
					
					</tr>	
					<tr>
						<td>Threat to internal validity?:</td>
						<td colspan="2">
							<span id="validitythreatflag">
								<input type="radio" class="studies_table" value="Y" class="studies_table" name="validitythreatflag">Yes
								<input type="radio" class="studies_table" value="N" class="studies_table" name="validitythreatflag">No
							</span>
						</td>					
					</tr>
					<tr>
						<td>
							Select type(s) of threats to internal validity:
						</td>
						<td>
							<select id="validity_threats" multiple="multiple" name="validity_threats" class="multiselect general-multiselect code_results_table">
								<?php //$dd_singleton_options are indexed by the div id - "abstractor", for example
									foreach( $dd_singleton_options['validity_threats'] as $k => $v ){
									echo '<option value="' . $k . '">' . $v->descr . '</option>';
								
								} ?>								
							</select>
						</td>
						<td></td>					
					</tr>				
					<tr>
						<td class="not-reported"><label>Threat to internal validity not reported:</label></td>
						<td>
							<input id="validitythreat_notreported" class="studies_table" type="checkbox" />
						</td>
						<td></td>					
					</tr>

					<tr>
						<td colspan="2"></td>
						<td class="submit_form">
							<a class="button save_study alignright">SAVE STUDY</a>
						</td>
					</tr>
				</table>
		   </div> 
	   </div>
    
	   <div class="tab primary_tab">
		   <input type="radio" id="tab-2" name="tab-group-1" class="noshow">
		   <label for="tab-2" class="primary_tab_label">Population</label>
		   
		   <div id="population_content" class="content">
			   <?php 
				//render pops tab in all its glory
				cc_transtria_render_populations_tab( $field_data ); ?>
		   </div> 
	   </div>
		
		<div class="tab primary_tab">
		   <input type="radio" id="tab-3" name="tab-group-1" class="noshow">
		   <label for="tab-3" class="primary_tab_label">Intervention/Partnerships</label>
		 
		   <div id="intervention_content" class="content">
			   <?php 
				
				cc_transtria_render_intervention_partnerships_tab( $field_data ); ?>
		   </div> 
	   </div>

		<div class="tab primary_tab">
		   <input type="radio" id="tab-4" name="tab-group-1" class="noshow">
		   <label for="tab-4" class="primary_tab_label">Results</label>
		 
		   <div id="results_content" class="content">
			   <?php
			   
			   cc_transtria_render_results_tab( $field_data ); ?>
		   </div> 
	   </div>   
	</div>	
	
	

	
	</form>

	<?php

	
}
	
/**
 * Renders citation data/info
 *
 */
function cc_render_citation_data(){

?>
	<tr class="endnote_citation_data">
		<td colspan="5"> 

			<div id="citation_tabs">
				<ul>
					<li class="active"><a data-whichtab="basic_citation_tab">CITATION INFO</a></li>
					<li><a data-whichtab="otherdates_tab">OTHER DATE INFO</a></li>
					<li><a data-whichtab="otherids_tab">OTHER ID INFO</a></li>
					<li><a data-whichtab="otherpubinfo_tab">OTHER PUBLICATION INFO</a></li>
				</ul>
				<div id="basic_citation_tab" class="one_citation_tab">
				
					<table class="showolddata-table">
						<tr>
							<td colspan="5" class="citation-info"><h4>CITATION INFO</h4></td>
						</tr>
						<tr>
							<td><label>Author</label></td>
							<td><span id="endnotes_contributors_authors_author" readonly></span></td>
						</tr>
						<tr>
							<td><label>Journal/Secondary Title</label></td>
							<td><span id="endnotes_titles_secondary-title" readonly></span></td>
							<td><label>Date</label></td>
							<td><span id="endnotes_dates_pub-dates_date" readonly></span></td>
						</tr>

						<tr>
							<td><label>Volume</label></td>
							<td><span id="endnotes_volume"></span></td>
							<td><label>Number</label></td>
							<td><span id="endnotes_number"></span></td>
						</tr>

						<tr class="">
							<td><label>Pages</label></td>
							<td><span id="endnotes_pages"></span></td>
						</tr>

						<tr class="hr-bottom">
							<td colspan="4"></td>
						</tr>

						<tr>
							<td><label>PubMed ID</label></td>
							<td><span id="endnotes_accession-num"></span></td>
						</tr>

						<tr>
							<td><label>Name of Database</label></td>
							<td><span id="endnotes_remote-database-name" class="endnotes_remotedatabasename"></span></td>
							<td><label>Database Provider</label></td>
							<td><span id="endnotes_remote-database-provider" class="endnotes_remotedatabaseprovider"></span></td>
						</tr>
						<tr>
							<td><label>Type of article/work</label></td>
							<td><span id="endnotes_work-type"></span></td>
						</tr>

						<tr class="hr-bottom">
							<td colspan="4"></td>
						</tr>

						<tr class="">
							<td><label>Key words</label></td>
							<td><span id="endnotes_keywords_keyword"></span></td>
						</tr>

						<tr class="hr-bottom">
							<td colspan="4"></td>
						</tr>

						<tr class="">
							<td class="maxwidth200"><label>Abstract</label></td>
							<td colspan="3"><span id="endnotes_abstract"></span></td>
						</tr>
						
						<tr class="">
							<td class="maxwidth200"><label>URLS</label></td>
						<td colspan="3"><span id="endnotes_urls_related-urls_url"></span></td>

						</tr>

						<tr>
							<td class="showolddata-button" colspan="5">
								<a class="button show_citation_data">HIDE ENDNOTE CITATION DATA</a>
							</td>
						</tr>

					</table>
				</div>
			
				<div id="otherdates_tab" class="one_citation_tab">
					<table class="showolddata-table">
						<tr>
							<td colspan="4" class="otherdate-info"><h4>OTHER DATE INFO</h4></td>
						</tr>
						<tr>
							<td><label>Epub date</label></td>
							<td><span id="endnotes_epub"></span></td>
							<td><label>Access date</label></td>
							<td><span id="endnotes_accessdate"></span></td>
						</tr>
						<tr>
							<td><label>Added to library</label></td>
							<td><span id="endnotes_addedlibrary"></span></td>
							<td><label>Last updated</label></td>
							<td><span id="endnotes_lastupdated"></span></td>
						</tr>

						<tr>
							<td class="showolddata-button" colspan="5">
								<a class="button show_citation_data">HIDE ENDNOTE CITATION DATA</a>
							</td>
						</tr>

					</table>
				</div>

				<div id="otherids_tab" class="one_citation_tab">
					<table class="showolddata-table">
						<tr>
							<td colspan="4" class="otherdate-info"><h4>OTHER ID INFO</h4></td>
						</tr>
						<tr>
							<td><label>ISBN</label></td>
							<td><span id="endnotes_isbn"></span></td>
							<td><label>DOI</label></td>
							<td><span id="endnotes_doi"></span></td>
						</tr>
						<tr>
							<td><label>PMCID</label></td>
							<td><span id="endnotes_pmcid"></span></td>
							<td><label>NIHMSID</label></td>
							<td><span id="endnotes_nihmsid"></span></td>
						</tr>
						<tr>
							<td><label>Call Number</label></td>
							<td><span id="endnotes_call-num"></span></td>
						</tr>

						<tr>
							<td class="showolddata-button" colspan="5">
								<a class="button show_citation_data">HIDE ENDNOTE CITATION DATA</a>
							</td>
						</tr>

					</table>
				</div>

				<div id="otherpubinfo_tab" class="one_citation_tab">
					<table class="showolddata-table">
						<tr>
							<td colspan="4" class="otherdate-info"><h4>OTHER PUBLICATION INFO</h4></td>
						</tr>
						<tr>
							<td><label>Language</label></td>
							<td><span id="endnotes_language"></span></td>
							<td><label>Label</label></td>
							<td><span id="endnotes_label"></span></td>
						</tr>

						<tr class="hr-bottom">
							<td colspan="4"></td>
						</tr>

						<tr>
							<td><label>Notes</label></td>
							<td><span id="endnotes_notes"></span></td>
							<td><label>Research Notes</label></td>
							<td><span id="endnotes_researchnotes"></span></td>
						</tr>
						<tr>
							<td><label>Legal Notes</label></td>
							<td><span id="endnotes_legalnotes"></span></td>
						</tr>

						<tr class="hr-bottom">
							<td colspan="4"></td>
						</tr>

						<tr>
							<td><label>Author Address</label></td>
							<td><span id="endnotes_auth-address"></span></td>
							<td><label>Place Published</label></td>
							<td><span id="endnotes_pub-location"></span></td>
						</tr>
						<tr>
							<td><label>Subsidiary Author</label></td>
							<td><span id="endnotes_contributors_authors_author"></span></td>
							<td><label>Translated Author</label></td>
							<td><span id="endnotes_translatedauthor"></span></td>
						</tr>
						<tr>
							<td><label>Secondary Author</label></td>
							<td><span id="endnotes_secondaryauthor"></span></td>
							<td><label>Tertiary Author</label></td>
							<td><span id="endnotes_tertiaryauthor"></span></td>
						</tr>
						<tr>
							<td><label>Tertiary title</label></td>
							<td><span id="endnotes_titles_secondary-title"></span></td>
						</tr>

						<tr class="hr-bottom">
							<td colspan="4"></td>
						</tr>

						<tr>
							<td><label>Original Publication</label></td>
							<td><span id="endnotes_orig-pub"></span></td>
							<td><label>Section</label></td>
							<td><span id="endnotes_section"></span></td>
						</tr>
						<tr>
							<td><label>Edition</label></td>
							<td><span id="endnotes_edition"></span></td>
							<td><label>Reprint Edition</label></td>
							<td><span id="endnotes_reprintedition"></span></td>
						</tr>
						<tr>
							<td><label>Reviewed Item</label></td>
							<td><span id="endnotes_revieweditem"></span></td>
							<td><label>Short Title</label></td>
							<td><span id="endnotes_titles_short-title"></span></td>
						</tr>
						<tr>
							<td><label>Alternate Journal/Title</label></td>
							<td><span id="endnotes_titles_alt-title"></span></td>
							<td><label>Start Page</label></td>
							<td><span id="endnotes_startpage"></span></td>
						</tr>
						<tr>
							<td><label>Rating</label></td>
							<td><span id="endnotes_rating"></span></td>
						</tr>

						<tr class="hr-bottom">
							<td colspan="4"></td>
						</tr>

						<tr>
							<td><label>Custom Field 1</label></td>
							<td><span id="endnotes_custom1"></span></td>
						</tr>
						
						<tr>
							<td><label>Custom Field 2</label></td>
							<td><span id="endnotes_custom2"></span></td>
						</tr>
						
						<tr>
							<td><label>Custom Field 6</label></td>
							<td><span id="endnotes_custom6"></span></td>
						</tr>

						<tr>
							<td class="showolddata-button" colspan="5">
								<a class="button show_citation_data">HIDE ENDNOTE CITATION DATA</a>
							</td>
						</tr>

					</table>
				</div>
		
			</div>
		</td>
	</tr>   


								
<?php			
}