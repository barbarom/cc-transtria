<?php 
if ( class_exists( 'BP_Group_Extension' ) ) : // Recommended, to prevent problems during upgrade or when Groups are disabled

class CC_Transtria_Extras_Extension extends BP_Group_Extension {

    function __construct() {
        $args = array(
            'slug' => cc_transtria_get_slug(),
            'name' => 'Community Planning Tool',
            'visibility' => 'private',
            'enable_nav_item'   => $this->aha_tab_is_enabled(),
            // 'access' => 'members',
            // 'show_tab' => 'members',
            'nav_item_position' => 15,
            // 'nav_item_name' => ccgn_get_tab_label(),
            'screens' => array(
                'edit' => array(
                  'enabled' => false,
                ),
                'create' => array(
                    'enabled' => false,
                    // 'position' => 100,
                ),
                'admin' => array(
                    'enabled' => false,
                ),


            ),
        );
        parent::init( $args );
    }
 
    public function display( $group_id = null ) {

        cc_transtria_render_tab_subnav();

        if ( cc_aha_on_main_screen() ) {

            cc_transtria_print_introductory_text();

        } else if ( cc_aha_on_survey_screen() ) {

			// Get the right page of the form to display. bp_action_variable(1) is the page number
			cc_aha_render_form( bp_action_variable(1) );
			

        } else if ( cc_aha_on_analysis_screen() ) {
           
			// We'll store the selected metro id in a cookie for persistence.
			cc_aha_print_metro_select_container_markup();
			// Get the right summary page to display.
			cc_aha_render_summary_page();
            
            
        } else if ( cc_aha_on_survey_quick_summary_screen() ) {
            cc_aha_render_all_questions_and_answers();
        } 
    }

    public function aha_tab_is_enabled(){

    	if ( cc_aha_is_aha_group() ) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}
bp_register_group_extension( 'CC_Transtria_Extras_Extension' );
 
endif;