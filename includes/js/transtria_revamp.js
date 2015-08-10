
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
	
	//save selected study
	jQuery("a.save_study").on("click", save_study );
	
	//TODO: ability status listener
	//jQuery("").on("click", ability_status_limiter );
	
	
	//TODO: restrict options in EA tabs based on intervention tabs. 

	//TODO: variables in ea tabs on 'adjusted'
	
	//TODO: ea direction!
	
	//TODO: list indicators selected (Intervention/Partnerships tab)
	
	
	//Add new ESE tabs
	jQuery('#add-ese-tab').on("click", copy_ese_tab );
	
	jQuery('a.add_ea_button').on("click", add_empty_ea_tab);
	//copy EA tabs from dropdown
	jQuery('.ea_copy_tab_button').on("click", copy_ea_tab );
	
	//TODO: add new EA tab from a#add_effect_association_row
	//jQuery("#add_effect_association_row").on("click", add_ea_tab);  //from div#effect_association_tab_template

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
			checkAllText: 'Select all states',
			uncheckAllText: 'Deselect all states',
			close: function( event, ui){

			}
		});
		
		jQuery("#searchtooltype").multiselect({
			header: true,
			position: {my: 'left bottom', at: 'left top'},
			selectedText: '# of # checked',
			checkAllText: 'Select all states',
			uncheckAllText: 'Deselect all states',
			close: function( event, ui){

			}
		});
		//searchtooltype

		
		jQuery("[id$=_ability_status]").multiselect({
			header: true,
			position: {my: 'left bottom', at: 'left top'},
			selectedText: '# of # checked',
			checkAllText: 'Select stati',
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
				
				if( index == "endnotes_accession-num" ){
					jQuery( "#accession-num" ).val( value );
				} else if ( index == "endnotes_remote-database-name" ){
					jQuery( "#remote-database-name" ).val( value );
				} else if( index == "endnotes_remote-database-provider" ){
					jQuery( "#remote-database-provider" ).val( value );
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
			//console.log( ea_meat);	
					
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
			
			//now handle incoming single popualation data
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
				
					//uncheck all?
					//selector_obj.multiselect("uncheckAll");
					
					//mark child options of that value as 'selected'
					selector_obj.val( element ).prop("checked", true);
				
					//selector_obj.multiselect("refresh");
					//console.log( selector_obj);
					//console.log( index );
					//console.log( element );
				}
			});
			
		}).complete( function(data){
			//we're done!  Tell the user
			spinny.css("display", "none");
			usrmsg.html("Study ID " + this_study_id + " loaded successfully!" );
			usrmsgshell.fadeOut(6000);
			
			//refresh all our multiselects
			jQuery(".general-multiselect").multiselect("refresh");
			jQuery(".ea_multiselect").multiselect("refresh");
			
			//refresh the endnote info
			get_citation_data();
			
			//refresh copytab dropdown options
			refresh_ea_copy_tab();
			
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
	//what's the study id in the url?
	this_study_id = getURLParameter('study_id');
	
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
	
	//console.log( studies_table_vals);

	//ajax data
	var ajax_action = 'save_study_data';
	var ajax_data = {
		'action': ajax_action,
		'this_study_id' : this_study_id,
		'transtria_nonce' : transtria_ajax.ajax_nonce,
		'num_ea_tabs' : num_ea_tabs,
		'num_ese_tabs' : num_ese_tabs,
		'studies_table_vals' : studies_table_vals,
		'population_table_vals' : pops_table_vals
	};
	

	//Save study data
	jQuery.ajax({
		url: transtria_ajax.ajax_url, 
		data: ajax_data, 
		type: "POST",
		dataType: "json",
		beforeSend: function() {
			//show user message and spinny
			usrmsg.html("Saving Study" );
			usrmsgshell.fadeIn();
			spinny.fadeIn();
			
		}
	}).success( function( data ) {
		
		//var post_meat = JSON.parse( data );
		console.log('success: ' + data['studies_test']);
		
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
		usrmsgshell.fadeOut( 1000 );

		
	
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

//TODO - this on form instantiation
function ability_status_limiter( all_pops ){


	//console.log( jQuery(this).val() );
	var which_selected = jQuery(this).val();
	var which_pop = jQuery(this).parents(".subpops_content").children("input.population_type").val();
	
	//hide all ability percents in this pop type
	jQuery( "tr." + which_pop + "-ability-percent").hide();
	
	//show only those ability percents chosen
	jQuery.each( which_selected, function() {
	
		var this_selection = parseInt(this);
		jQuery( "tr." + which_pop + "-ability-percent[data-ability-value='" + this_selection + "']" ).show();
	
	});

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
	
	//add a new tab to the pops section
	jQuery('#sub_pops_tabs').append("<div id='ese" + new_tab_id + "-tab' class='subpops_tab'><label class='subpops_tab_label' for='ese" + new_tab_id + "-tab' data-whichpop='ese" + new_tab_id + "'>ese" + new_tab_id + "</label></div>");
	
	//we will need to copy the main ese tab
	var new_ese_copy = jQuery('.ese_content').clone(true,true);
	
	//vars
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
	
	//change all the div ids that beginw ese
	var all_ese_ids = new_ese_copy.find("[id^=ese]");
	var all_ese_names = new_ese_copy.find("[name^=ese]");
	
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
		
	//append to population div id="populations_Tabs
	new_ese_copy.appendTo( jQuery("#population_tabs") );
	
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
		
	//Mel doesn't think we need to get data from the server at all but rather from the page itself, so she's commenting this out for now..
	/*
	//ajax data
	var ajax_action = 'create_evaluation_sample_div';
	var ajax_data = {
		'action': ajax_action,
		'new_tab_id' : new_tab_id,
		'transtria_nonce' : transtria_ajax.ajax_nonce
	};		
	jQuery.ajax({
		url: transtria_ajax.ajax_url, 
		data: ajax_data, 
		type: "POST",
		dataType: "json",

	}).success( function( data ) {
		console.log(data.d);
	}).complete( function( data ){
				//we're done!
				//spinny.css("display", "none");

				//refresh all our multiselects
				//jQuery(".multiselect").multiselect("refresh");
				
	}).always(function() {
		//regardless of outcome, hide spinny
		//jQuery('.action-steps').removeClass("hidden");
	});
	*/

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
	
	//we will need to copy whichever tab is selected
	var new_ea_copy = jQuery('#effect_association_tab_' + whichtab_to_copy ).clone(true,true);
	
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

}

//TODO: can we combine this with the copy function?
//adds blank ea tab to page, copying hidden div
function add_empty_ea_tab(){

	//remove click listener from copy tab while we're here
	jQuery('.ea_copy_tab_button').off("click", copy_ea_tab );

	var num_current_tabs = jQuery("#effect_association_tabs ul li").length;
	var new_tab_num = num_current_tabs + 1;
	
	//add a new tab to the ea tabs section
	jQuery('#effect_association_tabs ul').append("<li id='ea-tab-" + new_tab_num + "' class='ea_tab'><label class='ea_tab_label' for='ea-tab-" + new_tab_num + "' data-whichea='" + new_tab_num + "'>EA TAB " + new_tab_num + "</label></li>");
	
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
	
	//enable clicklisteners
	clickListen();
	
	//set up our multiple checkboxes
	setup_multiselect();
	
	//get current study info
	get_current_study_info();
	
	
});


