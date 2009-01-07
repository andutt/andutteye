<?php
if(empty($_GET['domain'])){
        $domain = "";
}else{
        $domain = $_GET['domain'];
}
include_once("php-ofc-library/open-flash-chart.php");
include_once("../db.php");

$data  = array();
$label = array();

$sql    = $db->query("select distinct(group_name) from andutteye_systems where domain_name = '$domain'");
while ($row = $sql->fetch()) {
        $group_name = $row['group_name'];

        $subsql    = $db->query("select seqnr from andutteye_systems where group_name = '$group_name' and domain_name = '$domain'");
        $number   = $subsql->fetchAll();
        $number   = count($number);

        $label[] = $group_name;
        $data[]  = $number;
}

$g = new graph();

$g->bg_colour = 'transparent';
$g->pie(60,'#fccc69','#1d7ffc',false,1);
$g->pie_values( $data, $label );
$g->pie_slice_colours( array('#d01f3c','#79f373','#1ea4fd') );
$g->set_tool_tip( '#val# systems is included in this group.' );
$g->title('Domain system groups', '{font-size:9px; color: #fccc69}' );
echo $g->render();

?>
