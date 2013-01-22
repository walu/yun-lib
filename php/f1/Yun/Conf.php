<?php
/**
 * 对Conf进行统一管理的地方
 * 
 * 区分Conf与Setting
 * 	1. 如果一个值随着部署环境不同而改变，我们称之为Conf。
 *  2. 如果一个值永远固定，我们称之为Setting。
 *  3. Setting配置中决不允许有Conf。
 * 
 * 备注：
 * 1. conf的值不允许是null
 * 2. 本类不要与文件、网络产生依赖，因为他是f1的基础，需要能够方面的生成、传递。
 * 
 * @author walu<imcnan@gmail.com>
 */
class Yun_Conf {
	
	private $conf = array();
	
	private static $instance = null;
	
	public static function getInstance() {
		if (null === self::$instance) {
			self::$instance = new Yun_Conf();
		}
		return self::$instance;
	}
	
	/**
	 * 
	 * @param string $key
	 * @return null|mixed
	 */
	public function get($key) {
		return isset($this->conf[$key]) ? $this->conf[$key] : null;
	}
	
	/**
	 * 
	 * 设置配置
	 * 
	 * $conf = Yun_Conf::getInstance();
	 * 
	 * <pre>
	 * $conf_mysql = new Yun_Db_Mysql_Conf();
	 * $conf->set('default_mysql', $conf_mysql);
	 * </pre>
	 * 
	 * @param string $key
	 * @param string $value
	 */
	public function set($key, $value) {
		$this->conf[$key] = $value;
	}
	
	/**
	 * 是否有某个conf配置
	 * 
	 * @param string $key
	 * @return bool
	 */
	public function hasConf($key) {
		return isset($this->conf[$key]);
	}
}