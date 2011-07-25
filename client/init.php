<?php
header("Content-Type:text/html; charset=UTF-8");
require_once 'Gc.php';//加载全局变量文件
class Initializer
{
    const ROOT_CORE="core";
    /**
     * 框架核心所有的对象类对象文件
     * @var array 二维数组
     * 一维：模块名称
     * 二维：对象类名称
     */
    public static $coreFiles;
    /**
     * 开发者自定义所有类对象文件
     * @var array 二维数组
     * 一维：模块名称
     * 二维：对象类名称
     * @static
     */
    public static $moduleFiles;     
    /**
     * 初始化加载类和路径
     */
    public static function init(){  
        require_once 'core/Enum.php';   
        require_once 'core/common.php';
        /**
         * 加载全局变量文件
         */
        require_once 'Gc.php'; 
        //定义异常报错信息
        if (Gc::$dev_debug_on){
            if(defined('E_DEPRECATED')) error_reporting(E_ALL ^ E_DEPRECATED);
            else error_reporting(E_ALL);
        }else{
            error_reporting(0);
        }

        $root_core="core";
        $nav_core_path=Gc::$nav_framework_path.$root_core.DIRECTORY_SEPARATOR;
        $core_util="util";
        $include_paths=array(
                $nav_core_path,
                $nav_core_path.$core_util,
                $nav_core_path.$core_util.DIRECTORY_SEPARATOR."common",
        );
        set_include_path(get_include_path().PATH_SEPARATOR.join(PATH_SEPARATOR, $include_paths));
        
        $include_paths=UtilFileSystem::getAllDirsInDriectory($nav_core_path);   
        set_include_path(get_include_path().PATH_SEPARATOR.join(PATH_SEPARATOR, $include_paths)); 
        
        $nav_domain_path=Gc::$nav_root_path.Gc::$domain_root.DIRECTORY_SEPARATOR;
        $include_paths=UtilFileSystem::getAllDirsInDriectory($nav_domain_path); 
        set_include_path(get_include_path().PATH_SEPARATOR.join(PATH_SEPARATOR, $include_paths)); 
        
        //self::recordCoreClasses();
        //self::recordDomainClasses();                 
    }


    /**
     * 记录框架核心所有的对象类
     */
    private static function recordCoreClasses() 
    {
        $dirs_root=array(
                self::$NAV_CORE_PATH
        );

        $files = new AppendIterator();
        foreach ($dirs_root as $dir) {
            $tmp=new ArrayObject(UtilFileSystem::getAllFilesInDirectory($dir));
            if (isset($tmp)) $files->append($tmp->getIterator());
        }

        foreach ($files as $file) {
            self::$coreFiles[self::ROOT_CORE][basename($file,self::SUFFIX_FILE_PHP)]=$file;
        }
    }

    /**     
     * 记录所有实体数据对象模块下的文件路径
     */
    public static function recordDomainClasses() 
    { 
        $module_dir= Gc::$nav_root_path.Gc::$domain_root.DIRECTORY_SEPARATOR;
        load_module(Gc::$domain_root, $module_dir);
    }    
}


/**
 * 相当于__autoload加载方式
 * 但是当第三方如Flex调用时__autoload无法通过其ZendFrameWork加载模式；
 * 需要通过spl_autoload_register的方式进行加载,方能在调用的时候进行加载
 * @param string $class_name 类名
 */
function class_autoloader($class_name) {
     class_exists($class_name) ||require($class_name.".php");
}

spl_autoload_register("class_autoloader");

Initializer::init();
?>
