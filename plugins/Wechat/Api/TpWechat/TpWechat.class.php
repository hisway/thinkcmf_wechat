<?php
// +----------------------------------------------------------------------
// | TpWechat.class.php
// +----------------------------------------------------------------------
// | Author: polo <gao.bo168@gmail.com>
// +----------------------------------------------------------------------
// | Data: 2015-3-3下午4:28:01
// +----------------------------------------------------------------------
// | Version: 2015-3-3下午4:28:01
// +----------------------------------------------------------------------
// | Copyright: ShowMore
// +----------------------------------------------------------------------
namespace plugins\Wechat\Api\TpWechat;
use plugins\Wechat\Api\Wechat\Wechat;
class TpWechat extends Wechat{
    /**
     * log overwrite
     * @see Wechat::log()
     */
    protected function log($log){
        if ($this->debug) {
            if (function_exists($this->logcallback)) {
                if (is_array($log)) $log = print_r($log,true);
                return call_user_func($this->logcallback,$log);
            }elseif (class_exists('\Think\Log')) {
	            \Think\Log::write('wechat：'.$log, \Think\Log::DEBUG);
	        }
        }
        return false;
    }
    
    /**
     * 重载设置缓存
     * @param string $cachename
     * @param mixed $value
     * @param int $expired
     * @return boolean
     */
    protected function setCache($cachename,$value,$expired){
        return S($cachename,$value,$expired);
    }
    
    /**
     * 重载获取缓存
     * @param string $cachename
     * @return mixed
     */
    protected function getCache($cachename){
        return S($cachename);
    }
    
    /**
     * 重载清除缓存
     * @param string $cachename
     * @return boolean
     */
    protected function removeCache($cachename){
        return S($cachename,null);
    }
}