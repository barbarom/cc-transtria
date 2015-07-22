
/**** Doc to hold revamp'd javascript **/

function clickListen(){

	jQuery('#sub_pops_tabs label.subpops_tab_label').on("click", function(){
		
		//hide all subpops content
		jQuery('.subpops_content').hide();
		
		var which_pop = jQuery(this).data("whichpop");
		var which_content = which_pop + '_content';
		
		//console.log(which_pop);
		//show this subpops content
	
		jQuery('.subpops_content.' + which_content).show();
	
	
	
	});


} 

jQuery( document ).ready(function() {

	 
		jQuery('#abstractorstarttime').datetimepicker();
		jQuery('#abstractorstoptime').datetimepicker();
		jQuery('#validatorstarttime').datetimepicker();
		jQuery('#validatorstoptime').datetimepicker();
		
		//enable clicklisteners
		clickListen();
	
	
});


