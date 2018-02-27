<?php
/*调用方法
require_once 'cache.class.php';

$cache = new Cache($dir);
$cache->add(1, 'test');
$cache->set(2, 'abc',60);//60秒，不加时间永远有效
$cache->get(1);  
if(!$cache->get(1)){
    echo $cache->tip_message;
}else{
    echo $cache->get(1);
}
$cache->delete(1);
$cache->flush();
$cache->auto_delete_expired_file();
*/


// +----------------------------------------------------------------------
// |缓存类
// +----------------------------------------------------------------------
// | Author: justmepzy(justmepzy@gmail.com)
// +----------------------------------------------------------------------



class Cache{

    //提示信息
    public $tip_message;
     
    //缓存目录
    protected $cache_dir;
    //缓存文件名
    private $cache_file_name;
    //缓存文件后缀
    private $cache_file_suffix;
     
    public function __construct($dir,$cache_file_suffix = '.php'){
        $this->cache_dir = isset($this->$dir)?$dir:dirname(__FILE__).DIRECTORY_SEPARATOR.'default_cache_data';
 
        $this->cache_file_suffix = $cache_file_suffix;
         
        if(!$this->dir_isvalid($this->cache_dir)){
            die($this->tip_message);//创建目录失败
        }
             
    }
    // +----------------------------------------------------------------------
    // |添加一个值，如果已经存在，则返回false,写入文件失败返回false
    // +----------------------------------------------------------------------
    // | 
    // +----------------------------------------------------------------------
    public function add($cache_key,$cache_value,$life_time=1800){
         if(file_exists($this->get_cache_file_name($cache_key))){
            $this->tip_message = '缓存数据已存在.';
            return false;
         }
         $cache_data['data'] = $cache_value;
         $cache_data['life_time'] = $life_time;
         //以JSON格式写入文件
         if(file_put_contents($this->get_cache_file_name($cache_key), json_encode($cache_data))){
            return true;
         }else{
            $this->tip_message = '写入缓存失败.';
            return false;
         }
         
    }
    // +----------------------------------------------------------------------
    // |添加一个值，如果已经存在，则覆写
    // +----------------------------------------------------------------------
    // | 
    // +----------------------------------------------------------------------
    public function set($cache_key,$cache_value,$life_time=1800){
         
        $cache_data['data'] = $cache_value;
        $cache_data['life_time'] = $life_time;
        if(file_put_contents($this->get_cache_file_name($cache_key), json_encode($cache_data))){
            return true;
         }else{
            $this->tip_message = '写入缓存失败.';
            return false;
         }
    }
    // +----------------------------------------------------------------------
    // |获取一个key值
    // +----------------------------------------------------------------------
    // | 
    // +----------------------------------------------------------------------
    public function get($cache_key){
        if(!file_exists($this->get_cache_file_name($cache_key))){
            return false;
        }
        $data = $this->object_to_array(json_decode(file_get_contents($this->get_cache_file_name($cache_key))));
         
        if($this->check_isvalid($data['life_time'])){
            unset($data['life_time']);
            return $data['data'];
        }else{
            unlink($this->cache_file_name);
            $this->tip_message = '数据已过期.';
            return false;
        }   
    }
    // +----------------------------------------------------------------------
    // |删除一个key值
    // +----------------------------------------------------------------------
    // | 
    // +----------------------------------------------------------------------
    public function delete($cache_key){
        if(file_exists($this->get_cache_file_name($cache_key))){
            if(unlink($this->get_cache_file_name($cache_key)))
                return true;
            else
                return false;
        }else{
            $this->tip_message = '文件不存在.';
            return true;
        }
    }
    // +----------------------------------------------------------------------
    // |清除所有缓存文件
    // +----------------------------------------------------------------------
    // | 
    // +----------------------------------------------------------------------
    public function flush(){
        $this->delete_file($this->cache_dir);
    }
    // +----------------------------------------------------------------------
    // |自动清除过期文件
    // +----------------------------------------------------------------------
    // | 
    // +----------------------------------------------------------------------
    public function auto_delete_expired_file(){
        $this->delete_file($this->cache_dir,false);
    }
    // +----------------------------------------------------------------------
    // |检查目录是否存在,不存在则创建
    // +----------------------------------------------------------------------
    // | 
    // +----------------------------------------------------------------------
    private function dir_isvalid($dir){
         
        if (is_dir($dir)) 
            return true;
        try {
           mkdir($dir,0777);
        }catch (Exception $e) {
             $this->tip_message = '所设定缓存目录不存在并且创建失败!请检查目录权限!';
             return false;            
       }
       return true;
    }
    // +----------------------------------------------------------------------
    // |检查有效时间
    // +----------------------------------------------------------------------
    // | 
    // +----------------------------------------------------------------------
    private function check_isvalid($expired_time = 0) {
        //if(!file_exists($this->cache_file_name)) return false;
        if (!(@$mtime = filemtime($this->cache_file_name))) return false;
        if (time() -$mtime > $expired_time) return false;
        return true;
   }
    // +----------------------------------------------------------------------
    // |获得缓存文件名
    // +----------------------------------------------------------------------
    // | 
    // +----------------------------------------------------------------------
    private function get_cache_file_name($key){
        $this->cache_file_name = $this->cache_dir.DIRECTORY_SEPARATOR.md5($key).$this->cache_file_suffix;
        return $this->cache_file_name;
    }
    // +----------------------------------------------------------------------
    // |object对象转换为数组
    // +----------------------------------------------------------------------
    // | 
    // +----------------------------------------------------------------------
    protected function object_to_array($obj){
          
        $_arr = is_object($obj) ? get_object_vars($obj) : $obj; 
        foreach ($_arr as $key => $val) { 
            $val = (is_array($val) || is_object($val)) ? object_to_array($val) : $val; 
            $arr[$key] = $val; 
        } 
        return $arr; 
    }
    // +----------------------------------------------------------------------
    // |删除目录下的所有文件
    // +----------------------------------------------------------------------
    // | $mode true删除所有 false删除过期
    // +----------------------------------------------------------------------
    protected function delete_file($dir,$mode=true) {  
        $dh=opendir($dir); 
        while ($file=readdir($dh)) { 
            if($file!="." && $file!="..") { 
                $fullpath=$dir."/".$file; 
                if(!is_dir($fullpath)) { 
                    if($mode){
                        unlink($fullpath); 
                    }else{
                        $this->cache_file_name = $fullpath;
                        if(!$this->get_isvalid_by_path($fullpath)) unlink($fullpath); 
                    }
                     
                } else { 
                    delete_file($fullpath,$mode); 
                } 
            } 
        } 
        closedir($dh); 
    }
    private function get_isvalid_by_path($path){
        $data = $this->object_to_array(json_decode(file_get_contents($path)));
        return $this->check_isvalid($data['life_time']);
    }
}
?>