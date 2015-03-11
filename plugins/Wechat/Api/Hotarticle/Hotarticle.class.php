<?php
// +----------------------------------------------------------------------
// | Hotarticle.class.php
// +----------------------------------------------------------------------
// | Author: polo <gao.bo168@gmail.com>
// +----------------------------------------------------------------------
// | Data: 2015-3-10下午4:01:54
// +----------------------------------------------------------------------
// | Version: 2015-3-10下午4:01:54
// +----------------------------------------------------------------------
// | Copyright: ShowMore
// +----------------------------------------------------------------------
namespace plugins\Wechat\Api\Hotarticle;
class Hotarticle{
    public function __construct() {
        import("QueryList");
    }
    /**
     * [queryHotCat 采集微信热门文章分类]
     * @return boolean|unknown|Ambigous <multitype:, array>
     * @access public
     * @author polo<gao.bo168@gmail.com>
     * @version 2015-3-11 上午9:54:54
     * @copyright Show More
     */
    public static function queryHotCat(){
        $reg = array(
            'name'=>array('#wx-tabbox-ul li','text','',function($v,$k){
                if(preg_match('/更多/',$v,$arr)){
                    unset($v);
                    return false;
                }
                return $v;
            }),
            "code"=>array('#wx-tabbox-ul li','html','',function($v,$k){
                if(preg_match('/more_anchor/',$v,$arr)){
                    unset($v);
                    return false;
                }
                if(preg_match('/uigs=\"(.+)\">(.*)<\/a>/',$v,$arr)){
                    $v = $arr[1];
                }
                return $v;
            })
        );
        $obj = \QueryList::Query('http://weixin.sogou.com/',$reg);
        $hotCat = $obj->jsonArr;
        foreach($hotCat as $k=>$v){
            if(empty($v['name'])){
                array_splice($hotCat,$k,1);
            }
        }
        return $hotCat;
    }
    /**
     * [queryHotList 采集分类下的文章]
     * @param unknown $cat
     * @return Ambigous <multitype:, array>
     * @access public
     * @author polo<gao.bo168@gmail.com>
     * @version 2015-3-11 上午10:35:25
     * @copyright Show More
     */
    public static function queryHotList($cat){
        $url = 'http://weixin.sogou.com/pcindex/pc/' . $cat . '/' . $cat . '.html';
        $reg = array(
            "Title" => array('.wx-news-info2 h4','text'),
            'Description' => array('.wx-news-info2>a','text'),
            'PicUrl' => array('.wx-img-box a img','src'),
            'Url' => array('.wx-img-box a','href')
        );
        $obj = \QueryList::Query($url,$reg);
        return $obj->jsonArr;
    }
}