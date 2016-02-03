<?php 
/**
 * CC Transtria Analysis Template Tags
 *
 * @package   CC Transtria Extras
 * @author    CARES staff
 * @license   GPL-2.0+
 * @copyright 2015 CommmunityCommons.org
 */

/**
 * Output logic for the analysis page. 
 *
 * @since   1.0.0
 * @return 	outputs html
 */
function cc_transtria_render_download_page(){

     
    // Define the full path to your folder from root
	$root_ish = site_url();
	//echo $root_ish;
    //$path =  dirname( __FILE__ ) . "/downloads";
    //$path =  $root_ish . 'PHPExcel/transtria/strategies.xls';
	$path = $root_ish . "/PHPExcel/transtria/";
	$analysis_vars_path = $path . "analysis.xls";
	$intermediate_vars_path = $path . "intermediate.xls";
	$strat_vars_path = $path . "strategies.xls";
	$studies_vars_path = $path . "single_studies.xls";
	//$path = "../../PHPExcel/transtria/strategies.xls";
	//echo $path;
	
	echo "<h2>Reloading this page refreshes the following Excel documents:</h2>";
	echo "<a href=\"$analysis_vars_path\">$analysis_vars_path</a><br>";
	echo "<a href=\"$intermediate_vars_path\">$intermediate_vars_path</a><br>";
	echo "<a href=\"$strat_vars_path\">$strat_vars_path</a><br>";
	
	//make button to refresh raw data since it's a process to build these spreadsheets
	echo "<a id='studies_download_refresh' class='button'>Refresh Raw Studies Data</a><br />";
	echo "<a href=\"$studies_vars_path\">$studies_vars_path</a><br>";

	//include the csv file....GATKNGEKTREA TN
	require_once( dirname( __FILE__ ) . '\transtria_analysis_download.php' );	
	require_once( dirname( __FILE__ ) . '\transtria_intermediate_download.php' );	
	require_once( dirname( __FILE__ ) . '\transtria_strategies_download.php' );	
	//require_once( dirname( __FILE__ ) . '\transtria_studies_download.php' );	
	
}