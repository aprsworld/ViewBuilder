<?
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
require $_SERVER['DOCUMENT_ROOT'] . '/world_config.php';

$db=_open_mysql("worldDataView");
	
$sql="select 
count(windDirection) as nData

from 
view_A3351 

where
packet_date>date_add(now(), interval -24 hour) 
";

$query=mysql_query($sql,$db);

$r=mysql_fetch_array($query,MYSQL_ASSOC);

$nDataTotal=$r["nData"];

$sql="select 
count(windDirection) as nData,
windDirection, 
sum(if(windAverageMS0>=0 AND windAverageMS0<2,1,0)) AS below2,
sum(if(windAverageMS0>=2 AND windAverageMS0<4,1,0)) AS below4,
sum(if(windAverageMS0>=4 AND windAverageMS0<6,1,0)) AS below6,
sum(if(windAverageMS0>=6 AND windAverageMS0<8,1,0)) AS below8,
sum(if(windAverageMS0>=8 AND windAverageMS0<10,1,0)) AS below10,
sum(if(windAverageMS0>=10, 1,0)) AS equalAbove10

from 
view_A3351 

where
packet_date>date_add(now(), interval -24 hour) 

Group BY
windDirection
;";

$query=mysql_query($sql,$db);
$count=0;



$windRose=array();



$r=mysql_fetch_array($query,MYSQL_ASSOC);
while ( $r=mysql_fetch_array($query,MYSQL_ASSOC) ) {
	
	$windRose[$r["windDirection"]]=$r;

}
$modifier=45;//22.5;
$start=0;
$nData=0;
$next=0;

$below2=0;
$below4=0;
$below6=0;
$below8=0;
$below10=0;
$equalAbove10=0;

$compassDir[]="N";
$compassDir[]="NE";
$compassDir[]="E";
$compassDir[]="SE";
$compassDir[]="S";
$compassDir[]="SW";
$compassDir[]="W";
$compassDir[]="NW";

$chartAr=array();

for($i=0;$i<=360;$i++){
	if(isset($windRose[$i])){
		
		//print_r($windRose[$i]);
		$nData+=$windRose[$i]['nData'];
	
		$below2+=$windRose[$i]['below2'];
		$below4+=$windRose[$i]['below4'];
		$below6+=$windRose[$i]['below6'];
		$below8+=$windRose[$i]['below8'];
		$below10+=$windRose[$i]['below10'];
		$equalAbove10+=$windRose[$i]['equalAbove10'];		
		
		//echo "<br />";
			
		
	}else{
		//printf("%d does not<br />",$i);
	}
	
	if(floor(($i-22.5)/$modifier)==$next){
		
		printf("<hr /><h3>Between %d and %d there was %d data packets( %s )</h3>",$start, $i,$nData,$compassDir[$next]);
		
		if($nData>0){

			$chartAr[$compassDir[$next]][]=round($below2/$nDataTotal*100,2);
			$chartAr[$compassDir[$next]][]=round($below4/$nDataTotal*100,2);
			$chartAr[$compassDir[$next]][]=round($below6/$nDataTotal*100,2);
			$chartAr[$compassDir[$next]][]=round($below8/$nDataTotal*100,2);
			$chartAr[$compassDir[$next]][]=round($below10/$nDataTotal*100,2);
			$chartAr[$compassDir[$next]][]=round($equalAbove10/$nDataTotal*100,2);

			printf("<h4>%d of those packets where between %d and %d (%1.2f%%)</h4>",$below2, 0,2,(round($below2/$nDataTotal*100,2)));
			printf("<h4>%d of those packets where between %d and %d (%1.2f%%)</h4>",$below4, 2,4,(round($below4/$nDataTotal*100,2)));
			printf("<h4>%d of those packets where between %d and %d (%1.2f%%)</h4>",$below6, 4,6,(round($below6/$nDataTotal*100,2)));
			printf("<h4>%d of those packets where between %d and %d (%1.2f%%)</h4>",$below8, 6,8,(round($below8/$nDataTotal*100,2)));
			printf("<h4>%d of those packets where between %d and %d (%1.2f%%)</h4>",$below10, 8,10,(round($below10/$nDataTotal*100,2)));
			printf("<h4>%d of those packets where above %d (%1.2f%%)</h4>",$equalAbove10, 10,(round($equalAbove10/$nDataTotal*100,2)));
		}
		$next++;
		$start=$i;
		$nData=0;
		$below2=0;
		$below4=0;
		$below6=0;
		$below8=
		$below10=0;
		$equalAbove10=0;
	}
	
}
printf("<hr /><h3>Between %d and %d there was %d data packets</h3>",$start, $i,$nData);
		
