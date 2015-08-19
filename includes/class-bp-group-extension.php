<?php 
if ( class_exists( 'BP_Group_Extension' ) ) : // Recommended, to prevent problems during upgrade or when Groups are disabled

class CC_Transtria_Extras_Extension extends BP_Group_Extension {

    function __construct() {
        $args = array(
            'slug' => cc_transtria_get_slug(),
            'name' => 'Study Entry',
            'visibility' => 'private',
            'enable_nav_item'   => $this->transtria_tab_is_enabled(),
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

        if ( cc_transtria_on_main_screen() ) {
			cc_transtria_render_form( bp_action_variable(1) );
            //cc_transtria_print_study_form();

        } else if ( cc_transtria_on_assignments_screen() ) {

			// Get the right page of the form to display. bp_action_variable(1) is the page number
			cc_transtria_render_assignments_form( bp_action_variable(1) );
			

        } else if ( cc_transtria_on_analysis_screen() ) {
           
			cc_transtria_render_analysis_form( bp_action_variable(1) );
            
            
        } 
    }

    public function transtria_tab_is_enabled(){

    	if ( cc_transtria_is_transtria_group() ) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}
bp_register_group_extension( 'CC_Transtria_Extras_Extension' );
 
endif;