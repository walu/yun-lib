<?php
class Yun_Log_Handler {
    
    public $log_file_path = 'php://stderr';
    
    public function factoryByPath($file_path) {
        $handler = new Yun_Log_Handler();
        $handler->log_file_path = $file_path;
        return $handler;
    }
    

    public function log($str) {
        file_put_contents($this->log_file_path, $str, FILE_APPEND);
    }   
}