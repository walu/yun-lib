<?php
/**
 * Yun_Db_Main
 * 
 * 本类为yun-lib/db中最重要的一个类，开发者一般只与此类打交道即可。
 * 
 * 一、如何配置
 * 1. 找到自己使用的数据库对应的配置类，如Yun_Db_Mysql_Conf，Yun_Db_Postgresql_Conf等，参照它们的注释。
 * 
 * 二、如何使用
 * 1. 仅涉及一个表的CURD。
 * - 1.1 本类提供的方法已经完全够用
 *     <code>
 *     $dao = new Yun_Db_Main();
 *     $dao->setMainTable('table-name');
 *     $list = $dao->select('id', $id);
 *     $re   = $dao->insert(array('id'=>99, 'name'=>'walu', 'email'=>'imcnan@gmail.com'));
 *     $list = $dao->selectAll();
 *     $re   = $dao->delete('id', 99);
 *     //and so on.
 *     </code>
 * 
 * - 1.2 还需要封装自己的逻辑操作
 *     <code>
 *     class Bloglist extends Yun_Db_Main {
 *         public function __construct() {
 *             $this->setMainTable('table-name');
 *         }
 *         
 *         //分页获取(仅仅是demo，并未所数据库异常等判断)
 *         public function getPagedList($condition, $page, $pagesize) {
 *             $sql_list = func($condition, $page, $pagesize);
 *             $sql_total = func($condition);
 *             $list = $this->query($sql_list);
 *             $total = $this->queryOneRow($sql_total);
 *             return array('list'=>$list, 'total'=>$total[0]['total']);
 *         }
 *     }
 *     </code>
 * 
 * 2. 涉及多个表的逻辑操作。
 *     <code>
 *     class BlogTagLogic extends Yun_Db_Main {
 *         public function __construct() {
 *             $this->table_blog = '';
 *             $this->table_tag  = '';
 *         }
 *         
 *         public function getHottestBlog() {
 *             $sql = "SELECT * FROM {$this->table_blog} LEFT JOIN {$this->table_tag} ON ...... WHERE .....";
 *             return $this->query($sql);
 *         } 
 *     }
 *     </code>
 * 
 * 
 * 备注：
 * 1. 默认将使用Yun_Conf::get('yun_db_conf')的配置。
 * 2. 本类自带的所有SELECT类操作，如果没有符合条件的数据，则返回空数组。如果DB失败，则返回false。
 * 
 * @author walu
 *
 */
class Yun_Db_Main {
    
    /**
     * Yun_Db_Main 默认使用的Yun_Conf配置项
     *
     * @var string
     */
    const CONF_KEY = 'yun_db_conf';
    
    /**
     * @var Yun_Db_Conf_Interface
     */
    protected $main_conf = null;
	
	/**
	 * 本类对应的基准表
	 * 
	 * @var string
	 */
	protected $main_table;
	
	protected $error_code = 0;
	
	protected $error_info = '';
	
	protected $last_adapter;
	
	/**
	 * 设置配置文件
	 * 默认将使用Yun_Conf::getInstance()->get('yun_db_conf');
	 * 
	 * @param Yun_Db_Conf_Interface $conf
	 */
	public function setMainConf(Yun_Db_Conf_Interface $conf) {
	    $this->main_conf = $conf;
	}
	
	/**
	 * 为select、selectOneRow等方法设置基准表
	 * 
	 * @param string $table_name
	 */
	public function setMainTable($table_name) {
	    $this->main_table = $table_name;
	}

	/**
	 * 执行一条SQL并返回结果
	 * 
	 * @param string $sql
	 * @return bool|array
	 */
	public function query($sql) {
	    $sql_prefix = substr($sql, 0, 6);
		$adapter = $this->getAdapter($sql_prefix);
		if (false === $adapter) {
			return false;
		}
		$re = $adapter->query($sql);
		if (false === $re) {
			$this->error_code = $adapter->errorCode();
			$this->error_info = $adapter->errorInfo();
		}
		return $re;
	}

    /**
     * 执行一条SQL并返回其第一行
     *
     * 如果没有数据则返回空数组，sql执行失败返回false
	 * 
	 * @param string $sql
	 * @return bool|array
	 */
	public function queryOneRow($sql) {
	    $sql_prefix = substr($sql, 0, 6);
		$adapter = $this->getAdapter($sql_prefix);
		if (false === $adapter) {
			return false;
		}
		$re = $adapter->query($sql);
		if (false === $re) {
			$this->error_code = $adapter->errorCode();
			$this->error_info = $adapter->errorInfo();
        } else {
            $re = is_array($re) && count($re) ? reset($re) : array();
        }
        	return $re;
	}

