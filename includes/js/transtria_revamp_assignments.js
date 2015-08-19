


//function to get values for assignment tab
function get_assignment_data_basic( ){

	var studies = [];
    //clear __CARES__.used_endnotedids
	//__CARES__.used_endnoteids=[]; //clearing method works if NO REFERENCES

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
			//usrmsg.html("Loading Study ID: " + this_study_id );
			//usrmsgshell.fadeIn();
			//spinny.fadeIn();
			
		}
	}).success( function( data ) {
	
	//now take json data and turn into Main table on Assignments tab		
		if( data.assignments_info ){
		//console.log( data.assignments_info );
			var len = data.assignments_info.length;
			var txt = "";
       
			if( len > 0 ){
				for( var i=0; i<len; i++ ){
                //if( data[i].StudyGroupingID && data[i].StudyID && data[i].AbstractionComplete){
					//placeholders
					var studygroup;
					var studygroup_val;
					var endnoteid;
					var endnoteobject = {};
					if( ! jQuery.isEmptyObject( data.endnotes_info) ){
						endnoteobject = data.endnotes_info;
					} 
					var readyanalysis;
					var readyanalysis_checked;
					var sgid_clone;

					//for table links
					var basepath = transtria_ajax.study_home;

					if( data.assignments_info[i] ){
                   //console.log(i);
                   //handle null/empty data
                   if ( data.assignments_info[i].StudyGroupingID != undefined ) { 
                      studygroup_val = (String( data.assignments_info[i].StudyGroupingID.length > 0 )) ? parseInt( data.assignments_info[i].StudyGroupingID ) : ""; 
                   } else {
                      studygroup_val = 0;
                   } 

                   studygroup = ""; //null prev value
                   //Study Group here as clone of AssignmentSGID, so we don't have to render all the comboboxes
                   sgid_clone = jQuery("#StudyGroupingIDAssignment").clone();
                   sgid_clone.addClass("sgid_dd");
                   sgid_clone.removeAttr('id');

                   //hide original combobox, just a placeholder for cloning
                   //jQuery("#StudyGroupingIDAssignment").combobox("hide");
                   jQuery("#StudyGroupingIDAssignment").hide();

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
                   studygroup = "<select class='StudyGroupingClass'>";
                   //studygroup += "<option value=''></option>" + sgid_clone.html() + "</select>"; //"<input class='StudyGroupingDD'></input>";
                   //studygroup += "<option value=''></option>" + sgid_html + "</select>"; //"<input class='StudyGroupingDD'></input>";
                   studygroup += sgid_html + "</select>"; //"<input class='StudyGroupingDD'></input>";
                   
                   //get endnoteid and summary; add to __CARES__.used_endnoteids
                   endnoteid = data.assignments_info[i].EndNoteID || ""; 
/*					if( endnoteid != "" ){
                      endnoteobject = __CARES__.endnote_summaries[ endnoteid ];
                      __CARES__.used_endnoteids.push(endnoteid);
                   }
*/
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
					txt += "<tr class='assignment-study phase_";

					if( endnoteobject != null && endnoteid > 0 && endnoteobject != undefined ){
						txt += endnoteobject.Phase;
					}
					txt += " " + data.assignments_info[i].StudyID + "' data-studyid='" + data.assignments_info[i].StudyID + "'>"; 
					txt += "<td>" + studygroup + "</td>";
					txt += "<td class='studyid_val' style='font-weight:bold;'><a class='link' href='" + basepath + "&study_id=" + data.assignments_info[i].StudyID + "'>" + data.assignments_info[i].StudyID + "</a></td>";

					//EndNote rec number, for now since Mel see that as unique
					txt += "<td>" + endnoteid + "</td>";
					txt += "<td>";

					if( endnoteobject != null && endnoteid > 0 && endnoteobject != undefined ){
						txt += endnoteobject.Phase;
					}				   
					txt += "</td>";

					if( endnoteobject[ endnoteid ] != null && endnoteid > 0 && endnoteobject[ endnoteid ] != undefined ){
                   //from endnote: author, year, title
						txt += "<td class='author'>" + endnoteobject[ endnoteid ][ 'contributors_authors_author' ] + "</td>";
						txt += "<td>" + endnoteobject[ endnoteid ][ 'dates_pub-dates_date' ] + " " + endnoteobject[ endnoteid ][ 'dates_year' ] + "</td>";
						txt += "<td class='title'>" + endnoteobject[ endnoteid ][ 'titles_title' ] + "</td>";
					} else {
                      //placeholders for author, year, title
						txt += "<td>----</td>";
						txt += "<td>----</td>";
						txt += "<td>----</td>";
					}
					txt += "<td>" + data.assignments_info[i].abstraction_complete + "</td>";
					txt += "<td>----</td>";
					txt += "<td>" + data.assignments_info[i].validation_complete + "</td>";
					//new checkbox field with study id in value..
					txt += "<td><input type='checkbox' name='ready-analysis' value='" + studygroup_val + "' " + readyanalysis_checked + "></input></td>";

                   //old stuff
                   //txt += "<td><input type='checkbox' name='ready-analysis' value='" + studygroup + "' " + readyanalysis_checked + "></input></td>";

                   txt += "</tr>";

                   data_studyids.push( data.assignments_info[i].StudyID );
                }
            // }
					if( txt != "" ){
						//console.log(studygroup_val);
						//jQuery("#assignment-table tbody").append(txt);
						var appendD = jQuery(txt).appendTo(jQuery("#assignment-table tbody"));
						if (studygroup_val != 0) { //we have a prior SG value
						   //console.log(appendD);
						   var f = appendD.find('.StudyGroupingClass');
						   //console.log(f);

						   f.val(studygroup_val);
						}                    
						//appendD.find('.StudyGroupingClass').console.log(appendD);
						
					}
					txt = "";
				} //end foreach

			}
		}

		//now, add strategy search drop down (clone from intevention page)
		var strategy_clone = jQuery("#strategies").clone();
		var strategy_html="";
		strategy_clone.addClass("strategy_filter");
		strategy_clone.removeAttr('id');
		strategy_html = "<select>";
		strategy_html += "<option value=''></option>" + strategy_clone.html() + "</select>";
		jQuery("#assignment-strategy").html( strategy_html );

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

       //make all studygroupid checkboxes "Ready for Analysis" update together (if same #)
       //turn on listeners for analysis checkbox and searches/filters
 /*      readyAnalysisListen();
       getAssignmentDataNext();
       phaseFilterButtonListen();
       searchButtonListen();
       strategyFilterListen();
*/
    }).complete( function( ) {

       console.log( data_studyids );
       
       //call ajax function to pull in multiselect study strategy data
      // get_assignment_strategies_js( data_studyids, '/cgi-bin/Transtria/dataentry/basic_info.py' );

	   
	   
	   
	   
	   
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


  //function to ajax-get study strategy data for main assignment table
  function get_assignment_strategies_js( data, url ){

    var strategies = [];

    jQuery.ajax({
       url: url,
       dataType: "json",
       data: {
          action: 'get_assignment_strategies',
          data:JSON.stringify({ data: data }) 
       }
    }).success( function( data ) {
       //now take json data and turn into Main table on Assignments tab

       data = data.strategies;
       if(data){
          var len = data.length;
          var whichDataRow; 

          if(len > 0){
             //iterate through strategies data from ajax
             for( var i=0; i<len; i++ ){
                //assign strategies to data-class in main assignment table row
                whichDataRow = jQuery("#assignment-table").find("tr[data-studyid='" + data[i].StudyID + "']");                
                //jQuery.data( whichDataRow, "strategies", data[i].CodeResult );
                //whichDataRow.data( "strategies", parseInt(data[i].CodeResult) );
                whichDataRow.addClass("strategy_" + data[i].CodeResult );
                //console.log( data[i].StudyID ); 
             }  
          }
          //console.log( data);
       }

    });

  }


	//add phase filter listener to Main table on Assignments Tab
	function phaseFilterButtonListen(){
		jQuery("#phase1_filter").off("click", phaseFilterAssignmentsTable);
		jQuery("#phase1_filter").on("click", function() {
		   phaseFilterAssignmentsTable("1");
		   jQuery("#assignment-tab .filters button").removeClass("active");
		   jQuery(this).addClass("active");
		});
		jQuery("#phase2_filter").off("click", phaseFilterAssignmentsTable);
		jQuery("#phase2_filter").on("click", function() {
		   phaseFilterAssignmentsTable("2");
		   jQuery("#assignment-tab .filters button").removeClass("active");
		   jQuery(this).addClass("active");
		});
		jQuery("#phaseall_filter").off("click", phaseFilterAssignmentsTable);
		jQuery("#phaseall_filter").on("click", function() {
		   phaseFilterAssignmentsTable("all");
		   jQuery("#assignment-tab .filters button").removeClass("active");
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
    }

  }

  function searchButtonListen(){
    jQuery("#assignment-tab .filters .search_button").on("click", function() {
       var searchterm = jQuery("#search_text").val();
       searchMainAssignmentTable( searchterm );
       jQuery("#assignment-tab .filters button").removeClass("active");
       jQuery(this).addClass("active");
    });
    jQuery("#assignment-tab .filters .clear_search").on("click", function() {
       searchMainAssignmentTable( "" );
       jQuery("#assignment-tab .filters .search_button").removeClass("active");
       jQuery("#assignment-tab .filters button").removeClass("active");
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
             author = authorElement.text();
             authorIndex = author.indexOf(searchterm);
             titleElement = row.find("td.title");
             title = titleElement.text();
             titleIndex = title.indexOf(searchterm);

             //need to beef up logic for rows
             //if( ( authorIndex !=0 ) || ( titleIndex !=0 ) ){
             //if( authorIndex !=0 ){
             if( ( authorIndex == -1 ) && ( titleIndex == -1 ) ){
                row.hide();
             } else if ( authorIndex !=0 && authorIndex != -1 ) {
                addMainTableHighlight(authorElement, searchterm);
                row.show();
                count++;

             } else if ( titleIndex != 0 && titleIndex != -1 ){

             //else {
                //addMainTableHighlight(authorElement, searchterm);
                addMainTableHighlight(titleElement, searchterm);
                row.show();
                count++;
             }
          }
       });
      if( count <= 0 ){
         //display a message saying no results found
         jQuery("#assignment-tab .filters .no-results").show();
         jQuery("#assignment-tab .filters .no-results").html('No Results Found for searchterm: ' + searchterm ).show();
      } else {
         jQuery("#assignment-tab .filters .no-results").hide();
      }
    }
  }
 
  function addMainTableHighlight(element, textToHighlight){
    var text = element.text();
    var highlightText = '<em class="yellow">' + textToHighlight + '</em>';
    var newText = text.replace(textToHighlight, highlightText);

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
    jQuery("#assignment-strategy").on("change", function(){
       selectedStrategy = parseInt( jQuery("#assignment-strategy option:selected").val() );
       if( selectedStrategy > 0 ){
          strategyFilterAssignmentTable( selectedStrategy );
       } else { //show all
          strategyFilterAssignmentTable( 'all' ); 
       }
       jQuery("#assignment-tab .filters button").removeClass("active");
    });

    //implement 'clear strategy' button
    jQuery("#assignment-tab .filters .clear_strategy").on("click", function(){
       strategyFilterAssignmentTable( 'all' );
       jQuery("#assignment-tab .filters button").removeClass("active");
    });

  }

  function strategyFilterAssignmentTable( strategy_num ){

    if( ( strategy_num != 'all' ) && (strategy_num != undefined ) ){

       strategy_num = "strategy_" + strategy_num;

       //make sure appropriate rows are visible, all else will be hidden
       if( !( jQuery("tr.assignment-study." + strategy_num + "").is(":visible") )){
          jQuery("tr.assignment-study." + strategy_num + "").show();
       }

       //now, hide by tr.class, or show 'all' 
       jQuery("tr.assignment-study:not(." + strategy_num + " )").hide();

    } else {
       jQuery("tr.assignment-study").show();
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

    var basepath = window.location.origin + window.location.pathname + "?page=basic_form";

    jQuery("#phase1_submit").on("click", function(){
       var whichEndNote = parseInt( jQuery( "#next_phase1 :selected").val());
       window.location = basepath + "&endnoteid=" + whichEndNote;
    });

    jQuery("#phase2_submit").on("click", function(){
       var whichEndNote = parseInt( jQuery( "#next_phase2 :selected").val());
       window.location = basepath + "&endnoteid=" + whichEndNote;
    });

  }
  

jQuery( document ).ready(function() {

	get_assignment_data_basic();



});