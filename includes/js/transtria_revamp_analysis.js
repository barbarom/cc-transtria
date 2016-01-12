
/**** Doc to hold revamp'd javascript for analysis tab **/

function analysisClickListen(){

	//load in studies given study id
	jQuery("a#get_vars_by_group").on("click", get_vars_by_grouping );
	
	//run intermediate analysis for study group
	jQuery("a#run_intermediate_analysis").on("click", run_intermediate_analysis );
	
	//run final analysis for study group
	jQuery("a#run_analysis").on("click", run_analysis );
	jQuery("a#run_second_analysis").on("click", run_second_analysis );
	
	//show/hides
	jQuery("a#hide_im_table").on("click", toggle_var_table );
	jQuery("a#hide_direction_table").on("click", toggle_var_table );
	jQuery("a#hide_design_table").on("click", toggle_var_table );
	jQuery("a#hide_analysis_im_table").on("click", toggle_var_table );
	jQuery("a#hide_analysis_effect_table").on("click", toggle_var_table );
	jQuery("a#hide_analysis_population_table").on("click", toggle_var_table );
	
	jQuery("a#hide_component_table").on( "click", toggle_var_table );
	
	
	//when clicking on the analysis/intermediate vars tabs
	jQuery('.analysis_tab_label').on( "click", analysis_tab_toggle );

	//show/hide algorithms
	jQuery('#intermediate_vars_content #show_direction_algorithm').on( "click", algorithm_toggle );
	jQuery('#analysis_vars_content #show_effect_algorithm').on( "click", algorithm_toggle );

	//saving analysis vars
	jQuery('#analysis_vars_content .analysis_save').on("click", save_analysis_vars );




}

//tabs toggle for analysis var sections
function analysis_tab_toggle(){

	var whichtab = jQuery(this).data("whichanalysistab");
	
	//fade out all, remove active class from all l
	jQuery("#analysis_content .single_analysis_content").fadeOut();
	jQuery("label.analysis_tab_label").removeClass("active");
	
	jQuery("#" + whichtab + ".single_analysis_content").fadeIn();
	jQuery(this).addClass("active");
	//console.log(whichtab);
	
}

//toggle intermediate direction algorithm (could be a switch-case)
function algorithm_toggle(){

	//get which algorithm to show/hide from the data-whichalgorithm attr
	var which_alg = jQuery( this ).attr("data-whichalgorithm");
	
	if( jQuery("#" + which_alg).is(":visible") ){
		jQuery( "#" + which_alg ).hide();
		jQuery( this ).html("SHOW ALGORITHM DETAILS");
	} else {
		jQuery( "#" + which_alg ).show();
		jQuery( this ).html("HIDE ALGORITHM DETAILS");
	}

}

//toggles visibility of variable tables
function toggle_var_table(){

	var which_table_attr = jQuery( this ).attr("data-whichtable");
	var which_table = jQuery("table#" + which_table_attr);
	var which_label = jQuery( this ).attr("data-whichlabel");
	
	if( which_table.is(":visible") ){
		which_table.slideUp();
		jQuery( this ).html("SHOW " + which_label);
	} else {
		which_table.slideDown();
		jQuery( this ).html("HIDE " + which_label);
	}
	
}

