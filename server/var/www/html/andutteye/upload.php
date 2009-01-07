<?php
ob_start();
require_once 'db.php';
require_once 'func.php';
require_once 'Zend/Auth/Adapter/DbTable.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

if(empty($_POST['param1'])){
        $param1 = "";
}else{
        $param1 = $_POST['param1'];
}
if(empty($_POST['param2'])){
        $param2 = "";
}else{
        $param2 = $_POST['param2'];
}
if(empty($_POST['param3'])){
        $param3 = "";
}else{
        $param3 = $_POST['param3'];
}
if(empty($_POST['param4'])){
        $param4 = "";
}else{
        $param4 = $_POST['param4'];
}

$date = date("20y-m-d");
$time = date("H:m:s");
$filename=$_FILES["file"]["name"];
$filetype=$_FILES["file"]["type"];
$filesize=$_FILES["file"]["size"];
$filetemp=$_FILES["file"]["tmp_name"];

$fileHandle = fopen($filetemp, "r");
$fileContent = fread($fileHandle, fileSize($filetemp));


if($filesize == 0) {
	print "ERROR Something went wrong, recived 0b in file\n";
	sleep(2);
	header("Location:/?main=upload_documentation");
	exit;
}

$data = array(
	'domain_name'		=> "$param2",
	'group_name'		=> "$param3",
	'system_name'		=> "$param4",
	'content'		=> "$fileContent",
	'content_description'	=> "$param1",
	'content_name'		=> "$filename",
	'content_type'		=> "$filetype",
	'content_size'		=> "$filesize",
	'upload_type'		=> "upload",
	'created_by'		=> "$authNamespace->andutteye_username",
	'created_date'		=> "$date",
	'created_time'		=> "$time"
);
$db->insert('andutteye_uploads', $data);
header("Location:index.php?main=upload_documentation");
?>
