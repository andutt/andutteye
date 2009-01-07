<?php
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

unset($authNamespace->andutteye_username);
unset($authNamespace->andutteye_password);
Zend_Session::destroy();
header("Location:login.php");
