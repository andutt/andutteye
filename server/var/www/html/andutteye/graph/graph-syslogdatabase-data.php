<?php

include_once("php-ofc-library/open-flash-chart.php");
include_once("../db.php");

$data  = array();
$label = array();

$sql    = $db->query("select distinct(monitortype) from andutteye_changeevent");
while ($row = $sql->fetch()) {
        $monitortype = $row['monitortype'];

        $subsql    = $db->query("select seqnr from andutteye_changeevent where monitortype = '$monitortype'");
        $number   = $subsql->fetchAll();
        $number   = count($number);

        $label[] = $monitortype;
        $data[]  = $number;
}

$g = new graph();

$g->bg_colour = '#191919';
$g->pie(60,'#fccc69','#1d7ffc',false,1);
$g->pie_values( $data, $label );
$g->pie_slice_colours( array('#d01f3c','#79f373','#1ea4fd','fccc69') );
$g->set_tool_tip( '#val# changeevents with this type.' );
$g->title( 'Syslog database', '{font-size:9px; color: #fccc69}' );
echo $g->render();

?>
