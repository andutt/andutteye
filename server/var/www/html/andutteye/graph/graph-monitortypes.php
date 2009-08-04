<?php
if(empty($_GET['system'])){
        $system = "";
}else{
        $system = $_GET['system'];
}
include_once("php-ofc-library/open-flash-chart.php");
include_once("../db.php");

$data  = array();
$label = array();

$sql    = $db->query("select distinct(monitortype) from andutteye_monitor_configuration where system_name = '$system'");
while ($row = $sql->fetch()) {
        $monitortype = $row['monitortype'];

        $subsql    = $db->query("select seqnr from andutteye_monitor_configuration where monitortype = '$monitortype' and system_name = '$system'");
        $number   = $subsql->fetchAll();
        $number   = count($number);

        $label[] = $monitortype;
        $data[]  = $number;
}

$g = new graph();

$g->bg_colour = '#191919';
$g->pie(60,'#fccc69','#1d7ffc',false,1);
$g->pie_values( $data, $label );
$g->pie_slice_colours( array('#d01f3c','#79f373','#1ea4fd') );
$g->set_tool_tip( '#val# monitors with this monitortype.' );
$g->title('Monitortype spread for '.$system.'', '{font-size:9px; color: #fccc69}' );
echo $g->render();

?>
