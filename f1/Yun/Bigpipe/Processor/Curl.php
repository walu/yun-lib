<?php
/**
 * 使用Curl Multi进行操作的bigpipe处理器
 *
 * @author walu<imcnan@gmail.com>
 */
class Yun_Bigpipe_Processor_Curl implements Yun_Bigpipe_Processor_Interface {

    private $ycm;

    private $data;

    private $callback_function;

    public function __construct() {
        $this->ycm = new Yun_Curl_Multi();
    }

    public function addPagelet($id, $url, $option=array()) {
        $option[CURLOPT_RETURNTRANSFER] = true;
        $key = $this->ycm->addUrl($url, $option);
        $this->data[$key] = $id;
    }

    public function getTotal() {
        return count($this->data);
    }

    public function execute($callback_function) {
        $this->callback_function = $callback_function;
        $this->ycm->doAndCallback(array($this, 'callback'));
    }

    public function callback($key, $curl_info, $body) {
        call_user_func(
            $this->callback_function, 
            $this->data[$key],
            $body
        );
    }
}
