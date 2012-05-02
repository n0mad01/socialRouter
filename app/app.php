<?php
/**
 *	app/classes/app.php
 *	a collection of "global" functions & variables
 */


/**
 *  A simple message/error dump
 */
function dumper($error)
{
    $d = debug_backtrace();
    echo '<pre>';
        echo '<br />';
        echo $d[0]['file'] . ' on line ' . $d[0]['line'];
        echo '<br />';
        echo print_r($error, TRUE);
        //echo '<hr>';
    echo '</pre>';
}