//Get/Display all the vars!
function get_vars_by_grouping(){

	this_study_group = jQuery("select#StudyGroupingIDList").val();
	
	//update hidden input
	jQuery("input#secret_study_group").val( this_study_group );
	
	//user messages
	var spinny = jQuery('.analysis_messages .spinny');
	var usrmsg = jQuery('.analysis_messages .usr-msg');
	var usrmsgshell = jQuery('.analysis_messages');
	
	//where to add table data after success
	which_tr_parent = jQuery("table#intermediate_vars_im tr#data_parent");
	which_tr_parent_dir = jQuery("table#intermediate_vars_direction tr#data_parent");
	which_tr_parent_intermediate_design = jQuery("table#intermediate_vars_design tr#data_parent");
	which_tr_parent_intermediate_components = jQuery("table#intermediate_vars_components tr#data_parent");
	which_tr_parent_intermediate_complexity = jQuery("table#intermediate_vars_complexity tr#data_parent");
	which_tr_parent_intermediate_purpose = jQuery("table#intermediate_vars_purpose tr#data_parent");
	which_tr_parent_intermediate_summary = jQuery("table#intermediate_vars_summary tr#data_parent");
	which_tr_parent_intermediate_settingtype = jQuery("table#intermediate_vars_settingtype tr#data_parent");
	which_tr_parent_intermediate_pse = jQuery("table#intermediate_vars_pse tr#data_parent");
	which_tr_parent_intermediate_support = jQuery("table#intermediate_vars_support tr#data_parent");
	which_tr_parent_intermediate_opposition = jQuery("table#intermediate_vars_opposition tr#data_parent");
	which_tr_parent_intermediate_sustainability = jQuery("table#intermediate_vars_sustainability tr#data_parent");
	
	which_tr_parent_intermediate_domestic = jQuery("table#intermediate_vars_domestic tr#data_parent");
	which_tr_parent_intermediate_intl = jQuery("table#intermediate_vars_intl tr#data_parent");
	
	which_tr_parent_intermediate_duration = jQuery("table#intermediate_vars_duration tr#data_parent");
	
	which_tr_parent_analysis_im = jQuery("table#analysis_vars_im tr#data_parent");
	which_tr_parent_analysis_effect = jQuery("table#analysis_vars_effect tr#data_parent");
	which_tr_parent_analysis_pops = jQuery("table#analysis_vars_population tr#data_parent");
	which_tr_parent_effectiveness_hr = jQuery("table#analysis_vars_effectiveness_hr tr#data_parent");
	which_tr_parent_domestic = jQuery("table#analysis_vars_domestic tr#data_parent");
	which_tr_parent_multi_component = jQuery("table#analysis_vars_multi_component tr#data_parent");
	which_tr_parent_complex = jQuery("table#analysis_vars_complex tr#data_parent");
	which_tr_parent_participation = jQuery("table#analysis_vars_participation tr#data_parent");
	
	which_tr_parent_hr_black = jQuery("table#analysis_vars_hr_black tr#data_parent");
	which_tr_parent_hr_asian = jQuery("table#analysis_vars_hr_asian tr#data_parent");
	which_tr_parent_hr_nativeamerican = jQuery("table#analysis_vars_hr_nativeamerican tr#data_parent");
	which_tr_parent_hr_pacisland = jQuery("table#analysis_vars_hr_pacificislander tr#data_parent");
	which_tr_parent_hr_hispanic = jQuery("table#analysis_vars_hr_hispanic tr#data_parent");
	which_tr_parent_hr_lowincome = jQuery("table#analysis_vars_hr_lowincome tr#data_parent");
	
	which_tr_parent_popreach = jQuery("table#analysis_vars_popreach tr#data_parent");
	which_tr_parent_hr_popreach = jQuery("table#analysis_vars_hr_popreach tr#data_parent");
	
	which_tr_parent_state = jQuery("table#analysis_vars_state tr#data_parent");
	which_tr_parent_quality = jQuery("table#analysis_vars_quality tr#data_parent");
	which_tr_parent_inclusiveness = jQuery("table#analysis_vars_inclusiveness tr#data_parent");
	which_tr_parent_access = jQuery("table#analysis_vars_access tr#data_parent");
	which_tr_parent_size = jQuery("table#analysis_vars_size tr#data_parent");
	
	//ajax data
	var ajax_action = 'get_im_dyads_and_data_by_group';
	var ajax_data = {
		'action': ajax_action,
		'transtria_nonce' : transtria_ajax.ajax_nonce,
		'this_study_group' : this_study_group
	};
	
	if( this_study_group == -1 ){
		return;
	}
	
	//ajax get the studies for this group
	jQuery.ajax({
		url: transtria_ajax.ajax_url, 
		data: ajax_data, 
		type: "POST",
		dataType: "json",
		beforeSend: function() {
			usrmsg.html("Retrieving data, hang tight..." );
			usrmsgshell.fadeIn();
			spinny.fadeIn();
			
			//clear tables: intermediate
			jQuery("table#intermediate_vars_im tr").not(".no_remove").remove();
			jQuery("table#intermediate_vars_direction tr").not(".no_remove").remove();
			jQuery("table#intermediate_vars_design tr").not(".no_remove").remove();
			jQuery("table#intermediate_vars_components tr").not(".no_remove").remove();
			jQuery("table#intermediate_vars_complexity tr").not(".no_remove").remove();
			jQuery("table#intermediate_vars_purpose tr").not(".no_remove").remove();
			jQuery("table#intermediate_vars_summary tr").not(".no_remove").remove();
			jQuery("table#intermediate_vars_settingtype tr").not(".no_remove").remove();
			jQuery("table#intermediate_vars_pse tr").not(".no_remove").remove();
			jQuery("table#intermediate_vars_support tr").not(".no_remove").remove();
			jQuery("table#intermediate_vars_opposition tr").not(".no_remove").remove();
			jQuery("table#intermediate_vars_sustainability tr").not(".no_remove").remove();
			
			jQuery("table#intermediate_vars_domestic tr").not(".no_remove").remove();
			jQuery("table#intermediate_vars_intl tr").not(".no_remove").remove();
			
			jQuery("table#intermediate_vars_duration tr").not(".no_remove").remove();
			
			//clear tables: analysis
			jQuery("table#analysis_vars_im tr").not(".no_remove").remove();
			jQuery("table#analysis_vars_effect tr").not(".no_remove").remove();
			jQuery("table#analysis_vars_population tr").not(".no_remove").remove();
			jQuery("table#analysis_vars_effectiveness_hr tr").not(".no_remove").remove();
			jQuery("table#analysis_vars_domestic tr").not(".no_remove").remove();
			jQuery("table#analysis_vars_multi_component tr").not(".no_remove").remove();
			jQuery("table#analysis_vars_complex tr").not(".no_remove").remove();
			jQuery("table#analysis_vars_participation tr").not(".no_remove").remove();
			
			//clear table: analysis hr populations
			jQuery("table#analysis_vars_hr_black tr").not(".no_remove").remove();
			jQuery("table#analysis_vars_hr_asian tr").not(".no_remove").remove();
			jQuery("table#analysis_vars_hr_nativeamerican tr").not(".no_remove").remove();
			jQuery("table#analysis_vars_hr_pacificislander tr").not(".no_remove").remove();
			jQuery("table#analysis_vars_hr_hispanic tr").not(".no_remove").remove();
			jQuery("table#analysis_vars_hr_lowincome tr").not(".no_remove").remove();
			
			//population reaches
			jQuery("table#analysis_vars_popreach tr").not(".no_remove").remove();
			jQuery("table#analysis_vars_hr_popreach tr").not(".no_remove").remove();
			
			//state, quality, inclusiveness
			jQuery("table#analysis_vars_state tr").not(".no_remove").remove();
			jQuery("table#analysis_vars_quality tr").not(".no_remove").remove();
			jQuery("table#analysis_vars_inclusiveness tr").not(".no_remove").remove();
			jQuery("table#analysis_vars_access tr").not(".no_remove").remove();
			jQuery("table#analysis_vars_size tr").not(".no_remove").remove();
			
		}
	}).success( function( data ) {
		
		if( data == "0" || data == 0 )  {
			//console.log('what');=
			return;
		} else {
		
			//draw table#intermediate_vars_im (4 cols)
			var txt = "";
			var txt_dir = "";
			var txt_design = "";
			var txt_components = "";
			var txt_complexity = "";
			var txt_representativeness = "";
			var txt_purpose = "";
			var txt_summary = "";
			var txt_settingtype = "";
			var txt_pse = "";
			var txt_support = "";
			var txt_opposition = "";
			var txt_sustainability = "";
			var txt_domestic = "";
			var txt_intl = "";
			
			var txt_duration = "";
			var txt_strat = "";
			
			var txt_a_im = "";
			var txt_a_effects = "";
			var txt_a_effects_hr = "";
			var txt_a_pops = "";
			var txt_a_domestic = "";
			var txt_a_multicomp = "";
			var txt_a_complex = "";
			var txt_a_participation = "";
			
			var txt_a_hr_black = "";
			var txt_a_hr_asian = "";
			var txt_a_hr_nativeamerican = "";
			var txt_a_hr_pacisland = "";
			var txt_a_hr_hispanic = "";
			var txt_a_hr_lowincome = "";
			
			//population reaches
			var txt_a_popreach = "";
			var txt_a_hr_popreach = "";
			
			//editables: stage, state, quality, inclusiveness
			var txt_a_stage = "";
			var txt_a_state = "";
			var txt_a_quality = "";
			var txt_a_inclusiveness = "";
			var txt_a_access = "";
			var txt_a_size = "";
			
			
			//for each study
			if( data.intermediate_vars != undefined ){
				jQuery.each( data.intermediate_vars, function (){
					//for each row in intermediate table for this study
					var this_study_data = jQuery( this );
					jQuery.each( this_study_data, function(){
						//console.log( this );
						
						txt += "<tr>";
						//txt += "<td>" + this.StudyID + "</td>";
						txt += "<td>" + this.info_id + "</td>";
						//txt += "<td>" + this.ea_seq_id + "</td>"; //this info now embedded in info_id
						txt += "<td>" + this.indicator + "</td>";
						txt += "<td>" + this.measure + "</td>";
						txt += "<td>" + this.result_subpopulationYN + "</td>";
						if( this.result_subpop_unserial == false ){
							txt += "<td>no subpop</td>";
						} else {
							if( this.result_subpop_unserial[0].descr != undefined ){
								txt += "<td>" + this.result_subpop_unserial[0].descr + "</td>";
							} else {
								txt += "<td>subpop error</td>";
							}
						}
						if( this.result_eval_unserial == false ){
							txt += "<td>no eval pop</td>";
						} else {
							if( this.result_eval_unserial[0].descr != undefined ){
								txt += "<td>" + this.result_eval_unserial[0].descr + "</td>";
							} else {
								txt += "<td>eval pop error</td>";
							}
						}
						
						txt += "</tr>";
						
						//also populate the ea direction table
						txt_dir += "<tr>"; 
						txt_dir += "<td>" + this.info_id + "</td>";
						txt_dir += "<td>" + this.indicator + "</td>";
						txt_dir += "<td>" + this.measure + "</td>";
						txt_dir += "<td>" + this.outcome_type + "</td>";
						txt_dir += "<td>" + this.calc_ea_direction + "</td>";
						
						txt_dir += "</tr>";
						
						
						//duration
						txt_duration += "<tr><td>" + this.info_id + "</td>";
						txt_duration += "<td>" + this.indicator + "</td>";
						txt_duration += "<td>" + this.measure + "</td>";
						txt_duration += "<td>" + this.outcome_duration + "</td></tr>";
						
						//strategies
						txt_strat += "<tr><td>" + this.info_id + "</td>";
						txt_strat += "<td>" + this.indicator + "</td>";
						txt_strat += "<td>" + this.measure + "</td>";
						
						txt_strat += "<td>" + this.indicator_strategies_unserial + "</td></tr>";
						
					
					});
					//console.log( jQuery(this ) );
				
				});
			} //end if intermediate_vars
			
			if( data.intermediate_vars_study != undefined ){
				//update header label
				jQuery("#intermediate_vars_content h3#intermediate_vars_header_text").html("Intermediate Variables: Study Grouping " + this_study_group );
				jQuery.each( data.intermediate_vars_study, function ( index, this_inter_study_data){
					//for each row in intermediate table for this study					
					txt_design += "<tr><td>" + index + "</td>";
					txt_design += "<td>" + this.StudyDesignValue + "</td>";
					txt_design += "<td>" + this.otherStudyDesign + "</td></tr>";
					//purpose
					txt_purpose += "<tr><td>" + index + "</td>";
					txt_purpose += "<td>" + this.intervention_purpose + "</td>";
					txt_purpose += "<td>" + this.interventionpurpose_notreported + "</td></tr>";
					//summary
					txt_summary += "<tr><td>" + index + "</td>";
					txt_summary += "<td>" + this.intervention_summary + "</td>";
					txt_summary += "<td>" + this.interventionsummary_notreported + "</td></tr>";
					//support
					txt_support += "<tr><td>" + index + "</td>";
					txt_support += "<td>" + this.support + "</td>";
					txt_support += "<td>" + this.support_notreported + "</td></tr>";
					//opposition
					txt_opposition += "<tr><td>" + index + "</td>";
					txt_opposition += "<td>" + this.opposition + "</td>";
					txt_opposition += "<td>" + this.opposition_notreported + "</td></tr>";
					//sustainability
					txt_sustainability += "<tr><td>" + index + "</td>";
					txt_sustainability += "<td>" + this.sustainability_flag + "</td>";
					txt_sustainability += "<td>" + this.sustainabilityplan_notreported + "</td></tr>";
					
					
					//domestic
					txt_domestic += "<tr><td>" + index + "</td>";
					txt_domestic += "<td>" + this.domestic_setting + "</td>";
					txt_domestic += "<td>" + this.domesticintlsetting_notreported + "</td></tr>";
					
					//intl
					txt_intl += "<tr><td>" + index + "</td>";
					txt_intl += "<td>" + this.international_setting + "</td>";
					txt_intl += "<td>" + this.domesticintlsetting_notreported + "</td></tr>";
					
					//multis
					if( this_inter_study_data.multi != undefined ){
						//complexity
						txt_complexity += "<tr><td>" + index + "</td>";
						var complex_string = ""; //for flattening arrays for display
						if( this_inter_study_data.multi.complexity != undefined ){
							if( this_inter_study_data.complexity_notreported == "Y" ){
								txt_complexity += "<td></td><td>Y</td></tr>";
							} else if( this_inter_study_data.multi.complexity.length > 0 ){
								//console.log( this_inter_study_data.multi.complexity );
								jQuery.each( this_inter_study_data.multi.complexity, function( complex_i, complex_v ){
									if( complex_v.length > 0 ){
										//console.log( complex_v);
										//console.log( complex_v[0]["value"] );
										complex_string += "\n" + complex_v[0]["value"] + ": " + complex_v[0]["descr"] + ";";
									}
								});
								txt_complexity += "<td>" + complex_string + "</td>";
								txt_complexity += "<td>N</td></tr>";
							} else {
								txt_complexity += "<td>No data</td>";
								txt_complexity += "<td>No data</td></tr>";
							}
							
						} else {
							txt_complexity += "<td>No data</td>";
							txt_complexity += "<td>No data</td></tr>";
						}
						
						//intervention components
						txt_components += "<tr><td>" + index + "</td>";
						complex_string = ""; //reset  
						if( this_inter_study_data.multi.intervention_components != undefined ){
							if( this_inter_study_data.interventioncomponents_notreported == "Y" ){
								txt_components += "<td></td><td>Y</td></tr>";
							} else if( this_inter_study_data.multi.intervention_components.length > 0 ){
								jQuery.each( this_inter_study_data.multi.intervention_components, function( complex_i, complex_v ){
									if( complex_v.length > 0 ){
										//console.log( complex_v);
										//console.log( complex_v[0]["value"] );
										complex_string += "\n" + complex_v[0]["value"] + ": " + complex_v[0]["descr"] + ";";
									}
								});
								txt_components += "<td>" + complex_string + "</td>";
								txt_components += "<td>N</td></tr>";
							} else {
								txt_components += "<td>No data</td>";
								txt_components += "<td>No data</td></tr>";
							}
							
						} else {
							txt_components += "<td>No data</td>";
							txt_components += "<td>No data</td></tr>";
						}
						
						//setting type
						txt_settingtype += "<tr><td>" + index + "</td>";
						complex_string = ""; //reset  
						if( this_inter_study_data.multi.setting_type != undefined ){
							if( this_inter_study_data.settingtype_notreported == "Y" ){
								txt_settingtype += "<td></td><td></td><td>Y</td></tr>";
							} else if( this_inter_study_data.multi.setting_type.length > 0 ){
								jQuery.each( this_inter_study_data.multi.setting_type, function( complex_i, complex_v ){
									if( complex_v.length > 0 ){
										//console.log( complex_v);
										//console.log( complex_v[0]["value"] );
										complex_string += "\n" + complex_v[0]["value"] + ": " + complex_v[0]["descr"] + ";";
									}
								});
								txt_settingtype += "<td>" + complex_string + "</td>";
								txt_settingtype += "<td>" + this_inter_study_data.other_setting_type + "</td>";
								txt_settingtype += "<td>N</td></tr>";
							} else {
								txt_settingtype += "<td>No data</td>";
								txt_settingtype += "<td>" + this_inter_study_data.other_setting_type + "</td>";
								txt_settingtype += "<td>No data</td></tr>";
							}
							
						} else {
							txt_settingtype += "<td>No data</td>";
							txt_settingtype += "<td>" + this_inter_study_data.other_setting_type + "</td>";
							txt_settingtype += "<td>" + this_inter_study_data.settingtype_notreported + "</td></tr>";
						}
						
						//pse components
						txt_pse += "<tr><td>" + index + "</td>";
						complex_string = ""; //reset  
						if( this_inter_study_data.multi.pse_components != undefined ){
							if( this_inter_study_data.psecomponents_notreported == "Y" ){
								txt_pse += "<td></td><td>Y</td></tr>";
							} else if( this_inter_study_data.multi.pse_components.length > 0 ){
								jQuery.each( this_inter_study_data.multi.pse_components, function( complex_i, complex_v ){
									if( complex_v.length > 0 ){
										//console.log( complex_v);
										//console.log( complex_v[0]["value"] );
										complex_string += "\n" + complex_v[0]["value"] + ": " + complex_v[0]["descr"] + ";";
									}
								});
								txt_pse += "<td>" + complex_string + "</td>";
								txt_pse += "<td>N</td></tr>";
							} else {
								txt_pse += "<td>No data</td>";
								txt_pse += "<td>No data</td></tr>";
							}
							
						} else {
							txt_pse += "<td>No data</td>";
							txt_pse += "<td>No data</td></tr>";
						}
						//console.log( this_inter_study_data.multi );
					}
					
				});
			} //end if intermedaite_vars_study
			
			if( data.analysis_vars != undefined ){
				//update header label
				jQuery("#analysis_vars_content h3#analysis_vars_header_text").html("Analysis Variables: Study Grouping " + this_study_group );
				jQuery.each( data.analysis_vars, function (){
					//for each row in intermediate table for this study
					var this_analysis_data = jQuery( this );
					jQuery.each( this_analysis_data, function(){
						//console.log( this );
						
						txt_a_im += "<tr>";
						txt_a_im += "<td>" + this.info_id + "</td>";
						txt_a_im += "<td>" + this.indicator + "</td>";
						txt_a_im += "<td>" + this.measure + "</td>";
						txt_a_im += "<td>" + this.info_id_list + "</td>";
						txt_a_im += "<td>" + this.net_effects + "</td>";
						txt_a_im += "<td>" + this.outcome_type + "</td>";
						txt_a_im += "<td>" + this.effectiveness_general + "</td>";
						
						txt_a_im += "</tr>";
						
						//populate the net effects table
						txt_a_effects += "<tr>";
						txt_a_effects += "<td class='analysis_id'>" + this.info_id + "</td>";
						txt_a_effects += "<td>" + this.indicator + "</td>";
						txt_a_effects += "<td>" + this.measure + "</td>";
						
						if( this.duplicate_ims == "N" ) { //static net effect, since it's coming from only 1 IM
							txt_a_effects += "<td>" + this.net_effects + "</td>";
						} else { //dropdown
							txt_a_effects += "<td><select class='net_effects'><option value='-1'> -- Select Net Effect or Association -- </option>";
							var net_effects_vars = this.net_effects;
							jQuery.each( transtria_ajax.effect_direction_lookup, function( i, v ){
								//console.log( v.descr );
								if( parseInt( net_effects_vars ) == parseInt( i ) ){
									var selected = true;
								} else {
									var selected = false;
								}
								txt_a_effects += "<option value='" + i + "'";
								//if we are on our selected value
								if( selected == true ){
									txt_a_effects += " selected='selected' ";
								}
								txt_a_effects += ">" + i + " - " + v.descr + "</option>";
							});
							txt_a_effects += "</select></td>";
						
						}
						
						txt_a_effects += "</tr>";
						//console.log( txt_a_effects );
						
						
						txt_a_effects_hr += "<tr>";
						txt_a_effects_hr += "<td class='analysis_id'>" + this.info_id + "</td>";
						txt_a_effects_hr += "<td>" + this.result_population_result + "</td>";
						txt_a_effects_hr += "<td>" + this.indicator + "</td>";
						txt_a_effects_hr += "<td>" + this.measure + "</td>";
						txt_a_effects_hr += "<td>" + this.effectiveness_hr + "</td>";
						txt_a_effects_hr += "</tr>";
						
						
						//populate the populations table
						txt_a_pops += "<tr>";
						txt_a_pops += "<td class='analysis_id'>" + this.info_id + "</td>";
						txt_a_pops += "<td>" + this.info_id_list_hr + "</td>";
						if( this.result_evaluation_population[0] != undefined ){
							if( this.result_evaluation_population[0].descr != undefined ){
								txt_a_pops += "<td>" + this.result_evaluation_population[0].descr + "</td>";
							} 
						} else {
							txt_a_pops += "<td>no eval pop</td>";
						}
						txt_a_pops += "<td>" + this.result_subpopulationYN + "</td>";
						if( this.result_subpopulation[0] != undefined ){
							if( this.result_subpopulation[0].descr != undefined ){
								txt_a_pops += "<td>" + this.result_subpopulation[0].descr + "</td>";
							}
						} else {
							txt_a_pops += "<td>no sub pop</td>";
						}
						//txt_a_pops += "<td>" + this.result_subpopulation + "</td>";
						txt_a_pops += "<td>" + this.result_population_result + "</td>";
						txt_a_pops += "</tr>";
						
						//domestic
						txt_a_domestic += "<tr>";
						txt_a_domestic += "<td class='analysis_id'>" + this.info_id + "</td>";
						txt_a_domestic += "<td>" + this.domestic_international + "</td>";
						txt_a_domestic += "<td>" + domestic_lookup( this.domestic_international ) + "</td>";
						txt_a_domestic += "</tr>";
						
						//multicomponent //which_tr_parent_multi_component
						txt_a_multicomp += "<tr>";
						txt_a_multicomp += "<td class='analysis_id'>" + this.info_id + "</td>";
						txt_a_multicomp += "<td>" + this.multi_component + "</td>";
						txt_a_multicomp += "<td>" + multi_component_lookup( this.multi_component ) + "</td>";
						txt_a_multicomp += "</tr>";
						
						//complex_lookup
						txt_a_complex += "<tr>";
						txt_a_complex += "<td class='analysis_id'>" + this.info_id + "</td>";
						txt_a_complex += "<td>" + this.complexity + "</td>";
						txt_a_complex += "<td>" + complex_lookup( this.complexity ) + "</td>";
						txt_a_complex += "</tr>";
						
						//participation
						txt_a_participation += "<tr>";
						txt_a_participation += "<td class='analysis_id'>" + this.info_id + "</td>";
						txt_a_participation += "<td>" + this.participation_exposure + "</td>";
						txt_a_participation += "<td>" + participation_lookup( this.participation_exposure ) + "</td>";
						txt_a_participation += "</tr>";
						
						//hr population: black
						txt_a_hr_black += "<tr>";
						txt_a_hr_black += "<td class='analysis_id'>" + this.info_id + "</td>";
						txt_a_hr_black += "<td>" + this.hr_black + "</td>";
						txt_a_hr_black += "<td>" + hr_population_lookup( this.hr_black ) + "</td>";
						txt_a_hr_black += "</tr>";
						
						//hr population: asian
						txt_a_hr_asian += "<tr>";
						txt_a_hr_asian += "<td class='analysis_id'>" + this.info_id + "</td>";
						txt_a_hr_asian += "<td>" + this.hr_asian + "</td>";
						txt_a_hr_asian += "<td>" + hr_population_lookup( this.hr_asian ) + "</td>";
						txt_a_hr_asian += "</tr>";
						
						//hr population: native american
						txt_a_hr_nativeamerican += "<tr>";
						txt_a_hr_nativeamerican += "<td class='analysis_id'>" + this.info_id + "</td>";
						txt_a_hr_nativeamerican += "<td>" + this.hr_nativeamerican + "</td>";
						txt_a_hr_nativeamerican += "<td>" + hr_population_lookup( this.hr_nativeamerican ) + "</td>";
						txt_a_hr_nativeamerican += "</tr>";
						
						//hr population: pacific islander
						txt_a_hr_pacisland += "<tr>";
						txt_a_hr_pacisland += "<td class='analysis_id'>" + this.info_id + "</td>";
						txt_a_hr_pacisland += "<td>" + this.hr_pacificislander + "</td>";
						txt_a_hr_pacisland += "<td>" + hr_population_lookup( this.hr_pacificislander ) + "</td>";
						txt_a_hr_pacisland += "</tr>";
						
						//hr population: hispanic
						txt_a_hr_hispanic += "<tr>";
						txt_a_hr_hispanic += "<td class='analysis_id'>" + this.info_id + "</td>";
						txt_a_hr_hispanic += "<td>" + this.hr_hispanic + "</td>";
						txt_a_hr_hispanic += "<td>" + hr_population_lookup( this.hr_hispanic ) + "</td>";
						txt_a_hr_hispanic += "</tr>";
						
						//hr population: low income
						txt_a_hr_lowincome += "<tr>";
						txt_a_hr_lowincome += "<td class='analysis_id'>" + this.info_id + "</td>";
						txt_a_hr_lowincome += "<td>" + this.hr_lowerincome + "</td>";
						txt_a_hr_lowincome += "<td>" + hr_population_lookup( this.hr_lowerincome ) + "</td>";
						txt_a_hr_lowincome += "</tr>";
						
						//population reach
						txt_a_popreach += "<tr>";
						txt_a_popreach += "<td class='analysis_id'>" + this.info_id + "</td>";
						txt_a_popreach += "<td>" + this.potential_pop_reach + "</td>";
						txt_a_popreach += "<td>" + popreach_lookup( this.potential_pop_reach ) + "</td>";
						txt_a_popreach += "</tr>";
						
						//hr population: low income
						txt_a_hr_popreach += "<tr>";
						txt_a_hr_popreach += "<td class='analysis_id'>" + this.info_id + "</td>";
						txt_a_hr_popreach += "<td>" + this.potential_hr_pop_reach + "</td>";
						txt_a_hr_popreach += "<td>" + hr_population_lookup( this.potential_hr_pop_reach ) + "</td>"; //hr pops has same vals/descr as hr popreach!
						txt_a_hr_popreach += "</tr>";
						
						//state:
						txt_a_state += "<tr>";
						txt_a_state += "<td class='analysis_id'>" + this.info_id + "</td>";
						//dropdown
						txt_a_state += "<td><select class='state'><option value='-1'> -- Select State -- </option>";
						var state_var = this.state;
						jQuery.each( transtria_ajax.state_lookup, function( i, v ){
							//console.log( v.descr );
							if( parseInt( state_var ) == parseInt( v.value ) ){
								var selected = true;
							} else {
								var selected = false;
							}
							txt_a_state += "<option value='" + v.value + "'";
							//if we are on our selected value
							if( selected == true ){
								txt_a_state += " selected='selected' ";
							}
							txt_a_state += ">" + v.value + " - " + v.descr + "</option>";
						});
						txt_a_state += "</select></td>";
						txt_a_state += "</tr>";
						
						//quality
						txt_a_quality += "<tr>";
						txt_a_quality += "<td class='analysis_id'>" + this.info_id + "</td>";
						//dropdown
						txt_a_quality += "<td><select class='quality'><option value='-1'> -- Select Quality -- </option>";
						var quality_var = this.quality;
						jQuery.each( transtria_ajax.quality_lookup, function( i, v ){
							//console.log( v.descr );
							if( parseInt( quality_var ) == parseInt( v.value ) ){
								var selected = true;
							} else {
								var selected = false;
							}
							txt_a_quality += "<option value='" + v.value + "'";
							//if we are on our selected value
							if( selected == true ){
								txt_a_quality += " selected='selected' ";
							}
							txt_a_quality += ">" + v.value + " - " + v.descr + "</option>";
						});
						txt_a_quality += "</select></td>";
						txt_a_quality += "</tr>";
						
						//inclusiveness
						txt_a_inclusiveness += "<tr>";
						txt_a_inclusiveness += "<td class='analysis_id'>" + this.info_id + "</td>";
						//dropdown
						txt_a_inclusiveness += "<td><select class='inclusiveness'><option value='-1'> -- Select Inclusiveness -- </option>";
						var inclu_var = this.inclusiveness;
						jQuery.each( transtria_ajax.inclusiveness_lookup, function( i, v ){
							//console.log( v.descr );
							if( parseInt( inclu_var ) == parseInt( v.value ) ){
								var selected = true;
							} else {
								var selected = false;
							}
							txt_a_inclusiveness += "<option value='" + v.value + "'";
							//if we are on our selected value
							if( selected == true ){
								txt_a_inclusiveness += " selected='selected' ";
							}
							txt_a_inclusiveness += ">" + v.value + " - " + v.descr + "</option>";
						});
						txt_a_inclusiveness += "</select></td>";
						txt_a_inclusiveness += "</tr>";
						
						//access
						txt_a_access += "<tr>";
						txt_a_access += "<td class='analysis_id'>" + this.info_id + "</td>";
						//dropdown
						txt_a_access += "<td><select class='access'><option value='-1'> -- Select Access -- </option>";
						var access_var = this.access;
						jQuery.each( transtria_ajax.access_lookup, function( i, v ){
							//console.log( v.descr );
							if( parseInt( access_var ) == parseInt( v.value ) ){
								var selected = true;
							} else {
								var selected = false;
							}
							txt_a_access += "<option value='" + v.value + "'";
							//if we are on our selected value
							if( selected == true ){
								txt_a_access += " selected='selected' ";
							}
							txt_a_access += ">" + v.value + " - " + v.descr + "</option>";
						});
						txt_a_access += "</select></td>";
						txt_a_access += "</tr>";
						
						//size
						txt_a_size += "<tr>";
						txt_a_size += "<td class='analysis_id'>" + this.info_id + "</td>";
						//dropdown
						txt_a_size += "<td><select class='size'><option value='-1'> -- Select Size -- </option>";
						var size_var = this.size;
						jQuery.each( transtria_ajax.size_lookup, function( i, v ){
							//console.log( v.descr );
							if( parseInt( size_var ) == parseInt( v.value ) ){
								var selected = true;
							} else {
								var selected = false;
							}
							txt_a_size += "<option value='" + v.value + "'";
							//if we are on our selected value
							if( selected == true ){
								txt_a_size += " selected='selected' ";
							}
							txt_a_size += ">" + v.value + " - " + v.descr + "</option>";
						});
						txt_a_size += "</select></td>";
						txt_a_size += "</tr>";
						
						
						
						
						
						//TODO: populate the hr pops table
					
					});
					//console.log( jQuery(this ) );
				
				});
			} //end if analysis_vars
			
			//do we have study grouping-level vars we need to show (study design)
			if( data.study_grouping != undefined ){
				//console.log( data.study_grouping );
				
				//update StudyDesign select for this study group
				jQuery("select.analysis_study_design").val( data.study_grouping.study_design );
				jQuery("select.analysis_study_design_hr").val( data.study_grouping.study_design_hr );
				
				//update text
				if( data.study_grouping.study_design == 0 ){
					jQuery('h4#study_design_label').html("Study Design for Study Group " + this_study_group + ": no design selected" );
				} else {
					jQuery('h4#study_design_label').html("Study Design for Study Group " + this_study_group + ":" + data.study_grouping.study_design );
				}
			
			}
			
			
			//add html to page
			which_tr_parent.after( txt );
			which_tr_parent_dir.after( txt_dir );
			which_tr_parent_intermediate_design.after( txt_design );
			which_tr_parent_intermediate_components.after( txt_components );
			which_tr_parent_intermediate_complexity.after( txt_complexity );
			which_tr_parent_intermediate_purpose.after( txt_purpose );
			which_tr_parent_intermediate_summary.after( txt_summary );
			which_tr_parent_intermediate_settingtype.after( txt_settingtype );
			which_tr_parent_intermediate_pse.after( txt_pse );
			which_tr_parent_intermediate_support.after( txt_support );
			which_tr_parent_intermediate_opposition.after( txt_opposition );
			which_tr_parent_intermediate_sustainability.after( txt_sustainability );
			
			which_tr_parent_intermediate_domestic.after( txt_domestic );
			which_tr_parent_intermediate_intl.after( txt_intl );
			
			which_tr_parent_intermediate_duration.after( txt_duration );
	
			which_tr_parent_analysis_im.after( txt_a_im );
			which_tr_parent_analysis_effect.after( txt_a_effects );
			which_tr_parent_analysis_pops.after( txt_a_pops );
			which_tr_parent_effectiveness_hr.after( txt_a_effects_hr );
			which_tr_parent_domestic.after( txt_a_domestic );
			which_tr_parent_multi_component.after( txt_a_multicomp );
			which_tr_parent_complex.after( txt_a_complex );
			which_tr_parent_participation.after( txt_a_participation );
			
			which_tr_parent_hr_black.after( txt_a_hr_black );
			which_tr_parent_hr_asian.after( txt_a_hr_asian );
			which_tr_parent_hr_nativeamerican.after( txt_a_hr_nativeamerican );
			which_tr_parent_hr_pacisland.after( txt_a_hr_pacisland );
			which_tr_parent_hr_hispanic.after( txt_a_hr_hispanic );
			which_tr_parent_hr_lowincome.after( txt_a_hr_lowincome );
			
			which_tr_parent_popreach.after( txt_a_popreach );
			which_tr_parent_hr_popreach.after( txt_a_hr_popreach );
		
			which_tr_parent_state.after( txt_a_state );
			which_tr_parent_quality.after( txt_a_quality );
			which_tr_parent_inclusiveness.after( txt_a_inclusiveness );
			which_tr_parent_access.after( txt_a_access );
			which_tr_parent_size.after( txt_a_size );
			
			
			
		}
		
		
	}).complete( function( data ) {

		console.log( data );
		usrmsgshell.fadeOut();
		spinny.fadeOut();
		
	});


}


