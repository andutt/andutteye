<?php
if(empty($_GET['system'])){
        $system = "";
}else{
        $system = $_GET['system'];
}
if(empty($_GET['statistics'])){
        $statistics = "";
}else{
        $statistics = $_GET['statistics'];
}

include_once("php-ofc-library/open-flash-chart.php");
include_once("../db.php");

$data_1 = array();
$data_2 = array();
$data_3 = array();
$data_4 = array();
$data_5 = array();
$labels = array();

$max="10";
$count="1";

$sql    = $db->query("select * from andutteye_statistics where system_name = '$system' and systemstatisticsname = '$statistics' order by created_date desc limit 0,500");
while ($row = $sql->fetch()) {
	$created_date = $row['created_date'];
	$created_time = $row['created_time'];
	$systemstatisticsresult = $row['systemstatisticsresult'];

	$splitted=split(" ", $systemstatisticsresult);
	$elements=count($splitted);

	if(!$splitted[0]) {
		$splitted[0] = 0;
	}else {
		$splitted[0]=rtrim($splitted[0]);
	}
	if(!$splitted[1]) {
		$splitted[1] = 0;
	} else {
		$splitted[1]=rtrim($splitted[1]);
	}
	if(!$splitted[2]) {
		$splitted[2] = 0;
	} else {
		$splitted[2]=rtrim($splitted[2]);
	}
	if(!$splitted[3]) {
		$splitted[3] = 0;
	} else {
		$splitted[3]=rtrim($splitted[3]);
	}
	if(!$splitted[4]) {
		$splitted[4] = 0;
	} else {
		$splitted[4]=rtrim($splitted[4]);
	}
	if(!$splitted[5]) {
		$splitted[5] = 0;
	} else {
		$splitted[5]=rtrim($splitted[5]);
	}

	$label1[] = "$created_date $created_time";
	$dates[] = $created_date;
  	$data_1[] = "$splitted[0]";

	if($splitted[0] > $max) {
		$max=($splitted[0] + 1);
	}
	if($elements == 2) {
  		$data_2[] = "$splitted[1]";
	
		if($splitted[0] || $splitted[1] > $max) {
			$max=($splitted[2] + 1);
		}
	}
	if($elements == 3) {
  		$data_2[] = "$splitted[1]";
  		$data_3[] = "$splitted[2]";
		
		if($splitted[0] || $splitted[1] || $splitted[2] > $max) {
		
			for ($i = 1; $i <= 5; $i++) {
				if($splitted[$i] > $max) {
					$max=($splitted[$i] + 1);
				}
			}
		}
	}
	if($elements == 4) {
  		$data_2[] = "$splitted[1]";
  		$data_3[] = "$splitted[2]";
  		$data_4[] = "$splitted[3]";

		if($splitted[0] || $splitted[1] || $splitted[2] || $splitted[3] > $max) {
			for ($i = 1; $i <= 5; $i++) {
				if($splitted[$i] > $max) {
					$max=($splitted[$i] + 1);
				}
			}
		}
	}
	if($elements == 5) {
  		$data_2[] = "$splitted[1]";
  		$data_3[] = "$splitted[2]";
  		$data_4[] = "$splitted[3]";
  		$data_5[] = "$splitted[4]";

		if($splitted[0] || $splitted[1] || $splitted[2] || $splitted[3] || $splitted[4] > $max) {
			for ($i = 1; $i <= 5; $i++) {
				if($splitted[$i] > $max) {
					$max=($splitted[$i] + 1);
				}
			}
		}
	}
	
$count++;
}
$g = new graph();
$g->title( "$statistics for $system", '{font-size: 10px; color: #fccc69}' );
$g->bg_colour = '#191919';
$g->set_inner_background( '#DDEFFA', '#CBD7E6', 90 );
$g->x_axis_colour( '#799191', '#FFFFFF' );
$g->y_axis_colour( '#799191', '#FFFFFF' );

// we add 1 sets of data:
$g->set_data( $data_1 );
$g->set_data( $data_2 );
$g->set_data( $data_3 );
$g->set_data( $data_4 );
$g->set_data( $data_5 );

$g->area_hollow( 1, 1, 25, '#1ea4fd', 'Axis1', 10);
$g->area_hollow( 1, 1, 25, '#799191', 'Axis2', 10);
$g->area_hollow( 1, 1, 25, '#fccc69', 'Axis3', 10);
$g->area_hollow( 1, 1, 25, '#FFFFFF', 'Axis4', 10);
$g->area_hollow( 1, 1, 25, '#000000', 'Axis5', 10);

$g->set_tool_tip( '#key#: #val# (#x_label#)<br>Key: #key#<br>X Label: #x_label#<br>Value: #val#.00<br>X Legend: #x_legend#' );

$g->set_x_labels( $label1 );
$g->set_x_label_style( 0, '#fccc69', 0, 0 );
$g->set_x_legend( 'Statistics input days', 9, '#fccc69' );

$g->set_y_label_style( 10, '#fccc69', 0, 2 );
$g->set_y_max($max);
$g->y_label_steps( 1 );
$g->set_y_legend( 'Y axis count', 9, '#fccc69' );
echo $g->render();

?>
