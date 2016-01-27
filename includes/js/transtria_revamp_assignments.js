


//function to get values for assignment tab
function get_assignment_data_basic( ){

	var studies = [];
    //clear __CARES__.used_endnotedids
	//__CARES__.used_endnoteids=[]; //clearing method works if NO REFERENCES

	var usrmsg = jQuery(".assignments_messages .usr-msg");
	var usrmsgshell = jQuery(".assignments_messages");
	var spinny = jQuery(".assignments_messages .spinny");
    //placeholder for studyids in table
	var data_studyids = []; 

	//ajax data
	var ajax_action = 'get_assignments';
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
			usrmsg.html("Loading Assignments.." );
			usrmsgshell.fadeIn();
			spinny.fadeIn();
			
		}
	}).success( function( data ) {
	
	//now take json data and turn into Main table on Assignments tab		
		if( data.assignments_info ){
			
			var len = data.assignments_info.length;
			var endnotesEmptyBool = jQuery.isEmptyObject( data.endnotes_info);
			
			var assignmentTable = document.getElementById('assignment-table');
			var assignmentTableBody = document.getElementById('assignment-table').getElementsByTagName('tbody')[0];
			var tdHolder = document.createDocumentFragment();
			var sg_assigment_dd = jQuery("#StudyGroupingIDAssignment");
			
			//cell holders
			var sgid_cell_txt = "";
			var studyid_cell_txt = "";
			var recnumber_cell_txt = "";
			var phase_cell_txt = "";
			var author_cell_txt = "";
			var date_cell_txt = "";
			var title_cell_txt = "";
			var abstract_cell_txt = "";
			var assigned_cell_txt = "";
			var validcomplete_cell_txt = "";
			var readyanalysis_cell_txt = "";
			var sgcomplete_cell_txt = "";
			
			var txt = "";
			
			//clone the Study Grouping IDdropdown
			var sgid_clone;
			sgid_clone = sg_assigment_dd.clone();
			sgid_clone.addClass("sgid_dd");
			sgid_clone.removeAttr('id');
			
			sg_assigment_dd.hide();

			//get html of sgid_options; two methods, since Chrome shows empty for html()
			var sgid_html = sgid_clone.html();
			if( sgid_html == "" ){
				var childs = sgid_clone.children();
				var childs_html = "";
				var childs_temp;

				jQuery.each( childs, function( index, value ) {
					childs_temp = jQuery( value ).html();
					childs_html += "<option value ='" + childs_temp + "'>" + childs_temp + "</option>"; 

				});
				sgid_html = childs_html;

			}
			
			//parse text to add dropdown to table, adding null option
			var studygroup = "<select class='StudyGroupingClass'>";
			studygroup += sgid_html + "</select>";
			studygroup_obj = jQuery( studygroup );
			
			if( len > 0 ){
				for( var i=0; i<len; i++ ){
					
					var row = document.createElement('tr');
					//adding rows 
					//var row = assignmentTableBody.insertRow(i);
					var sgid_cell = row.insertCell(0);
					var studyid_cell = row.insertCell(1);
					var recnumber_cell = row.insertCell(2);
					var phase_cell = row.insertCell(3);
					var author_cell = row.insertCell(4);
					author_cell.className = "author";
					var date_cell = row.insertCell(5);
					var title_cell = row.insertCell(6);
					title_cell.className = "title";
					var abstract_cell = row.insertCell(7);
					var assigned_cell = row.insertCell(8);
					var validcomplete_cell = row.insertCell(9);
					var readyanalysis_cell = row.insertCell(10);
					var sgcomplete_cell = row.insertCell(11);
					
					//placeholders
					var studygroup_val;
					
					//get endnoteid and summary; TODO: add to __CARES__.used_endnoteids
					endnoteid = data.assignments_info[i].EndNoteID || ""; 
					
					var endnoteobject = {};
					if( ! endnotesEmptyBool ){
						endnoteobject = data.endnotes_info;
					} 
					var readyanalysis;
					var readyanalysis_checked;
					var this_phase = get_phase_by_endnoteid( endnoteid );

					//for table links
					var basepath = transtria_ajax.study_home;

					//we got some assignment data back from the server, woo.
					if( data.assignments_info[i] ){
						//console.log(i);
						//handle null/empty data
						if ( data.assignments_info[i].StudyGroupingID != undefined ) {
							studygroup_val = (String( data.assignments_info[i].StudyGroupingID.length > 0 )) ? parseInt( data.assignments_info[i].StudyGroupingID ) : ""; 
						} else {
							studygroup_val = 0;
						} 

						studygroup_obj.val( String(studygroup_val) ).change();
						studygroup_obj.attr("data-whichsg", studygroup_val );
						var studygroup_string = studygroup_obj.prop('outerHTML');
						
						//if ReadyAnalysis is null, mark as N
						if (data.assignments_info[i].readyAnalysis != undefined) {
							readyanalysis = (String( data.assignments_info[i].readyAnalysis.length > 0 )) ? data.assignments_info[i].readyAnalysis : "N"; 
						} else { //there is no val in db
							readyanalysis = "N";
						}

						//set checked property
						if( ( readyanalysis == "Y" ) || ( readyanalysis == "true" ) ){
							readyanalysis_checked = 'checked';
						} else {
							readyanalysis_checked = '';
						}

						//have tr hold phase and study id info as class
						row.className = ""; //reset row classname
						row.className += "assignment-study"; 

						if( endnoteobject != null && endnoteid > 0 && endnoteobject != undefined ){
							row.className += " phase_" + this_phase; //add class to row
						}
						row.className += " " + data.assignments_info[i].StudyID;
						row.className += " studyid_" + data.assignments_info[i].StudyID;
						row.dataset.studyid = data.assignments_info[i].StudyID;

						
						//sgid_cell_text = studygroup;
						sgid_cell_text = studygroup_string;
						sgid_cell.innerHTML = sgid_cell_text;
						
						studyid_cell.className = ""; //reset class name
						studyid_cell.className = "studyid_val"; //reset class name
						studyid_cell_txt = "<a class='link' href='" + basepath + "?study_id=" + data.assignments_info[i].StudyID + "'>" + data.assignments_info[i].StudyID + "</a>";
						studyid_cell.innerHTML = studyid_cell_txt;
						
						//EndNote rec number, for now since Mel see that as unique
						recnumber_cell_txt = endnoteid;
						recnumber_cell.innerHTML = recnumber_cell_txt;
					
						//phase
						if( endnoteobject != null && endnoteid > 0 && endnoteobject != undefined ){
							phase_cell_txt = this_phase;
							phase_cell.innerHTML = phase_cell_txt;
						} else {
							phase_cell_txt = ""; //reset phase
							phase_cell.innerHTML = phase_cell_txt;
						}
						
						if( endnoteobject[ endnoteid ] != null && endnoteid > 0 && endnoteobject[ endnoteid ] != undefined ){
						//from endnote: author, year, title
						
							author_cell_txt = endnoteobject[ endnoteid ][ 'contributors_authors_author' ];
							author_cell.innerHTML = author_cell_txt;
							date_cell_txt = endnoteobject[ endnoteid ][ 'dates_pub-dates_date' ] + " " + endnoteobject[ endnoteid ][ 'dates_year' ];
							date_cell.innerHTML = date_cell_txt;
							title_cell_txt = endnoteobject[ endnoteid ][ 'titles_title' ];
							title_cell.innerHTML = title_cell_txt;
							
						} else {
						  //placeholders for author, year, title
							author_cell_txt = "----";
							author_cell.innerHTML = author_cell_txt;
							date_cell_txt = "----";
							date_cell.innerHTML = date_cell_txt;
							title_cell_txt = "----";
							title_cell.innerHTML = title_cell_txt;

						}
						
						//abstraction complete, assignment Y/N, validation complete tds
						abstract_cell_txt = data.assignments_info[i].abstraction_complete;
						abstract_cell.innerHTML = abstract_cell_txt;
						assigned_cell_txt = "";
						assigned_cell.innerHTML = assigned_cell_txt;
						validcomplete_cell_txt = data.assignments_info[i].validation_complete;
						validcomplete_cell.innerHTML = validcomplete_cell_txt;
						
						
						//new checkbox field with study id in value..
						readyanalysis_cell_txt = "<input type='checkbox' name='ready-analysis' value='" + studygroup_val + "' " + readyanalysis_checked + "></input>";
						readyanalysis_cell.innerHTML = readyanalysis_cell_txt;
						

						data_studyids.push( data.assignments_info[i].StudyID );
						
						
						if ( i > 30 ){
							break;
						}
						
						
					}
					
					
					assignmentTableBody.appendChild(row);
		
				} //end foreach

			} //end if len > 0
			
		}

		/*
		jQuery.tablesorter.addParser({
			id:'select',
			is: function() {
			 return false;
			},
			format: function(s, table, cell, cellIndex) {
			 var $c = jQuery(cell);

			 return $c.find('select').val() || s;
			},
			type: 'text'
		});

		jQuery("#assignment-table").tablesorter(
			{widgets: ['zebra'],
			headers: {
			  3: {
				 sorter: 'select'
			  }
			}
			}
		);

		*/
		
		
		//make all studygroupid checkboxes "Ready for Analysis" update together (if same #)
		//turn on listeners for analysis checkbox and searches/filters
		phaseFilterButtonListen();
		searchButtonListen();
	   
	   
		//readyAnalysisListen();
		//getAssignmentDataNext();
		//
		
		
		//go through the Study Grouping ID selects and get the right one selected (having trouble doing it above, maybe b/c not displayed yet?)
		jQuery('select.StudyGroupingClass').each( function(){
			jQuery(this).val( jQuery(this).attr("data-whichsg") );
			
		});

    }).complete( function( ) {
		
		spinny.css("display", "none");
		usrmsg.html("Assignments Loaded!");
		usrmsgshell.fadeOut(4000);
		
		//get moving on strategies
		get_strategy_data();
		
		//console.log( data_studyids );
       
    });

  }

