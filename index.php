<?php

// Errors and warnings on 
ini_set('error_reporting', E_ALL | E_NOTICE);
ini_set('display_errors', 'on');
ini_set('short_open_tag', 'on');

// Time zone
date_default_timezone_set('Europe/Bratislava');

// Bootstrap
require_once ( dirname(__FILE__) . "/Application/bootstrap.php" );
