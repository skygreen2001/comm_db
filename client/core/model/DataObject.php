<?php 

//<editor-fold defaultstate="collapsed" desc="枚举类型">
DataObjectSpec::init();
//</editor-fold>

/**
 +-----------------------------------------<br/>
 * 所有数据实体类如POJO的父类<br/>
 * 该实体类设计为ActiveRecord模式。<br/>
 * 可直接在对象上操作CRUD增删改查操作<br/>
 * 查主要为：根据主键和名称查找对象。<br/>
 *          总记录数和分页查找等常规方法。<br/>
 * 框架定义数据对象的默认列[关键字可通过数据对象列规格$field_spec修改]：<br/>
 *     id,commitTime，updateTime<br/>
 * id:数据对象的唯一标识<br/>
 * committime:数据创建的时间，当没有updateTime时，其亦代表数据最后更新的时间<br/>
 * updateTime:数据最后更新的时间。<br/>
 +-----------------------------------------<br/>
 * @category betterlife
 * @package core.model
 * @author skygreen
 */
abstract class DataObject extends Object implements ArrayAccess {     
    //<editor-fold defaultstate="collapsed" desc="定义部分">
    /**
    * @var enum $id_name_strategy ID名称定义的策略
    */
    public static $idname_strategy=EnumIDNameStrategy::ID;    
    /**
    * ID名称中的连接符。<br/>
    * ID名称定义的策略为TABLENAME_ID有效。
    * @static
    */
    public static $idname_concat='_';
    /**
    * @var enum $foreignid_name_strategy Foreign ID名称定义的策略
    */
    public static $foreignid_name_strategy=EnumForeignIDNameStrategy::TABLENAMEID;    
    /**
    * Foreign ID名称中的连接符。<br/>
    * Foreign ID名称定义的策略为TABLENAME_ID有效。
    * @static
    */
    public static $foreignid_concat='_'; 
    /**
    * 数据对象定义需定义字段：public $field_spec<br/>
    * 它定义了当前数据对象的列规格说明。<br/>
    * 数据对象的列规格说明可参考DataObjectSpec::$field_spec_default的定义
    */
    public $field_spec;
    /**
     * @var mixed 数据对象的唯一标识 
     */
    protected $id;
    /**
     * @var int 记录创建的时间timestamp 
     */
    protected $commitTime;
    /**
     * @var int 记录最后更新的时间，当表中无该字段时，一般用commitTime记录最后更新的时间。
     */
    protected $updateTime;    
    /**
     * @var IDao 当前使用的数据库调用对象
     */
    private static $currentDao;    
    /**
     * 获取当前使用的数据库调用对象
     * @return IDao 
     */
    public static function dao() {
        if (empty(self::$currentDao)) {
            self::$currentDao=Manager_Db::newInstance()->dao();
        }
        return self::$currentDao;
    }
    //</editor-fold>   
    
    //<editor-fold defaultstate="collapsed" desc="魔术方法">
    /**
     * 说明：若每个具体的实现类希望不想实现set,get方法；<br/>
     *      则将该方法复制到每个具体继承他的对象类内。<br/>
     * 可设定对象未定义的成员变量[但不建议这样做]<br/>
     * 可无需定义get方法和set方法<br/>
     * 类定义变量访问权限设定需要是pulbic<br/>
     * @param string $method 方法名
     * @param array $arguments 传递的变量数组
     */
    public function __call($method, $arguments)
    {
        return DataObjectFunc::call($this,$method,$arguments);
    }

    /**
     * 可设定对象未定义的成员变量[但不建议这样做]<br/>
     * 类定义变量访问权限设定需要是pulbic
     * @param mixed $property 属性名
     * @return mixed 属性值
     */
    public function __get($property) 
    {
        return DataObjectFunc::get($this,$property);
    }

    /**
     * 可设定对象未定义的成员变量[但不建议这样做]<br/>
     * 类定义变量访问权限设定需要是pulbic
     * @param mixed $property 属性名
     * @param mixed $value 属性值
     */
    public function __set($property, $value) 
    {
        return DataObjectFunc::set($this,$property,$value);
    }
    
     /**
     * 打印当前对象的数据结构
     * @return string 描述当前对象。
     */
    public function __toString() {
        return DataObjectFunc::toString($this);
    }    
    //</editor-fold>
    
    /**
     * 处理表之间一对一，一对多，多对多的关系
     */
    public function getMutualRelation($property,&$isRelation=false) {
         return DataObjectRelation::getMutualRelation($this,$property,$isRelation);    
    }
    
    //<editor-fold defaultstate="collapsed" desc="默认列Setter和Getter"> 
    /**
     * @var array 存放当前数据对象的列规格说明
     */
    public $real_fieldspec;  
    
    /**
     * 设置唯一标识
     * @param mixed $id 
     */
    public function setId($id) 
    {        
        if (DataObjectSpec::isNeedID($this)){
           $columnName=DataObjectSpec::getRealIDColumnName($this);  
           $this->$columnName=$id;
        }
    }
    
