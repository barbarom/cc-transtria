
/**** Doc to hold revamp'd javascript **/

function clickListen(){

	//when clicking on the subpopulations tabs..
	jQuery('#sub_pops_tabs label.subpops_tab_label').on("click", sub_pops_tabber ); 
	
	//when clicking on the citation info tabs
	jQuery('#citation_tabs li').on( "click", citation_tab_toggle );
	
	//when clicking on the ea tabs
	jQuery('#effect_association_tabs li').on( "click", ea_tab_toggle );
	
	//get citation info
	jQuery("select#EndNoteID").on("change", get_citation_data);
	
	//show citation info
	jQuery("a.show_citation_data").on("click", show_citation_data);
	
	//load in selected study
	jQuery("a#load_this_study").on("click", load_selected_study );
	
	//load in selected study
	jQuery("a#start_new_study").on("click", function(){
		//construct url
		var redirectTo = transtria_ajax.study_home;
		//aaand, redirect
		window.location.replace( redirectTo );
	});
	
	//save selected study
	jQuery("a.save_study").on("click", save_study );

	//reminder messages on results page
	jQuery("#validatorstoptime, #abstractorstoptime").on("change", function() {
		stop_time_validate();
	});
	//TODO: restrict options in EA tabs based on intervention tabs.
	
	//TODO: ea direction!
	//jQuery("input[id$='_result_effect_association_direction']").
	jQuery("select[id$='_result_indicator_direction']").on( "change", ea_direction_calc );
	jQuery("select[id$='_result_outcome_direction']").on( "change", ea_direction_calc );
	
	
	//Confounders Type option - show if Confounders is YES, hide if NO
	confounder_type_show(); //on init
	jQuery("[name='confounders']").on("change", function(){
		confounder_type_show();
	});

	//IPE applicability question shows HR Subpops select
	ipe_hr_subpops_show();
	jQuery("[name='ipe_applicability_hr_pops']").on("change", function(){
		ipe_hr_subpops_show();
	});

	//ESE oversampling question shows HR subpops select
	ese_hr_subpops_show();
	jQuery("[name='ese_oversampling']").on("change", function(){
		ese_hr_subpops_show();
	});

	//Limit strategies on EA/results tab based on ones selected on intervention
	jQuery("#strategies").on("change", function(){
		strategy_limit_results(); 
	});
	//strategy_limit_results(); //hmm, not yet, multiselects take a while to set up, apparently

	
	//Add new ESE tabs
	jQuery('#add-ese-tab').on("click", copy_ese_tab );
	
	//add new ea tab
	jQuery('a.add_ea_button').on("click", add_empty_ea_tab);
	
	//copy EA tabs from dropdown
	jQuery('.ea_copy_tab_button').on("click", copy_ea_tab );
	
	//when clicking 'not reported', unselected related radio fields
	jQuery('.not_reported_clear').on("click", uncheck_not_reported_related_fields);
	
	//other populations checkbox should enable other populations description (textarea). 
	//TODO: are we clearing anything?
	jQuery('.other_populations_textenable').on("click", other_populations_textarea_enable);
	
} 

function ea_clickListen(){
	//show/hide variables textarea if 'adjusted'/'crude' is selected
	jQuery("input[name$='_result_type']").on("click", show_adjusted_variables );

	//initialize intervention indicator limiter 
	/*var num_current_tabs = jQuery("#effect_association_tabs ul li").length;
	for ( var tabCounter = 1; tabCounter <= num_current_tabs; tabCounter++ ) {
		//our current ea indicator tab
		intervention_indicator_limiter( jQuery('#ea_' + tabCounter + '_result_indicator') );
	} 
	//update our template, as well
	intervention_indicator_limiter( jQuery('#ea_template_result_indicator') );
	*/
}



//function setup_multiselect(comp) {
function setup_multiselect() {

	//console.log( ms_id_array );
	jQuery( function(){

		
		jQuery(".general-multiselect").multiselect({
			header: true,
			position: {my: 'left bottom', at: 'left top'},
			selectedText: '# of # checked',
			//selectedList: 4, 
			close: function( event, ui ){
				//multiselect_listener( jQuery(this) );
			}
		}); 
		
		//ea multiselects
		jQuery(".ea_multiselect").multiselect({
			header: true,
			position: {my: 'left bottom', at: 'left top'},
			selectedText: '# of # checked',
			//selectedList: 4, 
			close: function( event, ui ){
				//multiselect_listener( jQuery(this) );
			}
		}); 
		
		jQuery("#state_setting").multiselect({
			header: true,
			position: {my: 'left bottom', at: 'left top'},
			selectedText: '# of # checked',
			checkAllText: 'Select all states and territories',
			uncheckAllText: 'Deselect all states and territories',
			close: function( event, ui){

			}
		});
		
		jQuery("#searchtooltype").multiselect({
			header: true,
			position: {my: 'left bottom', at: 'left top'},
			selectedText: '# of # checked',
			checkAllText: 'Select all',
			uncheckAllText: 'Deselect all',
			close: function( event, ui){

			}
		});
		//searchtooltype..why is this special?
		
		jQuery('#intervention_indicators').multiselect({
		
			close : function (e) {
				var num_current_tabs = jQuery("#effect_association_tabs ul li").length;
				for ( var tabCounter = 1; tabCounter <= num_current_tabs; tabCounter++ ) {
					//our current ea indicator tab
					intervention_indicator_limiter( jQuery('#ea_' + tabCounter + '_result_indicator') );
				} 
				//update our template, as well
				intervention_indicator_limiter( jQuery('#ea_template_result_indicator') );
				outcomes_assessed_limiter( jQuery('#ea_template_outcome_accessed') );
           }
		});

		jQuery('#intervention_outcomes_assessed').multiselect({
		
			close : function (e) {
				var num_current_tabs = jQuery("#effect_association_tabs ul li").length;
				for ( var tabCounter = 1; tabCounter <= num_current_tabs; tabCounter++ ) {
					//our current ea indicator tab
					outcomes_assessed_limiter( jQuery('#ea_' + tabCounter + '_result_outcome_accessed') );
				} 
				//update our template, as well
				outcomes_assessed_limiter( jQuery('#ea_template_outcome_accessed') );
           }
		});
		
		jQuery("[id$=_ability_status]").multiselect({
			header: true,
			position: {my: 'left bottom', at: 'left top'},
			selectedText: '# of # checked',
			checkAllText: 'Select all',
			close: ability_status_limiter
		});
		
		jQuery.each( jQuery('.general-multiselect'), function(){
		
			jQuery(this).multiselect("uncheckAll");
		
		});
	});
	

/*
	var ms=selector(comp.id);
 
	ms.find('option').remove();
	ms.attr('multiple', 'multiple') // add multiple attribute just in case it isn't there (or set incorrectly)

     for(var j=0; j < comp.options.length; j++) {
        var _o=jQuery("<option>")
        _o.val(comp.options[j].value)
        _o.text(comp.options[j].text)

        if (comp.options[j].selected==true) _o.attr("selected", "selected");
                  
        _o.appendTo(ms);
     }

     try {ms.multiselect('destroy')}catch(e){}
     
	if( comp.id == "state_setting"){
		ms.multiselect(
			{header: true,
			position: {my: 'left bottom', at: 'left top'},
			selectedText: '# of # checked',
			checkAllText: 'Select all states',
			uncheckAllText: 'Deselect all states',
			close: function( event, ui){

			}
		})
	} else if( comp.id == "searchtoolname" || comp.id == "searchtooltype"){
		ms.multiselect(
			{header: true,
			position: {my: 'left bottom', at: 'left top'},
			selectedText: '# of # checked',
			checkAllText: 'Select all',
			uncheckAllText: 'Deselect all',
			close: function( event, ui){

			}
		})
	} else if ( comp.id == "intervention_indicators" ) {
		ms.multiselect(
			{header: true,
			position: {my: 'left bottom', at: 'left top'},
			selectedText: '# of # checked',
			close: function( event, ui ){
				multiselect_selected_display( "intervention_indicators", "intervention_indicators_display");
			}
		})

	} else {
		ms.multiselect({header: 'Choose option(s)',
                     position: {my: 'left bottom', at: 'left top'},
                     selectedText: '# of # checked',
		     close: function( event, ui ){
				multiselect_listener( jQuery(this) );
		     }
     	        })
        }
		
*/
}

