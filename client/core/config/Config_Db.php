<?php

//<editor-fold defaultstate="collapsed" desc="枚举类型">
/**
 +---------------------------------------<br/>
 * 枚举类型：数据库方式类别|数据源定义
 +---------------------------------------<br/>
 * @category betterlife
 * @package core.config
 * @author skygreen
 */
class EnumDbSource extends Enum{
    /**
     * 默认：数据库：Mysql
     */
    const DB_MYSQL=0;
}

/**
 +---------------------------------------<br/>
 * 枚举类型：数据源操作方式引擎定义
 +---------------------------------------<br/>
 * @category betterlife
 * @package core.config
 * @author skygreen
 */
class EnumDbEngine extends Enum{
    /**
     * 默认：经典的MYSQLI访问数据库方法函数
     */
    const ENGINE_OBJECT_MYSQL_MYSQLI=1;
}
//</editor-fold>

/**
 +--------------------------------------------------<br/>
 * 所有数据库配置的父类<br/>
 * @todo Sql Server 和 Pdo的测试-尚不知道如何找到Php5.3的php_pdo_mssql驱动；暂无需求<br/>
 *        Sql Server 第三方方案：http://www.easysoft.com/developer/languages/php/sql_server_unix_tutorial.html#driver<br/>
 * 说明： 目前可使用PHP自带的ODBC方案使用Sql Server；通过配置Config_Db:$db=DB_SQLSERVER和Config_Db:$engine=ENGINE_OBJECT_ODBC即可<br/> 
 +--------------------------------------------------<br/>  
 * @category betterlife
 * @package core.config
 * @author skygreen
 */
class Config_Db {
    /**
     * @var int 当前应用使用Mysql数据库
     * @static
     */
    public static $db=EnumDbSource::DB_MYSQL;//self::DB_SQLSERVER;
    /**
     * @var int 数据库使用调用引擎
     * @static
     */
    public static $engine=EnumDbEngine::ENGINE_OBJECT_MYSQL_MYSQLI;//ENGINE_OBJECT_MYSQL_MYSQLI   self::ENGINE_DAL_ADODB;
    /**
     * @var string Host 默认本地 localhost                   
     * @static
     */
    public static $host="localhost";//UF-T4300-2003-9
    /**
     * 默认端口如下：<br/>
     * Mysql 3306<br/>
     * Postgres 5432<br/>
     * DB2 50000<br/>
     * Microsoft Sql Server 10060<br/>
     * @var string 默认端口<br/>
     * 
     * @static
     */
    public static $port="";
    /**
     * @var string 数据库用户名
     * @static
     */
    public static $username="root";//zyp
    /**
     * @var string 数据库密码
     * @static
     */
    public static $password="";//zyp
    /**
     * 如果数据库指定是文件，如Microsoft Access,Sqlite<br/>
     * 则为数据库文件名<br/>
     * 如果是Oracle数据库则是SID名称       <br/>                           
     * @var string 数据库名称
     * @static
     */
    public static $dbname="betterlife";
    /**
     +--------------------------------------------------<br/>
     * 目前调试通过  该参数对Config_Db::$engine<br/>
     *     *ENGINE_DAL_ADODB<br/>
     *     *ENGINE_OBJECT_ODBC 有效  <br/> 
     * 
     * 是否使用了Dsn的设置 <br/>
     * true:在Windows里进行了系统DSN的设置，只需在Config_Db::$dbname里输入DSN设置的名称即可<br/>
     * false:未进行设置，则需要在Config_Db::$dbname设置数据库文件所在路径或者数据库名称<br/>
     * 特殊情况：当数据库为Sql server时； *ENGINE_DAL_ADODB 只支持  $is_dsn_set=false；这是由Adodb自身所决定的<br/>
     +--------------------------------------------------<br/>
     * @var <type>
     */
    public static $is_dsn_set=false;
    /**
     * @var boolean 是否持久化数据库
     * @static
     */
    public static $is_persistent=false;

    /**
     * @var string 数据库表名前缀；
     * @static
     */
    public static $table_prefix="bb_";