function run_intermediate_analysis(){

	this_study_group = jQuery("select#StudyGroupingIDList").val();
	//update hidden input
	jQuery("input#secret_study_group").val( this_study_group );
	
	//user messages
	var spinny = jQuery('.analysis_messages .spinny');
	var usrmsg = jQuery('.analysis_messages .usr-msg');
	var usrmsgshell = jQuery('.analysis_messages');
	
	//ajax data
	var ajax_action = 'run_intermediate_analysis';
	var ajax_data = {
		'action': ajax_action,
		'transtria_nonce' : transtria_ajax.ajax_nonce,
		'this_study_group' : this_study_group
	};
	
	//ajax get the studies for this group
	jQuery.ajax({
		url: transtria_ajax.ajax_url, 
		data: ajax_data, 
		type: "POST",
		dataType: "json",
		beforeSend: function() {
			usrmsg.html("Running Intermediate Analysis for Study Group: <strong>" + this_study_group + "</strong>, hang tight..." );
			usrmsgshell.fadeIn();
			spinny.fadeIn();
			
		}
	}).success( function( data ) {

		if( data == "0" || data == 0 )  {
			//console.log('what');=
			return;
		} else {
			//var parsed = JSON.parse( data );
			//console.log(parsed.responseText);
			console.log( data );
			
			
		}
		//var post_meat = data['single']; // = JSON.parse(data);
	}).complete( function( data ) {

		//console.log( data );
		usrmsg.html("Intermediate Calculations Complete for Study Group: <strong>" + this_study_group + "</strong>" );
		//usrmsgshell.fadeOut();
		spinny.fadeOut();
		
	});


}

