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
function cc_transtria_print_study_form(){
    ?>
    <p>
        Study Form will go here.
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
				<li <?php if ( cc_transtria_on_main_screen() ) { echo 'class="current"'; } ?>>
                    <a href="<?php echo cc_transtria_get_home_permalink(); ?>">Study Form</a>
                </li>
				<li <?php if ( cc_transtria_on_assignments_screen() ) { echo 'class="current"'; } ?>>
					<a href="<?php echo cc_transtria_get_assignments_permalink(); ?>">Assignments</a>
				</li>
                <li <?php if ( cc_transtria_on_studygrouping_screen() ) { echo 'class="current"'; } ?>>
                    <a href="<?php echo cc_transtria_get_studygrouping_permalink(); ?>">Study Groupings</a>
                </li>
                <li <?php if ( cc_transtria_on_analysis_screen() ) { echo 'class="current"'; } ?>>
                    <a href="<?php echo cc_transtria_get_analysis_permalink(); ?>">Analysis</a>
                </li>

			

            </ul>
        </div>
        <?php
}

