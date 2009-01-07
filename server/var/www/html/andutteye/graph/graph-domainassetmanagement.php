<?php
include_once 'php-ofc-library/open_flash_chart_object.php';
open_flash_chart_object('100%', '100%', 'http://'. $_SERVER['SERVER_NAME'] ."/graph/graph-domainassetmanagement-data.php", false );
?>