    /**
     * 获取唯一标识
     * @return mixed
     */
    public function getId() 
    {
        if (DataObjectSpec::isNeedID($this)){
            $columnName=DataObjectSpec::getRealIDColumnName($this);
            return $this->$columnName;
        }  else {
            return null;
        }
    }

    
    /**
     * 设置数据创建的时间
     * @param mixed $commitTime 
     */
    public function setCommitTime($commitTime) 
    {
        if (DataObjectSpec::isNeedCommitTime($this)){
            DataObjectSpec::setRealProperty($this,EnumColumnNameDefault::COMMITTIME,$commitTime);
        }        
    }

    /**
     * 获取数据创建的时间
     * @return mixed 
     */
    public function getCommitTime() 
    {
        if (DataObjectSpec::isNeedCommitTime($this)){
            $columnName=DataObjectSpec::getRealColumnName($this,EnumColumnNameDefault::COMMITTIME);
            return $this->$columnName;
        }  else {
            return null;
        }
        //return $this->commitTime;
    }
    
    /**
     * 设置数据最后更新的时间
     * @param mixed $updateTime 
     */
    public function setUpdateTime($updateTime) 
    {
        if (DataObjectSpec::isNeedUpdateTime($this)){            
            DataObjectSpec::setRealProperty($this,EnumColumnNameDefault::UPDATETIME,$updateTime);
        }else{
            $this->setCommitTime($updateTime); 
        }
    }

    /**
     * 获取数据最后更新的时间
     * @return mixed 
     */
    public function getUpdateTime() 
    {
        if (DataObjectSpec::isNeedUpdateTime($this)){
            $columnName=DataObjectSpec::getRealColumnName($this,EnumColumnNameDefault::UPDATETIME);
            return $this->$columnName;
        }  else {
            $this->getCommitTime($updateTime); 
        }
        //return $this->updateTime;        
    }  
    //</editor-fold>
    
    //<editor-fold defaultstate="collapsed" desc="定义数组进入对象方式">
    public function offsetExists($key) 
    {
        $method="get".ucfirst($key);
        return method_exists($this,$method);
    }
    public function offsetGet($key) 
    {
        $method="get".ucfirst($key);
        return $this->$method();
    }
    public function offsetSet($key, $value) 
    {
        $method="set".ucfirst($key);
        $this->$method($value);
//        $this->$key = $value;
    }
    public function offsetUnset($key) 
    {
        unset($this->$key);
    }
    //</editor-fold>
    
    //<editor-fold defaultstate="collapsed" desc="数据持久化：数据库的CRUD操作">
    /**
     * 保存前操作
     */
    protected function onBeforeWrite() {
    }
    
    /**
     * 保存当前对象
     * @return boolen 是否新建成功；true为操作正常
     */
    protected function write() 
    {
        $this->save();
    }

    /**
     * 保存当前对象
     * @return int 保存对象记录的ID标识号
     */
    public function save() 
    {
        $this->onBeforeWrite();
        return self::dao()->save($this);
    }

    /**
     +----------------------------------------------------<br>
     * 数据对象存在多对多|从属于多对多关系时，因为存在一张中间表。<br>
     * 因此它们的关系需要单独进行存储<br>
     * 示例1【多对多-主控端】：<br>
     *      $user=new User();<br>
     *      $user->setId(2);<br>
     *      $user->saveRelationForManyToMany("roles","3",array("commitTime"=>date("Y-m-d H:i:s")));<br>
     *      说明:roles是在User数据对象中定义的变量：<br>
     *      static $many_many=array(<br>
     *        "roles"=>"Role",<br>
     *      );<br>
     * 示例2【多对多-被控端】：<br>
     *      $role=new Role();
     *      $role->setId(5);
     *      $role->saveRelationForManyToMany("users","6",array("commitTime"=>date("Y-m-d H:i:s")));
     *      说明:users是在Role数据对象中定义的变量：<br>
     *      static $belongs_many_many=array(
     *        "users"=>"User",
     *      );
     +----------------------------------------------------<br>
     * @param mixed $relation_object 多对多|从属于多对多关系定义对象
     * @param mixed $relation_id_value 关系对象的主键ID值。
     * @param array $other_column_values  其他列值键值对【冗余字段便于查询的数据列值】，如有一列：记录关系创建时间。
     * @return mixed 保存对象后的主键 
     */
    public function saveRelationForManyToMany($relation_object,$relation_id_value,$other_column_values=null)
    {    
        return DataObjectRelation::saveRelationForManyToMany($this,$relation_object,$relation_id_value,$other_column_values);
    }

    /**
     * 删除当前对象
     * @return boolen 是否删除成功；true为操作正常
     */
    public function delete() 
    {
        return self::dao()->delete($this);
    }

    /**
     * 更新当前对象
     * @return boolen 是否更新成功；true为操作正常
     */
    public function update()
    {
        return  self::dao()->update($this);
    }

