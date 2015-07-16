  var __CARES__ = __CARES__ || {};
  __CARES__.component_data=[];
  __CARES__.ese_tab_component_data=[];
  __CARES__.endnote_summary=[];
  __CARES__.StudyID='';
  __CARES__.EndNoteID=''; 
  __CARES__.afterRenderList=[];
  __CARES__.ese_components_index_list=[];
  
  //endnoteids in use at assignment tab load
  __CARES__.used_endnoteids=[];
  __CARES__.new_endnotes=[];;
  __CARES__.new_phase1=[];
  __CARES__.new_phase2=[];
//  __CARES__.studyDesigns=[];
  __CARES__.ese_tabCount=0;

  function selector(id) {
      id=id.replace('.', ' #')
      var _obj=$("#" + id);
      if (_obj.length == 0) {
		//console.log("Cannot find " + id)
	}
      return _obj;
  }

  function create_checkbox(comp) {
     return $('<input/>', {
                  type: 'checkbox',
                    id: comp.id,
               checked: comp.checked})
  }

  
  function initialize_components(data) {
     $('#msg_dialog').text("Loading")
     $('#msg_dialog').show()

     //console.log(data.components.length)
     __CARES__.component_data=data.components;
     __CARES__.ese_tab_component_data=data.ese_tab_components;
     __CARES__.ea_tabCount=data.ea_tabCount || 0
     __CARES__.ese_tabCount=0
     __CARES__.endnote_summaries=data.endnote_summaries || {}
     __CARES__.StudyID_list=data.studyID_list || []
 //    __CARES__.StudyDesigns=get_study_designs();
	 var prefix;
	 

     //need to create optional tabs if this StudyID was created sometime
     //in the past
     if (parseInt(data.ese_tabCount) > 0) {
        for (var i=0; i < parseInt(data.ese_tabCount); i++) {
            add_extra_ese_tab( "pageload" );
        }
     }

	 //put data into components
     for(var i=0; i < __CARES__.component_data.length; i++) {
        var comp = __CARES__.component_data[i];
		
		//stop gap
		//if we have ese tab data here (which we do, whhyyyy Python - no time to debug, arg), bounce!
		//TODO: beef this up for all the ese s
		prefix = comp.id.substring(0,4);
		if( ( prefix == 'ese0' ) || ( prefix == 'ese1' ) || ( prefix == 'ese2' ) 
			|| ( prefix == 'ese3' ) || ( prefix == 'ese4' ) || ( prefix == 'ese5' ) 
			|| ( prefix == 'ese6' ) || ( prefix == 'ese7' ) || ( prefix == 'ese8' ) || ( prefix == 'ese9' ) ) {
					
			//__CARES__.component_data.splice(i , 1);
			//console.log(comp);
			/*if( comp.type="MULTISELECT" ){ //why isn't this accurate?
				//TODO: fix whatever Python is making this break (showing ALL ese# components as MULTISELECTS in components)
				if( comp.id.match("_ability_status$") || comp.id.match("geographic_scale$") || comp.id.match("hr_subpopulations$") || 
					comp.id.match("_professional_populations$") || comp.id.match("_sub_populations$") || comp.id.match("_youth_populations$") ) {
					//console.log(comp); //25Feb, correct!
					 
					//move to ese_component data?
					__CARES__.ese_tab_component_data.push(comp);	
					//__CARES__.component_data.splice(i , 1); //not yet, it's reindexing and screwing things up
				} 
			}
			*/
			//remove this component from __CARES__.component_data, but not while we're doing the loop
			__CARES__.ese_components_index_list.push(i);
			
			continue;
		}
        //if (comp.id.substring(0,4) == 'ese0') console.log(comp)
        //console.log(comp.type)
        switch (comp.type) {
          case "CHECKBOX":
               selector(comp.id).prop("type", "checkbox");
               selector(comp.id).prop("checked", comp.checked == 'Y');
               break;
          case "DERIVED":
               selector(comp.id).text(comp.text)
               break;
          case "RADIOBUTTON":
               var _it=selector(comp.id);

               _it.empty();

               for(var j=0; j < comp.options.length; j++) {
                  var _s='<input name="' + comp.id + '" type="radio"'; 
                  _s+='value="' + comp.options[j].value+'">' + comp.options[j].text + '</input>';

                  var _o=$(_s)

                  if (comp.options[j].selected==true) {
                     _o.prop('checked', true)
                     //console.log(comp.id + " true")
                  } else {
                     _o.prop('checked', false)
                     //console.log(comp.id + " false")
                  }
                  _it.append(_o)
               }

               break;
          case "INPUTTEXT":
				if (comp.id.substring(0,4) == 'ese0'){
					//console.log(comp);
				}
				if (comp.id.substring(0,4) == 'ese_'){
					//console.log(comp);
				}
               var _it=selector(comp.id);
               //console.log(comp.text)
               _it.val(comp.text);
               // check for size
               if (comp.size !== undefined) {
                  _it.attr('size', comp.size);
                  if (comp.maxlength === undefined) {
                     _it.attr('maxlength', comp.size);
                  }
               }
               // check for regex
               if (comp.regex !== undefined) {
                  _it.attr('regex', comp.regex);
                  _it.focusout(function(e) {
					 var _re=new RegExp($(e.target).attr('regex'));
					 if (!_re.test(e.target.value)) {
						//console.log(e.target.value);
						$(e.target).css('background', 'red');
					 } else {
						$(e.target).css('background', 'white');
					 }
				  });
               }
               break;
          case "COMBOBOX-OTHER":
               //ties a combobox with an input text so that "Other" 
               //can be specified
               __CARES__.afterRenderList.push({'func': setup_combobox_other,
                                          'args': [comp.id, comp.other_id]})
          case "COMBOBOX":
               setup_combobox(comp);
               break;
          case "MULTISELECT":
               setup_multiselect(comp); 
               break;
          case "TEXTAREA":
               var _it=selector(comp.id);
               _it.val(comp.text);

               // check for rows/cols
               if (comp.rows !== undefined) _it.attr('rows', comp.rows);
               if (comp.cols !== undefined) _it.attr('cols', comp.cols);
               break;
          default:
               console.log("Invalid component type: " + comp.type + "for ID:" + comp.id);
        }// switch
     } // for loop

	 //do all the same for ese0, ese1, etc..
	 
	 //initialize_ese_components();


     for(var i=0; i < __CARES__.ea_tabCount; i++) {
        addEffectAssociationTab()
     }


     $('#msg_div').hide()
     is_general_pop_listener();

     //multiselect_selected_display( "intervention_indicators", "intervention_indicators_display");

     //initialize message on results page and then add change listener to stop time inputs
     stop_time_validate();

     var valSelector, absSelector;
     valSelector = jQuery("#validator").siblings(".custom-combobox").find(".custom-combobox-input");
     absSelector = jQuery("#abstractor").siblings(".custom-combobox").find(".custom-combobox-input");

     jQuery("#validatorstoptime, #abstractorstoptime").on("change", function() {
       stop_time_validate();
     });

     //detect change in abstractor/validator combobox to fire stop time message
     jQuery("#abstractor, #validator").combobox({
         select: function( event, ui ){
            stop_time_validate( this.id, ui.item.value );
            //console.log('SELECT');
         },
         selected: function(){
            //console.log('selected');
         }
     });

     //Representativeness subpopulations option - show if YES, hide if NO
     rep_subpops_show(); //on init  
     jQuery("[name='representativeness']").on("change", function(){
         rep_subpops_show();
     });

     //Confounders Type option - show if Confounders is YES, hide if NO
     confounder_type_show(); //on init
     jQuery("[name='confounders']").on("change", function(){
         confounder_type_show();
     });

     //IPE applicability question shows HR Subpops select
     ipe_hr_subpops_show();
     jQuery("[name='ipe_applicability_hr_pops']").on("change", function(){
         ipe_hr_subpops_show();
     });

     //ESE oversampling question shows HR subpops select
     ese_hr_subpops_show();
     jQuery("[name='ese_oversampling']").on("change", function(){
         ese_hr_subpops_show();
     });

     //Ability Status percent fields show for ability stati selected
     //subpopulations_ability_pct_show(); //23Feb, prob because comboboxes on ese subtabs not initialized
     /*jQuery("[id$='_ability_status']").on("change", function(){
         subpopulations_ability_pct_show();
     });
	 */

     //Show results variables textfield if result_type == 'adjusted'
     results_variables_show();
     jQuery("[name$='result_type']").on("change", function(){
         results_variables_show( jQuery(this) );
     });

     //Limit strategies on EA/results tab based on ones selected on intervention
     jQuery("#strategies").on("change", function(){
        strategy_limit_results();
     });
     strategy_limit_results();


     //Limit outcome assessed on EA/results based on intervention selection
     //outcome_assessed_limit_results(); 

     //Limit indicator on EA/results tab based on intervention selection
     //indicator_limit_results();

 
  } // initialize_components

  function initialize_ese_components(){
  
	//first, remove ese components (duplicates and incorrects) from __CARES__.component_data, given __CARES__.ese_components_index_list
	//NOTE: this has to be done backwards, since reindexing is a Thing.
	var i = __CARES__.component_data.length;
	
	/*while (i--) {
		//if there's something at that index AND the index matches the index_list
		if ( (__CARES__.component_data[i] != undefined ) && ( jQuery.inArray( i, __CARES__.ese_components_index_list ) != "-1" ) ) {
			__CARES__.component_data.splice(i , 1);
		}
	}*/
	
	for(var i=0; i < __CARES__.ese_tab_component_data.length; i++) {
		var comp = __CARES__.ese_tab_component_data[i];
	
	//console.log(comp.type)
		switch (comp.type) {
		  case "CHECKBOX":
			   selector(comp.id).prop("type", "checkbox");
			   selector(comp.id).prop("checked", comp.checked == 'Y');
			   break;
		  case "DERIVED":
			   selector(comp.id).text(comp.text)
			   break;
		  case "RADIOBUTTON":
			   var _it=selector(comp.id);

			   _it.empty();

			   for(var j=0; j < comp.options.length; j++) {
				  var _s='<input name="' + comp.id + '" type="radio"'; 
				  _s+='value="' + comp.options[j].value+'">' + comp.options[j].text + '</input>';

				  var _o=$(_s)

				  if (comp.options[j].selected==true) {
					 _o.prop('checked', true)
					 //console.log(comp.id + " true")
				  } else {
					 _o.prop('checked', false)
					 //console.log(comp.id + " false")
				  }
				  _it.append(_o)
			   }

			   break;
		  case "INPUTTEXT":
			   var _it=selector(comp.id);
			   //console.log(comp.text)
			   _it.val(comp.text);
			   // check for size
			   if (comp.size !== undefined) {
				  _it.attr('size', comp.size);
				  if (comp.maxlength === undefined) {
					 _it.attr('maxlength', comp.size);
				  }
			   }
			   // check for regex
			   if (comp.regex !== undefined) {
				  _it.attr('regex', comp.regex);
				  _it.focusout(function(e) {
					 var _re=new RegExp($(e.target).attr('regex'));
					 if (!_re.test(e.target.value)) {
						//console.log(e.target.value);
						$(e.target).css('background', 'red');
					 } else {
						$(e.target).css('background', 'white');
					 }
				  });
			   }
			   break;
		  case "COMBOBOX-OTHER":
			   //ties a combobox with an input text so that "Other" 
			   //can be specified
			   __CARES__.afterRenderList.push({'func': setup_combobox_other,
										  'args': [comp.id, comp.other_id]})
		  case "COMBOBOX":
			   setup_combobox(comp);
			   break;
		  case "MULTISELECT":
			   setup_multiselect(comp); 
			   break;
		  case "TEXTAREA":
			   var _it=selector(comp.id);
			   _it.val(comp.text);

			   // check for rows/cols
			   if (comp.rows !== undefined) _it.attr('rows', comp.rows);
			   if (comp.cols !== undefined) _it.attr('cols', comp.cols);
			   break;
		  default:
			   console.log("Invalid component type: " + comp.type + " for ID: " + comp.id);
		}// switch
	
	}
	
	//instantiate listener on subpopulation ability status..
	subpopulations_ability_pct_show();
	jQuery("[id$='_ability_status']").on("change", function(){
         subpopulations_ability_pct_show();
     });
  
  }
  //if 'TP is general population' == 'Y'
  function is_general_pop_listener(){
     var pops = ["tp", "ipe", "ipu", "ese", "esu"];

     jQuery.each( pops, function( index, value ) { 
       jQuery("input[name='" + value + "_general_population']").on("change", function(){


         if(this.value == "Y"){
	     jQuery("." + value + "_not_general").css("color", "#d6d6d6");
             jQuery("." + value + "_not_general input, ." + value + "_not_general button, ." + value + "_not_general .ui-button").css("opacity", "0.5");
         } else {
             jQuery("." + value + "_not_general").css("color", "#000000");
             jQuery("." + value + "_not_general input, ." + value + "_not_general button, ." + value + "_not_general .ui-button").css("opacity", "1");
         }
       });
     });
  }


  //display message on Results page if stop time isn't entered
  function stop_time_validate( thisid, thisvalue ){
     //console.log("stop time validate functiooon");
     //really convoluted way of detecting SELECTED event with combobox...change not working, grr
     thisid = thisid || '';
     thisvalue = thisvalue || '';

     //if abstractor selected and no stop time selected, give message 
     var abstractorVal = jQuery("#abstractor").siblings(".custom-combobox").find(".custom-combobox-input").val();
     var validatorVal = jQuery("#validator").siblings(".custom-combobox").find(".custom-combobox-input").val();

     if( ( ( validatorVal != '' ) && ( validatorVal != 'None' ) && ( thisid == '' ) ) ||
        ( ( thisid == "validator" ) && ( thisvalue != "00" ) ) ) {
         if ( jQuery('#validatorstoptime').val() == '' ){
            jQuery('.validator-stop-time-reminder').show();
         } else {
            jQuery('.validator-stop-time-reminder').hide();
         }
     } else if( ( ( ( validatorVal == '' ) || ( validatorVal == 'None' ) ) && ( thisid == '' ) ) || 
        ( ( thisid == "validator" ) && ( thisvalue == "00" ) ) ) {
         jQuery('.validator-stop-time-reminder').hide();
     }

     if( ( ( abstractorVal != '' ) && ( abstractorVal != 'None' ) && ( thisid == '' ) ) ||
        ( ( thisid == "abstractor" ) && ( thisvalue != "00" ) ) ) { 
         if( jQuery('#abstractorstoptime').val() == '' ){
            jQuery('.abstractor-stop-time-reminder').show(); 
         } else {
            jQuery('.abstractor-stop-time-reminder').hide();
         }
     } else if( ( ( ( abstractorVal == '' ) || ( abstractorVal == 'None' ) ) && ( thisid == '' ) ) ||
        ( ( thisid == "abstractor" ) && ( thisvalue == "00" ) ) ) {
         jQuery('.abstractor-stop-time-reminder').hide();
     }
  }


  //display list of multiselected selections in div
  function multiselect_selected_display( multiselect_id, display_to_id ){
	var isOpened = jQuery("#" + multiselect_id ).multiselect("isOpen");
	var checkedIDs = jQuery("#" + multiselect_id ).multiselect("getChecked");	

	var tr_display = jQuery("tr." + display_to_id);	
	if ( checkedIDs.length > 0 ) {
	  //clear any indicators displayed
	  tr_display.find("li").remove();
	  tr_display.find("ul").remove();

	  var output = "";
 
	  //display tr with checked ID names
	  output += "<ul>";
	  //tr_display.append("<ul>");
	  
	  jQuery.each( checkedIDs, function( index, value ) {
	     output += "<li>" + value.title + "</li>";
	     //tr_display.append("<li>" + value.title + "</li>"); 
	  });
	  tr_display.append(output);
	  tr_display.append("</ul>");
	  
	  tr_display.show();
	} else {
	  tr_display.find("li").remove();	
	  tr_display.find("ul").remove();
	
	  //hide that noise
	  tr_display.hide();
	}
	console.log( checkedIDs );
  }

  //trying to get the multiselect to update when deselected
  function multiselect_listener( whereFrom ){
	var output = 'wherefrom' + jQuery(whereFrom);
    console.log( output );

     whereFrom.multiselect("refresh");

  }

  //show/hide representativeness subpopulations on representativeness radio YES/NO
  function rep_subpops_show(){
    if( jQuery("[name='representativeness']:checked").val() == "Y" ){
       jQuery("#representative_subpopulations_div").show();
    } else {
       jQuery("#representative_subpopulations_div").hide();
    }

  }

  //show/hide confounders text area on confounders YES/NO
  function confounder_type_show(){
    if( jQuery("[name='confounders']:checked").val() == "Y" ){
       jQuery("tr#confounders_type").show();
    } else {
       jQuery("tr#confounders_type").hide();
    }
  }

  //show/hide HR subpopulations on IPE tabs based on applicability to HR sub pops question
  function ipe_hr_subpops_show(){
    if( jQuery("[name='ipe_applicability_hr_pops']:checked").val() == "Y" ){
       jQuery("tr.ipe_hr_subpopulations").show();
    } else {
       jQuery("tr.ipe_hr_subpopulations").hide();
    }
  } 

  //show/hide HR subpopulations on ESE tab based on oversampling question
  function ese_hr_subpops_show(){
    if( jQuery("[name='ese_oversampling']:checked").val() == "Y" ){
       jQuery("tr.ese_hr_subpopulations").show();
    } else {
       jQuery("tr.ese_hr_subpopulations").hide();
    }
  }

  //show/hide ability percent fields (for each selected ability status on subpops tabs
  function subpopulations_ability_pct_show(){
    var ability_multiselects = jQuery("[id$='_ability_status']");
    var vals;
    var valsArray =[];
    var dataval;
    var subpopname;
	var ese_subname = "";

    //for each of the subpopulations, get multiselect vals
    jQuery.each( ability_multiselects, function(){
		vals = jQuery(this).multiselect('getChecked');   
		//'console.log( vals );
		subpopname = jQuery(this).attr("id").replace("_ability_status", "");

		if ( vals.length > 0 ){
		//put into array
		  jQuery.each( vals, function(){

			 dataval = jQuery(this).attr("value");
			 dataval = parseInt( jQuery.trim(dataval) );
			 valsArray.push( dataval );
		  });
		}  //end put into array 

		for ( var i = 1; i < 7; i++ ){
			for( var j = 0; j<10; j++ ){
				var other_subpopname = "ese" + j;
				if ( subpopname.substr(0,4) == other_subpopname ){
					//subpopname = "ES-E_" + j;
					ese_subname = "#ES-E_" + j;
				}
			}
			if( ese_subname != "" ){
				var percent_obj = jQuery( ese_subname ).find("[data-ability-value='" + i + "']");
				
			} else {
				var percent_obj = jQuery("#tabs-" + subpopname ).find("[data-ability-value='" + i + "']");
			}

			//show or hide percent boxes based on data-ability-value
			if( jQuery.inArray( i, valsArray ) != "-1" ){
			 percent_obj.show(); 
			} else {
			 percent_obj.hide();
			}
			ese_subname = ""; //reset ese
		} 

		//clear original array (this is fastest sol'n, believe it or not..)
		while( valsArray.length ) {
		  valsArray.pop();
		}

    });
  }

  //show results_variables field if results_type == 'adjusted'
  function results_variables_show( e ){
    if (e == undefined) {e = null;}
    //for loop to set up variables field, if 'adjusted' selected on result type
    if( e == null ){ //on page load
       var tab_upper_bound = __CARES__.ea_tabCount;

       for( i=1; i<= tab_upper_bound; i++ ) {
          
          //which tab / result_type are we on?
          var whichResultType = jQuery( "[name='ea_" + i + "_result_type']:checked" ).attr("value");  
          if ( whichResultType != undefined ) {
             var tr_variables = jQuery( "#ea_" + i + "_results_variables" ).parents(".tr_results_variables");
             if ( whichResultType == "A" ) {
                tr_variables.show();
             } else {
                tr_variables.hide();
             } 
          }
       }


    } else { //on radio change
       var ea_count = e.parent().data("ea-count");
       var tr_variables = jQuery( "#ea_" + ea_count + "_results_variables").parents(".tr_results_variables");
       if( e.attr("value") == "A" ){

          tr_variables.show();
       } else {
          tr_variables.hide();
       } 
    }

    //var ea_count =  
    //get (this) if exists
    /*if( jQuery("[name='result_type']:checked").val() == "adjusted" ){
       jQuery("tr.tr_results_variables").show();
    } else {
       jQuery("tr.tr_results_variables").hide();
    }
    */
  }


  //Limit strategy selects on EA/Results based on intervention selection
  function strategy_limit_results( ){

     var selected = jQuery("#strategies").multiselect("getChecked");
     //get values and titles
     var selection = {};
     var selections = [];  //array to hold selection objects

     jQuery.each( selected.filter(":input"), function(){
        selection = { 
           _value : this.value,
           _title : this.title
        }    
        selections.push(selection);

     });
     //update the results strategies dropdowns
     var resultsDropdowns = jQuery("[id$=_result_strategy]");

     jQuery.each( resultsDropdowns, function() {
        var result = jQuery(this); 
        //remove ALL options
        jQuery(this).find("option").remove();

        //iterate for each value
        jQuery.each(selections, function(){
           //result.find("option[value=" + this + "]").remove();   
           //add options
           result.append(
              jQuery('<option></option>').val(this._value).html(this._title)
           );
        });
     });

  }

  function setup_combobox(comp) {
     var cb=selector(comp.id);
 
     if (comp.id == 'EndNoteID') {console.log(comp)}
     //console.log(comp)

     cb.combobox()
     cb.find('option').remove();

     for(var j=0; j < comp.options.length; j++) {
        var _o=$("<option>")
        _o.val(comp.options[j].value)
        _o.text(comp.options[j].text)

        if (comp.options[j].selected==true) _o.attr("selected", "selected");
                  
        _o.appendTo(cb);
     }

     // update jquery ui generated input component with selected value
     cb.next().find(':input').val(cb.children(':selected').text());
  }

  // coordinates a combobox with an "Other" input box.
  // input box is disabled when combobox is not set to 'Other' 
  function setup_combobox_other() {
     var combo_id=arguments[0][0];
     var other_id=arguments[0][1];

     var el=$('#'+combo_id).next().find('input')
     $('#'+other_id).prop('disabled', true)

     $(el).bind("autocompleteselect", 
                {'combo_id':combo_id, 'other_id':other_id}, 
        function(e, ui) {
          var _text=ui.item.value

          var _disabled=false;
          if (_text.toUpperCase() != 'OTHER') {
             $('#'+e.data.other_id).val('')
             _disabled=true
          }

          $('#'+e.data.other_id).prop('disabled', _disabled)
          if (!_disabled) $('#'+e.data.other_id).focus() 
     });
  }

  function setup_multiselect(comp) {
     var ms=selector(comp.id);
 
     ms.find('option').remove();
     ms.attr('multiple', 'multiple') // add multiple attribute just in case it isn't there (or set incorrectly)

     for(var j=0; j < comp.options.length; j++) {
        var _o=$("<option>")
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
  }


  function upload_form_data_basic(e, upload_url) {
     //console.log('upload_form_data_basic')
     //number of tabs in ea section
     __CARES__.ea_tabCount=$('#effect_association_tabs >ul >li').size()
     __CARES__.visible_ea_tabs=[];

     var $tabs = $('#effect_association_tabs').tabs();  

     //tabs('length') no longer works in jQuery 1.10
     tabNumber = jQuery('#effect_association_tabs').size();

     //for (var i = 0; i < $tabs.tabs('length'); i++) {  
     //for (var i = 0; i < tabNumber; i++) {  
     //    if ($tabs.tabs(i).is('visible')) __CARES__.visible_ea_tabs.push(i+1);
     //}
     __CARES__.visible_ea_tabs=get_active_tab_id_nums()

     //console.log(__CARES__.visible_ea_tabs)
     for (var i=0; i < __CARES__.component_data.length; i++) {
        var comp=__CARES__.component_data[i];
		
		//ignore the ese tabs in component data (not where it's at)
		prefix = comp.id.substring(0,4);
		if( ( prefix == 'ese0' ) || ( prefix == 'ese1' ) || ( prefix == 'ese2' ) 
			|| ( prefix == 'ese3' ) || ( prefix == 'ese4' ) || ( prefix == 'ese5' ) 
			|| ( prefix == 'ese6' ) || ( prefix == 'ese7' ) || ( prefix == 'ese8' ) || ( prefix == 'ese9' ) ) {
			//console.log('In upload, skip over: ')
			//console.log( comp);
			continue;
		}

        // if our tab isn't visible
        if (comp.id.substring(0,2) == 'ea' && __CARES__.visible_ea_tabs.indexOf(comp.id.substr(3,1)) == -1) {
           continue
        } 

        switch (comp.type) {
          case "CHECKBOX":
               comp.checked=selector(comp.id).prop("checked");
               break;
          case "RADIOBUTTON":
               var _span=selector(comp.id);
               var _radios=_span.find(':radio');
               var _value='NULL';
               for(var j=0; j < _radios.length; j++) {
                  if(_radios[j].checked) _value=_radios[j].value
               }

               for(var j=0; j < comp.options.length; j++) {
                  comp.options[j].selected= comp.options[j].value == _value 
               }
               break;
          case "TEXTAREA":
          case "INPUTTEXT":
				//TODO:
               comp.text=selector(comp.id).val();
               break;
          case "COMBOBOX-OTHER":
          case "COMBOBOX":
               //get selected value
               var cb = selector(comp.id).combobox();
               var _text=cb.next().find(':input').val();

               for(var j=0; j < comp.options.length; j++) {
                  comp.options[j].selected= comp.options[j].text == _text 
               }
               break;

          case "MULTISELECT":
               var s = selector(comp.id);
               var _selected = s.multiselect("getChecked").map(function(){return this.value;}).get();
               if(_selected.length > 0) {
                 for(var j=0; j < comp.options.length; j++) {
                    if (_selected.indexOf(comp.options[j].value) > -1) {
                       comp.options[j].selected= true
                    } else { //mel added 28Oct
                       comp.options[j].selected=false;
                    }
                 }
               } else { //there are no selected and all must be cleared
                 for( var j=0; j<comp.options.length; j++ ) {
                    comp.options[j].selected=false;
                 }
               }
               break;
          case "DERIVED":
               var s = selector(comp.id);
               comp.text=s.text()
               break
          default:
               console.log("Invalid component type: " + comp.type, comp);
        } // switch  
     }  //for loop
	 
	 for (var i=0; i < __CARES__.ese_tab_component_data.length; i++) {
        var comp=__CARES__.ese_tab_component_data[i];
		
		// if our tab isn't visible
        if (comp.id.substring(0,2) == 'ea' && __CARES__.visible_ea_tabs.indexOf(comp.id.substr(3,1)) == -1) {
           continue
        } 

        switch (comp.type) {
          case "CHECKBOX":
               comp.checked=selector(comp.id).prop("checked");
               break;
          case "RADIOBUTTON":
               var _span=selector(comp.id);
               var _radios=_span.find(':radio');
               var _value='NULL';
               for(var j=0; j < _radios.length; j++) {
                  if(_radios[j].checked) _value=_radios[j].value
               }

               for(var j=0; j < comp.options.length; j++) {
                  comp.options[j].selected= comp.options[j].value == _value 
               }
               break;
          case "TEXTAREA":
          case "INPUTTEXT":
               comp.text=selector(comp.id).val();
               break;
          case "COMBOBOX-OTHER":
          case "COMBOBOX":
               //get selected value
               var cb = selector(comp.id).combobox();
               var _text=cb.next().find(':input').val();

               for(var j=0; j < comp.options.length; j++) {
                  comp.options[j].selected= comp.options[j].text == _text 
               }
               break;

          case "MULTISELECT":
               var s = selector(comp.id);
               var _selected = s.multiselect("getChecked").map(function(){return this.value;}).get();
               if(_selected.length > 0) {
                 for(var j=0; j < comp.options.length; j++) {
                    if (_selected.indexOf(comp.options[j].value) > -1) {
                       comp.options[j].selected= true
                    } else { //mel added 28Oct
                       comp.options[j].selected=false;
                    }
                 }
               } else { //there are no selected and all must be cleared
                 for( var j=0; j<comp.options.length; j++ ) {
                    comp.options[j].selected=false;
                 }
               }
               break;
          case "DERIVED":
               var s = selector(comp.id);
               comp.text=s.text()
               break
          default:
               console.log("Invalid component type: " + comp.type, comp);
        } // switch  
     }  //for loop

	 //tack ese_tab_component_data on to component_data
	 var all_components = __CARES__.component_data.concat( __CARES__.ese_tab_component_data );
	 
     $('#msg_div').text('Saving..')
     $('#msg_div').show()
     // upload data to server
     $.ajax({
        url: upload_url,
        dataType: "json",
        type: 'POST',
        data: {'action': 'save', 'epnpid': __CARES__.StudyID, 
               'data': JSON.stringify({//'components': __CARES__.component_data,
									  // 'ese_tab_components': __CARES__.ese_tab_component_data,
									   'components': all_components,
                                       'ea_tabCount': __CARES__.ea_tabCount,
                                       'visible_ea_tabs': __CARES__.visible_ea_tabs,
                                       'ese_tabCount': __CARES__.ese_tabCount})},
     }).done(function (data) { 
        // data saved...
        if (data.epnpid !== undefined) __CARES__.StudyID=data.epnpid;
        $('#msg_div').hide()
        $('#StudyID').val(__CARES__.StudyID);  // put id in input text box

        //TODO: update the epnpid in the URL and the page itself.
        //if epnpid param doesn't exist, update
        var epnpid_param = getURLParameterByName("epnpid") || '';

        if( epnpid_param == '' ){
           //add epnpid val to dropdown
           jQuery("#StudyIDList")
              .append(jQuery("<option></option>")
              .attr("value", __CARES__.StudyID)
              .text("Study Number: " + __CARES__.StudyID));

           jQuery("#StudyIDList").combobox("set_value", __CARES__.StudyID);

           //make the url reflect new form but don't actually reload
           //var basepath = window.location.origin + window.location.pathname + "?page=basic_form&epnpid" + epnpid_param;
           var pushToURL = window.location.pathname + "?page=basic_form";
           pushToURL += "&epnpid=" + __CARES__.StudyID;
           history.pushState( null, "Studies Entry Form", pushToURL );          
        } 

        //clear assignment table body
        jQuery("#assignment-table tbody").html("");
        //reload assignment data after ajax call
        get_assign_data();

     }).fail(function() { 
           // display error message
           alert("Failure saving data.."); 
     });

  }// upload_form_data


  function get_data_basic(e, url) {
    __CARES__.StudyID=$('#StudyID').val() || '';

    $.ajax({
       url: url,
       dataType: "json",
       data: {action: 'get', epnpid: __CARES__.StudyID},
    }).done(function (data) {
             console.log(data)
             initialize_components(data);
    });
  }

//function to get values for assignment tab
  function get_assignment_data_basic(e, url){

    var studies = [];
    //clear __CARES__.used_endnotedids
    __CARES__.used_endnoteids=[]; //clearing method works if NO REFERENCES

    //placeholder for studyids in table
    var data_studyids = []; 

    jQuery.ajax({
       url: url,
       dataType: "json",
       data: {
          action: 'get_assignments'
       }
    }).success( function( data ) {
       //now take json data and turn into Main table on Assignments tab

       data=data.studies;

       if(data){
          var len = data.length;
          var txt = "";
       
          if(len > 0){
             for( var i=0; i<len; i++ ){
                //if( data[i].StudyGroupingID && data[i].StudyID && data[i].AbstractionComplete){
                //placeholders
                var studygroup;
                var studygroup_val;
                var endnoteid;
                var endnoteobject = {};
                var readyanalysis;
                var readyanalysis_checked;
                var sgid_clone;

                //for table links
                var basepath = window.location.origin + window.location.pathname + "?page=basic_form";

                if( data[i] ){
                   //console.log(i);
                   //handle null/empty data
                   if (data[i].StudyGroupingID != undefined) { 
                      studygroup_val = (String( data[i].StudyGroupingID.length > 0 )) ? parseInt( data[i].StudyGroupingID ) : ""; 
                   } else {
                      studygroup_val = 0;
                   } 

                   studygroup = ""; //null prev value
                   //Study Group here as clone of AssignmentSGID, so we don't have to render all the comboboxes
                   sgid_clone = jQuery("#StudyGroupingIDAssignment").clone();
                   sgid_clone.addClass("sgid_dd");
                   sgid_clone.removeAttr('id');

                   //hide original combobox, just a placeholder for cloning
                   jQuery("#StudyGroupingIDAssignment").combobox("hide");

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
                   endnoteid = data[i].EndNoteID || ""; 
                   if( endnoteid != "" ){
                      endnoteobject = __CARES__.endnote_summaries[ endnoteid ];
                      __CARES__.used_endnoteids.push(endnoteid);
                   }

                   //if ReadyAnalysis is null, mark as N
                   if (data[i].ReadyAnalysis != undefined) {
                      readyanalysis = (String( data[i].ReadyAnalysis.length > 0 )) ? data[i].ReadyAnalysis : "N"; 
                   } else { //there is no val in db
                      readyanalysis = "N";
                   }

                   //set checked property
                   if( readyanalysis == "Y" ){
                      readyanalysis_checked = 'checked';
                   } else {
                      readyanalysis_checked = '';
                   }

                   //have tr hold phase and study id info as class
                   txt += "<tr class='assignment-study phase_";
				   
				   if( endnoteobject != null && endnoteid > 0 && endnoteobject != undefined ){
				      txt += endnoteobject.Phase;
                   }
				   txt += " " + data[i].StudyID + "' data-studyid='" + data[i].StudyID + "'>"; 
                   txt += "<td>" + studygroup + "</td>";
                   txt += "<td class='studyid_val'><a class='link' href='" + basepath + "&epnpid=" + data[i].StudyID + "'>" + data[i].StudyID + "</a></td>";

                   //EndNote rec number, for now since Mel see that as unique
                   txt += "<td>" + endnoteid + "</td>";
                   txt += "<td>";
				   
				   if( endnoteobject != null && endnoteid > 0 && endnoteobject != undefined ){
				      txt += endnoteobject.Phase;
                   }				   
				   txt += "</td>";

                   if( endnoteobject != null && endnoteid > 0 && endnoteobject != undefined ){
                   //from endnote: author, year, title
                      txt += "<td class='author'>" + endnoteobject.authors + "</td>";
                      txt += "<td>" + endnoteobject.dates + "</td>";
                      txt += "<td class='title'>" + endnoteobject.title + "</td>";
                   } else {
                      //placeholders for author, year, title
                      txt += "<td>----</td>";
                      txt += "<td>----</td>";
                      txt += "<td>----</td>";
                   }
                   txt += "<td>" + data[i].AbstractionComplete + "</td>";
                   txt += "<td>----</td>";
                   txt += "<td>" + data[i].VerificationComplete + "</td>";
                   //new checkbox field with study id in value..
                   txt += "<td><input type='checkbox' name='ready-analysis' value='" + studygroup_val + "' " + readyanalysis_checked + "></input></td>";

                   //old stuff
                   //txt += "<td><input type='checkbox' name='ready-analysis' value='" + studygroup + "' " + readyanalysis_checked + "></input></td>";

                   txt += "</tr>";

                   data_studyids.push( data[i].StudyID );
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
       readyAnalysisListen();
       getAssignmentDataNext();
       phaseFilterButtonListen();
       searchButtonListen();
       strategyFilterListen();

    }).complete( function( ) {

       console.log( data_studyids );
       
       //call ajax function to pull in multiselect study strategy data
       get_assignment_strategies_js( data_studyids, '/cgi-bin/Transtria/dataentry/basic_info.py' );

    });

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

  function setEndNoteInput(){

    if( __CARES__.EndNoteID != "" ){
       jQuery("select#EndNoteID").combobox("set_value", __CARES__.EndNoteID);
    }

  }


  //set listener on Ready for Analysis checkboxes on assignment tab so all Study Group IDs update together
  function readyAnalysisListen(){

    jQuery("input[name='ready-analysis']").on("click", function(){

       var value = jQuery(this).attr('value');
       var checked = jQuery(this).is(":checked");

       if( value != "" ){

          jQuery("input[name='ready-analysis']").filter(function(){return this.value==value}).each( function() {

             if( checked == true ){

                jQuery(this).prop('checked', true);
             } else {
                jQuery(this).prop('checked', false);
             }
          });

       }

    });


  }

  //after clicking Save on Assignments tab...
  function upload_assignment_data(e){
    //Send study id, study grouping id and ready analysis info thru ajax
    var studyGroupings = {};
    var all_tr = jQuery("#assignment-table tr");
    var sg_val;
    var sid_val;
    jQuery.each(all_tr, function(){
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

    //now, ajax those vals to our studies database
    jQuery.ajax({
       url: "/cgi-bin/Transtria/dataentry/basic_info.py",
       dataType: "json",
       data: {
          action: 'save_assignments',
          'data': JSON.stringify({ 'studygrouping_data' : studyGroupings, 'assignment_data' : readyAnalysis })
       }
    }).success( function( data ) {

       //console.log('Assignments saved');
       //console.log(data);

    });

    //return readyAnalysis;

  }

  //after clicking Save on Analysis tab...
  function upload_analysis_data(e){

    //get values
    var analysisData = [];
    
    //studygroupingid
    var studygroupingid = parseInt(jQuery("#StudyGroupingIDAnalysis").combobox('value'));
    //study type
    var studytype = parseInt(jQuery("#StudyGroupTypeList").combobox('value'));
    //outcome
    var outcome = parseInt( jQuery("#outcome").combobox('value') );
    //accessibility
    var accessibility = parseInt( jQuery("#accessibility").combobox('value') );
    //general applicability
    var gen_app = parseInt( jQuery("#general_applicability").combobox('value') );
    //applicability to HR populations
    var app_to_HR = parseInt( jQuery("#applicability_to_HR_populations").combobox('value'));

    //ajax vals to study groupings db
    jQuery.ajax({
       url: "/cgi-bin/Transtria/dataentry/basic_info.py",
       dataType: "json",
       data: {
          action: 'save_analysis',
          'data': JSON.stringify({ 'StudyGroupingID' : studygroupingid, 'StudyType' : studytype, 'Outcome' : outcome, 'Accessibility' : accessibility, 'GeneralApplicability' : gen_app, 'ApplicabilityToHRPopulations' : app_to_HR })
       }
    }).success( function( data ) {

       console.log(data);

    });

    return analysisData;

  }


  //clicking on 'Submit' for Study Grouping ID on ANALYSIS tab
  function get_study_grouping_data_analysis_basic(e, url){

    //get value of Study Grouping ID drop down on ANALYSIS TAB
    var whichIDNum = parseInt( jQuery("#StudyGroupingIDAnalysis").combobox('value') );

    if( whichIDNum > 0 ){
       jQuery.ajax({
          url: url,
          dataType: "json",
          data: {
             action: 'get_study_grouping_data',
             'data': JSON.stringify({ 'studygroupingid' : whichIDNum })
          },
          beforeSend: function( ) {
             jQuery("#msg_div").show();
          }
       }).success( function( data ) {
          jQuery("#msg_div").hide();

          //clear prior data in the table on the ANALYSIS tab
          jQuery("#analysis-table tbody").empty(); 

          //console.log('Study Grouping data got?');
          //output StudyGroupings data
          var sgdata = data.study_grouping_data;
          var studydata = data.study_data;
          var studydesigns = data.all_study_designs;
          var sd_array =[];

          //turn json to array for study design
          if (studydesigns){
             jQuery.each( studydesigns, function(index, value){
                 jQuery.each(value, function(index, value){
                     sd_array[ parseInt(index) ] = value;
                 });
             });
          }

          if( sgdata ){
              var len = sgdata.length;
              var txt = "";

              if( len > 0 ){
              //should be only one.
                 sgdata = sgdata[0];
                 var studytype;
                 var readyanalysis;
 
                 if( sgdata.StudyType != undefined ){
                    studytype = (String( sgdata.StudyType.length > 0 )) ? parseInt( sgdata.StudyType ) : "";
                 } else {
                    studytype = "";
                 }
                   
                 //TODO: set values in dropdown for Study Type (since only 2)
 
                 if( sgdata.ReadyAnalysis != undefined) {
                    readyanalysis = (String( sgdata.ReadyAnalysis.length > 0 )) ? sgdata.ReadyAnalysis : "";
                 } else {
                    readyanalysis = "";
                 }
                 //console.log('studytype' + studytype + ', readyanalsis ' + readyanalysis);
              }
           }

           if( studydata ){
               var len = studydata.length;
               var txt = "";

               if ( len > 0 ){
                  for( var i=0; i<len; i++ ){
                   
                     var studyid;
                     var studydesignNum;
                     var studydesign;

                     if( studydata[i] ){
                        if( studydata[i].StudyDesignID != undefined ){
                           studydesignNum = (String( studydata[i].StudyDesignID.length > 0 )) ? parseInt( studydata[i].StudyDesignID ) : "" ;
                           studydesign = sd_array[studydesignNum]; 


                        } else {
                           studydesignNum = "";
                           studydesign = "";
                        } 
                     }
                     //now build table data

                     txt += "<tr>";
                     txt += "<td>" + studydata[i].StudyID + "</td>";

                     txt += "<td>Author Placeholder</td>";
                     txt += "<td>Year</td>";
                     txt += "<td>Title Placeholder</td>";

                     txt += "<td>" + studydesign + "</td>";
                     txt += "</tr>";

                  }
                  if( txt != "" ){
                     jQuery("#analysis-table tbody").append(txt);
                  }
               }                 
           }
           jQuery("#analysis-table").tablesorter(
               { widgets: ['zebra'] }
           );

       });

    } 

  }

  //function to show/hide Endnote Citation data (old data)
  function show_endnote_data(){
     jQuery("td.showolddata-title button").on("click", function(){
       var datatable = jQuery("tr.showolddata-data");
       if( datatable.is(":visible")){
           datatable.hide();
           jQuery("td.showolddata-title button").html("SHOW ENDNOTE CITATION DATA");
       } else {
          datatable.show();
           jQuery("td.showolddata-title button").html("HIDE ENDNOTE CITATION DATA");
       }
     });
  } 

	function onReady(url, variables, events) {
		console.log('on ready called');
		$(function() {
			$.ajax({
				url:  url,  //"/cgi-bin/Transtria/dataentry/studies.py",
				dataType: "json",
				data: {action: 'get', epnpid: variables['StudyID'] || ''}
			}).done(function (data) {
				initialize_components(data);
				console.log('on ready done');
				if (events['afterRender'] !== undefined) {
				  events['afterRender']();
				}
				//afterRender();
				//console.log(data)
			});
    });
    
    //tablesorter on assignment tab - needs to update on change
    var alreadyUpdating = false;
    jQuery("#assignment-table").find("tbody").on("change", "select", function(e){
       if( !alreadyUpdating ){
          alreadyUpdating = true;
          jQuery(this).trigger('update');
          setTimeout(function() {
             alreadyUpdating = false;
          }, 10);
       }
    });
    
    //citation tabs on basic info
    show_endnote_data();
    jQuery("#citation_tabs").tabs();

  }

  function component_mapper() {
    __CARES__.component_mapper={}
    for (var i=0; i < __CARES__.component_data.length; i++) {
        var _comp=__CARES__.component_data[i];
        __CARES__.component_mapper[_comp.id]=_comp;
    }
  }
