<?php
if(empty($_GET['system'])){
        $system = "";
}else{
        $system = $_GET['system'];
}

include_once("php-ofc-library/open-flash-chart.php");
include_once("../db.php");

$data_1 = array();
$labels = array();
$max = "100";

$sql    = $db->query("select created_date,created_time,systemstatisticsresult from andutteye_statistics where system_name = '$system' and systemstatisticsname = 'Number of processes 'order by created_date desc, seqnr desc limit 0,80");
while ($row = $sql->fetch()) {
	$created_date = $row['created_date'];
	$created_time = $row['created_time'];
	$systemstatisticsresult = $row['systemstatisticsresult'];

	if($max < $systemstatisticsresult) {
		$max = $systemstatisticsresult;
	}
	$label1[] = "$created_date $created_time";
  	$data_1[] = "$systemstatisticsresult";

	
}
$g = new graph();
$g->title( "Processcount for $system", '{font-size: 10px; color: #fccc69}' );
$g->bg_colour = 'transparent';
$g->set_inner_background( '#DDEFFA', '#CBD7E6', 90 );
$g->x_axis_colour( '#799191', '#FFFFFF' );
$g->y_axis_colour( '#799191', '#FFFFFF' );

// we add 1 sets of data:
$g->set_data( $data_1 );

// we add the 1 line types and key labels
$g->area_hollow( 1, 1, 25, '#1ea4fd', 'Processcount', 10 );

$g->set_tool_tip( '#key#: #val# (#x_label#)<br>Key: #key#<br>X Label: #x_label#<br>Value: #val#<br>X Legend: #x_legend#' );

$g->set_x_labels( $label1 );
$g->set_x_label_style( 0, '#fccc69', 0, 0 );
$g->set_x_legend( 'Transaction days', 9, '#fccc69' );

$g->set_y_label_style( 10, '#fccc69', 0, 2 );
$g->set_y_max($max);
$g->y_label_steps( 5 );
$g->set_y_legend( 'Number of processes', 9, '#fccc69' );
echo $g->render();

?>
