<?php
function Yun_Loader($class_name) {
    $yun_lib_path = dirname(__FILE__);
    $lastpos = strrpos($class_name, "_");
    $path = '';
    if (false !== $lastpos) {
	$path = substr($class_name, 0, -(strrpos($class_name, '_')-1));
    	$path = '/' . str_replace('_', '/', $path);
    }

    $path_arr = array('f0', 'f1');
    foreach ($path_arr as $floor) {
        $tmppath = "{$yun_lib_path}/{$floor}{$path}/{$class_name}.php";
        if (file_exists($tmppath)) {
            require_once($tmppath);
            return;
        }
    }
}
Yun_Loader("Array");
spl_autoload_register("Yun_Loader");
