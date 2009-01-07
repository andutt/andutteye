<?php
ob_start();
require_once 'db.php';
require_once 'func.php';
require_once 'Zend/Auth/Adapter/DbTable.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

if(empty($_POST['system_name'])){
        $system_name = "";
}else{
        $system_name = $_POST['system_name'];
}
if(empty($_POST['file'])){
        $file = "";
}else{
        $file = $_POST['file'];
}


$date = date("20y-m-d");
$time = date("H:m:s");

$file_end=substr(strrchr("$file", "."), 1);
$fileHandle = fopen("$file", "r");
$fileContent = fread($fileHandle, filesize($file));
$filename = basename($file);
$filesize = filesize($file);

print "System:$system_name file:$file (end:$file_end)\n";
print "Filename:$filename Size:$filesize\n";
print "Content:$fileContent\n";

$data = array(
	'domain_name'		=> "",
	'group_name'		=> "",
	'system_name'		=> "$system_name",
	'content'		=> "$fileContent",
	'content_description'	=> "Transfer item $filename",
	'content_name'		=> "$filename",
	'content_type'		=> "application/$file_end",
	'content_size'		=> "$filesize",
	'upload_type'		=> "upload",
	'created_by'		=> "$authNamespace->andutteye_username",
	'created_date'		=> "$date",
	'created_time'		=> "$time"
);
$db->insert('andutteye_uploads', $data);
unlink("$file");
header("Location:index.php?main=system_overview&param1=$system_name");
?>