//sets up tabs for subpops 
function sub_pops_tabber( ){
	
	//console.log( jQuery(this) ); //looks at label
	var incoming = jQuery(this);
	
	//hide all subpops content
	jQuery('.subpops_content').hide();
	
	var which_pop = incoming.data("whichpop");
	var which_content = which_pop + '_content';
	
	//add selected class
	jQuery('label.subpops_tab_label').removeClass('active');
	incoming.addClass('active');

	jQuery('.subpops_content.' + which_content).show();

};


//tabs toggle for citation info (inner tabs)
function citation_tab_toggle(){

	var whichtab = jQuery(this).find('a').data("whichtab");
	
	//fade out all, remove active class from all l
	jQuery("tr.endnote_citation_data #citation_tabs .one_citation_tab").fadeOut();
	jQuery("tr.endnote_citation_data #citation_tabs ul li").removeClass("active");
	
	jQuery("tr.endnote_citation_data #citation_tabs #" + whichtab).fadeIn();
	jQuery(this).addClass("active");
	//console.log(whichtab);
	
}

//tabs toggle for effect associations
function ea_tab_toggle(){

	var whichtab = jQuery(this).find('label').data("whichea");
	
	//fade out all, remove active class from all l
	jQuery("#effect_association_tabs .one_ea_tab").fadeOut();
	jQuery("#effect_association_tabs ul li label").removeClass("active");
	
	jQuery("#effect_association_tabs #effect_association_tab_" + whichtab).fadeIn();
	jQuery(this).find('label').addClass("active");
	//console.log(whichtab);
	
}


//limit options for intervention components for ea tabs (based on #intervention_indicators)
function intervention_indicator_limiter( incoming ){

	//this should be incoming..
	//var which_ea_select = jQuery('#ea_template_result_indicator');
	var which_ea_select = incoming;
	
	//on the intervention tab - this is the original 
	var original_select = jQuery('#intervention_indicators').multiselect('getChecked');

	var _ea_options = which_ea_select.find('option');

	var _values = [];

	for ( var i=0; i < original_select.length; i++ ) {
		_values.push(original_select[i].value);
	}

	//remove all options from ea_option array
	for ( var i=_ea_options.length-1; i >= 0; i-- ) {
		if (_values.indexOf(_ea_options[i].value) == -1) {
			_ea_options[i].remove();
		}
	}

	_ea_values=[];
	for (var i=0; i < _ea_options.length; i++) {
		_ea_values.push(_ea_options[i].value);
	}

	//populate with the selected items from intervention_outcomes_assessed multiselect
	for (var i=0; i < original_select.length; i++) {
		if (_ea_values.indexOf( original_select[i].value ) == -1) {
			which_ea_select.append('<option value="' + original_select[i].value +'">' + original_select[i].title + "</option>");
		}
	}

	try { 
		which_ea_select.multiselect('refresh');
	} catch(e){
		console.log(e);
	}
}

//limit options for outcomes assessed for ea tabs (based on #intervention_outcomes_assessed)
function outcomes_assessed_limiter( incoming ){

	//this should be incoming..
	//var which_ea_select = jQuery('#ea_template_result_indicator');
	var which_ea_select = incoming;
	
	//on the intervention tab - this is the original 
	var original_select = jQuery('#intervention_outcomes_assessed').multiselect('getChecked');

	var _ea_options = which_ea_select.find('option');

	var _values = [];

	for ( var i=0; i < original_select.length; i++ ) {
		_values.push(original_select[i].value);
	}

	//remove all options from ea_option array
	for ( var i=_ea_options.length-1; i >= 0; i-- ) {
		if (_values.indexOf(_ea_options[i].value) == -1) {
			_ea_options[i].remove();
		}
	}

	_ea_values=[];
	for (var i=0; i < _ea_options.length; i++) {
		_ea_values.push(_ea_options[i].value);
	}

	//populate with the selected items from intervention_outcomes_assessed multiselect
	for (var i=0; i < original_select.length; i++) {
		if (_ea_values.indexOf( original_select[i].value ) == -1) {
			which_ea_select.append('<option value="' + original_select[i].value +'">' + original_select[i].title + "</option>");
		}
	}

	try { 
		which_ea_select.multiselect('refresh');
	} catch(e){
		console.log(e);
	}
}


//get endnote id citation info, for selected endnote id
function get_citation_data(){

	//what's the study id in the url?
	endnote_id = jQuery('#EndNoteID').val();
	
	//user messages
	var spinny = jQuery('.citation_spinny.spinny');
	//var usrmsg = jQuery('.citation_info_messages .usr-msg');
	//var usrmsgshell = jQuery('.citation_info_messages');

	//ajax data
	var ajax_action = 'get_citation_info';
	var ajax_data = {
		'action': ajax_action,
		'endnote_id' : endnote_id,
		'transtria_nonce' : transtria_ajax.ajax_nonce
	};
	
	if( ( endnote_id !== undefined ) && ( endnote_id !== "" ) ) {
		//Get data associate with this study
		jQuery.ajax({
			url: transtria_ajax.ajax_url, 
			data: ajax_data, 
			type: "POST",
			dataType: "json",
			beforeSend: function() {
				//show user message and spinny
				//usrmsg.html("Loading Study ID: " + endnote_id );
				//usrmsgshell.fadeIn();
				spinny.fadeIn();
				
			}
		}).success( function( data ) {
			//console.log('success: ' + data);
			
			
			//TODO: send message if empty (directing user to add priority page?)
			if( data == "0" || data == 0 )  {
				//console.log('what');=
				return;
			} else {
			
			}
			var post_meat = data; // = JSON.parse(data);
			var processed_meat = {};
			var new_index_name = "";
			
			jQuery.each( post_meat, function( index, value ){
				//to make sure the indeces don't conflict w others on the form, add endnotes_ to each 
				new_index_name = "endnotes_" + index;
				processed_meat[ new_index_name ] = value;
				
				//console.log(new_index_name);
				
			});
			
			//now.. populate fields!
			jQuery.each( processed_meat, function( index, value ){
			
				// TODO: edit study function in php to return indexes = div ids
				selector_obj = jQuery("#" + index );
				
				if( selector_obj.length > 0 ){
					//update the (readonly) value
					selector_obj.html( value );
				
				}
				
				//other divs not indexed to match db
				if( index == "endnotes_accession-num" ){
					jQuery( "#accession-num" ).val( value );
				} else if ( index == "endnotes_remote-database-name" ){
					jQuery( "#remote-database-name" ).val( value );
				} else if ( index == "endnotes_remote-database-provider" ){
					jQuery( "#remote-database-provider" ).val( value );
				} 
				//top of study form info
				else if ( index == "endnotes_contributors_authors_author" ){
					jQuery( "#endnote_author" ).html( value );
				} else if ( index == "endnotes_titles_title" ){
					jQuery( "#endnote_title" ).html( value );
				} else if ( index == "endnotes_dates_pub-dates_date" ){
					jQuery( "#endnote_dates" ).html( value );
				} else if ( index == "endnotes_dates_year" ){
					jQuery( "#endnote_dates_year" ).html( value );
				}
			
			});
			
		}).complete( function(data){
			//we're done!  Tell the user
			spinny.css("display", "none");
			//usrmsg.html("Study ID " + this_study_id + " loaded successfully!" );
			//usrmsgshell.fadeOut(6000);
			
			//refresh which phase this is
			var phase = get_phase_by_endnoteid( endnote_id );
			jQuery("#endnote_phase").html( phase );
			
		}).always(function() {
			//regardless of outcome, hide spinny
			//jQuery('.action-steps').removeClass("hidden");
		});
	}
}

