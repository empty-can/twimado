<?php
require_once ("init.php");

function sort_mutter_by_time($a, $b)
{
    if ($a['time'] == $b['time']) {
        return 0;
    }
    return ($a['time'] > $b['time']) ? -1 : 1;
}