//function to save study groupings
function save_assignment_data(){

    //Send study id, study grouping id and ready analysis info thru ajax
	var studyGroupings = {};
	var all_tr = jQuery("#assignment-table tbody tr");
	var sg_val;
	var sid_val;
	jQuery.each( all_tr, function(){
		sg_val = jQuery(this).find(".StudyGroupingClass").val(); 
		//sid_val = parseInt( jQuery(this).find(".studyid_val").html() );
		sid_val = parseInt( jQuery(this).data("studyid") );
		studyGroupings[ sid_val ] = sg_val;
	});

	//ready analysis info: get values on assignment tab checkbox
	var readyAnalysis = {};
	jQuery("input[name='ready-analysis']").each(function(){
		readyAnalysis[ jQuery(this).attr('value')] = jQuery(this).is(":checked"); 
	});

	//ajax data
	var ajax_action = 'save_assignments';
	var ajax_data = {
		'action': ajax_action,
		'transtria_nonce' : transtria_ajax.ajax_nonce,
		'studygrouping_data' : studyGroupings,
		'ready_analysis' : readyAnalysis
		//'data': JSON.stringify({ 'studygrouping_data' : studyGroupings, 'assignment_data' : readyAnalysis })
	};
	
	jQuery.ajax({
		url: transtria_ajax.ajax_url, 
		data: ajax_data, 
		type: "POST",
		dataType: "json",
		beforeSend: function() {
			//show user message and spinny
			//usrmsg.html("Loading Study ID: " + this_study_id );
			//usrmsgshell.fadeIn();
			//spinny.fadeIn();
			
		}
    }).success( function( data ) {

       console.log('Assignments saved');
       console.log(data);

    });

    //return readyAnalysis;

}

