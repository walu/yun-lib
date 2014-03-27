<?php
/**
 * 日志类
 *
 * 默认记录日志到本地文件，默认路径名通过Yun_Conf配置。
 *
 * 1. 增加新的日志记录方式
 * class Udp_Handler implements Yun_Log_Handler_Interface {
 *  ...
 *  public function getHandlerId() {return 'diy_handler';}
 *  ...
 * }
 *
 * $h = new Udp_Handler();
 * Yun_Log::getInstance()->registerhandler($h);
 *
 * Yun_Log::getInstance()->error($message, 'diy_handler');
 *
 * @author walu
 */
class Yun_Log {

    /**
     * 默认handler_id
     *
     * 新增的handler不能使用此名称
     */
    const DEFAULT_HANDLER_ID = 'default';

    /**
     * 默认的Yun_Log_File使用的日志文件
     */
    const CONF_KEY = 'yun_log_file_path';

    
    private static $instance = null;

    private $handler = array();

    private $debug_flag = false;


    public static $request_id = null;

    /**
     * 获取Yun_Log实例
     *
     * @return Yun_Log
     */
    public static function getInstance() {
        if (null === self::$instance) {
            self::$request_id = php_uname('n').'-'.getmypid().'-'.microtime(true);
            self::$instance = new Yun_Log();
        }
        return self::$instance;
    }

    /**
     * 注册新的日志处理器
     *
     * @param Yun_Log_Handler_Interface $handler
     */
    public function registerHandler(Yun_Log_Handler_Interface $handler) {
        $handler_id = $handler->getHandlerId();
        if (self::DEFAULT_HANDLER_ID == $handler_id) {
            die();//todo:log error info.
        }
        $this->handler[$handler_id] = $handler;
    }

    /**
     * 是否开启Debug日志
     *
     * @param bool $flag true:开启 false:关闭
     */
    public function switchDebug($flag) {
        $this->debug_flag = (bool)$flag;
    }

    private function __construct() {
        $conf = Yun_Conf::getInstance();
        $log_file_path = $conf->get(self::CONF_KEY);
        if (NULL === $log_file_path) {
            $log_file_path = "php://stderr";
        }

        $this->handler[self::DEFAULT_HANDLER_ID] = new Yun_Log_File(self::DEFAULT_HANDLER_ID, $log_file_path);
    }

    /**
     * 记录ERROR级别的日志
     *
     * 建议：此错误发生，程序应该终止运行。
     *
     * @param string|array  $msg 日志内容
     * @param string        $handler_id 使用哪一个日志处理器，默认使用default
     * @return bool
     */
    public function error($msg, $handler_id=self::DEFAULT_HANDLER_ID) {
        return $this->log('ERROR', $msg, $handler_id);
    }

    /**
     * 记录WARNING级别的日志
     *
     * 建议：此错误发生，程序继续运行
     *
     * @param string|array  $msg 日志内容
     * @param string        $handler_id 使用哪一个日志处理器，默认使用default
     * @return bool
     */
    public function warning($msg, $handler_id=self::DEFAULT_HANDLER_ID) {
        return $this->log('WARNING', $msg, $handler_id);
    }

    /**
     * 记录NOTICE级别的日志
     *
     * 建议：一些统计类信息。
     *
     * @param string|array  $msg 日志内容
     * @param string        $handler_id 使用哪一个日志处理器，默认使用default
     * @return bool
     */
    public function notice($msg, $handler_id=self::DEFAULT_HANDLER_ID) {
        return $this->log('NOTICE', $msg, $handler_id);
    }

    /**
     * 记录DEBUG级别的日志
     *
     * 如果没有$this->switchDebug(true)，则不会输出debug级别的日志信息。
     *
     * 建议：一些调试类信息。
     *
     * @param string|array  $msg 日志内容
     * @param string        $handler_id 使用哪一个日志处理器，默认使用default
     * @return bool
     */
    public function debug($msg, $handler_id=self::DEFAULT_HANDLER_ID) {
        if (true === $this->debug_flag) {
            return $this->log('DEBUG', $msg, $handler_id);
        }
        return true;
    }

    private function log($level, $msg, $handler_id) {
        $handler = Yun_Array::get($this->handler, $handler_id);
        if (! ($handler instanceof Yun_Log_Handler_Interface) ) {
            return $this->error($msg, $error, self::DEFAULT_HANDLER_ID);
        }
        $loginfo = sprintf("%s %s {$level} %s\n", date('Ymd-H:i:s'), self::$request_id, $msg);
        $handler->log($loginfo);
    }
}
