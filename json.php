<?

header("Cache-Control: no-cache");
header("Content-Type: text/plain");
require_once $_SERVER['DOCUMENT_ROOT'] . '/world_config.php';


$station_id=cleanStationID($_REQUEST["station_id"]);

$db = _open_mysql('worldDataView');

$sql=sprintf("SELECT * FROM view_%s ORDER BY packet_date DESC LIMIT 1",$station_id);


$query=mysql_query($sql,$db);

$r=mysql_fetch_array($query,MYSQL_ASSOC);

echo json_encode($r);

?>