//function to get, parse to trs in assignments table and display dropdown to strategies
function get_strategy_data(){

	//user message things
	var usrmsg = jQuery(".assignments_messages .usr-msg");
	var usrmsgshell = jQuery(".assignments_messages");
	var spinny = jQuery(".assignments_messages .spinny");
	
	//ajax data
	var ajax_action = 'get_assignment_strategies';
	var ajax_data = {
		'action': ajax_action,
		'transtria_nonce' : transtria_ajax.ajax_nonce
		//'data': JSON.stringify({ 'studygrouping_data' : studyGroupings, 'assignment_data' : readyAnalysis })
	};
	
	jQuery.ajax({
		url: transtria_ajax.ajax_url, 
		data: ajax_data, 
		type: "POST",
		dataType: "json",
		beforeSend: function() {
			//show user message and spinny
			usrmsg.html("Preparing Strategies" );
			usrmsgshell.fadeIn();
			spinny.fadeIn();
			
		}
    }).success( function( data ) {

		//console.log(data);
		//set up strategies
		if( data ){
			
			//put all the strategies in a drop down for filtering
			if( data.strategies_all ){

				//todo - return this in the same way as assignments returns.  Punting for now, b/c time

				var sel = document.getElementById('strategy_select');
				jQuery.each( data.strategies_all, function( i,v ){
					//create new option element
					var opt = document.createElement('option'); 
					// create text node to add to option element (opt)
					opt.appendChild( document.createTextNode( v.value + ": " + v.descr ) );
					opt.value = v.value; // set value property of opt
					sel.appendChild(opt); // add opt to end of select box (sel)
					
				});

			}
			
			//put strategies in tr classes by study id
			if( data.strategies_info ){
				//iterate through and assign classes to study ids
				jQuery.each( data.strategies_info, function( i,v ){
					jQuery.each( v, function( v_i, v_v ){
						jQuery( "tr.studyid_" + i ).addClass("strategy_" + v_v );
					});
					
					
					
				});
			}
		}

    }).complete( function( ) {
		
		spinny.css("display", "none");
		usrmsg.html("Strategies Loaded!");
		usrmsgshell.fadeOut(4000);
		
		//listen for select change
		strategyFilterListen();
    });


}


