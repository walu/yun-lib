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
	
	/**
	 * 设置配置文件
	 * 默认将使用Yun_Conf::getInstance()->get('yun_db_conf');
	 * 
	 * @param Yun_Db_Conf_Interface $conf
	 */
	public function setMainConf(Yun_Db_Conf_Interface $conf) {
	    $this->conf = $conf;
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
		return $adapter->query($sql);
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
		$sql = $builder->sqlOfSelectAll();
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
	public function selectOneRow($field, $value, array $order_by) {
		$builder = $this->getMainBuilder();
		$sql     = $builder->sqlOfSelectOneRow($this->getMainTable(), $field, $value, $order_by);
		return $this->query($sql);
	}
	
	/**
	 * 在$this->main_table中获取$field = $value[0] OR $field=$value[1] .... OR $field=$value[n] 的记录
	 * 
	 * @param string $field
	 * @param array $value
	 * @return array
	 */
	public function selectByMuiltyValue($field, array $value) {
		$builder = $this->getMainBuilder();
		$sql     = $builder->sqlOfSelectByMuiltyValue($this->getMainTable(), $field, $value);
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
		$sql     = $builder->sqlOfInsert($this->getMainTable(), $row);
		
		$re = $this->query($sql);
		if (false === $re) {
			return false;
		}
		
		$adapter = $this->getAdapter();
		return $adapter->lastInsertId();
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
		$sql	 = $builder->sqlOfInsertMuiltyRow($this->getMainTable(), $row_array);
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
		$sql     = $builder->sqlOfUpdate($this->getMainTable(), $row, $field, $value);
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
	    $sql     = $builder->sqlOfDelete($this->getMainTable(), $field, $value);
	    return $this->query($sql);
	}
	
	/**
	 * 获取本类对应的main_table
	 * 
	 * @return string
	 */
	public function getMainTable() {
		return $this->main_table;
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
	 * 获取本类对应的adapter
	 * 
	 * @return Yun_Db_Adapter
	 */
	protected function getAdapter($sql_prefix = '') {
	    $this->initConf();
	    return $this->main_conf->getAdapter($sql_prefix);
	}
	
	/**
	 * 初始化main_conf
	 * 如果没有设置，则读取默认设置
	 */
	protected function initConf() {
	    if (null === $this->main_conf) {
	        $conf = Yun_Conf::get(self::CONF_KEY);
	        if (!($conf instanceof Yun_Db_Conf_Interface)) {
	            $errormsg = 'Yun_Conf::get("yun_db_conf")\'s value must be implement Yun_Db_Conf_Interface.';
	            trigger_error($error_msg, E_USER_ERROR);
	        }
	        $this->main_conf = $conf;
	    }
	}
}