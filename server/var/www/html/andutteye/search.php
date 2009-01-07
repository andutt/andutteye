<?php
if(empty($_GET['search'])){
        $search = "";
}else{
        $search = $_GET['search'];
}
require 'db.php';
require_once 'Zend/Auth/Adapter/DbTable.php';

if (isset($_REQUEST['search'])) {
                header("Content-Type: application/json");

                echo "{\"results\": [";
                $arr = array();

		$sql = $db->query("select seqnr,system_name,domain_name from andutteye_systems order by system_name asc");
		while ($row = $sql->fetch()) {
			$seqnr       = $row['seqnr'];
			$system_name = $row['system_name'];
			$domain_name = $row['domain_name'];
			$info="";
                        $arr[] = "{\"id\": \"".$seqnr."\", \"value\": \"".$system_name."\", \"info\": \"".$info."\"}";
		}
                echo implode(", ", $arr);
                echo "]}";
} else {

	header("Content-Type: text/xml");
	echo "<?xml version=\"1.0\" encoding=\"utf-8\" ?><results>";

	$sql = $db->query("select seqnr,system_name from andutteye_systems order by system_name asc");
	while ($row = $sql->fetch()) {
		$seqnr       = $row['seqnr'];
		$system_name = $row['system_name'];
		echo "<rs id=\"".$seqnr."\" info=\"".$system_name."\">".$system_name."</rs>";

	}
	echo "</results>";
}