//show/hide citation data
function show_citation_data(){

	var citation_div = jQuery('.endnote_citation_data');
	var citation_button_div = jQuery('a.show_citation_data');
	
	
	//are we showing or hiding
	if( citation_div.is(":hidden") ){
		citation_div.slideDown();
		citation_button_div.html("HIDE ENDNOTE CITATION DATA");
	} else {
		citation_div.slideUp();
		citation_button_div.html("SHOW ENDNOTE CITATION DATA");
	}
	

}

//returns phase by endnote id
function get_phase_by_endnoteid( which_endnoteid ){

	if( ( which_endnoteid > 501 ) && ( which_endnoteid < 1103 ) ){
		return "1";
	} else {
		return "2";
	}
}


//get current study info via ajax
function get_current_study_info(){

	//what's the study id in the url?
	this_study_id = getURLParameter('study_id');
	
	//if study id not in current studies list, bounce!
	if( ( jQuery.inArray( parseInt( this_study_id ), transtria_ajax.all_studies ) == "-1" ) && ( this_study_id != undefined ) ){
		//update user message at top of page
		jQuery('.basic_info_messages .usr-msg').html('No Study ID ' + this_study_id + ' in Studies database (as specified in the url parameter).  Please contact CARES if you think this is in error.');
		jQuery('.basic_info_messages').show();
		return false;
	}
	
	//user messages
	var spinny = jQuery('.basic_info_messages .spinny');
	var usrmsg = jQuery('.basic_info_messages .usr-msg');
	var usrmsgshell = jQuery('.basic_info_messages');

	//ajax data
	var ajax_action = 'get_study_data';
	var ajax_data = {
		'action': ajax_action,
		'this_study_id' : this_study_id,
		'transtria_nonce' : transtria_ajax.ajax_nonce
	};
	
	if( ( this_study_id !== null ) && ( this_study_id > 0 ) ) {
		//Get data associate with this study
		jQuery.ajax({
			url: transtria_ajax.ajax_url, 
			data: ajax_data, 
			type: "POST",
			dataType: "json",
			beforeSend: function() {
				//show user message and spinny
				usrmsg.html("Loading Study ID: " + this_study_id );
				usrmsgshell.fadeIn();
				spinny.fadeIn();
				
			}
		}).success( function( data ) {
			//console.log('success: ' + data);
			
			
			//TODO: send message if empty (directing user to add priority page?)
			if( data == "0" || data == 0 )  {
				//console.log('what');=
				return;
			} else {
			
			}
			var post_meat = data['single']; // = JSON.parse(data);
			var pops_meat = data['population_single'];
			var ea_meat = data['ea'];
			var multi_meat = data['multiple'];
			//console.log( multi_meat);	
			
			//add a ea tab shell for all the incoming ea tabs - should have been added in php already
			//jQuery.each( data['num_ea_tabs'], add_empty_ea_tab );
			for( var it = 0; it < data['num_ea_tabs']; it++){
				//add_empty_ea_tab();
			}
			//console.log( data['num_ea_tabs'] );
					
			//now.. populate fields!
			//single data (from studies db table)
			jQuery.each( post_meat, function(index, element) {
				
				//do we have an element div id w this index?  
				// TODO: edit study function in php to return indexes = div ids
				selector_obj = jQuery("#" + index );
				selector_obj_by_name = jQuery("input[name='" + index + "']");
				
				if( selector_obj.length > 0 ){
					
					//console.log( jQuery( selector_obj ) ) ;
					var current_val;
					//what is our selector type?
					if( selector_obj.is('select') ){
						//see if there's a matching option
						var children = selector_obj.children('option');
						//iterate through option values
						jQuery.each( children, function(){
							//what is current option value
							current_val = jQuery(this).val();
							current_val = current_val.trim(); //if whitespace because sometimes there is..*sigh*
							
							var int_trial = parseInt( current_val, 10 );
							//is it string or int? Sometimes there are both ... would that matter? 
							//	Mel doesn't think so since this is to test equality
							if ( isNaN( int_trial ) ){
								//we have strings
								if ( current_val == element ){
									jQuery(this).attr('selected','selected');
									return;
								}
							} else {
								
								if ( int_trial == parseInt( element, 10 ) ){
									jQuery(this).attr('selected','selected');
									return;
								}
							}
						
						
						});
						//console.log( index ); 
						//console.log( element ); 
					} else if ( selector_obj.is('input:text') || selector_obj.is('textarea') ){
						//easy-peasy
						selector_obj.val( element );
					
					} else if ( selector_obj.is('input:checkbox') ){
						if( element == "Y" ){
							selector_obj.attr("checked", "checked");
						}
					} 
				}
				
				//if we have inputs with name instead (radios), update those
				if( selector_obj_by_name.length ){
					if ( selector_obj_by_name.is('input:radio') ){
						//mark as checked whichever radio == element
						jQuery("input[name='" + index + "'][value='" + element + "']").prop('checked',true);
					} 
				}
												
			});
			
			//now handle incoming single popualation data
			jQuery.each( pops_meat, function( pop_type, pop_data ) {
				
				jQuery.each( pop_data, function( index, element ){
					//do we have an element div id w this index?  
					selector_obj = jQuery("#" + index );
					selector_obj_by_name = jQuery("input[name='" + index + "']");
					
					if( selector_obj.length > 0 ){
						
						//console.log( jQuery( selector_obj ) ) ;
						var current_val;
						//what is our selector type?
						if( selector_obj.is('select') ){
							//see if there's a matching option
							var children = selector_obj.children('option');
							//console.log( children );
							
							//iterate through option values
							jQuery.each( children, function(){
								//what is current option value
								current_val = jQuery(this).val();
								current_val = current_val.trim(); //if whitespace because sometimes there is..*sigh*
								
								var int_trial = parseInt( current_val, 10 );
								
								//is it string or int? Sometimes there are both ... would that matter? 
								//	Mel doesn't think so since this is to test equality
								if ( isNaN( int_trial ) ){
									//we have strings
									if ( current_val == element ){
										jQuery(this).attr('selected','selected');
										return;
									}
								} else {
									
									if ( int_trial == parseInt( element, 10 ) ){
										jQuery(this).attr('selected','selected');
										return;
									}
								}
							
							
							});
							//console.log( index ); 
							//console.log( element ); 
						} else if ( selector_obj.is('input:text') || selector_obj.is('textarea') ){
							//easy-peasy
							selector_obj.val( element );
						
						} else if ( selector_obj.is('input:checkbox') ){
							if( element == "Y" ){
								selector_obj.attr("checked", "checked");
							}
						} 
					}
					
					//if we have inputs with name instead (radios), update those
					if( selector_obj_by_name.length ){
						if ( selector_obj_by_name.is('input:radio') ){
							//mark as checked whichever radio == element
							jQuery("input[name='" + index + "'][value='" + element + "']").prop('checked',true);
						} 
					}
										
				});
				
			});
			
			
			//now handle incoming ea data
			jQuery.each( ea_meat, function( ea_num, ea_data) {
				
				jQuery.each( ea_data, function( index, element ){
				
					//do we have an element div id w this index?  
					selector_obj = jQuery("#" + index );
					selector_obj_by_name = jQuery("input[name='" + index + "']");
					
					if( selector_obj.length > 0 ){
						
						//console.log( jQuery( selector_obj ) ) ;
						var current_val;
						//what is our selector type?
						if( selector_obj.is('select') ){
							//see if there's a matching option
							var children = selector_obj.children('option');
							//console.log( children );
							
							//iterate through option values
							jQuery.each( children, function(){
								//what is current option value
								current_val = jQuery(this).val();
								current_val = current_val.trim(); //if whitespace because sometimes there is..*sigh*
								
								var int_trial = parseInt( current_val, 10 );
								
								//is it string or int? Sometimes there are both ... would that matter? 
								//	Mel doesn't think so since this is to test equality
								if ( isNaN( int_trial ) ){
									//we have strings
									if ( current_val == element ){
										jQuery(this).attr('selected','selected');
										return;
									}
								} else {
									
									if ( int_trial == parseInt( element, 10 ) ){
										jQuery(this).attr('selected','selected');
										return;
									}
								}
							
							
							});
							//console.log( index ); 
							//console.log( element ); 
						} else if ( selector_obj.is('input:text') || selector_obj.is('textarea') ){
							//easy-peasy
							selector_obj.val( element );
						
						} else if ( selector_obj.is('input:checkbox') ){
							if( element == "Y" ){
								selector_obj.attr("checked", "checked");
							}
						} 
					}
					
					//if we have inputs with name instead (radios), update those
					if( selector_obj_by_name.length ){
						if ( selector_obj_by_name.is('input:radio') ){
							//mark as checked whichever radio == element
							jQuery("input[name='" + index + "'][value='" + element + "']").prop('checked',true);
						} 
					}
					
				});
				
			});
			
			
			//now handle the incoming multiple data
			jQuery.each( multi_meat, function(index, element) {
				
				//do we have an element div id w this index?  
				// TODO: edit study function in php to return indexes = div ids
				selector_obj = jQuery("#" + index );
				//selector_obj_by_name = jQuery("input[name='" + index + "']");
				
				if( selector_obj.length > 0 ){
					//mark child options of that value as 'selected'
					selector_obj.val( element ).prop("checked", true);
				
				}
			});
			
		}).complete( function(data){
			//we're done!  Tell the user
			spinny.css("display", "none");
			usrmsg.html("Study ID " + this_study_id + " loaded successfully!" );
			usrmsgshell.fadeOut(6000);
			
			//refresh all our multiselects
			jQuery(".multiselect").multiselect("refresh");
			jQuery(".ea_multiselect").multiselect("refresh"); //these are special
			
			//refresh the endnote info
			get_citation_data();
			
			//refresh copytab dropdown options
			refresh_ea_copy_tab();
			
			//initialize ability status limiter
			ability_status_initialize();
			
			//initialize the intervention component limiter and outcomes assessed limiter
			var num_current_tabs = jQuery("#effect_association_tabs ul li").length;
			for ( var tabCounter = 1; tabCounter <= num_current_tabs; tabCounter++ ) {
				//our current ea indicator tab
				intervention_indicator_limiter( jQuery('#ea_' + tabCounter + '_result_indicator') );
				outcomes_assessed_limiter( jQuery('#ea_' + tabCounter + '_result_outcome_accessed') );
			} 
			
			//update our template, as well
			intervention_indicator_limiter( jQuery('#ea_template_result_indicator') );
			outcomes_assessed_limiter( jQuery('#ea_template_result_outcome_accessed') );
			
			//listen to Result Type radio (and show variables textarea if adjusted is selected on any)
			jQuery("input[name$='_result_type']").on("click", show_adjusted_variables );
			
			//initialize adjusted variables (to show if saved value is 'adjusted' for ResultType radio
			init_adjusted_variables();
			
			//stop time messages on results page
			stop_time_validate();
			
			//field-specific limits
			strategy_limit_results();
			ese_hr_subpops_show();
			ipe_hr_subpops_show();
			confounder_type_show();
			
			//uncheck any not-reported radios/fields on incoming data
			var not_reported_checkboxes = jQuery('form#study_form .not_reported_clear');
			jQuery.each( not_reported_checkboxes, uncheck_not_reported_related_fields );
			
			//other populations textarea listen
			jQuery('.other_populations_textenable').off("click", other_populations_textarea_enable);
			jQuery('.other_populations_textenable').on("click", other_populations_textarea_enable);
			
			
		}).always(function() {
			//regardless of outcome, hide spinny
			//jQuery('.action-steps').removeClass("hidden");
		});
	}
}

