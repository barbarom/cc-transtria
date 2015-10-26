
/**** Doc to hold revamp'd javascript for analysis tab **/

function analysisClickListen(){

	//load in studies given study id
	jQuery("a#get_vars_by_group").on("click", get_vars_by_grouping );
	
	//run intermediate analysis for study group
	jQuery("a#run_intermediate_analysis").on("click", run_intermediate_analysis );
	
	//run final analysis for study group
	jQuery("a#run_analysis").on("click", run_analysis );
	
	//show/hides
	jQuery("a#hide_im_table").on("click", toggle_im_table );
	jQuery("a#hide_direction_table").on("click", toggle_direction_table );
	
	
	//when clicking on the ea tabs
	jQuery('.analysis_tab_label').on( "click", analysis_tab_toggle );






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

function get_vars_by_grouping(){

	this_study_group = jQuery("select#StudyGroupingIDList").val();
	
	//user messages
	var spinny = jQuery('.analysis_messages .spinny');
	var usrmsg = jQuery('.analysis_messages .usr-msg');
	var usrmsgshell = jQuery('.analysis_messages');
	
	//where to add table data after success
	which_tr_parent = jQuery("table#intermediate_vars_im tr#data_parent");
	which_tr_parent_dir = jQuery("table#intermediate_vars_direction tr#data_parent");
	which_tr_parent_analysis_im = jQuery("table#analysis_vars_im tr#data_parent");
	
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
			
			//clear taable
			jQuery("table#intermediate_vars_im tr").not(".no_remove").remove();
			
		}
	}).success( function( data ) {
		
		if( data == "0" || data == 0 )  {
			//console.log('what');=
			return;
		} else {
		
			//draw table#intermediate_vars_im (4 cols)
			var txt = "";
			var txt_dir = "";
			var txt_a_im = "";
			//for each study
			if( data.intermediate_vars != undefined ){
				jQuery.each( data.intermediate_vars, function (){
					//for each row in intermediate table for this study
					var this_study_data = jQuery( this );
					jQuery.each( this_study_data, function(){
						//console.log( this );
						
						txt += "<tr>";
						txt += "<td>" + this.StudyID + "</td>";
						txt += "<td>" + this.info_id + "</td>";
						//txt += "<td>" + this.ea_seq_id + "</td>"; //this info now embedded in info_id
						txt += "<td>" + this.indicator + "</td>";
						txt += "<td>" + this.measure + "</td>";
						
						txt += "</tr>";
						
						//also populate the ea direction table
						txt_dir += "<tr>"; 
						txt_dir += "<td>" + this.info_id + "</td>";
						txt_dir += "<td>" + this.indicator + "</td>";
						txt_dir += "<td>" + this.measure + "</td>";
						txt_dir += "<td>" + this.outcome_type + "</td>";
						txt_dir += "<td>" + this.calc_ea_direction + "</td>";
						
						txt_dir += "</tr>";
					
					});
					//console.log( jQuery(this ) );
				
				});
			} //end if intermediate_vars
			
			if( data.analysis_vars != undefined ){
				jQuery.each( data.analysis_vars, function (){
					//for each row in intermediate table for this study
					var this_analysis_data = jQuery( this );
					jQuery.each( this_analysis_data, function(){
						//console.log( this );
						
						txt_a_im += "<tr>";
						txt_a_im += "<td>" + this.StudyGroupingID + "</td>";
						txt_a_im += "<td>" + this.info_id + "</td>";
						txt_a_im += "<td>" + this.indicator + "</td>";
						txt_a_im += "<td>" + this.measure + "</td>";
						
						txt_a_im += "</tr>";
						
						//TODO: populate the net effects table
						
						
						//TODO: populate the effectiveness table
						
						
						//TODO: populate thehr pops table
					
					});
					//console.log( jQuery(this ) );
				
				});
			} //end if intermedaite_vars
			
			//add html to page
			which_tr_parent.after( txt );
			which_tr_parent_dir.after( txt_dir );
			which_tr_parent_analysis_im.after( txt_a_im );
			
		}
		
		
	}).complete( function( data ) {

		console.log( data );
		usrmsgshell.fadeOut();
		spinny.fadeOut();
		
	});


}


function run_intermediate_analysis(){

	this_study_group = jQuery("select#StudyGroupingIDList").val();
	
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
			
			
		}
		//var post_meat = data['single']; // = JSON.parse(data);
	}).complete( function( data ) {

		//console.log( data );
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
			//var parsed = JSON.parse( data );
			console.log(data);
			
			
		}
		//var post_meat = data['single']; // = JSON.parse(data);
	}).complete( function( data ) {

		//console.log( data );
		usrmsgshell.fadeOut();
		spinny.fadeOut();
		
	});


}


//toggles visibility of im dyad table
function toggle_im_table(){

	var which_table = jQuery("table#intermediate_vars_im");
	
	if( which_table.is(":visible") ){
		which_table.slideUp();
		jQuery("a#hide_im_table").html("SHOW I-M DYADS");
	} else {
		which_table.slideDown();
		jQuery("a#hide_im_table").html("HIDE I-M DYADS");
	}
	
}

//toggles visibility of im dyad table
function toggle_direction_table(){

	var which_table = jQuery("table#intermediate_vars_direction");
	
	if( which_table.is(":visible") ){
		which_table.slideUp();
		jQuery("a#hide_direction_table").html("SHOW I-M DIRECTIONS");
	} else {
		which_table.slideDown();
		jQuery("a#hide_direction_table").html("HIDE I-M DIRECTIONS");
	}
	
}



//hmm...
function draw_table( ){







}

















jQuery( document ).ready(function() {

	analysisClickListen();
	
});