<?php

include_once("php-ofc-library/open-flash-chart.php");
include_once("../db.php");

$data_1 = array();
$labels = array();
$max = "0";

$sql    = $db->query("select seqnr,created_date,assetmanagementname from andutteye_assetmanagement where assetmanagementname like '%Number of%'");
while ($row = $sql->fetch()) {
	$seqnr = $row['seqnr'];
	$created_date = $row['created_date'];
	$assetmanagementname = $row['assetmanagementname'];

	$subsql = $db->query("select assetmanagementresult from andutteye_assetmanagement where seqnr = '$seqnr'");
	$res = $subsql->fetchObject();

	$label1[] = "$assetmanagementname";
  	$data_1[] = "$res->assetmanagementresult";

	if($max < $res->assetmanagementresult) {
		$max="$res->assetmanagementresult";
	}
	
}
$g = new graph();
$g->title( 'Domain assetmanagement data', '{font-size: 10px; color: #799191}' );
$g->bg_colour = '';
$g->set_inner_background( '#DDEFFA', '#CBD7E6', 90 );
$g->x_axis_colour( '#799191', '#FFFFFF' );
$g->y_axis_colour( '#799191', '#FFFFFF' );

// we add 1 sets of data:
$g->set_data( $data_1 );

// we add the 1 line types and key labels
$g->line_dot( 3, 4, '#799191', 'Transactions', 10 );

$g->set_x_labels( $label1 );
$g->set_x_label_style( 10, '#799191', 0, 2 );
$g->set_x_legend( 'Transaction days', 9, '#799191' );

$g->set_y_label_style( 10, '#799191', 0, 2 );
$g->set_y_max($max);
$g->y_label_steps( 5 );
$g->set_y_legend( 'Transactions', 9, '#799191' );
echo $g->render();

?>