//refresh page with appropriate data in url (for now)
function load_selected_study(){

	//construct url
	var redirectTo = transtria_ajax.study_home + "?study_id=" + jQuery( "#studyid" ).val();
	
	//aaand, redirect
	window.location.replace( redirectTo );
}



//save study
function save_study(){
	
	//if hidden param 
	if( jQuery("#this_study_id").val().length > 0 ){
		this_study_id = jQuery("#this_study_id").val();
	} else {
		//what's the study id in the url?
		this_study_id = getURLParameter('study_id');
	}
	
	//user messages
	var spinny = jQuery('.basic_info_messages .spinny');
	var usrmsg = jQuery('.basic_info_messages .usr-msg');
	var usrmsgshell = jQuery('.basic_info_messages');
	
	//form metsdata
	var num_ea_tabs = jQuery("#effect_association_tabs ul li").length;
	var last_tab = jQuery('.subpops_tab').last().attr('id').split('-')[0];
	var last_tab_num = last_tab.replace('ese', '');
	var num_ese_tabs = parseInt( last_tab_num );
	
	//form data
	var studies_table_data = jQuery('.studies_table');
	var studies_table_vals = {};
	var population_table_data = jQuery('.population_table');
	var pops_table_vals = {};
	var ea_table_data = jQuery('.ea_table').not("[id^=ea_template]"); //ignore our ea template (hidden and from which we get/copy our ea tabs)
	var ea_table_vals = {};
	var code_table_data = jQuery(".multiselect"); //multiselects all go to code results table
	var checked_holder = {};
	var checked_holder_vals = []; //holds multiselect vals while iterating
	var code_table_vals = {};
	var index_name = "";
	
	jQuery.each( studies_table_data, function( index, element ){
	
		//if element is checkbox, index by name, else by id
		if( jQuery( element ).is('input:radio')){
			//we need to think about this.
			index_name = jQuery(this).attr("name");
			studies_table_vals[ index_name ] = jQuery('input[name="' + index_name + '"]:checked').val();
			
		} else {
			index_name = jQuery(this).attr("id");
			studies_table_vals[ index_name ] = get_field_value( jQuery(this ) );
		}
		
	});
	
	//cycle through pops data and put in flat object
	jQuery.each( population_table_data, function( index, element ){
	
		//if element is checkbox, index by name, else by id
		if( jQuery( element ).is('input:radio')){
			//we need to think about this.
			index_name = jQuery(this).attr("name");
			pops_table_vals[ index_name ] = jQuery('input[name="' + index_name + '"]:checked').val();
			
		} else {
			index_name = jQuery(this).attr("id");
			pops_table_vals[ index_name ] = get_field_value( jQuery(this ) );
		}
		
	});
	
	//cycle through ea data and put in flat object
	jQuery.each( ea_table_data, function( index, element ){
	
		//if element is checkbox, index by name, else by id
		if( jQuery( element ).is('input:radio')){
			//we need to think about this.
			index_name = jQuery(this).attr("name");
			ea_table_vals[ index_name ] = jQuery('input[name="' + index_name + '"]:checked').val();
			
		} else {
			index_name = jQuery(this).attr("id");
			ea_table_vals[ index_name ] = get_field_value( jQuery(this ) );
		}
		
	});
	
	jQuery.each( code_table_data, function( index, element ){
	
		checked_holder_vals = []; //clear our temp checked vals
		index_name = jQuery(this).attr("id");
		//multiselect returns object array of those checked
		checked_holder = jQuery(this).multiselect("getChecked");
		jQuery.each( checked_holder, function(  ){
			checked_holder_vals.push( jQuery(this).val() );
		
		});
		code_table_vals[ index_name ] = checked_holder_vals;
	});
	//console.log( code_table_vals);

	//ajax data
	var ajax_action = 'save_study_data';
	var ajax_data = {
		'action': ajax_action,
		'this_study_id' : this_study_id,
		'transtria_nonce' : transtria_ajax.ajax_nonce,
		'num_ea_tabs' : num_ea_tabs,
		'num_ese_tabs' : num_ese_tabs,
		'studies_table_vals' : studies_table_vals,
		'population_table_vals' : pops_table_vals,
		'ea_table_vals' : ea_table_vals,
		'code_table_vals' : code_table_vals
	};
	

	//Save study data
	jQuery.ajax({
		url: transtria_ajax.ajax_url, 
		data: ajax_data, 
		type: "POST",
		dataType: "json",
		beforeSend: function() {
			//scroll to top
			jQuery("html, body").animate({ scrollTop: 0 }, "slow");
			//show user message and spinny
			usrmsg.html("Saving Study, hang tight..." );
			usrmsgshell.fadeIn();
			spinny.fadeIn();
			
		}
	}).success( function( data ) {
		
		//var post_meat = JSON.parse( data );
		//console.log('success: ' + data['pops_success']);
		
		usrmsg.html("Saving Study, ID: " + data['study_id'] );
		
		
		//TODO: send message if empty (directing user to add priority page?)
		if( data == "0" || data == 0 )  {
			//console.log('what');=
			return;
		} else {
		
		}
		//var post_meat = data['single']; // = JSON.parse(data);
	}).complete( function( data ) {
		spinny.hide();
		//console.log( data );
		if( ( data.responseJSON["study_id"] > 0 ) && ( data.responseJSON["study_id"] != null ) && ( data.responseJSON["study_id"] != undefined ) ){
			usrmsg.html("Study ID " + data.responseJSON["study_id"] + " saved successfully!" );
			usrmsgshell.fadeOut( 6000 );
			
			//make sure our returned study id is saved to hidden param
			jQuery("#this_study_id").val( data.responseJSON["study_id"] );
			
			//add study value to select#studyid and mark as checked
			jQuery("select#studyid").append("<option value='" + data.responseJSON["study_id"] + "'>" + data.responseJSON["study_id"] + "</option>")
			jQuery("select#studyid").val( data.responseJSON["study_id"] );
			
		} else { 
			usrmsg.html("Problem occured while saving. <br /> Report: " + data.responseJSON );
			jQuery("#this_study_id").val("");
		}
		
	
	});;

	
	
}

