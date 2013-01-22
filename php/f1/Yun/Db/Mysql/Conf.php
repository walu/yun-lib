<?php
/**
 *
 * 每一个Conf只对应一个Adapter，也就是可以单例处理
 *
 * 一、如何设置服务器连接，如：host、port等
 * 首先应先看自己数据库服务器的架构，根据实际情况来选择何时的方案：
 * 1. 只有一台数据库服务器
 * $conf = new Yun_Db_Mysql_Conf();
 * $conf->addServer($host, $port, $uname, $pass, $db);
 *
 * 2. 采用了Mysql-proxy之类的负载均衡代理，这样来处理
 * $conf = new Yun_Db_Mysql_Conf();
 * $conf->addServer($host_1, $port, $uname, $pass, $db);
 * $conf->addServer($host_2, $port, $uname, $pass, $db);
 * ......
 *
 * 3. 采用了主从架构（ 主读写 - 从读）
 * $conf = new Yun_Db_Mysql_Conf();
 * $conf->addMasterServer(....);
 * .....
 * $conf->addSlaveServer(.....);
 * $conf->addSlaveServer(.....);
 * ....
 *
 * 二、如何选择合适的扩展[默认使用PDO]
 * 备注：扩展类型在脚本执行过程中只能设置一次，中间的再次更改不会起到任何作用
 *
 * 1. 使用mysqli扩展
 * $conf->useMysqliExt();
 *
 * 2.使用PDO扩展
 * $conf->usePdoExt();
 *
 * @author walu<imcnan@gmail.com>
 */
class Yun_Db_Mysql_Conf implements Yun_Db_Conf_Interface {

	private $adapter_class_name = 'Yun_Db_Mysql_Adapter_Pdo';

	private $adapter_instance = array();

	private $master_server = array();
	private $slave_server  = array();

	private $retry_time_connect = 0;

	private $default_names = 'utf8';

	/**
	 * 删除所有的数据库配置
	 */
	public function unsetServerConf() {
		$this->write_server = array();
		$this->read_server  = array();
	}

	/**
	 *
	 * 没有主从分布
	 * 如只有一台服务器，或者采用了Mysql-proxy做负载均衡，则使用此方法增加服务器
	 *
	 * @param string $host
	 * @param string $user
	 * @param string $pass
	 * @param string $dbname
	 * @param string $port
	 * @param string $socket
	 */
	public function addServer($host, $user, $pass, $dbname, $port, $socket='') {
		$row = array('host'=>$host, 'user'=>$user, 'pass'=>$pass, 'dbname'=>$dbname, 'port'=>$port, 'socket'=>$socket);
		$this->master_server[] = $row;
		$this->slave_server[]  = $row;
	}

	/**
	 * 增加主服务器，也就是常见的读写分离架构中的写服务器
	 *
	 * 要求主服务器同时具有读写权限
	 *
	 * @param string $host
	 * @param string $user
	 * @param string $pass
	 * @param string $dbname
	 * @param string $port
	 * @param string $socket
	 */
	public function addMasterServer($host, $user, $pass, $dbname, $port, $socket='') {
		$row = array('host'=>$host, 'user'=>$user, 'pass'=>$pass, 'dbname'=>$dbname, 'port'=>$port, 'socket'=>$socket);
		$this->master_server[] = $row;
	}

	/**
	 * 增加从服务器，也就是常见的读写分离架构中的读服务器
	 *
	 * @param string $host
	 * @param string $user
	 * @param string $pass
	 * @param string $dbname
	 * @param string $port
	 * @param string $socket
	 */
	public function addSlaveServer($host, $user, $pass, $dbname, $port, $socket='') {
		$row = array('host'=>$host, 'user'=>$user, 'pass'=>$pass, 'dbname'=>$dbname, 'port'=>$port, 'socket'=>$socket);
		$this->slave_server[]  = $row;
	}

	public function useMysqliExt() {
		$this->adapter_class_name = 'Yun_Db_Mysql_Adapter_Mysqli';
	}

	public function usePdoExt() {
		$this->adapter_class_name = 'Yun_Db_Mysql_Adapter_Pdo';
	}

	public function setNames($names) {
		$names = htmlspecialchars($names, ENT_QUOTES);
		$this->default_names = $names;
	}

	/**
	 * 选择一个adapter
	 *
	 * 每一个host-user-pass-dbname-port-socket在全局仅对应一个数据库连接
	 *
	 * @see Yun_Db_Conf_Interface::getAdapter()
	 */
	public function getAdapter($sql_prefix = '') {
		$act = substr($sql_prefix, 0, 6);
		if ('SELECT'==$act || 'EXPLAIN'==$act) {
			$conf = $this->slave_server;
		} else {
			$conf = $this->master_server;
		}
		 
		$server     = Yun_Array::rand($conf);
		 
		$host       = Yun_Array::get($server, 'host', '');
		$user       = Yun_Array::get($server, 'user', '');
		$pass       = Yun_Array::get($server, 'pass', '');
		$dbname     = Yun_Array::get($server, 'dbname', '');
		$port       = Yun_Array::get($server, 'port', '');
		$socket     = Yun_Array::get($server, 'socket', '');
		 
		$hash = "{$host}_{$user}_{$pass}_{$dbname}_{$port}_{$socket}";
		if (!isset($this->adapter_instance[$hash])) {
			$class_name = $this->adapter_class_name;
			$this->adapter_instance[$hash] = new $class_name();
			if (! ($this->adapter_instance[$hash] instanceof Yun_Db_Mysql_Adapter_Interface)) {
				$error_msg = 'Yun_Db_Mysql_Conf\'s adapter must be implements Yun_Db_Mysql_Adapter_Interface.';
				trigger_error($error_msg, E_USER_ERROR);
			}
			 
			$retry_time = $this->retry_time_connect;
			while (true) {
				$retry_time--;
				$re = $this->adapter_instance[$hash]->connect($host, $user, $pass, $dbname, $port, $socket);
				if (true === $re) {
					$this->adapter_instance[$hash]->query("SET NAMES {$this->default_names}");
					break;
				}

				if ($retry_time==0) {
					break;
				}
			}//end while
		}//endif

		return $this->adapter_instance[$hash];
	}

	/**
	 * @see Yun_Db_Conf_Interface::getBuilder()
	 */
	public function getBuilder() {
		return Yun_Db_Mysql_Builder::getInstance($this);
	}
}