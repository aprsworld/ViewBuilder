<?
$station_id=$_REQUEST["station_id"];
$view=$_REQUEST["view"];

require_once($_SERVER["DOCUMENT_ROOT"] . "/world_config.php");
$db=_open_mysql("worldData");

/* if not public, then we need to be authorized */
if ( 0==authPublic($station_id,$db) ) {
        require $_SERVER["DOCUMENT_ROOT"] . "/auth.php";
}

if ( "" == $station_id ) $station_id='Z1';
$hours=$_REQUEST["hours"];
if ( 0 == $hours ) $hours=24;
$height=$_REQUEST["height"];
if ( 0 == $height ) $height=48;
$parameter=$_REQUEST["parameter"];
if ( "" == $parameter ) $parameter="1";;

$start_date=$_REQUEST["start_date"];

if ( "" != $view )
        $view='_' . $view;

if ( "" != $start_date )  {
	$sql=sprintf("SELECT AVG(%s) FROM view_%s%s WHERE packet_date>='%s 00:00:00' AND packet_date<=DATE_ADD('%s 00:00:00',INTERVAL %d HOUR) GROUP BY LEFT(packet_date,15) ORDER BY packet_date",$parameter,$station_id,$view,$start_date,$start_date,$hours);

} else {
	$sql=sprintf("SELECT AVG(%s) FROM view_%s%s WHERE packet_date>=DATE_SUB(now(),INTERVAL %d HOUR) GROUP BY LEFT(packet_date,15) ORDER BY packet_date",$parameter,$station_id,$view,$hours);
}

//die($sql);
mysql_select_db("worldDataView");
$query=mysql_query($sql,$db);

$data=array();
while ( $r=mysql_fetch_array($query) ) {
	$data[]=$r[0];
}

//////////////////////////////////////////////////////////////////////////////
// build sparkline using standard flow:
//   construct, set, render, output
//
require_once($_SERVER["DOCUMENT_ROOT"] . '/sparkline/lib/Sparkline_Bar.php');

$sparkline = new Sparkline_Bar();
$sparkline->SetDebugLevel(DEBUG_NONE);
//$sparkline->SetDebugLevel(DEBUG_ERROR | DEBUG_WARNING | DEBUG_STATS | DEBUG_CALLS, '../log.txt');

//$sparkline->SetBarWidth(2);
//$sparkline->SetBarSpacing(1);
if ( "output_power"==$parameter ) {
	$sparkline->SetYMin(0);
	$sparkline->SetYMax(12000);
}

if ( is_numeric($_REQUEST['ymin']) ) 
	$sparkline->SetYMin($_REQUEST['ymin']);
if ( is_numeric($_REQUEST['ymax']) ) 
	$sparkline->SetYMax($_REQUEST['ymax']);

for ( $i=0 ; $i<count($data) ; $i++ ) {
	$color='black';
	if ( "output_power"==$parameter ) {
		if ( $data[$i] > 10000 ) 
			$color='orange';		
		else
			$color='green';
	} else {
		if ( $data[$i] >= 0 ) 
			$color='green';		
		else
			$color='red';

	}
	$sparkline->SetData($i,$data[$i],$color);
}

$sparkline->Render($height); // height only for Sparkline_Bar

$sparkline->Output();

?>
