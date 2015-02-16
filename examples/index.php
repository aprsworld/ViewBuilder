<?
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
//require $_SERVER['DOCUMENT_ROOT'] . '/world_config.php';

$station_id=$_REQUEST["station_id"];

$pScript="";
$pRow="";

require "pieces.php";

/* place table row instructions here 
   if you use one argument, then the title will be the same as the column name
   if you use two arguments, then the title will be the second argument */
simpleDataRow("packet_date","Date");
simpleDataRow("measurementNumber","Measurement Number");


anemometerSparkDataRow(0);
anemometerSparkDataRow(1);

sparkLineDataRow("temperatureIcC", "Ambient Temperature:","&deg;C");
sparkLineDataRow("temperatureC", "Temperature:","&deg;C");
sparkLineDataRow("relativeHumidity", "Relative Humidity:","%");
sparkLineDataRow("vInput", "Power Source Voltage:","VDC");
sparkLineDataRow("vBatt12", "12V Battery Bank:","VDC");
sparkLineDataRow("vBatt24", "24V Battery Bank:","VDC");
sparkLineDataRow("vBatt48", "48V Battery Bank:","VDC");
sparkLineDataRow("xrw2gUptimeMinutes", "XRW2G Uptime:","minutes");





/* -+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+- */

?>
<html>
<head>
	<link rel="stylesheet" href="http://data.aprsworld.com/world_style.css" type="text/css"/>

	<link rel="icon" type="image/gif" href="http://data.aprsworld.com/favicon.gif">
	<script type="text/javascript" src="http://code.jquery.com/jquery-1.8.3.min.js"></script>
	<script>

$(document).ready(function(){
	loadData();
	


});

function loadData(){
	console.log("yeah");
	var url="json.php?station_id=<? echo $station_id; ?>";	
	$.getJSON(url, 
		function(data) {
			<? echo $pScript; ?>
		});
	setTimeout(loadData,10000);
}
	</script>

</head>
<body>
<h1>View Table <? echo $station_id; ?></h1>
<table>
<tr><th>foo</th><th>bar</th></tr>
<? echo $pRows; ?>
</table>

</body>
</html>
