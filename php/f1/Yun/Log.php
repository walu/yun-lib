<?php
class Yun_Log {
    
    const LEVEL_DEBUG = 1;
    const LEVEL_ERROR = 2;
    const LEVEL_STAT  = 3;
    
    private $conf = array(
        self::LEVEL_DEBUG => 'DEBUG',
        self::LEVEL_ERROR => 'ERROR',
        self::LEVEL_STAT  => 'STAT',
    );
    
    private $log_level = 2;
    
    private $handler = array();
    
    private $log_id;
    
    /**
     * @var Yun_Log
     */
    private static $instance = null;
    
    /**
     * 获取Yun_Log单例
     * 
     * @return Yun_Log
     */
    public static function getInstance() {
        if (null === self::$instance) {
            self::$instance = new Yun_Log();
            
            $default_handler = new Yun_Log_Handler();
            self::$instance->regHandler($default_handler);
        }
        return self::$instance;
    }
    
    public function regHandler(Yun_Log_Handler $handler, $level=false) {
        $hash = spl_object_hash($handler);
        $level_keys = array_keys($this->conf);
        if (false !== $level) {
            $level_keys = array(intval($level));
        }
        
        foreach ($level_keys as $level) {
            if (!isset($this->handler[$level][$hash])) {
                $this->handler[$level][$hash] = $handler;
            }
        }
    }
    
    public function unsetHandler() {
        $this->handler = array();
    }
    
    public function setLogLevel($level) {
        $this->log_level = $level;
    }
    
    public function debug($log) {
        $this->log($log, self::LEVEL_DEBUG);
    }
    
    public function error($log) {
        $this->log($log, self::LEVEL_ERROR);
    }
    
    public function stat($log) {
        $this->log($log, self::LEVEL_STAT);
    }
    
    public function log($log_str, $level) {
        if (!isset($this->conf[$level]) || !isset($this->handler[$level])) {
            return;
        }
        
        $level_str = $this->conf[$level];
        $time      = date("Ymd H:i:s");
        $timezone  = date_default_timezone_get();
        $trace     = debug_backtrace();
        $file      = $trace[1]['file'];
        $line      = $trace[1]['line'];
        $log_str = "[{$time} {$timezone}] YUN-LOG[{$level_str} {$this->log_id}] file[{$file}] line[$line] {$log_str} \n";
        
        foreach ($this->handler[$level] as $h) {
            $h->log($log_str);
        }
    }
    
    private function __construct() {
        $this->log_id = uniqid(true);
    }
}