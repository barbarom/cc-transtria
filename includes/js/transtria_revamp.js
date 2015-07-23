
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


} 

//function setup_multiselect(comp) {
function setup_multiselect() {

	jQuery( function(){
		jQuery("select.multiselect").multiselect(
			{header: 'Choose option(s)',
			position: {my: 'left bottom', at: 'left top'},
			selectedText: '# of # checked',
			close: function( event, ui ){
				multiselect_listener( jQuery(this) );
			}
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
			//var post_meat = data; // = JSON.parse(data);
				
			//now.. populate fields!
			jQuery.each( data, function(index, element) {
				
				//do we have an element div id w this index?  
				// TODO: edit study function in php to return indexes = div ids
				selector_obj = jQuery("#" + index );
				if( selector_obj.length > 0 ){
					
					console.log( jQuery( selector_obj ) ) ;
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
				
				
				
			});
			
		}).complete( function(data){
			//we're done!
			//spinny.css("display", "none");

			
			
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
		
		//set up our multipole checkboxes
		setup_multiselect();
		
		//get current study info
		get_current_study_info();
	
	
});


