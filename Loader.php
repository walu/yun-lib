<?php
function Yun_Loader($class_name) {
    $yun_lib_path = dirname(__FILE__) . '/';
    $path = str_replace('_', '/', $class_name) . '.php';
    $path_arr = array('f0', 'f1');
    foreach ($path_arr as $floor) {
        $tmppath = "{$yun_lib_path}/{$floor}/{$path}";
        
        if (file_exists($tmppath)) {
            require_once($tmppath);
            return;
        }
    }
}
spl_autoload_register("Yun_Loader");