//first analysis - from the intermediate table
function run_analysis(){

	this_study_group = jQuery("select#StudyGroupingIDList").val();
	
	//update hidden input
	jQuery("input#secret_study_group").val( this_study_group );
	
	//user messages
	var spinny = jQuery('.analysis_messages .spinny');
	var usrmsg = jQuery('.analysis_messages .usr-msg');
	var usrmsgshell = jQuery('.analysis_messages');
	
	//ajax data
	var ajax_action = 'run_analysis';
	var ajax_data = {
		'action': ajax_action,
		'transtria_nonce' : transtria_ajax.ajax_nonce,
		'this_study_group' : this_study_group
	};
	
	//ajax get the studies for this group
	jQuery.ajax({
		url: transtria_ajax.ajax_url, 
		data: ajax_data, 
		type: "POST",
		dataType: "json",
		beforeSend: function() {
			usrmsg.html("Running Analysis for Study Group: <strong>" + this_study_group + "</strong>, hang tight..." );
			usrmsgshell.fadeIn();
			spinny.fadeIn();
			
		}
	}).success( function( data ) {
		if( data == "0" || data == 0 )  {
			//console.log('what');=
			return;
		} else {
			//var parsed = JSON.parse( data );
			console.log(data);
		}
		//var post_meat = data['single']; // = JSON.parse(data);
	}).complete( function( data ) {
		//console.log( data );
		usrmsg.html("Analysis Complete for Study Group: <strong>" + this_study_group + "</strong>" );
		//usrmsgshell.fadeOut();
		spinny.fadeOut();
	});
}

