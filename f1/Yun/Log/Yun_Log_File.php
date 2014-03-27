<?php
class Yun_Log_File implements Yun_Log_Handler_Interface {

    const CONF_KEY = 'yun_log_file_path';

    private $handler_id;

    private $log_file_path;

    private $fp = null;

    private $error_code;
    private $error_info;
    
    public function __construct($handler_id, $log_file_path = null) {
        $this->handler_id = $handler_id;
        $this->log_file_path = $log_file_path;
    }

    public function getHandlerId() {
        return $this->handler_id;
    }

    public function log($message) {
        if (null === $this->fp) {
            $this->fp = fopen($this->log_file_path, 'a');
            if (!$this->fp) {
                $this->error_code = -1;
                $this->error_info = "cannot open log file {$log_file_path}";
                Yun_Log::getInstance()->warning($this->error_info);
                return false;
            }
        }
        fwrite($this->fp, $message);
        return true;
    }

    public function errorCode() {
        return $this->error_code;
    }

    public function errorInfo() {
        return $this->error_info; 
    }
}
