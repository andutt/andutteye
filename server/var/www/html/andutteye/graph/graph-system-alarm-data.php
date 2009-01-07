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
$max = "0";

$sql    = $db->query("select distinct(created_date) from andutteye_alarm where system_name = '$system' order by seqnr asc limit 0,30");
while ($row = $sql->fetch()) {
	$created_date = $row['created_date'];

	$subsql    = $db->query("select seqnr from andutteye_alarm where created_date = '$created_date'");
	$transes   = $subsql->fetchAll();
	$transes   = count($transes);

	$label1[] = $created_date;
  	$data_1[] = $transes;

	if($max < $transes) {
		$max="$transes";
	}
	
}
$g = new graph();
$g->title( "Alarm trend for $system", '{font-size: 10px; color: #fccc69}' );
$g->bg_colour = 'transparent';
$g->set_inner_background( '#DDEFFA', '#CBD7E6', 90 );
$g->x_axis_colour( '#799191', '#FFFFFF' );
$g->y_axis_colour( '#799191', '#FFFFFF' );

// we add 1 sets of data:
$g->set_data( $data_1 );

// we add the 1 line types and key labels
$g->area_hollow( 1, 1, 25, '#1ea4fd', 'Alarms', 10);

$g->set_tool_tip( '#key#: #val# (#x_label#)<br>Key: #key#<br>X Label: #x_label#<br>Value: #val#<br>X Legend: #x_legend#' );

$g->set_x_labels( $label1 );
$g->set_x_label_style( 0, '#fccc69', 0, 0 );
$g->set_x_legend( 'Transaction days', 9, '#fccc69' );

$g->set_y_label_style( 10, '#fccc69', 0, 2 );
$g->set_y_max($max);
$g->y_label_steps( 5 );
$g->set_y_legend( 'Number of alarms', 9, '#fccc69' );
echo $g->render();

?>
