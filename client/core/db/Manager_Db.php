<?php
/**
 +---------------------------------<br/>
 * 数据库操作管理<br/>
 * 所有的数据库都通过这里进行访问<br/>
 +---------------------------------<br/>
 * @category betterlife
 * @package core.db
 * @author skygreen
 */
class Manager_Db {
    /**
     * @var IDao 默认Dao对象，采用默认配置 
     */
    private $dao_static;
    /**
     * @var mixed 实时指定的Dao或者Dal对象，实时注入配置 
     */
    private $dao_dynamic;      
    /**
     * @var mixed 当前使用的Dao或者Dal对象 
     */
    private $currentdao;
    /**
     * @var Manager_Db 当前唯一实例化的Db管理类。 
     */
    private static $manger_Db;
    
    /**
     * 构造器
     */
    private function __construct() {
    }

    /**
     * 单例化
     * @return Manager_Db 
     */
    public static function newInstance() {
        if (self::$manger_Db==null) {
            self::$manger_Db=new Manager_Db();
        }
        return self::$manger_Db;
    }
    
    /**
     * 返回当前使用的Dao
     * @return mixed 当前使用的Dao 
     */
    public function currentdao(){
      if ($this->currentdao==null){
          $this->dao();
      }  
      return $this->currentdao;
    }
    
    /**
     * 全局设定一个Dao对象；
     * 由开发者配置设定对象决定
     */
    public function dao() {
        if ($this->dao_static==null)  $this->dao_static=new Dao_MysqlI5();        
        $this->currentdao=$this->dao_static;
        return $this->dao_static;
    }


    /**
     * 使用经典的MYSQLI访问数据库方法函数
     * @param string $host
     * @param string $port
     * @param string $username
     * @param string $password
     * @param string $dbname
     * @param bool $forced 是否强制重新连接数据库获取新的数据库连接对象实例
     * @return mixed 实时指定的Dao对象
     */
    public function object_mysql_mysqli($host=null,$port=null,$username=null,$password=null,$dbname=null,$forced=false) {
        if (($this->dao_dynamic==null)||$forced) {
            $this->dao_dynamic=new Dao_MysqlI5($host,$port,$username,$password,$dbname);
        }else if (!($this->dao_dynamic instanceof Dao_MysqlI5)) {
            $this->dao_dynamic=new Dao_MysqlI5($host,$port,$username,$password,$dbname);
        }
        $this->currentdao=$this->dao_dynamic;
        return $this->dao_dynamic;
    }
}
?>