//gets the field value given a jQuery selector
//does not handle checkboxes right now!
function get_field_value( incoming ){

	var current_val;
	//what is our selector type?
	if( incoming.is('select') ){
		//see if there's a matching option
		var children = incoming.children('option');
		//iterate through option values
		jQuery.each( children, function(){
			if( jQuery(this).prop("selected") == true){
				//what is current option value
				current_val = jQuery(this).val();
				current_val = current_val.trim(); //if whitespace because sometimes there is..*sigh*
			}
		});
	} else if ( incoming.is('input:text') || incoming.is('textarea') ){
		//easy-peasy
		current_val = incoming.val( );
	
	} else if ( incoming.is('input:checkbox') ){
		if( incoming.is(":checked") ){
			current_val = "Y";
		} else {
			current_val = "N";
		}
	} 
	
	return current_val;
	//console.log( incoming );

}

//listen to ability status multiselect and show/hide corresponding percentage inputs

/***** form field functionality ****/

//show 'variables' textarea if corresponding result_type 'Adjusted' radio is selected
function show_adjusted_variables(){

	//get the parent?
	var input_wrapper = jQuery( this ).parent();
	
	//disable _result_type
	//var checked_results_types = jQuery("input[name$='_result_type']:checked");
	var checked_results_types = input_wrapper.find("input:checked");
	
	//if we have checked result types
	if( checked_results_types.length > 0 ) {
		jQuery.each( checked_results_types, function(){
			//if 'adjusted' is chosen, show variables box
			if( jQuery( this ).val() == "A" ){
				jQuery( this ).parents('.one_ea_tab').find('[id$="_results_variables_tr"]').removeClass("noshow");
			} else {
				//hide variables box
				jQuery( this ).parents('.one_ea_tab').find('[id$="_results_variables_tr"]').addClass("noshow");
			}
		
		});
	}
}

function init_adjusted_variables(){

	//get the parent?
	var input_wrapper = jQuery("input[name$='_result_type']").parent();
	
	jQuery.each( input_wrapper, function(){ 
		//var checked_results_types = jQuery("input[name$='_result_type']:checked");
		var checked_results_types = input_wrapper.find("input:checked");
		
		//if we have checked result types
		if( checked_results_types.length > 0 ) {
			jQuery.each( checked_results_types, function(){
				//if 'adjusted' is chosen, show variables box
				if( jQuery( this ).val() == "A" ){
					jQuery( this ).parents('.one_ea_tab').find('[id$="_results_variables_tr"]').removeClass("noshow");
				} else {
					//hide variables box
					jQuery( this ).parents('.one_ea_tab').find('[id$="_results_variables_tr"]').addClass("noshow");
				}
			
			});
		}
	});


}

function ability_status_limiter( all_pops ){


	//console.log( jQuery(this).val() );
	var which_selected = jQuery(this).val();
	var which_pop = jQuery(this).parents(".subpops_content").children("input.population_type").val();
	
	//hide all ability percents in this pop type
	jQuery( "tr." + which_pop + "-ability-percent").hide();
	
	//show only those ability percents chosen
	if( which_selected != null ){
		jQuery.each( which_selected, function() {
		
			var this_selection = parseInt(this);
			jQuery( "tr." + which_pop + "-ability-percent[data-ability-value='" + this_selection + "']" ).show();
		
		});
	}

}

function ability_status_initialize(){

	//on page laod, trigger ability status listener on all pop drop downs
	var initial_ability_dropdowns = jQuery( "[id$='_ability_status']" );
	jQuery.each( initial_ability_dropdowns, function() {
		jQuery( this ).on("click", ability_status_limiter );
		jQuery( this ).trigger("click" );
	});
	
}

//display message on Results page if stop time isn't entered
function stop_time_validate( thisid, thisvalue ){
	//console.log("stop time validate functiooon");
	//really convoluted way of detecting SELECTED event with combobox...change not working, grr
	thisid = thisid || '';
	thisvalue = thisvalue || '';

	//if abstractor selected and no stop time selected, give message 
	var abstractorVal = jQuery("#abstractor").val();
	var validatorVal = jQuery("#validator").val();

	if( ( ( validatorVal != '' ) && ( validatorVal != 'None' ) && ( thisid == '' ) ) ||
		( ( thisid == "validator" ) && ( thisvalue != "00" ) ) ) {
		if ( ( jQuery('#validatorstoptime').val() == '' ) || ( parseInt( jQuery('#validatorstoptime').val()  ) == 0 ) ){
			jQuery('.validator-stop-time-reminder').show();
		} else {
			jQuery('.validator-stop-time-reminder').hide();
		}
	} else if( ( ( ( validatorVal == '' ) || ( validatorVal == 'None' ) ) && ( thisid == '' ) ) || 
		( ( thisid == "validator" ) && ( thisvalue == "00" ) ) ) {
		jQuery('.validator-stop-time-reminder').hide();
	}

	if( ( ( abstractorVal != '' ) && ( abstractorVal != 'None' ) && ( thisid == '' ) ) ||
		( ( thisid == "abstractor" ) && ( thisvalue != "00" ) ) ) { 
		if ( ( jQuery('#abstractorstoptime').val() == '' ) || ( parseInt( jQuery('#abstractorstoptime').val() ) == 0 ) ){
			jQuery('.abstractor-stop-time-reminder').show(); 
		} else {
			jQuery('.abstractor-stop-time-reminder').hide();
		}
	} else if( ( ( ( abstractorVal == '' ) || ( abstractorVal == 'None' ) ) && ( thisid == '' ) ) ||
		( ( thisid == "abstractor" ) && ( thisvalue == "00" ) ) ) {
			jQuery('.abstractor-stop-time-reminder').hide();
	}
}

//change ea direction based on
function ea_direction_calc(){

	var which_tab = jQuery(this).parents('.one_ea_tab').attr("data-which_tab_num");
	
	var ind = jQuery('#ea_' + which_tab + '_result_indicator_direction').val();
	var out = jQuery('#ea_' + which_tab + '_result_outcome_direction').val();
	
	var this_direction = jQuery('#ea_' + which_tab + '_result_effect_association_direction');
	
	//if either indicator or outcome direction isn't selected, we have no EA direction
	if ( ind == undefined || out == undefined || ind == "" || out == "") {
		this_direction.val('');
		return;
	}
	
	//if both ind and out have values, algorithmize for ea direction!
	switch(ind) {
		case '01':
		case '04':
			if (out == '01' || out == '04') {
				this_direction.val('Positive(+)')
			} else if (out == '02' || out == '03') {
				this_direction.val('Negative(-)')
			} else {
				this_direction.val('')
			}
			return;
		case '02':
		case '03':
			if (out == '02' || out == '03') {
				this_direction.val('Positive(+)')
			} else if (out == '01' || out == '04') {
				this_direction.val('Negative(-)')
			} else {
				this_direction.val('')
			}
			return;
	} // switch

}

