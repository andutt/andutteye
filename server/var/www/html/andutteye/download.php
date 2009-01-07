<?php
ob_start();
require_once 'db.php';
require_once 'func.php';
require_once 'Zend/Auth/Adapter/DbTable.php';

if(empty($_GET['seqnr'])){
        $seqnr = "";
}else{
        $seqnr = $_GET['seqnr'];
}
//$sql = $db->query("select content,content_type,content_name from andutteye_uploads where seqnr = '$seqnr'");
//while ($row = $sql->fetch()) {
//      $content_type = $row['content_type'];
//      $content_name = $row['content_name'];
//      $content = $row['content'];
//}

$sql = $db->query("select * from andutteye_uploads where seqnr = '$seqnr'");
$res = $sql->fetchObject();

header("Content-type: $res->content_type");
header("Content-disposition: attachment; filename=$res->content_name");
print "$res->content";

?>