//second analysis - considering the form vars
function run_second_analysis(){

	this_study_group = jQuery("select#StudyGroupingIDList").val();
	
	//update hidden input
	jQuery("input#secret_study_group").val( this_study_group );
	
	//user messages
	var spinny = jQuery('.analysis_messages .spinny');
	var usrmsg = jQuery('.analysis_messages .usr-msg');
	var usrmsgshell = jQuery('.analysis_messages');
	
	//ajax data
	var ajax_action = 'run_second_analysis';
	var ajax_data = {
		'action': ajax_action,
		'transtria_nonce' : transtria_ajax.ajax_nonce,
		'this_study_group' : this_study_group
	};
	
	//ajax get the studies for this group
	jQuery.ajax({
		url: transtria_ajax.ajax_url, 
		data: ajax_data, 
		type: "POST",
		dataType: "json",
		beforeSend: function() {
			usrmsg.html("Re-Running Analysis with form variables (Study Design, Net effects) for Study Group: <strong>" + this_study_group + "</strong>, hang tight..." );
			usrmsgshell.fadeIn();
			spinny.fadeIn();
			
		}
	}).success( function( data ) {
		
		//get vars again
		get_vars_by_grouping();
		
		if( data == "0" || data == 0 )  {
			//console.log('what');=
			return;
		} else {
			//var parsed = JSON.parse( data );
			console.log(data);
		}
		//var post_meat = data['single']; // = JSON.parse(data);
	}).complete( function( data ) {
		//console.log( data );
		usrmsgshell.html("Secondary Analysis Complete for Study Group: <strong>" + this_study_group + "</strong>" );
		spinny.fadeOut();
	});
}