//show/hide confounders text area on confounders YES/NO
function confounder_type_show(){
	if( jQuery("[name='confounders']:checked").val() == "Y" ){
		jQuery("tr#confounders_type").show();
	} else {
		jQuery("tr#confounders_type").hide();
	}
}

//show/hide HR subpopulations on IPE tabs based on applicability to HR sub pops question
function ipe_hr_subpops_show(){
	if( jQuery("[name='ipe_applicability_hr_pops']:checked").val() == "Y" ){
		jQuery("tr.ipe_hr_subpopulations").show();
	} else {
		jQuery("tr.ipe_hr_subpopulations").hide();
	}
} 

//show/hide HR subpopulations on ESE tab based on oversampling question
function ese_hr_subpops_show(){
	if( jQuery("[name='ese_oversampling']:checked").val() == "Y" ){
		jQuery("tr.ese_hr_subpopulations").show();
	} else {
		jQuery("tr.ese_hr_subpopulations").hide();
	}
}

//Limit strategy selects on EA/Results based on intervention selection
function strategy_limit_results( ){

	var selected = jQuery("#strategies").multiselect("getChecked");
	//get values and titles
	var selection = {};
	var selections = [];  //array to hold selection objects

	jQuery.each( selected.filter(":input"), function(){
	selection = { 
	   _value : this.value,
	   _title : this.title
	}    
	selections.push(selection);

	});
	//update the results strategies dropdowns
	var resultsDropdowns = jQuery("[id$=_result_strategy]");

	jQuery.each( resultsDropdowns, function() {
		var result = jQuery(this); 
		//remove ALL options
		jQuery(this).find("option").remove();

		//ad a -- Select Option -- option
		result.append(
		  jQuery('<option></option>').val( "-1 ").html("---Select---")
	   );
		   
		//iterate for each value
		jQuery.each(selections, function(){
		   //result.find("option[value=" + this + "]").remove();   
		   //add options
		   result.append(
			  jQuery('<option></option>').val(this._value).html(this._title)
		   );
		});
	});

}
	

//when 'add ese' is clicked, copy original ESE tab 
function copy_ese_tab(){

	var new_tab_id = 0;
	var last_tab = jQuery('.subpops_tab').last().attr('id');
	var last_tab_arr = last_tab.split('-');
	var lastChar = last_tab_arr[0].substr(last_tab_arr[0].length - 1);
	if (!isNaN(lastChar)) 
	{
		new_tab_id = Number(lastChar) + 1;
	}
	
	if( new_tab_id > 9 ){
		console.log('max tabs reached!');
		return;
	}
	
	//add a new tab to the pops section
	jQuery('#sub_pops_tabs').append("<div id='ese" + new_tab_id + "-tab' class='subpops_tab'><label class='subpops_tab_label' for='ese" + new_tab_id + "-tab' data-whichpop='ese" + new_tab_id + "'>ese" + new_tab_id + "</label></div>");
	
	//destroy the multiselects before we clone
	try {
		jQuery('.ese_copy_multiselect').multiselect("destroy");
	} catch( err ) {
		console.log( "could not destroy ese_copy_multiselect");
	}
	
	//we will need to copy the main ese tab
	var new_ese_copy = jQuery('.ese_content').clone(true,true);
	var save_study_button_html = jQuery('.button.save_study');
	
	//copy textareas (clone does not do this: http://api.jquery.com/clone/)
	new_ese_copy.find("#ese_other_population_description").val( jQuery(".ese_content #ese_other_population_description").val() );
	
	//copy select selections (clone does not do this)
	var selections_object = {};
	
	//create object with old options and new select object (for fun and easy iteration)
	selections_object[ 'selected_geo_options' ] = {
		_options : jQuery('.ese_content').find('[id$="_geographic_scale"] option:selected'),
		_new_select : new_ese_copy.find('[id$="_geographic_scale"]')
	}
	selections_object[ 'selected_hr_pops_options' ] = {
		_options : jQuery('.ese_content').find('[id$="_hr_subpopulations"] option:selected'),
		_new_select : new_ese_copy.find('[id$="_hr_subpopulations"]')
	}
	selections_object[ 'selected_ability_options' ] = {
		_options : jQuery('.ese_content').find('[id$="_ability_status"] option:selected'),
		_new_select : new_ese_copy.find('[id$="_ability_status"]')
	}
	selections_object[ 'selected_subpops_options' ] = {
		_options : jQuery('.ese_content').find('[id$="_sub_populations"] option:selected'),
		_new_select : new_ese_copy.find('[id$="_sub_populations"]')
	}
	selections_object[ 'selected_youthpops_options' ] = {
		_options : jQuery('.ese_content').find('[id$="_youth_populations"] option:selected'),
		_new_select : new_ese_copy.find('[id$="_youth_populations"]')
	}
	selections_object[ 'selected_profpops_options' ] = {
		_options : jQuery('.ese_content').find('[id$="_professional_populations"] option:selected'),
		_new_select : new_ese_copy.find('[id$="_professional_populations"]')
	}
	selections_object[ 'selected_gender_option' ] = {
		_options : jQuery('.ese_content').find('[id$="_gender"] option:selected'),
		_new_select : new_ese_copy.find('[id$="_gender"]') //there should be just one
	}
	
	//mark 'selected' options as selected in new_ese_copy 
	jQuery.each( selections_object, function( index, value ){
	
		var org_options = this._options;
		var new_select = this._new_select;
		
		//if we have selected options in original tab
		if( org_options.length > 0 ){
		
			jQuery.each( org_options, function(){
				current_option_val = jQuery(this).val();
				//.find('option[value="01"]')
				new_option = new_select.find('option[value="' + current_option_val + '"]');
				if( new_option.length > 0 ) { //if new option exists
					new_option.attr("selected","selected");
				
				}
			});
		}
	});
	
	//var inits
	var new_pop_type = "";
	var old_id = "";
	var new_id = "";
	var old_name = "";
	var new_name = "";
	
	//what prepend do we need?  get current population type
	new_pop_type = "ese" + new_tab_id;
	
	//change current pop type
	new_ese_copy.find(".population_type").val( new_pop_type );
	
	//change subtitle
	new_ese_copy.find("td.inner_table_header").html("<strong>Evaluation Sample - EXPOSED: " + new_tab_id + "</strong>");
	
	//change subtab class
	new_ese_copy.removeClass("ese_content");
	new_ese_copy.addClass( new_pop_type + "_content");
	
	//change all the div ids that begin w ese
	var all_ese_ids = new_ese_copy.find("[id^=ese]");
	var all_ese_names = new_ese_copy.find("[name^=ese]");
	//var all_ese_multis = new_ese_copy.find(".multiselect");
	
	//go through each div in the clone and update the id
	jQuery.each( all_ese_ids, function() {
		//get old id
		old_id = jQuery(this).attr("id");
		//get first 3 digits (hint: it'll be 'ese' every time.  Why are we substringing, Mel?)
		old_id = old_id.substring(3); 
		
		//get ourselves a new id!
		new_id = new_pop_type + old_id;
		
		jQuery(this).attr("id", new_id);
	});
	
	//go through each name in the clone and update the name
	jQuery.each( all_ese_names, function() {
		//get old id
		old_name = jQuery(this).attr("name");
		//get first 3 digits (hint: it'll be 'ese' every time.  Why are we substringing, Mel?)
		old_name = old_name.substring(3); 
		
		//get ourselves a new id!
		new_name = new_pop_type + old_name;
		
		jQuery(this).attr("name", new_name);
	});
	
	//other populations textarea listen
	new_ese_copy.find('.other_populations_textenable').on("click", other_populations_textarea_enable);
	
		
	//append to population div id="populations_Tabs
	new_ese_copy.appendTo( jQuery("#population_tabs") );
	
	//remove old 'save study' button and re-place
	//save_study_button_html.remove();
	//save_study_button_html.appendTo( jQuery("#population_tabs") );
	
	//reattach click listeners to pops tabs
	var which_content = new_pop_type + '_content';
	
	jQuery('#sub_pops_tabs label.subpops_tab_label[data-whichpop="' + new_pop_type + '"]').on("click", function() {
	
		//hide all subpops content
		jQuery('.subpops_content').hide();
		
		//add selected active class after removing it from all
		jQuery('label.subpops_tab_label').removeClass('active');
		jQuery(this).addClass('active');

		jQuery('.subpops_content.' + which_content).show();
		
	});
	
	
	//recreate ese multiselects
	jQuery(".ese_copy_multiselect").multiselect({
		header: true,
		position: {my: 'left bottom', at: 'left top'},
		selectedText: '# of # checked',
		//selectedList: 4, 
		close: function( event, ui ){
			//multiselect_listener( jQuery(this) );
		}
	}); 

}

