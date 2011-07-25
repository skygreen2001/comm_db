<?php

//<editor-fold defaultstate="collapsed" desc="枚举类型">
//加载枚举类型定义
class_exists("Enum")||require(dirname(__FILE__)."/core/Enum.php");
/**
 * 日志记录方式
 */
class EnumLogType extends Enum{
    /**
     * 默认。根据在 php.ini 文件中的 error_log 配置，错误被发送到服务器日志系统或文件。 
     */
    const SYSTEM    = 0;
    /**
     * 日志通过邮件发送
     */
    const MAIL      = 1;
    /**
     * 通过 PHP debugging 连接来发送错误,在PHP3以后就不再使用了
     */
    const DEGUG     = 2;  
    /**
     * 错误发送到文件目标字符串
     */
    const FILE      = 3;
    /**
     * SAPI:Server Application Programming Interface 服务端应用编程端口.
     */
    const SAPI      = 4;
    /**
     * 浏览器显示。 
     */
    const BROWSER    = 11;    
    /**
     * 默认记录在数据库中
     */
    const DB        = 100;
    /**
     * 通过Firebug Console 输出。
     */
    const FIREBUG   = 101;    
}
//</editor-fold>

/**
 +-----------------------------------<br/>
 * 定义全局使用变量<br/>
 +------------------------------------

 * @access public
 */
class Gc 
{
    //<editor-fold desc="网站使用设置">
    /**
     * 网站应用的名称<br/>
     * 展示给网站用户
     * @var string
     * @static
     */
    public static $site_name="Betterlife 网站应用框架";
    /**
     * 网站应用的版本
     */
    public static $version="1.0";
    /**
     * 网站根路径的URL路径
     * @var string 
     * @static
     */
    public static $url_base;//="http://localhost/betterlife/";//获取网站URL根路径        
    /**
     * 网站根路径的物理路径
     * @var string
     * @static
     */
    public static $nav_root_path;//="C:\\wamp\\www\\betterlife\\";
    /**
     * 框架文件所在的路径 <br/>
     * 有两种策略可以部署<br/>
     * 1.框架和应用整合在一起；则路径同$nav_root_path   <br/>
     * 2.框架和应用分开，在php.ini里设置include_path="";添加框架所在的路径<br/>
     *                   则可以直接通过  <br/>
     * @var string
     * @static
     */
    public static $nav_framework_path;//="C:\\wamp\\www\\betterlife\\";
    //</editor-fold>
    
    //<editor-fold desc="开发者使用设置">
    /**
     * 网站应用的名称<br/>
     * 在网站运行程序中使用，不展示给网站用户；如标识日志文件的默认名称等等。
     * @var string
     * @static
     */
    public static $appName="comm_db";
    
    /**
     * 业务应用部署的根目录<br/>
     * 说明：该框架采用模块组建的方式<br/>
     * 每一个遵循该框架的网站业务应用都部署在该目录下<br/>
     * @var string 
     * @static
     */
    public static $domain_root="domain";
    /**
     * 是否打开Debug模式
     * @var bool
     * @static
     */
    public static $dev_debug_on=true;
    
    /**
     * 是否要Profile网站性能
     * @var bool
     * @static
     */
    public static $dev_profile_on=false;
    
    /**
     * @string 页面字符集<br/>
     * 一般分为：<br/>
     * UTF-8<br/>
     * GBK<br/>
     * 最终可以由用户选择
     * @var string
     * @static
     */
    public static $encoding="UTF-8";
    
    /**
     * @var string 文字显示默认语言
     * @static
     * 最终可以由用户选择
     */
    public static $language="Zh_Cn";
    
