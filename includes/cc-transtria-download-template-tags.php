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
 * Output logic for the /download page
 *
 * @since   1.0.0
 * @return 	outputs html
 */
function cc_transtria_render_download_page(){

	//include the csv file generators
	require_once( dirname( __FILE__ ) . '\transtria_analysis_download.php' );	
	require_once( dirname( __FILE__ ) . '\transtria_intermediate_download.php' );	
	require_once( dirname( __FILE__ ) . '\transtria_strategies_download.php' );	

    // Define the full path to your folder from root
	$root_ish = site_url();
	$path = $root_ish . "/PHPExcel/transtria/";
	
	$analysis_vars_path = $path . "analysis.xls";
	$intermediate_vars_path = $path . "intermediate.xls";
	$strat_vars_path = $path . "strategies.xls";
	$studies_vars_path = $path . "single_studies.xls";
	$pops_vars_path = $path . "populations.xls";
	$ea_vars_path = $path . "effect_association.xls";
	$metadata_vars_path = $path . "metadata.xls";
	$studygroupings_vars_path = $path . "studygroupings.xls";
	//$path = "../../PHPExcel/transtria/strategies.xls";
	//echo $path;
	
	echo "<h2>Reloading this page refreshes the following Excel documents:</h2>";
	echo "<a href=\"$analysis_vars_path\">$analysis_vars_path</a><br>";
	echo "<a href=\"$intermediate_vars_path\">$intermediate_vars_path</a><br>";
	echo "<a href=\"$strat_vars_path\">$strat_vars_path</a><br>";
	
	
}

/**
 * Output logic for the /raw-download page
 *
 * @since   1.0.0
 * @return 	outputs html
 */
function cc_transtria_render_raw_download_page(){
	
	set_time_limit(200);

	//include the csv file generators
	require_once( dirname( __FILE__ ) . '\transtria_studies_download.php' );	
	require_once( dirname( __FILE__ ) . '\transtria_ea_download.php' );	
	require_once( dirname( __FILE__ ) . '\transtria_meta_download.php' );	
	require_once( dirname( __FILE__ ) . '\transtria_populations_download.php' );	
	require_once( dirname( __FILE__ ) . '\transtria_codetbl_lookup_download.php' );	
	require_once( dirname( __FILE__ ) . '\transtria_coderesults_download.php' );	
	//require_once( dirname( __FILE__ ) . '\transtria_studygroupings_download.php' );	
	
    // Define the full path to your folder from root
	$root_ish = site_url();
	$path = $root_ish . "/PHPExcel/transtria/";
	
	$studies_vars_path = $path . "single_studies.xls";
	$pops_vars_path = $path . "populations.xls";
	$ea_vars_path = $path . "effect_association.xls";
	$metadata_vars_path = $path . "metadata.xls";
	//$studygroupings_vars_path = $path . "studygroupings.xls";
	$code_lookup_path = $path . "code_lookup.xls";
	$code_results_path = $path . "code_results.xls";

	echo "<h3>Raw Studies Data</h3>";
	
	echo "<a href=\"$studies_vars_path\">$studies_vars_path</a><br>";
	echo "<a href=\"$pops_vars_path\">$pops_vars_path</a><br>";
	echo "<a href=\"$ea_vars_path\">$ea_vars_path</a><br>";
	echo "<a href=\"$metadata_vars_path\">$metadata_vars_path</a><br>";
	echo "<a href=\"$code_results_path\">$code_results_path (multiple dropdown table)</a><br>";
	echo "<a href=\"$code_lookup_path\">$code_lookup_path</a><br>";
	//echo "<a href=\"$studygroupings_vars_path\">$studygroupings_vars_path</a><br>";

	
}

