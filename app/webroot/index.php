<?php

if (!defined('CSS_DIR')) {
    define('CSS_DIR', 'http://' . $_SERVER['SERVER_NAME'] . '/app/webroot/css/');
}
if (!defined('JS_DIR')) {
    define('JS_DIR', 'http://' . $_SERVER['SERVER_NAME'] . '/app/webroot/js/');
}
//if (!defined('FONTS_DIR')) {
    //define('FONTS_DIR', 'http://' . $_SERVER['SERVER_NAME'] . '/app/webroot/fonts/');
//}

require('app/classes/dispatcher.php');

$Dispatcher = new Dispatcher();
$Dispatcher->dispatch();

if($GLOBALS['renderPiwik']) :
?>
    <!-- Piwik -->
    <!-- End Piwik Tag -->
<?php
endif;
