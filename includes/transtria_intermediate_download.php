<?php
/**
 * PHPExcel
 *
 * Copyright (C) 2006 - 2014 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    1.8.0, 2014-03-02
 */

global $wpdb;
 
/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('America/Chicago');

if (PHP_SAPI == 'cli')
	die('This example should only be run from a Web Browser');

/** Include PHPExcel */
//CSV doc and PHPExcel things
$root_ish = ABSPATH;
//var_dump( $root_ish );

try{ 
	require_once $root_ish . '/PHPExcel/Classes/PHPExcel.php';
} catch ( Exception $e ) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
	exit;
} 


//what's the schema for this server?
switch ( get_home_url() ) {
	case 'http://localhost/wordpress':
		$schema = "";  //Mike's machine
		break;
	case 'http://localhost/cc_local':
		$schema = 'cclocal_db';  //Mel's compy
		break;
	case 'http://dev.communitycommons.org':
		$schema = 'ccdevelopment'; //TODO
		break;
	case 'http://www.communitycommons.org':
		$schema = 'ccmembers'; //TODO
		break;
	case 'http://staging.communitycommons.org':
		$schema = 587; //TODO
		break;
	default:
		$schema = 'ccdev';  //TODO
		break;
}



// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

$objPHPExcel->getProperties()
    ->setCreator("Transtria")
    ->setLastModifiedBy("Transtria")
    ->setTitle("Intermediate vars");
	
	
$objPHPExcel->setActiveSheetIndex(0);

	//number of columns
	$column_num_seq = "SELECT COUNT(`COLUMN_NAME`) FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`='" . $schema . "' AND `TABLE_NAME`='wp_transtria_analysis_intermediate'";
	$column_name_seq = "SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`='" . $schema . "' AND `TABLE_NAME`='wp_transtria_analysis_intermediate'";
	//var_dump( $column_num_seq );
	$column_num = $wpdb->get_var( $column_num_seq );
	$column_names = $wpdb->get_results( $column_name_seq, ARRAY_A );

	//var_dump( $column_names[0]["COLUMN_NAME"] );

//the query...for now
	$question_sql = 
		"
		SELECT *
		FROM $wpdb->transtria_analysis_intermediate
		"
		;
		
	$result = $wpdb->get_results( $question_sql, ARRAY_N );
	
	/*$form_rows = $wpdb->get_results( $question_sql, ARRAY_A );
	
	//get extra info from beyond the intermediate table
	foreach ( $form_rows as $index => $one_dyad ){
		//unserialize the Result Evaluation Pop and Result Subpops
		$one_dyad['result_eval_unserial'] = unserialize( $one_dyad['result_evaluation_population'] );
		$one_dyad['result_subpop_unserial'] = unserialize( $one_dyad['result_subpopulation'] );
		
		//get the race percentages of the Evaluation Population(s) 
		$one_dyad['race_percentages'] = get_race_percents_population( $s_id, $one_dyad['info_id'], $one_dyad['result_eval_unserial'] );
		
		//Strategies: unserialize the values; for each value, get text in codetbl
		$indexed_strats = array();
		$unserial_strats = unserialize( $one_dyad['indicator_strategies'] );
		
		if( !empty( $unserial_strats ) ){
			foreach( $unserial_strats as $i => $strat_val ){
				//go through and create indexed strategies with value/description pairs
				$indexed_strats[ $strat_val ] = $strategies_lookup[ $strat_val ];
			
			}
		} else {
			$indexed_strats = "";
		}
	
		$one_dyad['indicator_strategies_unserial'] = $indexed_strats;
		
		$form_rows[ $index ] = $one_dyad;
	
	}
	
	//adding columns for extra info
	$column_num = $column_num + 4;
	
	//assing column header for extra info
	
	$column_names[] = array( "COLUMN_NAME" => "result_evaluation_population");
	$column_names[] = array( "COLUMN_NAME" => "result_subpopulation");
	$column_names[] = array( "COLUMN_NAME" => "race_percentages");
	$column_names[] = array( "COLUMN_NAME" => "indicator_strategies");
	
	$result = $form_rows;
	
	*/
	
	
	//var_dump( $result );
	$row_count = $wpdb->num_rows;
	
	
	//total number of fields = row_count * col_count //TODO: remove if not used
	$num_fields = $row_count * $column_num;
	//var_dump( $num_fields );
	
	//return $form_rows;

//from http://stackoverflow.com/questions/12611148/how-to-export-data-to-an-excel-file-using-phpexcel	
// Initialise the Excel row number 
$rowCount = 1;  

//start of printing column names as names of MySQL fields  
$column = 'A';

//$wpdb->num_rows
for ($i = 0; $i < $column_num; $i++){
	//for( $col_index = 0; $col_index < count( 
    $objPHPExcel->getActiveSheet()->setCellValue( $column.$rowCount, $column_names[ $i]["COLUMN_NAME"] );
    $column++;
}
//end of adding column names  

//start while loop to get data  
$rowCount = 2;  
//while( $row <= $row_count ) {
for( $k = 0; $k < $row_count; $k++ ){
  
    $column = 'A';
    for( $j = 0; $j < $column_num; $j++) {  
        if( !isset( $result[$k][$j] ) )  
            $value = NULL;  
        else if ( $result[$k][$j] != "" )  
            $value = strip_tags( $result[$k][$j] );  
        else  
            $value = "";  

		//var_dump( $result[$k][$j] );
        $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $value);
        $column++;
    }  
    $rowCount++;
} 


//unset all the headers?
//header_remove();

// Redirect output to a client’s web browser (Excel5) 
//header('Content-Type: application/vnd.ms-excel'); 
//header('Content-Disposition: attachment;filename="Limesurvey_Results.xls"'); 
//header('Cache-Control: max-age=0'); 
//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); 
//$objWriter->save('php://output');
	
	






$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
//$objWriter->save('php://output'); //meeeeeeh?
//$objWriter->save( dirname( __FILE__ ) .'\downloads\write.xls');
$objWriter->save( $root_ish . '/PHPExcel/transtria/intermediate.xls');
