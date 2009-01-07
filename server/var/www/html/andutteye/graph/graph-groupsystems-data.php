<?php
if(empty($_GET['group'])){
        $group = "";
}else{
        $group = $_GET['group'];
}
include_once("php-ofc-library/open-flash-chart.php");
include_once("../db.php");

$data  = array();
$label = array();

$sql    = $db->query("select distinct(system_type) from andutteye_systems where group_name = '$group'");
while ($row = $sql->fetch()) {
        $system_type = $row['system_type'];

        $subsql    = $db->query("select seqnr from andutteye_systems where system_type = '$system_type' and group_name = '$group'");
        $number   = $subsql->fetchAll();
        $number   = count($number);

        $label[] = $system_type;
        $data[]  = $number;
}

$g = new graph();
$g->bg_colour = 'transparent';
$g->pie(60,'#fccc69','#1d7ffc',false,1);
$g->pie_values( $data, $label );
$g->pie_slice_colours( array('#d01f3c','#79f373','#1ea4fd') );
$g->set_tool_tip( '#val# systems with this systemtype.' );
$g->title('Group system types', '{font-size:9px; color: #fccc69}' );
echo $g->render();

?>