//saves analysis vars //TODO: make this apply to 'all' somehow
function save_analysis_vars(){
	//which analysis save button did we touch?
	var which_var_raw = jQuery(this).attr('data-whichvars'); //should match the select(s) that need savin'
	
	//save BOTH designs at once
	//tODO: make this more efficient on the front-end.
	if( which_var_raw == "analysis_study_design" ){
		var which_vars = ["analysis_study_design", "analysis_study_design_hr"];
	} else {
		var which_vars = which_var_raw;
	}
	
	var which_action = jQuery(this).attr('data-whichsave'); //let's us know at which level to save this analysis var
	var which_msg_class = jQuery(this).attr('data-whichmsg'); //let's us know at which level to save this analysis var
	var which_msg = jQuery("#" + which_msg_class);
	if( which_msg.length == 0 ){
		which_msg = jQuery(".analysis_messages .usr-msg"); //hmm, not sure about this check, but NO TIME
	}
	
	var which_id = 0;
	var selected_val = 0;
	var this_save_vars = {};
	var all_save_vars = {};
	
	if( which_action == "save_analysis_vars" ){
	
		//get all the selects of this class (b/c multiple analysis ids)
		jQuery.each( jQuery('select.' + which_vars ), function(){
			//get analysis id
			which_id = jQuery(this).parent('td').siblings('td.analysis_id').html();
			
			//get which value is selected
			selected_val = jQuery(this).val();
			if( selected_val != '-1' ){
				//add this value to the array
				this_save_vars[ which_id ] = selected_val;
			}
			
		});
		//console.log( one_var );
		
		all_save_vars[ which_vars ] = this_save_vars;
		
	} else { //studygroup-level savin' // TODO, 23dec, when does this happen?
	
		/* //23dec2015
		selected_val = jQuery('select.' + which_vars ).val();
		
		if( selected_val == '-1' ){ //don't waste my time
			return;
		}
		all_save_vars[ which_vars ] = selected_val;
		*/
		jQuery.each( which_vars, function(){
			//get analysis id
			which_id = this;
			
			//get which value is selected
			selected_val = jQuery("select." + this).val();
			if( selected_val != '-1' ){
				//add this value to the array
				//this_save_vars[ which_id ] = selected_val;
				all_save_vars[ which_id ] = selected_val;
			}
			
		});
	
	}
	
	//console.log( all_save_vars );
	
	//set up ajax data
	//change action depending on whether it's analysis-id-specific vars or studygroup-specific vars
	var ajax_action = which_action; //'save_analysis_vars';
	var ajax_data = {
		'action': ajax_action,
		'transtria_nonce' : transtria_ajax.ajax_nonce,
		'analysis_vars' : all_save_vars,
		'study_group' : jQuery("input#secret_study_group").val()
	};
	
	//AJAX that noise
	jQuery.ajax({
		url: transtria_ajax.ajax_url, 
		data: ajax_data, 
		type: "POST",
		dataType: "json",
		beforeSend: function() {
			which_msg.html("Saving...");
			//usrmsg.html("Retrieving data, hang tight..." );
			//usrmsgshell.fadeIn();
			//spinny.fadeIn();
			
			
		}
	}).success( function( data ) {
		
		if( data == "0" || data == 0 )  {
			//console.log('what');=
			return;
		} else {
		
		}
	}).complete( function( data ) {

		//console.log( data );
		which_msg.html("Saved!");
		//usrmsgshell.fadeOut();
		//spinny.fadeOut();
		
	}).error( function( data ) {
		which_msg.html("Oh no, error!");
	});;
}