//when 'add ese' is clicked, copy whichever ea tab is selected
function copy_ea_tab(){

	//remove click listener from copy tab while we're here
	jQuery('.ea_copy_tab_button').off("click", copy_ea_tab );

	var whichtab_to_copy = jQuery(this).siblings('select').val();
	var num_current_tabs = jQuery("#effect_association_tabs ul li").length;
	var new_tab_num = num_current_tabs + 1;
	
	//add a new tab to the ea tabs section
	jQuery('#effect_association_tabs ul').append("<li id='ea-tab-" + new_tab_num + "' class='ea_tab'><label class='ea_tab_label' for='ea-tab-" + new_tab_num + "' data-whichea='" + new_tab_num + "'>EA TAB " + new_tab_num + "</label></li>");
	
	//destroy the multiselects before we clone
	jQuery('.ea_multiselect').multiselect("destroy");
	
	//we will need to copy whichever tab is selected
	var tab_copied = jQuery('#effect_association_tab_' + whichtab_to_copy );
	var new_ea_copy = tab_copied.clone(true,true);
	
	//vars
	//what prepend do we need? 
	var new_prepend = "ea_" + new_tab_num;
	//what prepend are we getting rid of?
	var old_prepend = "ea_" + whichtab_to_copy;
	var old_id = "ea_" + whichtab_to_copy;
	var new_id = "";
	var old_name = "";
	var new_name = "";
	
	//change subtitle
	//new_ese_copy.find("td.inner_table_header").html("<strong>Evaluation Sample - EXPOSED: " + new_tab_num + "</strong>");
		
	//change all the div ids that begin w/ ea_old#
	var all_old_ea_ids = new_ea_copy.find("[id^='" + old_prepend + "']");
	var all_old_ea_names = new_ea_copy.find("[name^='" + old_prepend + "']");
	
	//update overall div#
	new_id = 'effect_association_tab_' + new_tab_num;
	new_ea_copy.attr("id", new_id);
	new_ea_copy.attr("data-which_tab_num", new_tab_num);
	//overall_div_id.attr("id", new_id);
	
	
	//go through each div in the clone and update the id
	jQuery.each( all_old_ea_ids, function() {
		//get old id
		old_id = jQuery(this).attr("id");
		
		//replace old prepend w new, save to new_id
		new_id = old_id.replace(old_prepend, new_prepend);  
		
		//replace div id
		jQuery(this).attr("id", new_id);
	});
	
	//go through each name in the clone and update the name
	jQuery.each( all_old_ea_names, function() {
		//get old id
		old_name = jQuery(this).attr("name");
		
		//replace old prepend w new, save to new_id
		new_name = old_name.replace(old_prepend, new_prepend);  
		
		//update the name?
		jQuery(this).attr("name", new_name);
	});
	
	//copy textareas (clone does not do this: http://api.jquery.com/clone/)
	new_ea_copy.find('[id$="_results_variables"]').val( tab_copied.find('[id$="_results_variables"]').val() );
	
	//go through each dropdown in the old and copy to new(jQuery clone() does not do this!)
	//copy select selections (clone does not do this)
	var selections_object = {};
	
	//create object with old, selected options and new select object (for fun and easy iteration)
	selections_object[ 'selected_duration' ] = {
		_options : tab_copied.find('[id$="_duration"] option:selected'),
		_new_select : new_ea_copy.find('[id$="_duration"]')
	}
	selections_object[ 'selected_stat_model' ] = {
		_options : tab_copied.find('[id$="_result_statistical_model"] option:selected'),
		_new_select : new_ea_copy.find('[id$="_result_statistical_model"]')
	}
	selections_object[ 'selected_result_eval_pop' ] = {
		_options : tab_copied.find('[id$="_result_evaluation_population"] option:selected'),
		_new_select : new_ea_copy.find('[id$="_result_evaluation_population"]')
	}
	selections_object[ 'selected_result_subpops' ] = {
		_options : tab_copied.find('[id$="_result_subpopulations"] option:selected'),
		_new_select : new_ea_copy.find('[id$="_result_subpopulations"]')
	}
	selections_object[ 'selected_ind_direction' ] = {
		_options : tab_copied.find('[id$="_result_indicator_direction"] option:selected'),
		_new_select : new_ea_copy.find('[id$="_result_indicator_direction"]')
	}
	selections_object[ 'selected_out_direction' ] = {
		_options : tab_copied.find('[id$="_result_outcome_direction"] option:selected'),
		_new_select : new_ea_copy.find('[id$="_result_outcome_direction"]')
	}
	selections_object[ 'selected_result_strategy' ] = {
		_options : tab_copied.find('[id$="_result_strategy"] option:selected'),
		_new_select : new_ea_copy.find('[id$="_result_strategy"]')
	}
	selections_object[ 'selected_outcome_type' ] = {
		_options : tab_copied.find('[id$="_result_outcome_type"] option:selected'),
		_new_select : new_ea_copy.find('[id$="_result_outcome_type"]')
	}
	selections_object[ 'selected_outcome_assessed' ] = {
		_options : tab_copied.find('[id$="_result_outcome_accessed"] option:selected'),
		_new_select : new_ea_copy.find('[id$="_result_outcome_accessed"]') //yes, this is a legacy typo.  Nope, we can't change it now. DONT DO IT PLZ.
	}
	selections_object[ 'selected_measures' ] = {
		_options : tab_copied.find('[id$="_result_measures"] option:selected'),
		_new_select : new_ea_copy.find('[id$="_result_measures"]')
	}
	selections_object[ 'selected_indicators' ] = {
		_options : tab_copied.find('[id$="_result_indicator"] option:selected'),
		_new_select : new_ea_copy.find('[id$="_result_indicator"]')
	}
	selections_object[ 'selected_stat_measure' ] = {
		_options : tab_copied.find('[id$="_result_statistical_measure"] option:selected'),
		_new_select : new_ea_copy.find('[id$="_result_statistical_measure"]') 
	}
	
	//mark 'selected' options as selected in new_ea_copy 
	jQuery.each( selections_object, function( index, value ){
	
		var org_options = this._options;
		var new_select = this._new_select;
		
		//if we have selected options in original tab
		if( org_options.length > 0 ){
		
			jQuery.each( org_options, function(){
				current_option_val = jQuery(this).val();
				//.find('option[value="01"]')
				new_option = new_select.find('option[value="' + current_option_val + '"]');
				if( new_option.length > 0 ) { //if new option exists
					new_option.attr("selected","selected");
				
				}
			});
		}
	});
	
	
	
	//append to population div id="populations_Tabs
	new_ea_copy.appendTo( jQuery("#effect_association_tabs") );
	
	//attach click listeners to this ea tab
	var which_content = "#effect_association_tab_" + new_tab_num;
	
	jQuery('#effect_association_tabs label.ea_tab_label[data-whichea="' + new_tab_num + '"]').parent().on("click", function() {
	
		//hide all subpops content
		jQuery('.one_ea_tab').hide();
		
		//add selected active class after removing it from all
		jQuery('label.ea_tab_label').removeClass('active');
		jQuery(this).children('label').addClass('active');

		jQuery(which_content + '.one_ea_tab').fadeIn();
		
	});
	
	//readd click listener to copy tabs (will include new dropdown)
	jQuery('.ea_copy_tab_button').on("click", copy_ea_tab );
	
	//refresh copytab options
	refresh_ea_copy_tab();
	
	//add clicklistener 'variables' textarea click listen based on whether "adjusted" is selected
	//turn off previous click listen and turn back on
	jQuery("input[name$='_result_type']").off("click", show_adjusted_variables );
	jQuery("input[name$='_result_type']").on("click", show_adjusted_variables );

	//create the multiselects again
	//ea multiselects
	jQuery(".ea_multiselect").multiselect({
		header: true,
		position: {my: 'left bottom', at: 'left top'},
		selectedText: '# of # checked',
		//selectedList: 4, 
		close: function( event, ui ){
			//multiselect_listener( jQuery(this) );
		}
	}); 
}

