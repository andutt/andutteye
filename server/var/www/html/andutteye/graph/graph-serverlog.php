<?php
include_once 'php-ofc-library/open_flash_chart_object.php';
open_flash_chart_object( '100%', 250, 'http://'. $_SERVER['SERVER_NAME'] ."/graph/graph-serverlog-data.php", false );
?>
