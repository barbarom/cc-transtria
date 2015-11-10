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
     
    $path =  dirname( __FILE__ ) . "/downloads";
     
     
     
    // Open the folder
     
    $dir_handle = @opendir($path) or die("Unable to open $path");
     
     
     
    // Loop through the files
     
    while ($file = readdir($dir_handle)) {
     
     
     
    if($file == "." || $file == ".." || $file == "index.php" )
     
     
     
    continue;
     
    echo "<a href=\"$file\">$file</a><br>&gt;";
     
     
     
    }
     
     
     
    // Close
     
    closedir($dir_handle);
     
     

	//include the csv file....GATKNGEKTREA TN
	require_once( dirname( __FILE__ ) . '\transtria_strategies.php' );	
	
}