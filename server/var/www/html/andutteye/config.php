<?php
//
// Andutteye configuration settings.
//
$database_tpe="Pdo_Mysql";
$database_hst="localhost";
$database_dbn="andutteye";
$database_usr="andutteye";
$database_pwd="andutteye";
$Transfer_dir_location="transfer";
$password_slt="0acf719778ed39b6847cc818ec3f72252c12ad6d";

$pdoParams = array(
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
);
$configArray = array(
    'database' => array(
        'adapter' => "$database_tpe",
        'params'  => array(
            'host'     => "$database_hst",
            'username' => "$database_usr",
            'password' => "$database_pwd",
            'dbname'   => "$database_dbn",
            'driver_options' => $pdoParams
        )
    )
);

?>
