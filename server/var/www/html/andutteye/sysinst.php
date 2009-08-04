<?php
ob_start();
require_once 'db.php';
require_once 'func.php';
require_once 'Zend/Auth/Adapter/DbTable.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

if(empty($_GET['system'])){
        $system = "";
}else{
        $system = $_GET['system'];
}
if(empty($_GET['revision'])){
        $revision = "";
}else{
        $revision = $_GET['revision'];
}
if(!isset($system)) {
	print "ERROR No system specified, specify a system. system=thundera";
	exit;
}
function unlink_file($system) {
	$file .= $system;
	$file .= '.auto';
	if (file_exists($file)) {
		unlink("$file");
	}

// End of subfunction
}
function write_to_file($line,$system) {

$file = $system;
$file .= '.auto';
$myFile     = "$file";
$fh	    = fopen($myFile, 'a') or die("ERROR Cant open file $file");
$stringData = "$line\n";
fwrite($fh, $stringData);
fclose($fh);

// End of subfunction
}
if($revision) {
	//Install with a specific revision
	$sql = $db->query("select * from andutteye_provisioning where system_name = '$system' and revision = '$revision'");
	$res = $sql->fetchObject();

	$result = split("\n", $res->autoinstfile);
	foreach($result as $i) {
		print "$i\n";
	}

} else {
	//Install with the latest revision
	$sql = $db->query("select * from andutteye_provisioning where system_name = '$system' order by revision desc limit 0,1");
	$res = $sql->fetchObject();

	//First unlink the auto install file if any are present from before.
	unlink_file($system);

	$result = split("\n", $res->autoinstfile);
	foreach($result as $i) {
		print "$i\n";
		// Append to autoinstallation file
		write_to_file($i,$system);
	}
}
?>