if($nData>0){
	$chartAr["N"][0]+=round($below2/$nDataTotal*100,2);
	$chartAr["N"][1]+=round($below4/$nDataTotal*100,2);
	$chartAr["N"][2]+=round($below6/$nDataTotal*100,2);
	$chartAr["N"][3]+=round($below8/$nDataTotal*100,2);
	$chartAr["N"][4]+=round($below10/$nDataTotal*100,2);
	$chartAr["N"][5]+=round($equalAbove10/$nDataTotal*100,2);

	printf("<h4>%d of those packets where between %d and %d (%1.2f%%)</h4>",$below2, 0,2,(round($below2/$nDataTotal*100,2)));
	printf("<h4>%d of those packets where between %d and %d (%1.2f%%)</h4>",$below4, 2,4,(round($below4/$nDataTotal*100,2)));
	printf("<h4>%d of those packets where between %d and %d (%1.2f%%)</h4>",$below6, 4,6,(round($below6/$nDataTotal*100,2)));
	printf("<h4>%d of those packets where between %d and %d (%1.2f%%)</h4>",$below8, 6,8,(round($below8/$nDataTotal*100,2)));
	printf("<h4>%d of those packets where between %d and %d (%1.2f%%)</h4>",$below10, 8,10,(round($below10/$nDataTotal*100,2)));
	printf("<h4>%d of those packets where above %d (%1.2f%%)</h4>",$equalAbove10, 10,(round($equalAbove10/$nDataTotal*100,2)));
}


?>
<hr />
<?

foreach($chartAr as $key => $val){


	printf('<tr nowrap>'); echo "\n";
		printf('<td class="dir">%s</td>',$key); echo "\n";
		printf('<td class="data">%1.2f</td>',$val[0]);echo "\n";
		printf('<td class="data">%1.2f</td>',$val[1]);echo "\n";
		printf('<td class="data">%1.2f</td>',$val[2]);echo "\n";
		printf('<td class="data">%1.2f</td>',$val[3]);echo "\n";
		printf('<td class="data">%1.2f</td>',$val[4]);echo "\n";
		printf('<td class="data">%1.2f</td>',$val[5]);echo "\n";
		printf('<td class="data">%1.2f</td>',($val[0]+$val[1]+$val[2]+$val[3]+$val[4]+$val[5]));echo "\n";
	printf('</tr>');	echo "\n";

	
}

?>
<hr />
<html>
<head>
	<link rel="stylesheet" href="http://data.aprsworld.com/world_style.css" type="text/css"/>

	<link rel="icon" type="image/gif" href="http://data.aprsworld.com/favicon.gif">
	<script type="text/javascript" src="http://code.jquery.com/jquery-1.8.3.min.js"></script>
	<script type="text/javascript" src="http://magnum-dev.aprsworld.com//mw/excanvas.min.js"></script>
	<script type="text/javascript" src="http://magnum-dev.aprsworld.com//mw/jquery.flot.js"></script>
	<script type="text/javascript" src="http://magnum-dev.aprsworld.com//mw/jquery.flot.threshold.js"></script>
	<script type="text/javascript" src="jquery.flot.direction.js"></script>
	<script type="text/javascript" src="http://mybergey.aprsworld.com/data/date.js"></script>

	<script>

$(document).ready(function(){

	var flotarray=[<? echo $farray; ?>];

	var dirarray=[<? echo $darray; ?>];
	
	$.plot("#flot", [{

		data: dirarray,
		lines: {
			fill: 1,
			lineWidth: 0
		},
		color: '#f7a11a',
	
		direction: {
			show: true,
			lineWidth: 1,
			color: "rgb(50, 60, 60)",
			fillColor: "rgb(200, 60, 60)",
			arrawLength: 8,
			angleType: "degree", //degree or radian
			openAngle: 40,
			zeroShow: false,
			threshold: 0.000001,
			angleStart: 0
		}

	},{
		data: flotarray,
		lines: {
			fill: 1,
			lineWidth: 0
		},
		color: '#f7a11a'
		
	}], {
		
	});

	



});


	</script>

</head>
<body>
<h1>test graph-style, yo</h1>

<div id="flot" style="width: 500px;height: 300px;font-size: 14px;line-height: 1em;"></div>
<div id="place_holder" style="width: 500px;height: 300px;font-size: 14px;line-height: 1em;"></div>

</body>
</html>
