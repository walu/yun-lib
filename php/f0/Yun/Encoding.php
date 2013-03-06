<?php
/**
 * 与编码有关的一些方法
 * 
 * @author walu
 * @todo complete functions for utf8 and gbk.
 *
 */
class Yun_Encoding {
	
    /**
     * 检测字符串是否为特定编码
     * 
     * @param string $string
     * @param string $encoding
     */
	public static function check($string, $encoding) {
		
	}
	
	/**
	 * 检测字符串是否utf8编码
     * 
     * @param string $string
	 * @return bool
	 */
	public static function isUtf8($string) {
		
	}
	
	/**
	 * 检测字符串是否gbk编码
	 * 
	 * @param string $string
	 * @return bool
	 */
	public static function isGbk($string) {
		
	}
	
	/**
	 * 检测字符串是否含有bom头
     *
	 * @param string $string
	 * @return bool
	 */
	public static function hasBom($string) {
        return substr($string, 0, 3) == self::getUtf8BomHeader();
	}
	
	/**
	 * 去除bom头
	 * 
	 * @param string $string
	 * @return string
	 */
	public static function removeBom($string) {
        if (self::hasBom($string)) {
            $string = substr($string, 4);
        }
        return $string;
	}
	
	/**
     * 为一段字符串增加Bom头
     *
	 * @param string $string
	 * @string
	 */
	public static function addBom($string) {
		return self::getUtf8BomHeader() . $string;
    }

    /**
     * 获取utf8的bom header string
     *
     * @link http://unicode.org/faq/utf_bom.html
     * @return string
     */
    public static function getUtf8BomHeader() {
        return "\xEF\xBB\xBF";
    }
}