    /**
     * 通常用于用邮件发送重要日志给管理员。
     * @var array 邮件的配置。
     * @static
     */
    //<editor-fold defaultstate="collapsed" desc="邮件的设置">        
    public static $email_config=array(
        "SMTP"=>"smtp.sina.com.cn",
        'smtp_port'=>"25",
        "sendmail_from"=>"skygreen2001@sina.com",        
        /**
         * 可在php.ini中设置sendmail_path，无法通过ini_set实时设置，因为它只能在php.ini或者httpd.conf中设置。<br/>
         * 因为官网文档【http://php.net/manual/en/mail.configuration.php】：sendmail_path "/usr/sbin/sendmail -t -i" PHP_INI_SYSTEM 
         */
        //"sendmail_path"=>"C:\wamp\sendmail\sendmail.exe -t",
        /**
         * 通过邮件发送日志的目的者
         */
        "mailto"=>"skygreen@sohu.com"
    );    
    //</editor-fold>
    
    /**
     * 日志的配置。
     * @var array 日志的配置。
     * @static
     */    
    //<editor-fold defaultstate="collapsed" desc="日志的设置">    
    public static $log_config=array(
        /**
         * 默认日志记录的方式。<br/>
         * 一般来讲，日志都通过log记录，由本配置决定它在哪里打印出来。<br/>
         * 可通过邮件发送重要日志，可在数据库或者文件中记录日志。也可以通过Firebug显示日志。
         */
        "logType"=>EnumLogType::BROWSER,
        /**
         * 允许记录的日志级别
         */
        "log_record_level"=> array('EMERG','ALERT','CRIT','ERR','INFO'),
        /**
         * 日志文件路径<br/>
         * 可指定日志文件放置的路径<br/>
         * 如果为空不设置，则在网站应用根目录下新建一个log目录，放置在它下面
         */
        "logpath"=>'',
        /**
         * 检测日志文件大小，超过配置大小则备份日志文件重新生成，单位为字节
         */
        "log_file_size"=>1024000000,
        /**
         * 日志记录的时间格式
         */
        "timeFormat" =>  '%Y-%m-%d %H:%M:%S',
        /**
         * 通过邮件发送日志的配置。 
         */
        "config_mail_log"=>array(
            'subject' => '重要的日志事件',
            'mailBackend' => '',
        ),
        "log_table"=>"bb_log_log",
    );
    //</editor-fold>    
    //</editor-fold>
        
    /**
    * 无需配置自动注入网站的网络地址和物理地址。
    */
    //<editor-fold defaultstate="collapsed" desc="初始化设置">        
    public static function init(){
        if (empty(Gc::$url_base)){
            if(isset($_SERVER['HTTPS']) && strpos('on',$_SERVER['HTTPS'])){
                $baseurl = 'https://'.$_SERVER['HTTP_HOST'];
                if($_SERVER['SERVER_PORT']!=443)$baseurl.=':'.$_SERVER['SERVER_PORT'];
            }else{
                $baseurl = 'http://'.$_SERVER['HTTP_HOST'];
                if($_SERVER['SERVER_PORT']!=80)$baseurl.=':'.$_SERVER['SERVER_PORT'];
            }            
            $baseDir = dirname($_SERVER['SCRIPT_NAME']);
            $baseurl.=($baseDir == '\\' ? '' : $baseDir).'/';
            Gc::$url_base=$baseurl;
        }
        if (empty(Gc::$nav_root_path)){
           Gc::$nav_root_path=dirname(__FILE__).DIRECTORY_SEPARATOR;
        }
       
        if (empty(Gc::$nav_framework_path)){
           Gc::$nav_framework_path=dirname(__FILE__).DIRECTORY_SEPARATOR;
        }
    }
    //</editor-fold>
}

//<editor-fold defaultstate="collapsed" desc="邮件在php.ini里全局的设置">
ini_set("SMTP", Gc::$email_config["SMTP"]);
ini_set("smtp_port", Gc::$email_config["smtp_port"]);
ini_set('sendmail_from', Gc::$email_config['sendmail_from']);
//</editor-fold>
Gc::init();
?>
