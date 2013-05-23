<?php
/**
 *
 * 通过本模块快速实现bigpipe机制
 *
 * @author walu<imcnan@gmail.com>
 */
class Yun_Bigpipe_Main {

    private $p;

    private $total;
    private $count;

    /**
     * 构造函数
     *
     * @param Yun_Bigpipe_Processor_Interface $processor bigpipe使用的处理器
     */
    public function __construct(Yun_Bigpipe_Processor_Interface $processor) {
        $this->p = $processor;
        $this->total = $processor->getTotal();
        $this->count = 0;
    }

    /**
     * 执行操作并直接输出结果
     *
     * 通过构造函数中注册的$processor并发获取每个pagelet的数据
     *
     */
    public function executeAndEcho() {
        if (!headers_sent()) {
            header('Transfer-Encoding: chunked');
        }
        $this->p->execute(array($this, 'callbackEcho'));
    }

    /**
     *
     * @param string $id 模板中的id，用于前端放置内容使用
     * @param string $api_response 必须是json格式的
     */
    public function callbackEcho($id, $api_response) {
        $this->count++;
        $api_response = json_decode($api_response, true);
        if (null === $api_response) {
            $api_response = array();
        }
        $api_response['id'] = $id;
        $api_response['is_last'] = $this->count == $total;
        $data = json_encode( $api_response );
        echo "<script language='javascript'>Yun_Bigpipe.onPageletArrive({$data});</script>";
        
        flush();
        ob_flush();
    }

    /* 
    public function callbackModifyOB($id, $html, $css_url, $js_url) {
        $html .= "<script language='javascript'>\n";
        foreach ($css_url as $url) {
            $html .= "Yun_Bigpipe.addCss({$url});\n";
        }
        foreach ($js_url as $url) {
            $html .= ""   
        }
    }
     */
}
