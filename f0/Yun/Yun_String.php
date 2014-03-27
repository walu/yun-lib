<?php
/**
 * Yun_String
 * 
 * 用于有效操作String的一些方法
 * 
 * @author walu
 */
class Yun_String {
	
	/**
	 * 
	 * 用于修复explode(',', '')得到array('')的问题
	 * 
	 * 在平常工作中，对于explode(',', '')，我们需要得到空数组
	 * 而不是array('')
	 * 
	 * @param string $delimiter
	 * @param string $string
	 * @return array
	 */
	public static function safeExplode($delimiter, $string) {
		if (''===$string || null===$string) {
			return array();
		}
		$re = explode($delimiter, $string);
		return $re;
	}
	
	/**
	 * 判断一个字符串是否以某个串结尾
	 * 
	 * @param string $haystack
	 * @param string $search
	 * @return boolean 
	 */
	public static function endWith($haystack, $search) {
		$len = strlen($search);
		return substr($haystack, 0, -$len)===$search;
	}
	
	/**
	 * 判断一个字符串是否以某个串开始
	 * 
	 * @param string $haystack
	 * @param string $search
	 * @return boolean
	 */
	public static function startWith($haystack, $search) {
		return strpos($haystack, $search) === 0;
    }

    /**
     * 生成随机字符串
     *
     * @param int $len      随机字符串长度
     * @param string $dict  字典（字符串取值范围）
     * @return string
     */
    public static function randomString($len, $dict='abcdefghijklmnopqrstuvwxyz0123456789') {
        //don't use empty(): "0", " "
        if (0 == strlen($dict)) {
            return '';
        }

        $re = '';
        $dict_len = strlen($dict);
        $rand_max = $dict_len-1;
        while ($len > 0) {
            $key = mt_rand(0, $rand_max) % $dict_len;
            $re .= $dict[$key];
            $len--;
        }
        return $re;
    }

    /**
     * 将string转换成16进制的表示方式
     *
     *
     * @param string str
     * @return string
     */
    public static function str2Hex($str) {
        $str = bin2hex($str);
        $str = chunk_split($str, 2, '\\x');
        return '\\x' . substr($str, 0, -2);
    }
    

    /**
     * 将string转换成8进制的表示方式
     *
     * @param string $str
     * @return string
     */
    public static function str2Oct($str) {
        $len = strlen($str);
        $re = '';
        
        for ($i=0; $i<$len; $i++) {
            $re .= '\\'.decoct(ord($str[$i]));    
        }
        return $re;
    } 
}