//add phase filter listener to Main table on Assignments Tab
function phaseFilterButtonListen(){
	jQuery("#phase1_filter").off("click", phaseFilterAssignmentsTable);
	jQuery("#phase1_filter").on("click", function() {
		phaseFilterAssignmentsTable("1");
		jQuery("#filters button").removeClass("active");
		jQuery(this).addClass("active");
	});
	jQuery("#phase2_filter").off("click", phaseFilterAssignmentsTable);
	jQuery("#phase2_filter").on("click", function() {
		phaseFilterAssignmentsTable("2");
		jQuery("#filters button").removeClass("active");
		jQuery(this).addClass("active");
	});
	jQuery("#phaseall_filter").off("click", phaseFilterAssignmentsTable);
	jQuery("#phaseall_filter").on("click", function() {
		phaseFilterAssignmentsTable("all");
		jQuery("#filters button").removeClass("active");
		jQuery(this).addClass("active");
	});
} 

//add phase filter function to Main table on Assignments Tab
function phaseFilterAssignmentsTable( phase_num ){

	if ( ( phase_num != "all" ) || ( phase_num == undefined ) ){    
		phase_num = parseInt( phase_num ) || 2;
	} 
	if( phase_num ){
		phase_num = "phase_" + phase_num;
	} else {
		phase_num = "-1";
	}

	//make sure correct rows are showing, all else hide
	if ( !( jQuery("tr.assignment-study." + phase_num + "").is(":visible") ) && ( phase_num !="-1" ) ){
		jQuery("tr.assignment-study." + phase_num + "").show();
	} 

	//now, hide by tr.class (added above when getting assignments)
	if( ( phase_num != "-1" ) && ( phase_num != "phase_all" ) ){
		jQuery("tr.assignment-study:not(." + phase_num + " )").hide();
	} else { //or, show all
		jQuery("tr.assignment-study").show();
		
		//clear search terms and strategies
		jQuery("#search_text").val("");
		jQuery("#filters .sort-status-message").html("No search terms" );
		jQuery("select#strategy_select").val("-1");
	}

}

