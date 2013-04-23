<?php
interface Yun_Log_Handler_Interface {

    /**
     *
     * 获取此Log处理器的名称
     *
     * 此名称必须是全局唯一的，且不允许是default，否则在registerHandler阶段将会
     * 产生fatal error。
     *
     * @return string
     */
    public function getHandlerId();

    /**
     *
     * 记载日志信息
     *
     * @param string $msg
     * @return bool true:成功，false:失败
     */
    public function log($msg);

    /**
     * 最近一次的错误码
     *
     * @return string|int
     */
    public function errorCode();

    /**
     * 最近一次的错误信息
     *
     * @return string|int
     */
    public function errorInfo();
}