/** LOOKUPS for display ***/
function domestic_lookup( domestic_value ){
	switch ( parseInt( domestic_value ) ){
		case 3:
			return "Both";
			break;
		case 1:
			return "D";
			break;
		case 2:
			return "I";
			break;
		default:
			return domestic_value;
			break;
		
	}
}

//multi_component_lookup
function multi_component_lookup( incoming_value ){
	switch ( parseInt( incoming_value ) ){
		case 1:
			return "Yes";
			break;
		case 0:
		default:
			return "No";
			break;		
	}
}

//complex lookup
function complex_lookup( incoming_value ){
	switch ( parseInt( incoming_value ) ){
		case 1:
			return "Yes";
			break;
		case 0:
			return "No";
			break;		
		case 999:
		default:
			return "Insufficient Information";
			break;	
	}
}

//participation lookup
function participation_lookup( incoming_value ){
	switch ( parseInt( incoming_value ) ){
		case 1:
			return "High";
			break;
		case 2:
			return "Low";
			break;		
		case 999:
		default:
			return "Insufficient Information";
			break;	
	}
}

//hr population lookup
function hr_population_lookup( incoming_value ){
	switch ( parseInt( incoming_value ) ){
		case 1:
			return "High";
			break;
		case 2:
			return "Moderate";
			break;	
		case 3:
			return "Low";
			break;	
		case 4:
			return "No";
			break;		
		case 999:
		default:
			return "Insufficient Information";
			break;	
	}
}

//population reach lookup
function popreach_lookup( incoming_value ){
	switch ( parseInt( incoming_value ) ){
		case 1:
			return "High";
			break;
		case 2:
			return "Low";
			break;		
		case 999:
		default:
			return "Insufficient Information";
			break;	
	}
}





jQuery( document ).ready(function() {

	analysisClickListen();
	
});