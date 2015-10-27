<?php 
/**
 * CC Transtria Analysis Functions
 *
 * @package   CC Transtria Extras
 * @author    CARES staff
 * @license   GPL-2.0+
 * @copyright 2015 CommmunityCommons.org
 */
 
/**
 * Returns the lookup int for Outcome Type
 *
 * @param string. Value from lookup table
 * @return int.
 */
function cc_transtria_calculate_ea_direction( $ind_dir, $out_dir ){
	
	if( ( $ind_dir == 1 || $ind_dir == 4 ) && ( $out_dire == 1 || $out_dir == 4 ) ){
		return 1;
	} else if ( ( $ind_dir == 2 || $ind_dir == 3 ) && ( $out_dire == 2 || $out_dir == 3 ) ){
		return 2;
	} else {
		return 3;
	}


}

/**
 * Searches multidimensional array by key=> value pair
 *
 * @param array, string, string
 * @return
 */
function md_array_search( $array, $key, $value){
    $results = array();

    if ( is_array($array) ) {
        if ( isset($array[$key]) && $array[$key] == $value ) {
            $results[] = $array;
        }

        foreach ($array as $i => $subarray) {
            $results[$i] = array_merge($results, md_array_search($subarray, $key, $value));
        }
    }

    return $results;
}