    /**
     * 以事务的方式执行多条Sql语句
     *
     * @param array $sql_array
     * @return bool
     */
    public function queryTranction(array $sql_array) {
        $sql_prefix=reset($sql_array);
        $adapter = $this->getAdapter($sql_prefix);
        if (false === $adapter) {
            return false;
        }

        $adapter->beginTransaction();
        foreach ($sql_array as $sql) {
            $re = $adapter->query($sql);
            if (false === $re) {
                $this->error_code = $adapter->errorCode();
                $this->error_info = $adapter->errorInfo();
                $this->rollBack();
                return false;//如果单条执行失败，则直接跳回
            }
        }

        $re = $adapter->commit();
        if (false === $re) {
            $this->error_code = $adapter->errorCode();
            $this->error_info = $adapter->errorInfo();
        }
        return $re;
    }
	
	/**
	 * 在$this->main_table中获取$filed = $value的记录
	 * 
	 * @param string $field
	 * @param string $value
	 * @return false|array
	 */
	public function select($field, $value) {
		$builder = $this->getMainBuilder();
        $field   = $this->quote($field);
        $value   = $this->quote($value);
        $table   = $this->getMainTable();

        if (in_array(false, array($field, $value, $table), true)) {
            return false;
        }

		$sql = $builder->sqlOfSelect($this->getMainTable(), $field, $value);
		return $this->query($sql);
	}
	
	/**
	 * 获取$this->main_table的所有记录
	 * 
	 * @return false|array
	 */
	public function selectAll() {
		$builder = $this->getMainBuilder();

        $table = $this->getMainTable();
        if (false === $table) {
            return false;
        }
        
        $sql = $builder->sqlOfSelectAll($table);
		return $this->query($sql);
	}
	
	/**
	 * 在$this->main_table中获取$filed = $value的一条记录
	 * 
	 * 如果传递了$order_by，则先根据$order_by进行排序。
	 * 
	 * 备注：
	 * 1.如果$field非主键，非唯一索引，则强烈建议排序，否则在多次执行时候获取的值可能会因为DB随机排序而导致不一致
	 * 2.本方法直接返回行对应的一维数组
	 * 
	 * @param string $field
	 * @param string $value
	 * @param array $order_by 如( 'id'=>'desc', 'date'=>'asc' )
	 * @return array
	 */
	public function selectOneRow($field, $value, array $order_by = array()) {
        $builder    = $this->getMainBuilder();
        $field      = $this->quote($field);
        $table      = $this->getMainTable();
        $value      = $this->quote($value);
        $order_by   = $this->quoteArray($order_by);

        if (in_array(false, array($field, $value, $table, $order_by), true)) {
            return false;
        }

		$sql     = $builder->sqlOfSelectOneRow($table, $field, $value, $order_by);
        return $this->queryOneRow($sql);
	}
	
	/**
	 * 在$this->main_table中获取$field = $value[0] OR $field=$value[1] .... OR $field=$value[n] 的记录
	 * 
	 * @param string $field
	 * @param array $value
	 * @return array
	 */
	public function selectByMuiltyValue($field, array $value_array) {
        $builder = $this->getMainBuilder();
        
        $field = $this->quote($field);
        $value = $this->quoteArray($value_array);
        $table = $this->getMainTable();

        if (in_array(false, array($field, $value, $table), true) ) {
            return false;
        }

		$sql = $builder->sqlOfSelectByMuiltyValue($table, $field, $value);
		return $this->query($sql);
	}
	
	/**
	 * 向$this->main_table中插入一行数据，KV关系如$row所述。
	 * 
	 * @param array $row
	 * @return bool|int false代表操作失败，true代表成功，int代表insert_id(如果表中相应字段的话)
	 */
	public function insert(array $row) {
        $builder = $this->getMainBuilder();
        $table = $this->getMainTable();
        $row   = $this->quoteArray($row);
        
        if (false === $table || false === $row) {
            return false;
        }

        $sql     = $builder->sqlOfInsert($table, $row);
        return $this->query($sql);
	}
	
	/**
	 * 返回lastinsertid，如果有的话
	 * 
	 * @return number
	 */
	public function lastInsertId() {
		return $this->last_adapter->lastInsertId();
	}
	
