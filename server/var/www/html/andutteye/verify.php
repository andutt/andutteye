<?php
require 'db.php';
require_once 'Zend/Auth/Adapter/DbTable.php';
require_once 'Zend/Session/Namespace.php';
$date = date("20y-m-d");

if(empty($_POST['andutteye_username'])){
        $andutteye_username = "";
}else{
        $andutteye_username = $_POST['andutteye_username'];
}
if(empty($_POST['andutteye_password'])){
        $andutteye_password = "";
}else{
        $andutteye_password = $_POST['andutteye_password'];
	$andutteye_password = sha1($password_slt . $andutteye_password);
}
if($andutteye_username == "") { 
	header("Location:login.php?status=NO_USERNAME_SPECIFIED.");
        exit;
}
if($andutteye_password == "") { 
	header("Location:login.php?status=NO_PASSWORD_SPECIFIED.");
        exit;
}

$authAdapter = new Zend_Auth_Adapter_DbTable($db, 'andutteye_users', 'andutteye_username', 'andutteye_password');

$authAdapter->setIdentity("$andutteye_username")
            ->setCredential("$andutteye_password");

$result = $authAdapter->authenticate();

switch ($result->getCode()) {

    case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
	print "ERROR FAILURE_IDENTITY_NOT_FOUND\n";
	sleep(2);
	header("Location:login.php?status=FAILURE_IDENTITY_NOT_FOUND");
        break;

    case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
	print "ERROR FAILURE_CREDENTIAL_INVALID\n";
	sleep(2);
	header("Location:login.php?status=FAILURE_CREDENTIAL_INVALID");
        break;

    case Zend_Auth_Result::SUCCESS:
	print "SUCCESS, setting parameters to session.\n";
	Zend_Session::start();
	$authNamespace = new Zend_Session_Namespace('Zend_Auth');
	$authNamespace->andutteye_username = "$andutteye_username";
	$authNamespace->andutteye_password = "$andutteye_password";

	$sql = $db->query("select * from andutteye_users where andutteye_username = '$authNamespace->andutteye_username'");
	$res = $sql->fetchObject();

	$authNamespace->andutteye_admin = "$res->is_admin";
	$authNamespace->andutteye_role = "$res->andutteye_role";

	$logincount=($res->nr_of_loggins + 1);
	$sql = "update andutteye_users set nr_of_loggins = '$logincount', last_loggedin = '$date' where andutteye_username = '$authNamespace->andutteye_username'";
        $db->query($sql);

	if(!"$res->andutteye_theme") {
		$authNamespace->andutteye_theme = "Phoenix";
	} else {
		$authNamespace->andutteye_theme = "$res->andutteye_theme";
	}
	header("Location:index.php");
        break;

    default:
	print "ERROR Other failure accured.\n";
	sleep(2);
	header("Location:login.php?status=OTHER_LOGIN_FAILURE_ACCURED");
        break;
}
