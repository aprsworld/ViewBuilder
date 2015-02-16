<?
/*
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
//*/

require $_SERVER['DOCUMENT_ROOT'] . '/world_config.php';

$station_id=$_REQUEST["station_id"];

$pScript="";
$pRow="";


require $_SERVER['DOCUMENT_ROOT'] ."/ViewBuilder/pieces.php";

/* 

   Place pieces here

*/

headline("Current Values");
simpleDataRow("packet_date","Report Date:");

/* -+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+- */

?>
<html>
<head>
	<link rel="stylesheet" href="http://data.aprsworld.com/world_style.css" type="text/css"/>

	<link rel="icon" type="image/gif" href="http://data.aprsworld.com/favicon.gif">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script language="javascript" type="text/javascript" src="http://ian.aprsworld.com/javascript/timeFunctions.js"></script>
	<script language="javascript" type="text/javascript" src="/data/date.js"></script>
	<script>

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
<h1><? echo $station_id; ?></h1>
<table style="margin-left: auto; margin-right:auto;">
<!--<tr><th>foo</th><th>bar</th></tr>-->
<? echo $pRows; ?>
</table>



</body>
</html>