function searchButtonListen(){
	jQuery("#filters .search_button").on("click", function() {
		var searchterm = jQuery("#search_text").val();
		searchMainAssignmentTable( searchterm );
		jQuery("#filters button").removeClass("active");
		jQuery(this).addClass("active");
	});
	jQuery("#filters .clear_search").on("click", function() {
		
		searchMainAssignmentTable( "" );
		jQuery("#filters .search_button").removeClass("active");
		jQuery("#filters button").removeClass("active");
	});
}

function searchMainAssignmentTable( searchterm ){
     
	removeMainTableHighlight( jQuery("table#assignment-table tr em") ); 

    //show all rows (reset)
	jQuery("table#assignment-table tr").show(); 
	if( searchterm != "" ){

		var authorElement;
		var author;
		var titleElement;
		var title;
		var authorIndex;
		var titleIndex;
		var count = 0;

		jQuery("table#assignment-table tr").each( function( index ) {
			if (index !== 0){
				row = jQuery(this);

				authorElement = row.find("td.author");
				author = authorElement.text().toLowerCase();
				authorIndex = author.indexOf(searchterm);
				titleElement = row.find("td.title");
				title = titleElement.text().toLowerCase();
				titleIndex = title.indexOf(searchterm);

				//need to beef up logic for rows
				//if( ( authorIndex !=0 ) || ( titleIndex !=0 ) ){
				//if( authorIndex !=0 ){
				if( ( authorIndex == -1 ) && ( titleIndex == -1 ) ){
					row.hide();
				} 
				if ( authorIndex != -1 ) {
					addMainTableHighlight(authorElement, searchterm);
					row.show();
					count++;

				}
				if ( titleIndex != -1 ){

					addMainTableHighlight(titleElement, searchterm);
					row.show();
					count++;
				}
			}
		});
		if( count <= 0 ){
			//display a message saying no results found
			//jQuery("#filters .no-results").show();
			jQuery("#filters .sort-status-message").html('No Results Found for search term: ' + searchterm );
		} else {
			jQuery("#filters .sort-status-message").html("Results for search term: " + searchterm );
		}
	} else {
		//clear out!
		jQuery("#search_text").val("");
		jQuery("#filters .sort-status-message").html("No search terms" );
	}
		
}
 
function addMainTableHighlight(element, textToHighlight){

	var text = element.text();
	var highlightText = '<em class="yellow">' + textToHighlight + '</em>';
	var newText = text.replace(textToHighlight, highlightText);
	
	//var highlightTextUC = '<em class="yellow">' + textToHighlight + '</em>'; //in case we are uppercase in our search results

	element.html(newText);
}

function removeMainTableHighlight(highlightedElements){
    highlightedElements.each( function() {
		var element = jQuery(this);
		element.replaceWith( element.html() );
    });
}

function strategyFilterListen(){
    //listen to strategy dropdown change
    //jQuery("select#strategy_select").on("change", function(){
    jQuery("button#strategy_button").on("click", function(){
		selectedStrategy = parseInt( jQuery("#strategy_select option:selected").val() );
		if( selectedStrategy > 0 ){
			strategyFilterAssignmentTable( selectedStrategy );
			jQuery("#filters button").removeClass("active");
			jQuery(this).addClass("active");
		} else { //show all
			strategyFilterAssignmentTable( 'all' ); 
			jQuery("#filters button").removeClass("active");
		}
    });

    //implement 'clear strategy' button
    jQuery("#filters .clear_strategy").on("click", function(){
		strategyFilterAssignmentTable( 'all' );
		jQuery("#filters button").removeClass("active");
    });

}

