<?
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
require $_SERVER['DOCUMENT_ROOT'] . '/world_config.php';

$station_id=$_REQUEST["station_id"];

$pScript="";
$pRow="";
$db = _open_mysql('worldData');
$sql=sprintf("SELECT * FROM thermok4_labels WHERE serialNumber='%s'",$station_id);
$query=mysql_query($sql,$db);
$l=mysql_fetch_array($query);


//print_r($l);

require $_SERVER['DOCUMENT_ROOT'] ."/ViewBuilder/pieces.php";

/* 

   place table row instructions here 
   if you use one argument, then the title will be the same as the column name
   if you use two arguments, then the title will be the second argument

*/

headline("Current Values");
simpleDataRow("packet_date","Report Date:");


/* Temperatures */
if ("" != $l["r0L"]) headline("Temperature");

for ( $i = 0 ; $i < 4 ; $i++ ){
	if ("" != $l["t".$i."L"]) toFixedDataRow("t".$i,$l["t".$i."L"],2," &deg;C");
}

/* analogue channels */
if ( ""!=$l["v0L"] || ""!=$l["v1L"] || ""!=$l["v2L"] || ""!=$l["v3L"] ) headline("Voltage");

for ( $i=0 ; $i<4 ; $i++ ) {
	if ("" != $l["v".$i."L"]) toFixedDataRow("vin".$i,$l["v".$i."L"],2," VDC");
}

/* relays */
if ( ""!=$l["r0L"] || ""!=$l["r1L"] || ""!=$l["r2L"] ) headline("Relay States");

for ( $i = 0 ; $i < 4 ; $i++ ){
	if ("" != $l["r".$i."L"]) simpleDataRow("relay".$i,$l["r".$i."L"]);
}

/* counters */
if ( ""!=$l["c0L"] ) headline("Event Counter");

if ("" != $l["c0L"]) toFixedDataRow("pulseCount",$l["c0L"],0," gallons");



/* -+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+- */

?>
<html>
<head>
	<link rel="stylesheet" href="http://data.aprsworld.com/world_style.css" type="text/css"/>

	<link rel="icon" type="image/gif" href="http://data.aprsworld.com/favicon.gif">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script language="javascript" type="text/javascript" src="http://ian.aprsworld.com/javascript/timeFunctions.js"></script>
	<script language="javascript" type="text/javascript" src="/data/date.js"></script>
	<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="/data/excanvas.min.js"></script><![endif]-->
	<script language="javascript" type="text/javascript" src="/data/jquery.flot.js"></script>
	<script language="javascript" type="text/javascript" src="/data/jquery.flot.threshold.js"></script>
	<script>
<?
$chartHours = 24;
if ( isset($_COOKIE["chartHours"]) ) {
	$chartHours = $_COOKIE["chartHours"];
}

$yScaleMode="auto";
$yMin=-10;
$yMax=100;


if ( isset( $_COOKIE["yScaleMode"] ) ) $yScaleMode = $_COOKIE["yScaleMode"];

if ( isset( $_COOKIE["yMin"] ) ) $yMin = $_COOKIE["yMin"];

if ( isset( $_COOKIE["yMax"] ) ) $yMax = $_COOKIE["yMax"];


?>
$(document).ready(function(){

	loadData();
	//plotGraph(<? echo $chartHours; ?>);
	
});

function loadData(){
	console.log("yeah");
	var url="http://ian.aprsworld.com/ViewBuilder/jsonNonView.php?station_id=<? echo $station_id; ?>";	
	$.getJSON(url, 
		function(data) {
			<? echo $pScript; ?>
		});
	setTimeout(loadData,10000);
}







	</script>

</head>
<body style="text-align: center;">
<h1>Thermok <? echo $station_id; ?></h1>
<table style="margin-left: auto; margin-right:auto;">
<!--<tr><th>foo</th><th>bar</th></tr>-->
<? echo $pRows; ?>
</table>



</body>
</html>
