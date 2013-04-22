<?php
class Yun_Log_File implements Yun_Log_Handler_Interface {

    const CONF_KEY = 'yun_log_file_path';

    private $handler_id;

    private $log_file_path;
    
    public function __construct($handler_id, $log_file_path = null) {
        $this->handler_id = $handler_id;
        if (null === $log_file_path) {
            $conf = Yun_Conf::getInstance();
            $log_file_path = $conf->get(self::CONF_KEY);
        }
        $this->log_file_path = $log_file_path;
    }

    public function getHandlerId() {
        return $this->handler_id;
    }

    public function log() {
    
    }

    public function error_code() {
    
    }

    public function error_info() {
    
    }
}
