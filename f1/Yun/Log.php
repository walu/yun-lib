<?php
class Yun_Log {

    const DEFAULT_HANDLER_ID = 'default';

    const CONF_KEY = 'yun_log_file_path';
    
    private $handler = array();    

    public function registerHandler(Yun_Log_Handler_Interface $handler) {
        $handler_id = $handler->getHandlerId();
        if (self::DEFAULT_HANDLER_ID == $handler_id) {
            die();//todo:log error info.
        }
        $this->handler[$handler_id] = $handler;
    }

    public function __construct() {
        $conf = Yun_Conf::getInstance();
        $log_file_path = $conf->get(self::CONF_KEY);
        if (NULL === $log_file_path) {
            $log_file_path = "php://stderr";
        }

        $this->handler[self::DEFAULT_HANDLER_ID] = new Yun_Log_File(self::DEFAULT_HANDLER_ID, $log_file_path);
    }

    public function error($msg, $handler_id=self::DEFAULT_HANDLER_ID) {
    }

    public function warning($msg, $handler_id=self::DEFAULT_HANDLER_ID) {
    }


    public function notice($msg, $handler_id=self::DEFAULT_HANDLER_ID) {
    } 

    public function debug($msg, $handler_id=self::DEFAULT_HANDLER_ID) {
    } 
}