//TODO: can we combine this with the copy function?
//adds blank ea tab to page, copying hidden div
function add_empty_ea_tab(){

	var spinny = jQuery("#results_content .spinny");
	spinny.show();
	
	var num_current_tabs = jQuery("#effect_association_tabs ul li").length;
	var new_tab_num = num_current_tabs + 1;
	//remove click listener from copy tab while we're here
	jQuery('.ea_copy_tab_button').off("click", copy_ea_tab );
	
	//add a new tab to the ea tabs section
	jQuery('#effect_association_tabs ul').append("<li id='ea-tab-" + new_tab_num + "' class='ea_tab'><label class='ea_tab_label' for='ea-tab-" + new_tab_num + "' data-whichea='" + new_tab_num + "'>EA TAB " + new_tab_num + "</label></li>");
	
	//destroy the multiselects before we clone
	jQuery('.ea_multiselect').multiselect("destroy");
	
	//we will need to copy whichever tab is selected
	var new_ea_copy = jQuery('#effect_association_tab_template' ).clone(true,true);


	//vars
	//what prepend do we need? 
	var new_prepend = "ea_" + new_tab_num;
	//what prepend are we getting rid of?
	var old_prepend = "ea_template";
	var old_id = "";
	var new_id = "";
	var old_name = "";
	var new_name = "";
	
	//change subtitle
	//new_ese_copy.find("td.inner_table_header").html("<strong>Evaluation Sample - EXPOSED: " + new_tab_num + "</strong>");
		
	//change all the div ids that begin w/ ea_old#
	var all_old_ea_ids = new_ea_copy.find("[id^='" + old_prepend + "']");
	var all_old_ea_names = new_ea_copy.find("[name^='" + old_prepend + "']");
	
	//update overall div#
	new_id = 'effect_association_tab_' + new_tab_num;
	new_ea_copy.attr("id", new_id);
	new_ea_copy.attr("data-which_tab_num", new_tab_num);
	//overall_div_id.attr("id", new_id);
	
	
	//go through each div in the clone and update the id
	jQuery.each( all_old_ea_ids, function() {
		//get old id
		old_id = jQuery(this).attr("id");
		
		//replace old prepend w new, save to new_id
		new_id = old_id.replace(old_prepend, new_prepend);  
		
		//replace div id
		jQuery(this).attr("id", new_id);
	});
	
	//go through each name in the clone and update the name
	jQuery.each( all_old_ea_names, function() {
		//get old id
		old_name = jQuery(this).attr("name");
		
		//replace old prepend w new, save to new_id
		new_name = old_name.replace(old_prepend, new_prepend);  
		
		//update the name?
		jQuery(this).attr("name", new_name);
	});
		
	//append to effect_associations_tab
	new_ea_copy.appendTo( jQuery("#effect_association_tabs") );
	
	var which_content = "#effect_association_tab_" + new_tab_num;
	
	//attach click listeners to this ea tab
	jQuery('#effect_association_tabs label.ea_tab_label[data-whichea="' + new_tab_num + '"]').parent().on("click", function() {
	
		//hide all subpops content
		jQuery('.one_ea_tab').hide();
		
		//add selected active class after removing it from all
		jQuery('label.ea_tab_label').removeClass('active');
		jQuery(this).children('label').addClass('active');

		jQuery(which_content + '.one_ea_tab').fadeIn();
		
	});
	
	//readd click listener to copy tabs (will include new dropdown)
	jQuery('.ea_copy_tab_button').on("click", copy_ea_tab );
	
	//hide all tabs
	jQuery('label.ea_tab_label').removeClass('active');
	jQuery('.one_ea_tab').hide();
	
	//make this tab visible
	jQuery('#effect_association_tabs label.ea_tab_label[data-whichea="' + new_tab_num + '"]').addClass('active');
	jQuery(which_content + '.one_ea_tab').fadeIn();
	
	//refresh copytab options
	refresh_ea_copy_tab();
	
	//add clicklistener 'variables' textarea click listen based on whether "adjsuted" is selected
	//turn off previous click listen and turn back on
	jQuery("input[name$='_result_type']").off("click", show_adjusted_variables );
	jQuery("input[name$='_result_type']").on("click", show_adjusted_variables );

	//create the multiselects again
	//ea multiselects
	jQuery(".ea_multiselect").multiselect({
		header: true,
		position: {my: 'left bottom', at: 'left top'},
		selectedText: '# of # checked',
		//selectedList: 4, 
		close: function( event, ui ){
			//multiselect_listener( jQuery(this) );
		}
	}); 
		
	spinny.hide();
}
	
//update the ea copy tab ('.ea_copy_tab') options
function refresh_ea_copy_tab(){
	
	//how many tabs?
	var num_tabs = jQuery("#effect_association_tabs ul li").length;
	var tab_selects = jQuery(".ea_copy_tab");
	
	var txt = "";
	for( var i=1; i<=num_tabs; i++ ){
		txt += "<option value='" + i + "'>" + i + "</option>"
	
	}
	
	tab_selects.html( txt );
	
}

//for unselecting related radio fields
function uncheck_not_reported_related_fields(){

	//have we selected the checkbox? We don't care if it's unselected..
	if( jQuery(this).is(":checked") ){
		//get the id of this checkbox
		var not_reported_id = jQuery(this).attr("id");
		
		//which radio is related to this 'not reported' checkbox?
		var not_reported_radio = jQuery('form#study_form').find("[data-notreported_id='" + not_reported_id +"']");
		
		//for each radio (Yes and No), clear selection
		jQuery.each( not_reported_radio, function(){
			jQuery(this).prop('checked', false); 
		});
	}
}

//other populations checkbox checked should enable other populations description
function other_populations_textarea_enable(){

	//have we selected the checkbox? 
	var is_checked = jQuery(this).is(":checked");
	
	//get the id of this checkbox
	var other_pops_checkbox_id = jQuery(this).attr("id");
	
	//which radio is related to this 'not reported' checkbox?
	var other_pops_textarea = jQuery('form#study_form').find("[data-otherpopcheckbox_id='" + other_pops_checkbox_id +"']");
	
	//for each radio (Yes and No), clear selection
	jQuery.each( other_pops_textarea, function(){
		jQuery(this).prop('disabled', !is_checked); 
	});

}


//helper function to get URL param
function getURLParameter(name) {
	return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search)||[,""])[1].replace(/\+/g, '%20'))||null
}




//On page load...

jQuery( document ).ready(function() {

	//load datetimepickers
	jQuery('#abstractorstarttime').datetimepicker();
	jQuery('#abstractorstoptime').datetimepicker();
	jQuery('#validatorstarttime').datetimepicker();
	jQuery('#validatorstoptime').datetimepicker();
	
	//set up our multiple checkboxes
	setup_multiselect();
	
	//get current study info
	get_current_study_info();
	
	//enable clicklisteners
	clickListen();
	
	//initialize ability status limiter
	ea_clickListen();

	//in case we have an endnoteid param in the url, get the citation data
	get_citation_data();
	//initialize message on results page and then add change listener to stop time inputs
	//stop_time_validate();
	
	/*
	jQuery.validator.setDefaults({
		debug: true,
		success: "valid"
		});
	jQuery( "#myform" ).validate({
		rules: {
			field: {
			  number: true
			}
		}
	}); */
	
	
});


