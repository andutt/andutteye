<?php
if(empty($_GET['graph_domain'])){
        $graph_domain = "";
}else{
        $graph_domain = $_GET['graph_domain'];
}


include_once("php-ofc-library/open-flash-chart.php");
include_once("../db.php");

$data  = array();
$label = array();

$sql    = $db->query("select distinct(system_type) from andutteye_systems where domain_name like '%$graph_domain%'");
while ($row = $sql->fetch()) {
        $system_type = $row['system_type'];

        $subsql    = $db->query("select seqnr from andutteye_systems where system_type = '$system_type'");
        $number   = $subsql->fetchAll();
        $number   = count($number);

        $label[] = $system_type;
        $data[]  = $number;
}

$g = new graph();


$g->bg_colour = 'transparent';
$g->pie(55, '#fccc69','#1d7ffc',false,1);
$g->pie_values( $data, $label );
$g->pie_slice_colours( array('#d01f3c','#79f373','#1ea4fd','fccc69') );
$g->set_tool_tip( '#val# with this system type.' );
$g->title( 'System types', '{font-size:9px; color: #fccc69}' );
echo $g->render();

?>
