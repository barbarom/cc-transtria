
/**** Doc to hold revamp'd javascript for analysis tab **/

function analysisClickListen(){

	//load in studies given study id
	jQuery("a#get_studies_by_group").on("click", get_studies_by_grouping );







}


function get_studies_by_grouping(){

	this_study_group = jQuery("select#StudyGroupingIDList").val();
	
	//user messages
	var spinny = jQuery('.analysis_messages .spinny');
	var usrmsg = jQuery('.analysis_messages .usr-msg');
	var usrmsgshell = jQuery('.analysis_messages');
	
	//ajax data
	var ajax_action = 'get_study_ids_by_group';
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
			usrmsg.html("Retrieving data, hang tight..." );
			usrmsgshell.fadeIn();
			spinny.fadeIn();
			
		}
	}).success( function( data ) {
		
		//var post_meat = JSON.parse( data );
		//console.log('success: ' + data['pops_success']);
		
		//usrmsg.html("Saving Study, ID: " + data['study_id'] );
		
		//TODO: send message if empty (directing user to add priority page?)
		if( data == "0" || data == 0 )  {
			//console.log('what');=
			return;
		} else {
			
		}
		//var post_meat = data['single']; // = JSON.parse(data);
	}).complete( function( data ) {

		console.log( data );
		usrmsgshell.fadeOut();
		spinny.fadeOut();
		
	});


}




















jQuery( document ).ready(function() {

	analysisClickListen();
	
});