function strategyFilterAssignmentTable( strategy_num ){

    if( ( strategy_num != 'all' ) && ( strategy_num != undefined ) ){

       strategy_num = "strategy_" + strategy_num;

       //make sure appropriate rows are visible, all else will be hidden
		if( !( jQuery("tr.assignment-study." + strategy_num + "").is(":visible") )){
			jQuery("tr.assignment-study." + strategy_num + "").show();
			//jQuery("#filters .sort-status-message").html('Sort by Strategy number: ' + strategy_num );
		}

       //now, hide by tr.class, or show 'all' 
       jQuery("tr.assignment-study:not(." + strategy_num + " )").hide();

    } else {
       jQuery("tr.assignment-study").show();
	   jQuery("select#strategy_select").val("-1");
	  // jQuery("#filters .sort-status-message").html('No sort/search params' );
    }
}

  //populate 'next' table/dropdowns on assignment tab: 
  //   showing phase 1 next, phase 2 next and list of endnotes not in use 
  function getAssignmentDataNext(){

    var new_endnotes={};
    var new_phase1={}; //subset of new endnotes, phase 1 only
    var new_phase2={}; //subset of new endnotes, phase 2 only

    // Get array of endnoteids not in use
    // foreach loop with __CARES__.endnote_summaries, continue if in __CARES__.used_endnoteids 
    jQuery.each( __CARES__.endnote_summaries, function( index, value ){

       index_int = parseInt(index);
       //if index is not in used endnotes list, add to new_endnotes
       if( jQuery.inArray( index_int, __CARES__.used_endnoteids ) < 0 ){
          new_endnotes[index_int] = value;
       
          //get next phase 1,2 endnoteid not in use
          /*if (value.Phase == "1") {
             new_phase1[index_int] = value;
          } else if (value.Phase == "2") {
             new_phase2[index_int] = value;
          }*/
		  
		  //as of 18May2015, indeex_ints between 503-1102 are Phase 1; else are Phase2
		  if( ( index_int >= 503 ) && ( index_int <= 1102 ) ){
			new_phase1[index_int] = value;
		  } else {
			new_phase2[index_int] = value;
		  }
		  
       }
       
    });
    //console.log(new_endnotes); //has null values since int?

    __CARES__.new_endnotes = new_endnotes;
    __CARES__.new_phase1 = new_phase1;
    __CARES__.new_phase2 = new_phase2;

    //now that we have our lists, render them in Assignment tab
    renderAssignmentDataNext();

  }

  function renderAssignmentDataNext(){
  
    //clear prior values in phase 1, phase 2 dropdowns
    jQuery("#next_phase1").html('');
    jQuery("#next_phase2").html('');
 
    var txt; 
    //put values in selects based on phase 1, phase 2 lists
    jQuery.each( __CARES__.new_phase1, function( index, value ) {
       if( index != undefined){
          txt = jQuery("<option></option>").attr("value", index).text(index + ": " + value.title);
          jQuery("#next_phase1").append(txt);
       }
    });

    jQuery.each( __CARES__.new_phase2, function( index, value ) {
       if( index != undefined){
          txt = jQuery("<option></option>").attr("value", index).text(index + ": " + value.title);
          jQuery("#next_phase2").append(txt);
       }
    });

    //listen to submit buttons and redirect to new study form w/ endnote
    nextAssignmentButtonListen();

  }

function nextAssignmentButtonListen(){

	var basepath = transtria_ajax.study_home;

    jQuery("#phase1_submit").on("click", function(){
		var whichEndNote = parseInt( jQuery( "#next_phase1 :selected").val());
		window.location = basepath + "?endnoteid=" + whichEndNote;
	});

	jQuery("#phase2_submit").on("click", function(){
		var whichEndNote = parseInt( jQuery( "#next_phase2 :selected").val());
		window.location = basepath + "?endnoteid=" + whichEndNote;
	});

}

//returns phase by endnote id
function get_phase_by_endnoteid( which_endnoteid ){

	if( ( parseInt( which_endnoteid ) > 502 ) && ( parseInt( which_endnoteid ) < 1103 ) ){
		return "1";
	} else {
		return "2";
	}
}

  

jQuery( document ).ready(function() {

	get_assignment_data_basic();
	
	nextAssignmentButtonListen();


});