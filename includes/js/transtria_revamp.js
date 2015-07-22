
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
	
	
});


