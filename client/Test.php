<?php
require_once("init.php");   
/**
  +---------------------------------<br/>
 * 模拟客户端发送请求到第三方<br/>
  +---------------------------------
 * @category betterlife
 * @package data.exchange.enjoyoung.client
 * @author skygreen
 */
class Test 
{
    public static $id=10;
    /**
     * 会员测试驱动数据
     * @param type $user 
     */
    public static function user_data($user)
    {  
        $user->name="skygreen";      
        $user->departmentId=1;          
        $user->password="13888888888";        
    }
    
    /**
    * 发送Get请求
    * @return type 
    */
    public static function user_get()
    {
        $result=User::get_by_id(self::$id);
        echo $result;        
    }     
    
    /**
    * 发送Post请求
    * @return type 
    */
    public static function user_save()
    {
        $user=new User();   
        self::user_data($user);
        $result=$user->save();
        echo $result;        
    }    

    /**
    * 发送Put请求
    */
    public static function user_update()
    {
        $user=new User(); 
        $user->id=self::$id; 
        self::user_data($user); 
        $result=$user->update();
        echo $result;
    }

    /**
    * 发送Delete请求
    */
    public static function user_delete()
    {
        $user=new User(); 
        $user->id=self::$id;  
        $result=$user->delete();
        echo $result;
    }
}

//Test::user_save(); 
//Test::user_update();
Test::user_delete(); 
//Test::user_get();
?>
