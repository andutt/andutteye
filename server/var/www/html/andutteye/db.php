<?php
require 'config.php';
require_once 'Zend/Db.php';
require_once 'Zend/Config.php';

$config = new Zend_Config($configArray);
$db = Zend_Db::factory($config->database);

try {
    $db->getConnection();
} catch (Zend_Db_Adapter_Exception $e) {
        print "ERROR Verify your login credentials, or perhaps the database isnt running.\n";
} catch (Zend_Exception $e) {
	print "ERROR Factory failed to load the specified Adapter class, verify that you have specified a supported database type.\n";
}
