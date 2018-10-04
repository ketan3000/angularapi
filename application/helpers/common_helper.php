<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');


function pr($array, $flag = 0) {
    echo "<pre>";
    print_r($array);
    echo "</pre>";
    if ($flag) {
        die();
    }
}

?>
    