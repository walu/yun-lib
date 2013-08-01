<?php
/**
 * Yun_Curl_Multi 
 *
 * 对curl_multi的封装，使其使用起来容易一些
 *
 * <code>
 * $ycm = new Yun_Curl_Multi();
 * $option = array(CURLOPT_RETURNTRANSFER=>true);
 *
 * $url_conn[$url] = $ycm->addUrl($url, $option);
 * $url_conn[$url] = $ycm->addUrl($url, $option);
 * $url_conn[$url] = $ycm->addUrl($url, $option);
 *
 * $ycm->doAndCallback($callback_function);//每一次请求结束后都会立即调用callback
 * or 
 * $content = $ycm->doAndGetResult();//所有请求结束后返回结果
 * </code>
 *
 * @author walu<imcnan@gmail.com>
 * @link https://github.com/walu/yun-lib
 */
class Yun_Curl_Multi {

    private $conn = array();

    private $version_lt_720;

    private $result;

    /**
     * 构造函数
     */
    public function __construct() {
        $curl_version = curl_version();
        $this->version_lt_720 = 1 == version_compare('7.20.0', $curl_version['version']);
    }

    /**
     * 添加URL，并设置相应配置
     *
     * @param string $url
     * @param array $curl_option_array 具体配置请查看curl_setopt_array
     * @return int|false int代表成功；false代表失败（一般是配置配错了，请查看$this->errorInfo()）。
     */
    public function addUrl($url, array $curl_option_array=array()) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //注意，也会返回null，当url参数不对的时候
        if (false === $ch || null === $ch) {
            $this->setError("curl_init errors with url: {$url}");
            return false;
        }
        $re = curl_setopt_array($ch, $curl_option_array);
        if (false === $re) {
            $this->setError(curl_error($ch), curl_errno($ch));
            curl_close($ch);
            return false;
        }

        $this->conn[] = $ch;
        return count($this->conn) - 1;
    }

    /**
     * 执行请求，并在每个请求执行完毕后调用callback
     *
     * 回调函数的定义为：function demo($key, $curl_info, $body) {...}
     * $key,        int,    $this->addUrl返回的索引
     * $curl_info,  array,  curl_getinfo函数返回的结果，包含了请求过程中的一些细节数据
     * $body,       string, 请求返回的结果
     *
     * @param callable $callback 回调函数
     * @return bool true:成功；false:失败
     */
    public function doAndCallback($callback) {
        $multi_handler = curl_multi_init();
        foreach ($this->conn as $value) {
            curl_multi_add_handle($multi_handler, $value);
        }

        $still_running = true;
        if ($this->version_lt_720) {
            do {
                $re = curl_multi_exec($multi_handler, $still_running);
            } while (CURLM_CALL_MULTI_PERFORM == $re);

            while ($still_running) {
                if (-1 != curl_multi_select($multi_handler, 0.1) ) {
                    do {
                        curl_multi_exec($multi_handler, $still_running);
                    } while (CURLM_CALL_MULTI_PERFORM == $re);    
                    $this->doneAndCallback($multi_handler, $callback);
                }
            };
        
        } else {
            curl_multi_exec($multi_handler, $still_running);
            while ($still_running) {
                if (-1 != curl_multi_select($multi_handler)) {
                    curl_multi_exec($multi_handler, $still_running);
                    $this->doneAndCallback($multi_handler, $callback);
                }
            }
        }

        foreach ($this->conn as $key=>$value) {
            curl_multi_remove_handle($multi_handler, $value);
            curl_close($value);
        }
        curl_multi_close($multi_handler);
    }

    /**
     * 执行请求，并获取每个链接的内容
     *
     * 只能等待所有链接执行完毕后，才能返回。
     *
     * 返回值的结构：
     * array(
     *   $key => array('curl_info'=>array(...), 'body'=>string),
     *   $key => array('curl_info'=>array(...), 'body'=>string),
     *   $key => array('curl_info'=>array(...), 'body'=>string),
     *   ......
     * );
     * $key为$this->addUrl返回的索引
     *
     * @return array
     */
    public function doAndGetResult() {
        $this->result = array();
        $this->doAndCallback(array($this, 'getResultHelper'));
        return $this->result;
    }

    /**
     * $this->doAndGetResult使用的回调函数
     *
     * @param int $key
     * @param array $info
     * @param string $body
     */
    private function getResultHelper($key, $info, $body) {
        $this->result[$key] = array(
            'curl_info' => $info,
            'body' => $body
        );
    }


    /**
     * 当一个请求执行完毕后，调用回调函数
     *
     * @param resource $multi_handler
     * @param callable $callback
     */
    private function doneAndCallback($multi_handler, $callback) {
        while (true) {
            $info = curl_multi_info_read($multi_handler);
            if (false === $info) {
                break;
            }

            if ($info['msg'] != CURLMSG_DONE) {
                continue;
            }
            $handle = $info['handle'];
            $key = array_search($handle, $this->conn);
            if (false === $key) {
                continue;
            }
            call_user_func($callback, $key, curl_getinfo($handle), curl_multi_getcontent($handle));
        };    
    }
    
    /**
     *
     * 获取最近的一次错误信息
     *
     * @return string
     */
    public function errorInfo() {
        return $this->error_info;
    }

    /**
     * 获取最近的一次错误码
     *
     * @return int
     */
    public function errorCode() {
        return $this->error_code;
    }

    private function setError($error_info, $error_code = -1) {
        $this->error_code = $error_code;
        $this->error_info = $error_info;
    }

    private function initHandler() {
        $this->multi_handler = curl_multi_init();
        foreach ($this->conn as $value) {
            curl_multi_add_handle($this->multi_handler, $value);
        }
    }
}

