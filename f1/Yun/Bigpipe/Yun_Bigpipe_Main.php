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

    private $cache_for_ob = array();

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
     * 执行操作，修改输出缓冲
     *
     * 本操作将修改输出缓冲，将网页内容在输出之前构造好。适合于喂蜘蛛等类似情况。
     */
    public function executeAndModifyOB() {
        if (0==ob_get_level()) {
            //没有开启缓冲的换，那什么也坐不了
            //如果只是想并发获取结果，请直接使用Yun_Curl_Multi
            return;
        }

        $this->cache_for_ob = array();
        $this->p->execute(array($this, 'callbackModifyOB'));

        $content = ob_get_clean();
        foreach ($this->cache_for_ob as $key=>$value) {
            $key = preg_quote($key);
            $content = preg_replace(
                "!(<([a-z]+)[^>]*?id\\s*=\\s*[\"']{$key}[\"'][^>]*?>).*?(</\\2\\s*>)!is", 
                "\$1{$value}\$3",
                $content
            );
        }
        echo $content;
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

    public function callbackModifyOB($id, $api_response) {
        $api_response = json_decode($api_response, true);
        if (null === $api_response) {
            $api_response = array();
        }
        
        $html = Yun_Array::get($api_response, 'html', '');
            
        $html .= "<script language='javascript'>";
        
        if (isset($api_response['css_url'])) {
            !is_array($api_response['css_url']) && ($api_response['css_url'] = array($api_response['css_url']));
            $tmp = json_encode($api_response['css_url']);
            $html .= "Yun_Bigpipe.css_handler({$tmp});";
        }
        
        if (isset($api_response['js_url'])) {
            !is_array($api_response['js_url']) && ($api_response['js_url'] = array($api_response['js_url']));
            $tmp = json_encode($api_response['js_url']);
            $html .= "Yun_Bigpipe.js_handler({$tmp});";
        }

        $html .= '</script>';
        $this->cache_for_ob[$id] = $html;
    }
}
