<?php
/**
 * PHPExcel
 *
 * Effect Association table xls builder
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
    ->setTitle("Effect Association table data: raw");
	
	
$objPHPExcel->setActiveSheetIndex(0);

	//number of columns
	$column_num_seq = "SELECT COUNT(`COLUMN_NAME`) FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`='" . $schema . "' AND `TABLE_NAME`='wp_transtria_effect_association'";
	$column_name_seq = "SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`='" . $schema . "' AND `TABLE_NAME`='wp_transtria_effect_association'";

	$column_num = $wpdb->get_var( $column_num_seq );
	$column_names = $wpdb->get_results( $column_name_seq, ARRAY_A );

	//the query...for now
	$question_sql = 
		"
		SELECT *
		FROM $wpdb->transtria_effect_association
		"
		;
		
	$result = $wpdb->get_results( $question_sql, ARRAY_N );
	
	$row_count = $wpdb->num_rows;

	//total number of fields = row_count * col_count //TODO: remove if not used
	$num_fields = $row_count * $column_num;
	
//from http://stackoverflow.com/questions/12611148/how-to-export-data-to-an-excel-file-using-phpexcel	
// Initialise the Excel row number 
$rowCount = 1;  

//start of printing column names as names of MySQL fields  
$column = 'A';

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

//save to excel sheet
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save( $root_ish . '/PHPExcel/transtria/effect_association.xls');
