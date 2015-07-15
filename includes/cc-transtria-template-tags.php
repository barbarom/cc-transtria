<?php
/**
 * CC Transtria Extras
 *
 * @package   CC Transtria Extras
 * @author    CARES staff
 * @license   GPL-2.0+
 * @copyright 2015 CommmunityCommons.org
 */
/**
 * Print content for the "Introduction" (default tab)
 *
 * @since   1.0.0
 * @return  HTML
 */
function cc_transtria_print_introductory_text(){
    ?>
    <p>
        Welcome to Transtria's Study editor.
    </p>
    <?php
}


/**
 * Builds the subnav of the AHA group tab
 *
 * @since   1.0.0
 * @return  HTML
 */
function cc_transtria_render_tab_subnav(){
        ?>
        <div id="subnav" class="item-list-tabs no-ajax">
            <ul class="nav-tabs">
              <!--  <li <?php if ( cc_transtria_on_main_screen() ) { echo 'class="current"'; } ?>>
                    <a href="<?php echo cc_aha_get_home_permalink(); ?>">Introduction</a>
                </li>
				<li <?php if ( cc_aha_on_survey_screen() ) { echo 'class="current"'; } ?>>
					<a href="<?php echo cc_aha_get_survey_permalink(); ?>">Assessment</a>
				</li>
                <li <?php if ( cc_aha_on_analysis_screen( 'health' ) ) { echo 'class="current"'; } ?>>
                    <a href="<?php echo cc_aha_get_analysis_permalink(); ?>">Health Analysis Report</a>
                </li>
				<li <?php if ( cc_aha_on_analysis_screen( 'revenue' ) ) { echo 'class="current"'; } ?>>
					<a href="<?php echo cc_aha_get_analysis_permalink( 'revenue' ); ?>">Revenue Analysis Report</a>
				</li>
				-->
				Testing nav tabs
            </ul>
        </div>
        <?php
}

