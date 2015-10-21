
/**** Doc to hold revamp'd javascript for analysis tab **/

function analysisClickListen(){

	//load in studies given study id
	jQuery("a#get_studies_by_group").on("click", get_studies_by_grouping );
	
	//run analysis for study group
	jQuery("a#run_analysis").on("click", run_analysis );







}


function get_studies_by_grouping(){

	this_study_group = jQuery("select#StudyGroupingIDList").val();
	
	//user messages
	var spinny = jQuery('.analysis_messages .spinny');
	var usrmsg = jQuery('.analysis_messages .usr-msg');
	var usrmsgshell = jQuery('.analysis_messages');
	
	//where to add table data after success
	which_tr_parent = jQuery("table#intermediate_vars tr#data_parent");
	
	//ajax data
	var ajax_action = 'get_im_dyads_by_group';
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
			
		}
	}).success( function( data ) {
		
			if( data == "0" || data == 0 )  {
			//console.log('what');=
			return;
		} else {
		
			//draw table#intermediate_vars (4 cols)
			var txt = "";
			//for each study
			jQuery.each( data, function (){
				//for each row in intermediate table for this study
				var this_study_data = jQuery( this );
				jQuery.each( this_study_data, function(){
					//console.log( this );
					
					txt += "<tr>";
					txt += "<td>" + this.StudyID + "</td>";
					txt += "<td>" + this.unique_id + "</td>";
					txt += "<td>" + this.ea_seq_id + "</td>";
					txt += "<td>" + this.indicator + "</td>";
					txt += "<td>" + this.measure + "</td>";
					
					
					
					txt += "</tr>";
				
				});
				//console.log( jQuery(this ) );
			
			});
			
			//add html to page
			which_tr_parent.after( txt );
			
		}
		
		
	}).complete( function( data ) {

		console.log( data );
		usrmsgshell.fadeOut();
		spinny.fadeOut();
		
	});


}


function run_analysis(){

	this_study_group = jQuery("select#StudyGroupingIDList").val();
	
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
			var parsed = JSON.parse( data );
			console.log(parsed.responseText);
			
			//draw table#intermediate_vars (4 cols)
			
			
		}
		//var post_meat = data['single']; // = JSON.parse(data);
	}).complete( function( data ) {

		//console.log( data );
		usrmsgshell.fadeOut();
		spinny.fadeOut();
		
	});


}


//hmm...
function draw_table( ){







}

















jQuery( document ).ready(function() {

	analysisClickListen();
	
});