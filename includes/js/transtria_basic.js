


  var tabCounter=1;
  var tabs;

  function upload_form_data(e) {
     upload_form_data_basic(e, "/cgi-bin/Transtria/dataentry/basic_info.py")
  }
  
  function get_data(e) {
     get_data_basic(e, "/cgi-bin/Transtria/dataentry/basic_info.py");
	 //console.log('get data called');
  }

  function get_assign_data(e) {
     get_assignment_data_basic(e, "/cgi-bin/Transtria/dataentry/basic_info.py")
  }

  function get_study_grouping_data_analysis(e) {
     get_study_grouping_data_analysis_basic(e, "/cgi-bin/Transtria/dataentry/basic_info.py")
  }

  function getURLParameterByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
  }

  //jQuery(function() {
  jQuery( document ).ready(function() {
     //look to see if any special variables are being passed in URL

     __CARES__.StudyID=getURLParameterByName("epnpid") || ''

     jQuery('#StudyID').val(__CARES__.StudyID)

     //if endnoteid passed in url, change endnoteid dropdown, pull up summary
     var endnoteid = getURLParameterByName("endnoteid") || '';
     __CARES__.EndNoteID = endnoteid;
     __CARES__.afterRenderList.push({'func': setEndNoteInput});
	 
	 //24Feb2015: Mel, turn into function (endnote_summary_listener), make afterRenderList
	 __CARES__.afterRenderList.push({'func': endnote_summary, 'args':[]});
     __CARES__.afterRenderList.push({'func': endnote_summary_listener, 'args':[]});
     

     jQuery("#header_tabs").tabs();
     //jQuery("#tabs").tabs();
     jQuery("#population_tabs").tabs();
     jQuery("#effect_association_tabs").tabs();

	 
     onReady("/cgi-bin/Transtria/dataentry/basic_info.py",
             {'StudyID': __CARES__.StudyID},
             {'afterRender': afterRender});

     
     var _inputs=['#validatorstarttime', '#validatorstoptime',
                  '#abstractorstarttime', '#abstractorstoptime'];
 
     for (var i=0; i < _inputs.length; i++) {
         jQuery(_inputs[i]).datetimepicker(
			{step:15,
			format:'Y-m-d H:i'}
		 );
     }
	 
	 //click listeners (Mel hates inline js)
	 jQuery(".remove_tab_button").on("click", remove_extra_ese_tab);
	 //afterRender();

     
  });

  function addEffectAssociationTab() {
       var tabs=jQuery("#effect_association_tabs").tabs();
       var tabContent= jQuery("#effect_association_content");
       var tabTemplate = "<li><a href='#{href}'>#{label}</a><span class='ui-icon ui-icon-close'>Remove Tab</span></li>";

       var label = "Tab " + tabCounter;
       var id = "effect_association_tab_" + tabCounter;
       var li = jQuery( tabTemplate.replace( /#\{href\}/g, "#" + id ).replace( /#\{label\}/g, label ) );

       tabs.find( ".ui-tabs-nav").append( li );
       //tabs.append( "<div id='" + id + "'><p>" + tabContent.html() + "</p></div>" );
       tabs.append( jQuery("#effect_association_tab_" + tabCounter));
       //"<div id='" + id + "'><p>" + tabContent.html() + "</p></div>" );

       tabs.delegate( "span.ui-icon-close", "click", function() {
            var panelId = jQuery( this ).closest( "li" ).remove().attr( "aria-controls" );
            jQuery( "#" + panelId ).remove();
            tabs.tabs( "refresh" );
       });

       if (jQuery('#StudyType').val() == 'Intervention') {
         jQuery('#ea_'+tabCounter+'_result_effect_association_type1').text('Effect')
       } else {
         jQuery('#ea_'+tabCounter+'_result_effect_association_type1').text('Associational')
       }

       /* look up selected values from intervention section, all outcomes accessed
			and populate results effect association tab, variable: outcome accessed.
	   */

       var _select=selector('ea_'+tabCounter+'_result_outcome_accessed')
	   
	   //get checked on ea subtabs
		var _selectSelected = _select.multiselect('getChecked');
		var _selectedArray = [];
		var valHolder;
		
		//make array for selected options
		_selectSelected.each( function() {
			valHolder = jQuery(this).val();
			_selectedArray.push(valHolder);
		});
		
       _options=selector('intervention_outcomes_assessed').multiselect('getChecked');
       _select.find('option').remove()   //remove existing items
       //populate with the selected items from intervention_outcomes_assessed
		for (var i=0; i < _options.length; i++) {
		
			if( jQuery.inArray( _options[i].value, _selectedArray ) != "-1" ) {
			   _select.append('<option value="' + _options[i].value +'" selected="selected">'+_options[i].title+"</options>")
			
			} else {
			   _select.append('<option value="' + _options[i].value +'">'+_options[i].title+"</options>")
			}
		   //console.log(_options[i])
		   //_select.append('<option value="' + _options[i].value +'">'+_options[i].title+"</options>")
		}

       _select.multiselect('refresh');
	   
	   //  END OUTCOMES ASSESSED POPULATING 
	   
	   /* look up selected values from intervention section, all indicators
			and populate results effect association tab, variable: results indicator.
	   */

	    _select=selector('ea_'+tabCounter+'_result_indicator')
	   
		//get checked on ea subtabs
		_selectSelected = _select.multiselect('getChecked');
		_selectedArray = []; //clear out array
		
		//make array for selected options
		_selectSelected.each( function() {
			valHolder = jQuery(this).val();
			_selectedArray.push(valHolder);
		});
		
       _options=selector('intervention_indicators').multiselect('getChecked');
       _select.find('option').remove()   //remove existing items
       //populate with the selected items from intervention_outcomes_assessed
		for (var i=0; i < _options.length; i++) {
		
			if( jQuery.inArray( _options[i].value, _selectedArray ) != "-1" ) {
			   _select.append('<option value="' + _options[i].value +'" selected="selected">'+_options[i].title+"</options>")
			
			} else {
			   _select.append('<option value="' + _options[i].value +'">'+_options[i].title+"</options>")
			}
		   //console.log(_options[i])
		   //_select.append('<option value="' + _options[i].value +'">'+_options[i].title+"</options>")
		}

       _select.multiselect('refresh');
	   
	   
	   
       jQuery('#intervention_outcomes_assessed').multiselect({
            close: function (event, ui) {
				//console.log("closed intervention_outcomes_assessed")
				//console.log(event, ui)
				//get the options on the Interventions/Partnerships tab
				var _options=selector('intervention_outcomes_assessed').multiselect('getChecked');
				
				var _selectedArray = [];
				var valHolder;
			   
				for (var i=1; i < 100; i++) {
					//clear array of selected options
					_selectedArray = [];
					
					//get the selector on the EA results subtab (EA tab)
					var _select=selector('ea_'+i+'_result_outcome_accessed');
					var _selectSelected = _select.multiselect('getChecked');

					//make array for selected options
					_selectSelected.each( function() {
						valHolder = jQuery(this).val();
						_selectedArray.push(valHolder);
					});

					_select.find('option').remove()   //remove existing items
					//populate with the selected items from intervention_outcomes_assessed
					for (var j=0; j < _options.length; j++) {
						if( jQuery.inArray( _options[j].value, _selectedArray ) != "-1" ) {
						   _select.append('<option value="' + _options[j].value +'" selected="selected">'+_options[j].title+"</options>")
						
						} else {
						   _select.append('<option value="' + _options[j].value +'">'+_options[j].title+"</options>")
						}
					}
					try {
					 _select.multiselect('refresh')
					} catch(e) {
					 console.log(e)
					}
				}
				
				
				//do the same for results_indicator
				_options=[];
				_options=selector('intervention_indicators').multiselect('getChecked');
				
				for (var i=1; i < 100; i++) {
					//clear array of selected options
					_selectedArray = [];
					
					//get the selector on the EA results subtab (EA tab)
					var _select=selector('ea_'+i+'_result_indicator');
					var _selectSelected = _select.multiselect('getChecked');

					//make array for selected options
					_selectSelected.each( function() {
						valHolder = jQuery(this).val();
						_selectedArray.push(valHolder);
					});

					_select.find('option').remove()   //remove existing items
					//populate with the selected items from intervention_outcomes_assessed
					for (var j=0; j < _options.length; j++) {
						if( jQuery.inArray( _options[j].value, _selectedArray ) != "-1" ) {
						   _select.append('<option value="' + _options[j].value +'" selected="selected">'+_options[j].title+"</options>")
						
						} else {
						   _select.append('<option value="' + _options[j].value +'">'+_options[j].title+"</options>")
						}
					}
					try {
					 _select.multiselect('refresh')
					} catch(e) {
					 console.log(e)
					}
				}
            }
       });

       //jQuery('#intervention_outcomes_assessed').multiselect('refresh')

       tabs.tabs("refresh");
       tabs.tabs({active: tabCounter-1});

       tabCounter++;
  }
 
  function search_tool_filter(code) {
        var target=jQuery('#searchtoolname').find('option')

        for (var i=0; i<target.length; i++) {
            var _code=jQuery(target[i]).attr('value').charAt(0)
            target[i].enabled=_code == code
            target[i].disabled = !target[i].enabled
        }

        jQuery('#searchtoolname').combobox('refresh')
  }

  //run this after EVERYTHING is loaded..
  afterRender = function() {
  //function afterRender(){
	console.log('after render function'); //24Feb, Mel, this isn't being called..
     jQuery('#searchtooltype').multiselect({
          close: function (event, ui) {
               console.log('got here');
               var _toolname=jQuery('#searchtoolname')
               _toolname.multiselect('uncheckAll');
               var _target = _toolname.find('option')
               for (var i=0; i < _target.length; i++) {
                   _target[i].disabled = true;
               }

               var _selected=jQuery('#searchtooltype').multiselect('getChecked');
               console.log(_selected);
               for (var i=0; i < _selected.length; i++) {
                   var _code=_selected[i].value.charAt(1)

                   //var _target = _toolname.find('option')
                   for (var j=0; j < _target.length; j++) {
                       var _code1=_target[j].value.charAt(0)
                       _target[j].enabled=_code == _code1
                       _target[j].disabled = !_target[j].enabled;
                   }
               }
               _toolname.multiselect('refresh')
          }
     });
                                      
     //var el=jQuery("#searchtooltype").next().find('input')
     //jQuery(el).bind("autocompleteselect", 
     //     function(e, ui) {
     //         // code is 01, 02, code in second position is the one we want.
     //         search_tool_filter(ui.item.code.charAt(1));
     //})

     jQuery('#InternationalFundingSourceType').click(function(e) {
        //var comp=e.target;
        //console.log("fired!")
        //console.log(this.checked)
        if (this.checked) {
           jQuery("#domesticfundingsources").combobox('disable');
        } else {
           jQuery("#domesticfundingsources").combobox('enable');
        }
     });

     jQuery('#InternationalSetting').click(function(e) {
        var _domestic=jQuery('#DomesticSetting')[0].checked;

        if (this.checked && !_domestic) {
           jQuery("#state_setting").multiselect('disable');
        } else {
           jQuery("#state_setting").multiselect('enable');
        }
     });

     function gender_combo_helper(e) {
       var prefix=e.data.prefix
       var _p='#' + prefix+'_gender';


       //get sibling span for value of gender
       var _value = jQuery(_p).siblings('span.custom-combobox').children('.custom-combobox-input').val();
       //var _value = jQuery(_p).val();

       //disable input text if selected values is 'Both'
       //jQuery(_p+'_pctmale').prop('disabled', _value != 'B ');
       jQuery(_p+'_pctmale').prop('disabled', _value != 'Both');
       //jQuery(_p+'_pctfemale').prop('disabled', _value != 'B ');
       jQuery(_p+'_pctfemale').prop('disabled', _value != 'Both');

       switch(_value) {
         case 'B ':   //both
         case 'Both':
           jQuery(_p+'_pctmale').prop('disabled', false);
           jQuery(_p+'_pctfemale').prop('disabled', false);

           //  jQuery(_p+'_pctmale').val('50')
           //  jQuery(_p+'_pctfemale').val('50')
         break
         case 'F ':
	 case 'Female':
           jQuery(_p+'_pctmale').val('0')
           jQuery(_p+'_pctfemale').val('100')
           break
         case 'M ':
	 case 'Male':
           jQuery(_p+'_pctmale').val('100')
           jQuery(_p+'_pctfemale').val('0')
           break
         default:
           jQuery(_p+'_pctmale').val('')
           jQuery(_p+'_pctfemale').val('')
       }
     }  //end function

     var _prefix=['tp', 'ipe', 'ipu', 'ese', 'esu'];
     for(var i=0; i < _prefix.length; i++) {
       // jQuery('#'+_prefix[i]+'_gender_pctmale').val('0')
       // jQuery('#'+_prefix[i]+'_gender_pctfemale').val('0')

       // jQuery('#'+_prefix[i]+'_gender_pctmale').prop('disabled', true)
       // jQuery('#'+_prefix[i]+'_gender_pctfemale').prop('disabled', true)

        var el=jQuery('#'+_prefix[i]+'_gender').next().find('input')
        jQuery(el).bind("autocompleteclose", {'prefix': _prefix[i]}, gender_combo_helper)
     }//for

     var el=jQuery('#intervention_component').next().find('input')
     jQuery(el).bind("autocompleteselect", function(e, ui) {
         var _value=ui.item.code

         console.log(_value)
         if (_value == '1' || _value == '2') {
            jQuery('#multi_component_flag').text('Multi-Component')
         //fix me
         }else if (_value =='3' && _value == '4') {
            jQuery('#multi_component_flag').text('Multi-Component')
         } else {
            jQuery('#multi_component_flag').text('')
         }
     })


     var el=jQuery('#complexity').next().find('input')
     jQuery(el).bind("autocompleteselect", function(e, ui) {
         var _value=ui.item.code

         // if already 'Multi-Component, then study is Multi-Component
         // (value of complexity does not affect study type
         if (jQuery('#multi_component_flag').text() == 'Multi-Component') return

         if (_value >= '1' && _value <= '8') {
            jQuery('#multi_component_flag').text('Complex')
         }
     })


     var el=jQuery('#evaluation_type').next().find('input')
     jQuery(el).bind("autocompleteselect", function(e, ui) {
        var code=ui.item.code.substr(1)

        var target=jQuery('#evaluation_methods').find('option')

        for (var i=0; i<target.length; i++) {
            var _code=jQuery(target[i]).attr('value')
            target[i].enabled=_code.substr(0,1) == code
            target[i].disabled = !target[i].enabled
        }

     })


     var el=jQuery('#StudyDesign').next().find('input')
     jQuery(el).bind("autocompleteselect", function(e, ui) {
         var code=ui.item.code

         var enable_disable=function(enable_flag) {
            // the following items are disabled.
            // todo.
            // pse_components.
            var items=['intervention_purpose','intervention_component',
                       'intervention_summary', 'complexity',
                       'duration', 'intervention_location',
                       'stage', 'state', 'quality', 'inclusiveness',
                       'replication', 'replication_descr',
                       'fidelity', 'implementation_limitations',
                       'lessons_learned', 'lessons_learned_descr']; 
            for (var i=0; i < items.length; i++) {
                for (var j=0; j < __CARES__.component_data.length; j++) {
                    if (__CARES__.component_data[j].id == items[i]) {
                       var _comp=jQuery('#'+items[i]);
                       switch(__CARES__.component_data[j].type) {
                          case 'MULTISELECT':
                             _comp.multiselect(enable_flag);
                             break
                          case 'COMBOBOX':
                             _comp.combobox(enable_flag);
                             break
                          case 'RADIOBUTTON':
                             _comp.find('input').attr('disabled', enable_flag=='disable');
                             break
                          case 'TEXTAREA':
                          case 'INPUTTEXT':
                             _comp.attr('disabled', enable_flag=='disable')
                             break
                          default:
                            console.log('invalid type', __CARES__.component_data[j].type)
                       } //switch
                    } // if
                } // for
            } // for
         }//enable_disable

         
         //console.log(code)
         //var target=jQuery('#StudyType').find('option')

         switch(code) {
            case '10':
	    case '11':    // study type is manually entered...

              //console.log('10 or 11') 
	      //jQuery('#StudyType').combobox('refresh')
              enable_disable('enable')
              break
            case '09':
            //case '10':   //association study type
              /*
              for (var i=0; i<target.length; i++) {
                 var _code=jQuery(target[i]).attr('value')
                 //jQuery(target[i]).prop('selected', _code == '0')   // association
                 if (_code == '1') {   // intervention
                    jQuery(target[i]).attr("selected", "selected")
                 } else {
                    jQuery(target[i]).removeAttr("selected")
                 }
              }
              */
              jQuery('#StudyType').text('Associational')

              enable_disable('disable')  //disable
              break
            case '01':
            case '02':
            case '03':
            case '04':
            case '05':
            case '06':
            case '07':
            case '08':    // Intervention study type
              console.log('code between 1 and 8')
              /*
              for (var i=0; i<target.length; i++) {
                  var _code=jQuery(target[i]).attr('value')
                  if (_code == '3') {   // intervention
                     jQuery(target[i]).attr("selected", "selected");
                  } else {
                     jQuery(target[i]).removeAttr("selected")
                  }
              }
              */
              jQuery('#StudyType').text('Intervention')

              for(var i=0; i < 100; i++) {
                 jQuery('#ea_'+i+'_result_effect_association_type1').text('Effect')
              }

              enable_disable('enable');  //true
              break;
         } // switch
         
     })


     function implementation_calc() {
         var _stage=0, _state=0, _quality=0, _inclusiveness=0

       setInterval(function () {
         switch(jQuery('#stage').combobox('value')) {
           case '01':
             _stage=1; break;
           case '02':
             _stage=0.66; break;
           case '03':
             _stage=0.33; break;
           case '04':
             _stage=0; break;
           default:
             _stage=0
         }

         switch(jQuery('#inclusiveness').combobox('value')) {
           case '01':
             _inclusiveness=1; break;
           case '02':
             _inclusiveness=0.66; break;
           case '03':
             _inclusiveness=0.33; break;
           case '04':
             _inclusiveness=0; break;
           default:
             _inclusiveness=0
         }


         switch(jQuery('#state').combobox('value')) {
           case '01':
             _state=1; break;
           case '02':
             _state=0.5; break;
           case '03':
             _state=0; break;
           default:
             _state=0
         }

         switch(jQuery('#quality').combobox('value')) {
           case '01':
             _quality=1; break;
           case '02':
             _quality=0.5; break;
           case '03':
             _quality=0; break;
           default:
             _quality=0
         }
       
         var _score=_stage+_state+_quality+_inclusiveness
         jQuery('#implementation_rating').text(_score+ " out of 4")
       }, 1000);  //setInterval
     }

     // do implementation calculation
     var el=jQuery('#stage').next().find('input')
     jQuery(el).bind("autocompleteselect", implementation_calc)
     
     var el=jQuery('#state').next().find('input')
     jQuery(el).bind("autocompleteselect", implementation_calc)
     
     var el=jQuery('#quality').next().find('input')
     jQuery(el).bind("autocompleteselect", implementation_calc)

     var el=jQuery('#inclusiveness').next().find('input')
     jQuery(el).bind("autocompleteselect", implementation_calc)

     // cost calculations
     function cost_calcs () {
         var _staff=0, _space=0, _equipment=0

         switch(jQuery('#staff_volunteer_costs').combobox('value')) {
           case 'L':  //low
           case 'L ':  //low
             _staff=1; break;
           case 'M':  //moderate
           case 'M ':  //moderate
             _staff=4; break;
           case 'H':  //high
           case 'H ':  //high
             _staff=6; break;
           case 'E':  //More evidence needed
           case 'E ':  //More evidence needed
             _staff=0; break;
           default:
             _staff=0
         }

         switch(jQuery('#space_infrastructure_costs').combobox('value')) {
           case 'L':  //low
           case 'L ':  //low
             _space=3; break;
           case 'M':  //moderate
           case 'M ':  //moderate
             _space=8; break;
           case 'H':  //high
           case 'H ':  //high
             _space=9; break;
           case 'E':  //More evidence needed
           case 'E ':  //More evidence needed
             _space=0; break;
           default:
             _staff=0
         }

         switch(jQuery('#equipment_material_costs').combobox('value')) {
           case 'L':  //low
           case 'L ':  //low
             _equipment=1; break;
           case 'M':  //moderate
           case 'M ':  //moderate
             _equipment=4; break;
           case 'H':  //high
           case 'H ':  //high
             _equipment=6; break;
           case 'E':  //More evidence needed
           case 'E ':  //More evidence needed
             _equipment=0; break;
           default:
             _equipment=0
         }
         var _rating = _space + _staff + _equipment
         jQuery('#cost_rating').text(_rating + " out of 21")
     }

     var el=jQuery('#staff_volunteer_costs').next().find('input')
     jQuery(el).bind("autocompleteselect", cost_calcs)

     var el=jQuery('#space_infrastructure_costs').next().find('input')
     jQuery(el).bind("autocompleteselect", cost_calcs)

     var el=jQuery('#equipment_material_costs').next().find('input')
     jQuery(el).bind("autocompleteselect", cost_calcs)

     //disable sample size radio button, unit of analysis, and 
     // population tabs if sample size available is equal to 'No'
     jQuery('#sample_size_available').find('input').on('change', function(e) {
          var comp=jQuery(this).val()
          //console.log(comp)
          //console.log(this.checked)
          if (!this.checked) return  // only want to look at the radio button selected
          comp=comp.trim()
          if (comp == 'Y') {
            jQuery('#unit_of_analysis').multiselect('enable')
            jQuery('#tabs-tp').show()
          } else {
            jQuery('#unit_of_analysis').multiselect('disable')
            jQuery('#tabs-tp').hide()
          }

          jQuery('#sample_estimate').find('input').prop('disabled', comp != 'Y')
          jQuery('#representativeness').find('input').prop('disabled', comp != 'Y')
          jQuery('#population_tabs').tabs({active:[0], disabled: comp != 'Y'})
     }).trigger('change')

     // effect/association rows.. (direction and type1)


     var effect_association_direction=function(e) {

         var i=e.data.value;
         setTimeout(function() {
           var _ind=jQuery('#ea_'+i+'_result_indicator_direction').combobox('value')
           var _out=jQuery('#ea_'+i+'_result_outcome_direction').combobox('value')

           //console.log(_ind)
           //console.log(_out)
           if (_ind == undefined || _out == undefined) {
              jQuery('#ea_'+i+'_result_effect_association_direction').text('')
              return
           }
 
           switch(_ind) {
             case '01':
             case '04':
                if (_out == '01' || _out == '04') {
                   jQuery('#ea_'+i+'_result_effect_association_direction').text('Positive(+)')
                } else if (_out == '02' || _out == '03') {
                   jQuery('#ea_'+i+'_result_effect_association_direction').text('Negative(-)')
                } else {
                   jQuery('#ea_'+i+'_result_effect_association_direction').text('')
                }
                return
             case '02':
             case '03':
                if (_out == '02' || _out == '03') {
                   jQuery('#ea_'+i+'_result_effect_association_direction').text('Positive(+)')
                } else if (_out == '01' || _out == '04') {
                   jQuery('#ea_'+i+'_result_effect_association_direction').text('Negative(-)')
                } else {
                   jQuery('#ea_'+i+'_result_effect_association_direction').text('')
                }
                return
           } // switch

           jQuery('#ea_'+i+'_result_effect_association_direction').text('')
        }, 100); // setTimeout
     } // end fun

     for (var i=1; i < 100; i++) {
        jQuery('#ea_'+i+'_result_indicator_direction').next().find('input').bind(
           "autocompleteselect", {value:i}, effect_association_direction); 

        jQuery('#ea_'+i+'_result_outcome_direction').next().find('input').bind(
           "autocompleteselect", {value: i}, effect_association_direction);

     }

     /*function clear_endnote_display() {
       var _inputs=['title', 'phase', 'author','dates', 'pubmed'];

       for (var i=0; i < _inputs.length; i++) {
           jQuery('#endnote_'+_inputs[i]).val('')
       }

       var _spans=['secondary_title', 'citationdates', 'volume',
                   'pages', 'citationpubmed', 'abstract', 'urls',
                   'isbn', 'language', 'notes', 'authoraddress',
                   'edition', 'alttitle', 'shorttitle', 'keywords',
                   'remotedatabasename', 'remotedatabaseprovider',
                   'number', 'worktype', 'section'];

       for (var i=0; i < _spans.length; i++) {
           jQuery('#endnote_'+_spans[i]).text('')
       }

     }*/

	 /*
     function endnote_summary( endnote_id ) {

        //endnote_id = endnote_id || "";
        if( endnote_id.length == 0 ){
           endnote_id = "";
        }

        //if( jQuery("#EndNoteID").combobox.length > 0 ) {
        //var _options=jQuery('#EndNoteID').multiselect('getChecked')
        //var _options=parseInt( jQuery('#EndNoteID').combobox('value') )

        var _options;
        if( endnote_id == "" ){
           _options=jQuery('#EndNoteID').combobox('value') 
           //console.log(_options); 
        } else {
           _options = endnote_id;
        }

        if( _options == undefined || _options.length == 0){
           clear_endnote_display()
           return
        } else { 
              
           //var _value=_options[0].value
           var _value=_options

           // begin: this is just temporary, until phase 1 is no more..
           
           //var _phase2=jQuery('#abstraction_complete').prop('checked');
           //if (_phase2==true) {
           //   _value='phase_2:' + _value
           //} else {
           //   _value='phase_1:' + _value
           //}
           // end: this is just temporary, until phase 1 is no more..

           var _summary=__CARES__.endnote_summaries[_value]
           if (_summary == undefined) {
              clear_endnote_display()
              return
           }

           jQuery('#endnote_title').text(_summary.title)
           jQuery('#endnote_phase').text(_summary.Phase)
           jQuery('#endnote_author').text(_summary.authors)
           jQuery('#endnote_dates').text(_summary.dates)
           jQuery('#endnotes_pubmed').text(_summary.accession_num)
           jQuery('#endnotes_secondary_title').text(_summary.secondary_title)
           jQuery('#endnotes_citationdates').text(_summary.dates)
           jQuery('#endnotes_volume').text(_summary.volume)
           jQuery('#endnotes_pages').text(_summary.pages)
           jQuery('#endnotes_citationpubmed').text(_summary.accession_num)
           jQuery('#endnotes_abstract').text(_summary.abstract)
           jQuery('#endnotes_urls').text(_summary.urls)
           jQuery('#endnotes_isbn').text(_summary.isbn)
           jQuery('#endnotes_doi').text(_summary.doi)
           jQuery('#endnotes_language').text(_summary.language)
           jQuery('#endnotes_notes').text(_summary.notes)
           jQuery('#endnotes_authoraddress').text(_summary.authaddress)
           jQuery('#endnotes_edition').text(_summary.edition)
           jQuery('#endnotes_alttitle').text(_summary.alttitle)
           jQuery('#endnotes_shorttitle').text(_summary.shorttitle)
           jQuery('#endnotes_keywords').text(_summary.keywords)
           jQuery('.endnotes_remotedatabasename').text(_summary.remote_database_name)
           jQuery('.endnotes_remotedatabaseprovider').text(_summary.remote_database_provider)
           jQuery('#endnotes_number').text(_summary.number)
           jQuery('#endnotes_worktype').text(_summary.worktype)
           jQuery('#endnotes_section').text(_summary.section)


           //}  //end if combobox length
        }

     }
	 */
     //__CARES__.afterRenderList.push({'func': endnote_summary, 'args':[]})

	 /*function endnote_summary_listener(){
		 jQuery("#EndNoteID").combobox({
			//change: function( event, ui ){
			//   endnote_summary();
			//   console.log('endnote summary fired');
			//}
			
			select: function(event, ui) {
			   //only works to pass in the value here because of synchronous functions? Otherwise, emdnote_summary() is pulling the OLD selected value..
			   endnote_summary( ui.item.value );
			}
		 });
	 
	 
	 
	 
	 }*/
	 
	 //24Feb2015: Mel, turn into function (endnote_summary_listener), make afterRenderList
     //__CARES__.afterRenderList.push({'func': endnote_summary_listener, 'args':[]});
     //listen to changes on endnoteid combobox
	 /*
     jQuery("#EndNoteID").combobox({
        //change: function( event, ui ){
        //   endnote_summary();
        //   console.log('endnote summary fired');
        //}
        
        select: function(event, ui) {
           //only works to pass in the value here because of synchronous functions? Otherwise, emdnote_summary() is pulling the OLD selected value..
           endnote_summary( ui.item.value );
        }
     });
	 */


     //now execute things that should happen after the page is rendered..
     for(var i=0; i < __CARES__.afterRenderList.length; i++) {
        var el=__CARES__.afterRenderList[i]
        el['func'](el['args'])
     }


     //execute calc functions just in case we have loaded an existing record.
     implementation_calc()
     cost_calcs()
     var ea_upper_bound = __CARES__.ea_tabCount + 1; 
     for (var i=1; i < ea_upper_bound; i++) {
         effect_association_direction({data: {value: i}})
     }

     for (var i=0; i < __CARES__.StudyID_list.length; i++) {
         var _item=__CARES__.StudyID_list[i]

         var _option='<option value="' + _item.StudyID +'" '
         if (_item.StudyID == __CARES__.StudyID && __CARES__.StudyID != '') {
            _option+= 'SELECTED'
         }

         _option+='>' + _item.Title + '</option>';

         jQuery('#StudyIDList').append(_option)
     }

     var _option='<option value="NEW">' + 'Add New Study' + '</option>';
     jQuery('#StudyIDList').append(_option)

     jQuery('#StudyIDList').combobox()

     var el=jQuery("#StudyIDList").next().find('input')
     jQuery(el).bind("autocompleteselect",
          function(e, ui) {
                var _value=ui.item.code
                if (_value == 'NEW') {
                   jQuery('#StudyID').val('')
                   jQuery('#StudyID').show()
                } else {
                   jQuery('#StudyID').val(_value)
                   jQuery('#StudyID').hide()
                }
     })

       // Messy, but I don't know how to check for native python types in Brython
       jQuery('#intervention_indicators').multiselect({
           close : function (e) {
              for (var tabCounter=1; tabCounter < 100; tabCounter++) {
                 var _select=selector('ea_'+tabCounter+'_result_indicator')
                 var _options=selector('intervention_indicators').multiselect('getChecked');
                 var _ea_options=_select.find('option')

                 var _values = []
                 for (var i=0; i < _options.length; i++) {
                      _values.push(_options[i].value)
                 }
                 for (var i=_ea_options.length-1; i >= 0; i--) {
                     if (_values.indexOf(_ea_options[i].value) == -1) {
                        _ea_options[i].remove()
                     } 
                 }

                 _ea_values=[]
                 for (var i=0; i < _ea_options.length; i++) {
                     _ea_values.push(_ea_options[i].value)
                 }

                 //populate with the selected items from intervention_outcomes_assessed
                 for (var i=0; i < _options.length; i++) {
                     if (_ea_values.indexOf(_options[i].value) == -1) {
                        _select.append('<option value="' + _options[i].value +'">'+_options[i].title+"</options>")
                     }
                 }

                 _select.multiselect('refresh')
              } // for

           }
     })
     
     //get assignment tab data
     get_assign_data();
	 
	 //initialize ese components (after regular init components, since that func appends to the ese data)
	 initialize_ese_components();

  }

	/////////////////////////////////////////////////////////////////
	//  ENDNOTE SUMMARY-RELATED FUNCTIONS 
	///////////////////////////////////////////////////////////////
	
	function clear_endnote_display() {
		var _inputs=['title', 'phase', 'author','dates', 'pubmed'];

		for (var i=0; i < _inputs.length; i++) {
		   jQuery('#endnote_'+_inputs[i]).val('')
		}

		var _spans=['secondary_title', 'citationdates', 'volume',
				   'pages', 'citationpubmed', 'abstract', 'urls',
				   'isbn', 'language', 'notes', 'authoraddress',
				   'edition', 'alttitle', 'shorttitle', 'keywords',
				   'remotedatabasename', 'remotedatabaseprovider',
				   'number', 'worktype', 'section'];

		for (var i=0; i < _spans.length; i++) {
		   jQuery('#endnote_'+_spans[i]).text('')
		}

	}
	
	function endnote_summary_listener(){
		jQuery("#EndNoteID").combobox({
			//change: function( event, ui ){
			//   endnote_summary();
			//   console.log('endnote summary fired');
			//}

			select: function(event, ui) {
			   //only works to pass in the value here because of synchronous functions? Otherwise, emdnote_summary() is pulling the OLD selected value..
			   endnote_summary( ui.item.value );
			}
		}); 
	}
	
	function endnote_summary( endnote_id ) {

        //endnote_id = endnote_id || "";
        if( endnote_id.length == 0 ){
           endnote_id = "";
        }

        //if( jQuery("#EndNoteID").combobox.length > 0 ) {
        //var _options=jQuery('#EndNoteID').multiselect('getChecked')
        //var _options=parseInt( jQuery('#EndNoteID').combobox('value') )

        var _options;
        if( endnote_id == "" ){
           _options=jQuery('#EndNoteID').combobox('value') 
           //console.log(_options); 
        } else {
           _options = endnote_id;
        }

        if( _options == undefined || _options.length == 0){
           clear_endnote_display()
           return
        } else { 
              
           //var _value=_options[0].value
           var _value=_options

           // begin: this is just temporary, until phase 1 is no more..
           
           //var _phase2=jQuery('#abstraction_complete').prop('checked');
           //if (_phase2==true) {
           //   _value='phase_2:' + _value
           //} else {
           //   _value='phase_1:' + _value
           //}
           // end: this is just temporary, until phase 1 is no more..

           var _summary=__CARES__.endnote_summaries[_value]
           if (_summary == undefined) {
              clear_endnote_display()
              return
           }

           jQuery('#endnote_title').text(_summary.title)
           jQuery('#endnote_phase').text(_summary.Phase)
           jQuery('#endnote_author').text(_summary.authors)
           jQuery('#endnote_dates').text(_summary.dates)
           jQuery('#endnotes_pubmed').text(_summary.accession_num)
           jQuery('#endnotes_secondary_title').text(_summary.secondary_title)
           jQuery('#endnotes_citationdates').text(_summary.dates)
           jQuery('#endnotes_volume').text(_summary.volume)
           jQuery('#endnotes_pages').text(_summary.pages)
           jQuery('#endnotes_citationpubmed').text(_summary.accession_num)
           jQuery('#endnotes_abstract').text(_summary.abstract)
           jQuery('#endnotes_urls').text(_summary.urls)
           jQuery('#endnotes_isbn').text(_summary.isbn)
           jQuery('#endnotes_doi').text(_summary.doi)
           jQuery('#endnotes_language').text(_summary.language)
           jQuery('#endnotes_notes').text(_summary.notes)
           jQuery('#endnotes_authoraddress').text(_summary.authaddress)
           jQuery('#endnotes_edition').text(_summary.edition)
           jQuery('#endnotes_alttitle').text(_summary.alttitle)
           jQuery('#endnotes_shorttitle').text(_summary.shorttitle)
           jQuery('#endnotes_keywords').text(_summary.keywords)
           jQuery('.endnotes_remotedatabasename').text(_summary.remote_database_name)
           jQuery('.endnotes_remotedatabaseprovider').text(_summary.remote_database_provider)
           jQuery('#endnotes_number').text(_summary.number)
           jQuery('#endnotes_worktype').text(_summary.worktype)
           jQuery('#endnotes_section').text(_summary.section)


           //}  //end if combobox length
        }

     }
	
	
	
	
	
	
	
  ////////////////////////////////////////////////////////////////////
  // functions to copy components from one tab to another..
  ////////////////////////////////////////////////////////////////////

  function copy_ea_tab(tab1, tab2) {
     var _spans=['result_effect_association_direction'] 
     var _inputs=['result_numeric', 'results_variables', 
                  'result_outcome_type_other', 
                  'result_outcome_accessed_other', 
                  'result_measures_other',
                  'statistical_measure_p_value', 
                  'statistical_measure_ci_value1',
                  'statistical_measure_ci_value2',
				  ]

     var _radio=['result_type',
                 'result_significant',
                 'result_subpopulationYN']
	
	 var _checkbox=['duration_notreported']

     var _combobox=['result_statistical_model',
                    'result_indicator_direction',
                    'result_outcome_direction', 
                    'result_strategy', 'result_outcome_type',
                    'result_indicator',
                    'result_statistical_measure',
					'duration']

     var _multiselect=['result_evaluation_population',
                       'result_outcome_accessed',
                       'result_subpopulations', 
                       'result_measures',
                       'result_indicator']

     //spans
     for (var i=0; i < _spans.length; i++) {
         var _root='_'+_spans[i]
         var _value=jQuery('#ea_'+tab1+_root).text()
         jQuery('#ea_'+tab2+_root).text(_value)
     }

     // inputs
     for (var i=0; i < _inputs.length; i++) {
         var _root='_'+_inputs[i]
         var _value=jQuery('#ea_'+tab1+_root).val()
         jQuery('#ea_'+tab2+_root).val(_value)
     }

	 
	 //checkbox
	 for (var i=0; i < _checkbox.length; i++) {
         var _root = '_'+_checkbox[i]
         var _ischecked = jQuery('#ea_' + tab1 + _root).is(":checked");
         //var _checked=jQuery('#ea_'+tab1+_root).find('input:checked')
         if (_ischecked == true) {
            // check box on target side
            jQuery('#ea_'+tab2+_root).prop("checked", "true");
         } else {
            // uncheck box on target side
            jQuery('#ea_'+tab2+_root).prop("checked", "");
		 }
     }
	 
	  // radio
     for (var i=0; i < _radio.length; i++) {
         var _root='_'+_radio[i]
         var _checked=jQuery('#ea_'+tab1+_root).find('input:checked')
         if (_checked.length == 0) {
            // need to make sure all items are unchecked on target side
            jQuery('#ea_'+tab2+_root).find('input').attr('checked', false)
         } else {
            var _value=_checked[0].value
            jQuery('#ea_'+tab2+_root).find('input[value="'+_value+'"]').prop('checked', true).trigger('change')
         }
     }

     //comboboxes
     for (var i=0; i < _combobox.length; i++) {
         var _root = '_' + _combobox[i]
         var _value
         try {
            _value=jQuery('#ea_'+tab1+_root).combobox('value')
         } catch(err) {
           console.log('#ea_'+tab1+_root+':'+err)
           continue
         }
         
         try {
           jQuery('#ea_'+tab2+_root).combobox('set_value', _value)
         } catch(err) {
           console.log('#ea_'+tab2+_root+':'+err)
         }
     }

     //multiselects
     for (var i=0; i < _multiselect.length; i++) {
         var _root='_'+_multiselect[i]
         var _options=jQuery('#ea_'+tab1+_root).multiselect('getChecked')
         var _values=[]
         for (var j=0; j < _options.length; j++) {
             _values.push(_options[j].value)
         }

         jQuery('#ea_'+tab2+_root).multiselect('uncheckAll')
         for (var j=0; j < _values.length; j++) {
             jQuery('#ea_'+tab2+_root).find('option[value="'+_values[j]+'"]').prop('selected', 'selected')
         }
         jQuery('#ea_'+tab2+_root).multiselect('refresh')
     }
  }

  function generate_copy_tab_select_options(e) {
       var target = jQuery('#'+e.target.id)
       //console.log(target)
       var _ids= get_active_tab_id_nums()

       target.children('option').remove()
       for (var i=0; i < _ids.length; i++) {
         _s='<option value="' + _ids[i] + '">' + _ids[i] + '</option>'
         target.append(_s)
       }
  }

  function get_active_tab_id_nums() {
     var _tabs=jQuery('#effect_association_tabs').children('div')

     var _ids=[]
     for (var i=0; i < _tabs.length; i++) {
         var _id=_tabs[i].id.split('_')[3]
         _ids.push(_id)
     }

     return _ids
  }

  function copy_tab(e) {
	// figure out what tab is currently selected
	var _active_tab_index = jQuery('#effect_association_tabs').tabs('option', 'active');

	//console.log(_active_tab_index)
	// retrieve the "name of the tab" from the list of tabs currently active
	var _tab_ids=get_active_tab_id_nums();
	var _active_tab=_tab_ids[_active_tab_index]

	// retrieve selected value (tab) from select box
	// this is the source we want to copy
	var _selected_tab = jQuery('#ea_'+_active_tab+'_copy_tab').val()

	//console.log(_selected_tab, _active_tab);
	copy_ea_tab(_selected_tab, _active_tab);
  }

  function copy_ese_tab( where_loaded ) {

	//where_loaded = "pageload" on page laod and "formcopy" if we're copying a tab within a form
  
    function recurse(node) {
      var _children=node.children()
      if (_children.length ==0) return

      var _prefix="ese" + __CARES__.ese_tabCount + '_'
      for (var i=0; i < _children.length; i++) {
          var _c=jQuery(_children[i])

          var _id=_c.attr('id')
          if(_id !== undefined && _id.substring(0,4) == 'ese_') {

             _c.attr('id',  _prefix + _id.substring(4))
             // copy 'new' ese-X component to __CARES_.component_data
             var _obj={}
             for (var j=0; j < __CARES__.component_data.length; j++) {
                 if (__CARES__.component_data[j].id == _id) {
                    //clone existing object
                    _obj=jQuery.extend(true, {}, __CARES__.component_data[j])
                    break
                 }
             }
             if (_obj != {}) {
               _obj.id=_prefix + _id.substring(4)
               if (_obj.name !== undefined) _obj.name=_prefix + _obj.name.substring(4);
			   if( where_loaded == "pageload" ){
				  __CARES__.component_data.push(_obj); //mel trying things 23Feb2015 to get new ESE tabs to save w their own data, not ESE main t
               } else {
				  __CARES__.ese_tab_component_data.push(_obj); //didn't work
			   }
               //console.log(_obj)
             
              
               if (_obj.type == 'COMBOBOX') {
                 try {
                   jQuery('#'+_obj.id).combobox('destroy')
                   jQuery('#'+_id).combobox()
                 } catch(e){
                   console.log(e)
                 }
               } else if (_obj.type == 'MULTISELECT') {
                 try {
                   jQuery('#'+_obj.id).multiselect('destroy')
                 } catch(e){
                   console.log(e)
                 }
                
                 //jQuery('#'+_obj.id).next('.ui-multiselect').remove()
                 //jQuery('#'+_obj.id).multiselect('enable')

                 jQuery('#' + _id).multiselect({header: 'Choose option(s)',
                     position: {my: 'left bottom', at: 'left top'},
                     selectedText: '# of # checked',
                     close: function( event, ui ){
                                multiselect_listener( jQuery('#' + _id) );
                     }
                 })

               }
              
             }
          }

          var _name=_c.attr('name')
          if(_name !== undefined && _name.substring(0,4) == 'ese_') {
             _c.attr('name', _prefix + _name.substring(4))
          }

          recurse(_c)
      }
    }

    var _clone=jQuery('#tabs-ese').children().clone(true,true);  //clone events as well

	//show remove tab button for this tab
	var remove_button = _clone.find(".remove_tab_button");
//	jQuery.data( remove_button, tabnumber, .data("
	remove_button.removeClass("hidden");
	
	//add which ese tab to remove_button.data
	//jQuery.data( remove_button, "tabnumber", __CARES__.ese_tabCount );
	remove_button.attr("data-tabnumber", __CARES__.ese_tabCount + 1);
	
    _clone.appendTo(document.body);
    recurse(_clone);
	
    return _clone
  }

  //Function that loads tabs (and components) on 'Add Tab' AND on form load
  function add_extra_ese_tab ( where_loaded ) {
	//where_loaded = "pageload" on page laod and "formcopy" if we're copying a tab within a form
    var tabTemplate = "<li><a href='#{href}'>#{label}</a></li>";
    var tabs=jQuery('#population_tabs').tabs()
    var _clone=copy_ese_tab( where_loaded )

    var label = "ES-E " + (__CARES__.ese_tabCount + 1)
    var id = "ES-E_" + __CARES__.ese_tabCount++

    var li = jQuery( tabTemplate.replace( /#\{href\}/g, "#" + id ).replace( /#\{label\}/g, label ) );

    tabs.find( ".ui-tabs-nav").append( li );
    //tabs.append(jQuery('#'+_clone.attr('id')).html())
    tabs.append(jQuery('<div id="'+id+'"></div>'))

    _clone.appendTo(jQuery('#'+id))

    // we need to now go thorugh all newly added dom objects and 
    // create combobox and multiselect ui components

    var _prefix="ese" + (__CARES__.ese_tabCount	 - 1) + '_';
	
	//mel trying to get newly-created ese tabs in ese_component_data: 23Feb2015
    //for (var i=0; i < __CARES__.component_data.length; i++) {
    for (var i=0; i < __CARES__.ese_tab_component_data.length; i++) {
        //var _comp=__CARES__.component_data[i]
        var _comp=__CARES__.ese_tab_component_data[i];
        //console.log(_comp)
		
        if (_comp.id.substring(0,5) == _prefix) {
           if (_comp.type == 'COMBOBOX') {
              // this is quite messy but this is the only way I have 
              // figured out how to copy a combobox from one tab
              // to another.

              jQuery('#'+_comp.id).next('.custom-combobox').remove()

              jQuery('#'+_comp.id).attr('id', _comp.id+'_tmp')
              var _s=jQuery('<select style="display:none">')
              _s.attr('id', _comp.id)
              var _c=jQuery('#'+_comp.id+'_tmp').children()
              _s.append(_c)
              jQuery('#'+_comp.id+'_tmp').before(_s)

              jQuery('#' + _comp.id).combobox()

           } else if (_comp.type == 'MULTISELECT') {
              // this is quite messy but this is the only way I have 
              // figured out how to copy a multiselect from one tab
              // to another.
              jQuery('#'+_comp.id).next('.ui-multiselect').remove()
              jQuery('#'+_comp.id).attr('id', _comp.id+'_tmp')
              var _s=jQuery('<select multiple="multiple" style="display:none">')
              _s.attr('id', _comp.id)
              var _c=jQuery('#'+_comp.id+'_tmp').children()
              _s.append(_c)
              jQuery('#'+_comp.id+'_tmp').before(_s)
              //jQuery('#'+_comp.id+'_tmp').remove()
            
              jQuery('#' + _comp.id).multiselect({header: 'Choose option(s)',
                     position: {my: 'left bottom', at: 'left top'},
                     selectedText: '# of # checked',
                     close: function( event, ui ){
                                multiselect_listener( jQuery('#' + _comp.id) );
                     }
              })
           }
        }
    }
    tabs.tabs("refresh")

  }
  
  //to remove the ESE tabs, even when saved.
  //TODO: This needs to be fixed. Python removes the data (all Population data on save), but the tab will show up again empty (not renumbering)
  function remove_extra_ese_tab () {
    var whichTab = jQuery(this).data("tabnumber");
	
	//remove tab from top tabs
	
    var tabs=jQuery('#population_tabs').tabs();
    //var _clone=copy_ese_tab();

    var label = "ES-E " + ( whichTab );
    var id = "ES-E_" + (whichTab - 1);
	var prepend = "ese" + (whichTab - 1); //for manual clearing of data fields

	var tabTemplate = "<li><a href='#{href}'>#{label}</a></li>";
    var li = jQuery( tabTemplate.replace( /#\{href\}/g, "#" + id ).replace( /#\{label\}/g, label ) );

    //tabs.find( ".ui-tabs-nav").remove( li );
    //tabs.append(jQuery('#'+_clone.attr('id')).html())
    //tabs.remove(jQuery('<div id="'+id+'"></div>'))

    //clear all multiselects
	jQuery("#" + prepend + "_geographic_scale").multiselect("uncheckAll");
	jQuery("#" + prepend + "_hr_subpopulations").multiselect("uncheckAll");
	jQuery("#" + prepend + "_ability_status").multiselect("uncheckAll");
	jQuery("#" + prepend + "_sub_populations").multiselect("uncheckAll");
	jQuery("#" + prepend + "_youth_populations").multiselect("uncheckAll");
	jQuery("#" + prepend + "_professional_populations").multiselect("uncheckAll");
	
	//clear radios
	jQuery("#" + prepend + "_representativeness input").attr("checked", false);
	jQuery("#" + prepend + "_oversampling input").attr("checked", false);
	jQuery("#" + id + " [type=radio]").attr("checked", false);

	//clear all checkboxes:
	jQuery("#" + id + " :checkbox").removeAttr('checked');
	
	//clear text boxes:
	jQuery("#" + id + " [type=text]").val('');
	jQuery("#" + id + " textarea").val('');
	
	//clear untyped inputs
	jQuery("#" + id + " input").val('');
	
	
	
    // remove tab contents:
	//jQuery("#"+id).html( "Tab data removed.  After saving, it will reload on page refresh with all form elements cleared.  \nTo undo this action, do not save this form until refreshing");

    //var _prefix="ese" + (__CARES__.ese_tabCount	 - 1) + '_'

    tabs.tabs("refresh")

  }