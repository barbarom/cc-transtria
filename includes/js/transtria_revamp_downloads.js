
function clickListen(){
	
	jQuery("a#studies_download_refresh").click( refreshRawStudiesData );
	
	
}


function refreshRawStudiesData(){
	
	//ajax the redraw of the excel sheets for studies data and notify when complete.
	
	//user message things
	var usrmsg = jQuery(".downloads_messages .usr-msg");
	var usrmsgshell = jQuery(".downloads_messages");
	var spinny = jQuery(".downloads_messages .spinny");
	
	//ajax data
	var ajax_action = 'run_studies_excel_update';
	var ajax_data = {
		'action': ajax_action,
		'transtria_nonce' : transtria_ajax.ajax_nonce
	};
	
	jQuery.ajax({
		url: transtria_ajax.ajax_url, 
		data: ajax_data, 
		type: "POST",
		dataType: "json",
		beforeSend: function() {
			
			//show user message and spinny
			usrmsg.html("Updating Raw Studies spreadsheets" );
			usrmsgshell.fadeIn();
			spinny.fadeIn();
			
		}
    }).success( function( data ) {

		//console.log(data);
		//set up strategies
		if( data ){
	

	
		}

    }).complete( function( ) {
		
		spinny.css("display", "none");
		usrmsg.html("Studies spreadheets updated!");
		usrmsgshell.fadeOut(4000);
		
    });

	
	
	
	
	
	
	
	
	
}