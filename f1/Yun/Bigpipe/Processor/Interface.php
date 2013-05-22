<?php
/**
 * bigpipe 处理器接口
 *
 * @author walu<imcnan@gmail.com>
 */
interface Yun_Bigpipe_Processor_Interface {

    /**
     * 并发执行请求，在每个请求结束后调用回调函数
     *
     * @param callable $callback_function
     */
    public function execute($callback_function);

    /**
     *
     * 获取本次并发处理的pagelet个数
     *
     * @return int
     */
    public function getTotal();
}