    /**
     * 更新对象指定的属性
     * @param string $sql_id 需删除数据的ID编号Sql语句<br/>
     * 示例如下：<br/>
     *     $sql_id:<br/>
     *         user_id=1<br/>
     * @param string $array_properties 指定的属性<br/>
     * 示例如下：<br/>
     *     $array_properties<br/>
     *      1.pass=1,name='sky'<br/>
     *      2.array("pass"=>"1","name"=>"sky")<br/>
     * @return boolen 是否更新成功；true为操作正常<br/>
     */
    public static function updateProperties($sql_id,$array_properties) 
    {
        return DataObjectFunc::updateProperties(get_called_class(),$sql_id,$array_properties);
    }

    /**
     * 查询当前对象列表
     * @param string $filter 查询条件，在where后的条件<br/>
     * 示例如下：<br/>
     *      0."id=1,name='sky'"<br/>
     *      1.array("id=1","name='sky'")<br/>
     *      2.array("id"=>"1","name"=>"sky")<br/>
     *      3.允许对象如new User(id="1",name="green");<br/>
     * 默认:SQL Where条件子语句。如："(id=1 and name='sky') or (name like 'sky')"<br/>
     * @param string $sort 排序条件<br/>
     * 示例如下：<br/>
     *      1.id asc;<br/>
     *      2.name desc;<br/>
     * @param string $limit 分页数目:同Mysql limit语法
     * 示例如下：<br/>
     *    0,10<br/>
     * @return 对象列表数组
     */
    public static function get($filter=null, $sort=Crud_SQL::SQL_ORDER_DEFAULT_ID, $limit=null) 
    {
        return self::dao()->get(get_called_class(), $filter, $sort, $limit);
    }

    /**
     * 查询得到单个对象实体
     * @param object|string|array $filter 查询条件，在where后的条件
     * 示例如下：<br/>
     *      0."id=1,name='sky'"<br/>
     *      1.array("id=1","name='sky'")<br/>
     *      2.array("id"=>"1","name"=>"sky")<br/>
     *      3.允许对象如new User(id="1",name="green");<br/>
     * 默认:SQL Where条件子语句。如：(id=1 and name='sky') or (name like 'sky')<br/>
     * @return 单个对象实体
     */
    public static function get_one($filter=null)
    {
        return self::dao()->get_one(get_called_class(), $filter);
    }

    /**
     * 根据表ID主键获取指定的对象[ID对应的表列]
     * @param string $id
     * @return 对象
     */
    public static function get_by_id($id) 
    {
        return self::dao()->get_by_id(get_called_class(), $id);
    }

    /**
     * 对象总计数
     * @param string|class $object 需要查询的对象实体|类名称
     * @param object|string|array $filter<br/>
     *      $filter 格式示例如下：<br/>
     *          0.允许对象如new User(id="1",name="green");<br/>
     *          1."id=1","name='sky'"<br/>
     *          2.array("id=1","name='sky'")<br/>
     *          3.array("id"=>"1","name"=>"sky")
     */
    public static function count($filter=null) 
    {
        return self::dao()->count(get_called_class(), $filter);
    }

    /**
     * 对象分页
     * @param string|class $object 需要查询的对象实体|类名称
     * @param object|string|array $filter 查询条件，在where后的条件
     * 示例如下：<br/>
     *      0."id=1,name='sky'"<br/>
     *      1.array("id=1","name='sky'")<br/>
     *      2.array("id"=>"1","name"=>"sky")<br/>
     *      3.允许对象如new User(id="1",name="green");<br/>
     * 默认:SQL Where条件子语句。如：(id=1 and name='sky') or (name like 'sky')<br/>
     * @param string $sort 排序条件<br/>
     * 默认为 id desc<br/>
     * 示例如下：<br/>
     *      1.id asc;<br/>
     *      2.name desc;
     * @return mixed 对象分页
     */
    public static function queryPage($startPoint,$endPoint,$filter=null,$sort=Crud_SQL::SQL_ORDER_DEFAULT_ID) 
    {
        return self::dao()->queryPage(get_called_class(),$startPoint,$endPoint,$filter,$sort);
    }
    //</editor-fold>
    
    //<editor-fold defaultstate="collapsed" desc="数据类型转换">
    /**
     * 将数据对象转换成xml
     * @param $filterArray 需要过滤不生成的对象的field<br/>
     * 示例：$filterArray=array("id","commitTime");
     * @param $isAll 是否对象所有的field都要生成，包括没有内容或者内容为空的field
     * @return xml内容
     */
    public function toXml($isAll=true,$filterArray=null)
    {
       return UtilObject::object_to_xml($this,$filterArray,$isAll);
    }
    
    /**
    * 将数据对象转换成Json类型格式
     * @param $isAll 是否对象所有的field都要生成，包括没有内容或者内容为空的field
    * @return Json格式的数据格式的字符串。
    */
    public function toJson($isAll=false)
    {
       return DataObjectFunc::toJson($this,$isAll);
    }
    
    /**
     * 将数据对象转换成Array
     * @param $isAll 是否对象所有的field都要生成，包括没有内容或者内容为空的field
     * @return 数组
     */
    public function toArray($isAll=true)
    {  
       return UtilObject::object_to_array($this,$isAll);
    }
    //</editor-fold>
    
}
?>
