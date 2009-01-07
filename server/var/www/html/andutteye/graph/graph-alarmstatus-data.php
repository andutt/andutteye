<?php

include_once("php-ofc-library/open-flash-chart.php");
include_once("../db.php");

$data  = array();
$label = array();

$sql    = $db->query("select distinct(status) from andutteye_alarm");
while ($row = $sql->fetch()) {
        $status = $row['status'];

        $subsql    = $db->query("select seqnr from andutteye_alarm where status = '$status'");
        $number   = $subsql->fetchAll();
        $number   = count($number);

        $label[] = $status;
        $data[]  = $number;
}

$g = new graph();

$g->bg_colour = 'transparent';
$g->pie(60,'#fccc69','#1d7ffc',false,1);
$g->pie_values( $data, $label );
$g->pie_slice_colours( array('#d01f3c','#79f373','#1ea4fd') );
$g->set_tool_tip( '#val# alarms with this alarmstatus.' );
$g->title( 'Monitoring status', '{font-size:9px; color: #fccc69}' );
echo $g->render();

?>