    /**
     +--------------------------------------------------<br/>
     * 数据库表和系统类的ORM映射<br/>
     * 命名规范:[类第一个字母大写，表名都是小写]<br/>
     * 数据库实体POJO对象需放置在domain目录下<br/>
     * 表名为三段：数据库表名前缀+“_”+[文件夹目录+“_”]...+(类名)<br/>
     * 示例如下：<br/>
     *     数据库表名前缀:apts_<br/>
     *     类  ：Task<br/>
     *     包  ：domain.business.work<br/>
     *     文件夹目录：domain/business/work<br/>
     *     表名：apts_business_work_task<br/>
     +--------------------------------------------------<br/>
     * @var array
     * @static
     */
    private static $orm=array( 
    );

    /**
     +--------------------------------------------<br/>
     * 对象定义和表定名映射<br/>
     * 相当于ORM<br/>
     * 无需进行$orm配置；通过规则生成表与类的映射；即规则优于配置
     +--------------------------------------------<br/>
     * @param classname 类名称
     * @return 根据对象定义返回表名
     * @final
     */
    final public static function orm($classname) {
        if (array_key_exists($classname, self::$orm)) {
            return self::$orm[$classname];//在Config_Db::$orm里手动配置类与表的对应关系
        }else {
            return self::ormByRule($classname);
        }
    }
    
    /**
     +--------------------------------------------<br/>
     * 根据表命名获取对应的对象定义
     * 表命名到对象定义映射<br/>
     * 相当于ORM<br/>
     * 无需进行$orm配置；通过规则生成表与类的映射；即规则优于配置<br/>
     +--------------------------------------------<br/>
     * @param $tablename 表名称
     * @return 根据表名称返回对象名称定义
     * @final
     */    
    final public static function tom($tablename){
        if (in_array($tablename, self::$orm)) {
            return array_search($tablename, self::$orm);//在Config_Db::$orm里手动配置类与表的对应关系
        }else {
            return self::tomByRule($tablename);
        }
    }

    /**
     * 表名段之间的连接符
     */
    const TABLENAME_CONCAT="_";
    /**
     * 关系表的类所在的文件夹目录
     */
    const TABLENAME_DIR_RELATION="_relation";
    /**
     * 关系表的段名
     */
    const TABLENAME_RELATION="_re";

    /**
     +-------------------------------------------------<br/>
     * 按照类和表的对应关系规则规范自动生成；<br/>
     * 要求是表的命名一定按照Config_Db::$orm的说明进行定义<br/>
     +-------------------------------------------------<br/>
     * @param string $classname 数据库实体对象POJO类名
     +-------------------------------------------------<br/>
     * @return string 遵循命名规则和规范的表名
     */
    private static function ormByRule($classname) {
        $class_root_dir=Gc::$domain_root;
        $class=new ReflectionClass($classname);
        $filename=strtolower(dirname($class->getFileName()));
        $subDirname=substr($filename,strpos($filename,$class_root_dir)+strlen($class_root_dir)+1);
        if ($subDirname){
            $subDirname=str_replace(DIRECTORY_SEPARATOR, self::TABLENAME_CONCAT, $subDirname);
            $subDirname=str_replace(self::TABLENAME_DIR_RELATION, self::TABLENAME_RELATION, $subDirname);
            $subDirname=$subDirname.self::TABLENAME_CONCAT;
        }else{
            $subDirname="";
        }
        $tablename=self::$table_prefix.$subDirname.strtolower($classname);
        return $tablename;
    }
    
     /**
     +-------------------------------------------------<br/>
     * 按照类和表的对应关系规则规范自动生成；<br/>
     * 要求是表的命名一定按照Config_Db::$orm的说明进行定义<br/>
     +-------------------------------------------------<br/>
     * @param string $tablename 遵循命名规则和规范的表名
     +-------------------------------------------------<br/>
     * @return string 数据库实体对象POJO类名
     */
    private static function tomByRule($tablename) {
        $classname=str_replace(self::$table_prefix,"",$tablename);
        $classnamepart=explode(self::TABLENAME_CONCAT,$classname);
        $classnamepart=array_reverse($classnamepart);
        $maybeClassname="";
        foreach ($classnamepart as $name) {
            $maybeClassname=ucfirst($name).$maybeClassname;
            if (class_exists($maybeClassname)){
                $classname=$maybeClassname;
                return $classname;
            }
            $maybeClassname=self::TABLENAME_CONCAT.$maybeClassname;
        }      
        return null;                
    }   
}

?>