	/**
	 * 向$this->main_table中同时插入多行
	 * 
	 * 如果$chunk_size==0,则整体一起插入，如果支持事务则使用事务。
	 * 如果$chunk_size>0，则先将$row_array分为多个小块，分批插入。如果支持事务则每批采用事务，前一批若失败则直接返回false
	 * 
	 * @param array $row_array
	 * @param int   $chunk_size=0
	 * @return bool
	 */
	public function insertMuiltyRow(array $row_array, $chunk_size=0) {
		$builder = $this->getMainBuilder();
        $table = $this->getMainTable();
        foreach ($row_array as $key=>$value) {
            $row_array[$key] = $this->quoteArray($value);
            if (false === $row_array[$key]) {
                return false;
            }
        }
        
        $sql_array = $builder->sqlOfInsertMuiltyRow($table, $row_array);
        return $this->queryTranction($sql_array);
	}

	/**
	 * 更新$this->main_table中$field=$value的记录
	 * 
	 * @param array $row
	 * @param string $field
	 * @param string $value
	 * @return bool
	 */
	public function update(array $row, $field, $value) {
        $builder = $this->getMainBuilder();
        $row   = $this->quoteArray($row);
        $field = $this->quote($field);
        $value = $this->quote($value);
        $table = $this->getMainTable();

        if (in_array(false, array($row, $field, $value, $table), true)) {
            return false;
        }

		$sql     = $builder->sqlOfUpdate($table, $row, $field, $value);
		return $this->query($sql);
	}
	
	/**
	 * 删除$this->main_table中$field=$value的记录
	 * 
	 * @param string $field
	 * @param string $value
	 * @return bool
	 */
	public function delete($field, $value) {
	    $builder = $this->getMainBuilder();
        $table = $this->getMainTable();
        $field = $this->quote($field);
        $value = $this->quote($value);

	    $sql     = $builder->sqlOfDelete($table, $field, $value);
	    return $this->query($sql);
	}
	
	/**
	 * 获取本类对应的main_table
	 * 
	 * @return string
	 */
	public function getMainTable() {
		return $this->quote($this->main_table);
	}
	
	/**
	 * 获取本类对应的builder
	 * 
	 * @return Yun_Db_Builder_Interface
	 */
	public function getMainBuilder() {
	    $this->initConf();
	    return $this->main_conf->getBuilder();
	}

    /**
     * 转义字符串，防止sql注入
     *
     * @param string
     * @return string 转义后的字符串
     */
    public function quote($string) {
        $adapter = $this->getAdapter();
        if (false === $adapter ) {
            return false;
        }

        $re = $adapter->quote($string);
        if (false === $re) {
            $this->error_code = $adapter->errorCode();
            $this->error_info = $adapter->errorInfo();
        }
        return $re;
    }
    
    /**
     * 将一个一维数组的key与value都进行转义
     *
     * @param array $array
     * @return array
     */
    public function quoteArray(array $array) {
        $adapter = $this->getAdapter();
        if (false === $adapter) {
            return false;
        }

        $tmp = array();
        foreach ($array as $k=>$v) {
            $k = $adapter->quote($k);
            $v = $adapter->quote($v);
            if (false === $k || false === $v) {
                $this->error_code = $adapter->errorCode();
                $this->error_info = $adapter->errorInfo();
                return false;
            }
            $tmp[$k] = $v;
        }
        return $tmp;
    }
	
	public function errorCode() {
		return $this->error_code;
	}
	
	public function errorInfo() {
		return $this->error_info;
	}
	
	/**
	 * 获取本类对应的adapter
	 * 
	 * @return Yun_Db_Adapter
	 */
	protected function getAdapter($sql_prefix = '') {
	    $this->initConf();
	    $adapter = $this->main_conf->getAdapter($sql_prefix);
	    if (false === $adapter) {
	    		$this->error_code = $this->main_conf->errorCode();
	    		$this->error_info = $this->main_conf->errorInfo();
	    }
	    $this->last_adapter = $adapter;
	    return $adapter;
	}
	
	/**
	 * 初始化main_conf
	 * 如果没有设置，则读取默认设置
	 */
	protected function initConf() {
	    if (null === $this->main_conf) {
	        $conf = Yun_Conf::getInstance()->get(self::CONF_KEY);
	        if (!($conf instanceof Yun_Db_Conf_Interface)) {
	            $error_msg = 'Yun_Conf::get("yun_db_conf")\'s value must be implement Yun_Db_Conf_Interface.';
	            trigger_error($error_msg, E_USER_ERROR);
	        }
	        $this->main_conf = $conf;
	    }
	}
}
