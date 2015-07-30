
/**** Doc to hold revamp'd javascript **/

function clickListen(){

	//when clicking on the subpopulations tabs..
	jQuery('#sub_pops_tabs label.subpops_tab_label').on("click", function(){
		
		//hide all subpops content
		jQuery('.subpops_content').hide();
		
		var which_pop = jQuery(this).data("whichpop");
		var which_content = which_pop + '_content';
		
		//add selected class
		jQuery('label.subpops_tab_label').removeClass('active');
		jQuery(this).addClass('active');
	
		jQuery('.subpops_content.' + which_content).show();
	
	});
	
	//TODO: ability status listener
	
	//TODO: restrict options in EA tabs based on intervention tabs. 

	
	
	//Add new ESE tabs
	jQuery('#add-ese-tab').on("click", function(){
		var new_tab_id = 0;
		var last_tab = jQuery('.subpops_tab').last().attr('id');
		var last_tab_arr = last_tab.split('-');
		var lastChar = last_tab_arr[0].substr(last_tab_arr[0].length - 1);
		if (!isNaN(lastChar)) 
		{
			new_tab_id = Number(lastChar) + 1;
		}		
		
		jQuery('#sub_pops_tabs').append("<div id='ese" + new_tab_id + "-tab' class='subpops_tab'><label class='subpops_tab_label' for='" + new_tab_id + "-tab' data-whichpop='" + new_tab_id + "'>ese" + new_tab_id + "</label></div>");
	});

} 

function get_multiselect_ids(){

	var ms_array = [
		'fundingpurpose',
		'domesticfundingsources',
		'searchtoolname'
		]

	return ms_array;


}

//function setup_multiselect(comp) {
function setup_multiselect() {

	var ms_id_array = get_multiselect_ids();
	//console.log( ms_id_array );
	jQuery( function(){
	
		//
		jQuery.each( ms_id_array, function(){
			var ms = jQuery( "#" + this + "" );
			console.log( ms );
			ms.multiselect({
				header: 'Choose option(s)',
				position: {my: 'left bottom', at: 'left top'},
				selectedText: '# of # checked',
				close: function( event, ui ){
					//multiselect_listener( jQuery(this) );
				}
			});
        });
		
		
		jQuery(".general-multiselect").multiselect({
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

		//TODO: what is happening above?  There's always something checked...not cool
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

//populate form with incoming values
function populate_form_existing_study( ){
	
}

//get current study info via ajax
function get_current_study_info(){

	//what's the study id in the url?
	this_study_id = getURLParameter('study_id');

	//ajax data
	var ajax_action = 'get_study_data';
	var ajax_data = {
		'action': ajax_action,
		'this_study_id' : this_study_id,
		'transtria_nonce' : transtria_ajax.ajax_nonce
	};
	
	if( this_study_id !== null ) {
		//Get data associate with this study
		jQuery.ajax({
			url: transtria_ajax.ajax_url, 
			data: ajax_data, 
			type: "POST",
			dataType: "json",
			beforeSend: function() {
				//show spinny
				//spinny.fadeIn();
				
			}
		}).success( function( data ) {
			//console.log('success: ' + data);
			//hmm
			
			//TODO: send message if empty (directing user to add priority page?)
			if( data == "0" || data == 0 )  {
				//console.log('what');=
				return;
			} else {
			
			}
			var post_meat = data['single']; // = JSON.parse(data);
			var pops_meat = data['population_single'];
			var multi_meat = data['multiple'];
			//console.log( post_meat);	
					
			//now.. populate fields!
			//jQuery.each( data, function(index, element) {
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
			
			jQuery(".general-multiselect").multiselect("uncheckAll");
			
			//now handle the incoming multiple data
			jQuery.each( multi_meat, function(index, element) {
				
				//do we have an element div id w this index?  
				// TODO: edit study function in php to return indexes = div ids
				selector_obj = jQuery("#" + index );
				//selector_obj_by_name = jQuery("input[name='" + index + "']");
				
				if( selector_obj.length > 0 ){
				
					//uncheck all?
					selector_obj.multiselect("uncheckAll");
					
					//mark child options of that value as 'selected'
					selector_obj.val( element ).prop("checked", true);
				
					//selector_obj.multiselect("refresh");
					//console.log( selector_obj);
					//console.log( index );
					//console.log( element );
				}
			});
			
		}).complete( function(data){
			//we're done!
			//spinny.css("display", "none");

			//refresh all our multiselects
			jQuery(".general-multiselect").multiselect("refresh");
			
		}).always(function() {
			//regardless of outcome, hide spinny
			//jQuery('.action-steps').removeClass("hidden");
		});
	}
}






function getURLParameter(name) {
	return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search)||[,""])[1].replace(/\+/g, '%20'))||null
}





//On page load...

jQuery( document ).ready(function() {

	 
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


