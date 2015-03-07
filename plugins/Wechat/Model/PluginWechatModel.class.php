<?php
// +----------------------------------------------------------------------
// | PluginWechatModel.class.php
// +----------------------------------------------------------------------
// | Author: polo <gao.bo168@gmail.com>
// +----------------------------------------------------------------------
// | Data: 2015-3-4下午5:28:06
// +----------------------------------------------------------------------
// | Version: 2015-3-4下午5:28:06
// +----------------------------------------------------------------------
// | Copyright: ShowMore
// +----------------------------------------------------------------------
namespace plugins\Wechat\Model;//Demo插件英文名，改成你的插件英文就行了
use Common\Model\CommonModel;//继承CommonModel
class PluginWechatModel extends CommonModel{
    public function __construct() {
    }
    public function reply($openid,$content,$weObj,$config){
        //根据content,查询关键字表,调用相应方法进行回复
        $lists = M('PluginWechatAutoreply')->where(array('status'=>1))->select();
        foreach($lists as $val){
        	if(preg_match($val['rule'],$content, $matchs)){
        		$this->$val['function']($openid,$weObj,$config,$matchs);
        		exit();
        	}
        }
        unset($val);
        $weObj->text("很抱歉,恩波不知道你在说什么,回复'帮助'或者'bz'或者'help'查看可用指令")->reply();
    }
    /**
     * 
     * [replyHelp 帮助回复信息]
     * @param unknown $openid
     * @param unknown $weObj
     * @param unknown $config
     * @param unknown $matchs
     * @access public
     * @author polo
     * @version 2015-3-7 下午3:25:34
     * @copyright Show More
     */
    public function replyHelp($openid,$weObj,$config,$matchs){
    	$weObj->text($config['Welcome'])->reply();
    }
    /**
     * 
     * [replyWeather 回复天气预报]
     * @param unknown $openid
     * @param unknown $weObj
     * @param unknown $config
     * @param unknown $matchs
     * @access public
     * @author polo
     * @version 2015-3-7 下午1:31:55
     * @copyright Show More
     */
    public function replyWeather($openid,$weObj,$config,$matchs){
    	$json_array = file_get_contents('http://api.map.baidu.com/telematics/v3/weather?location='.$matchs[1].'&output=json&ak=' . $config['BaiduAk']);
    	$json_array = json_decode($json_array,true);
    	$array = $json_array['results'][0]['weather_data'];
    	date_default_timezone_set ('Asia/Shanghai');
    	$h=date('H');
    	if($json_array['error'] > -3){
    		foreach ($array as $key=>$val){
    			date_default_timezone_set(PRC);
    			$h=date('H');
    			if($h>=8 && $h<=19){
    				$articles [$key] = array (
    						'Title' => $val['date']."\n".$val['weather']." ".$val['wind']." ".$val['temperature'],
    						'Description' => '',
    						'PicUrl' => $val['dayPictureUrl'],
    						'Url' => ''
    				);
    			}else {
    				$articles [$key] = array (
    						'Title' => $val['date']."\n".$val['weather']." ".$val['wind']." ".$val['temperature'],
    						'Description' => '',
    						'PicUrl' => $val['nightPictureUrl'],
    						'Url' => ''
    				);
    			}
    		}
    		$tarray = array (
    				'Title' => $json_array['results'][0]['currentCity']."天气预报",
    				'Description' => '',
    				'PicUrl' => '',
    				'Url' => ''
    		);
    		array_unshift($articles,$tarray);
    		$weObj->news($articles)->reply();
    	}else {
    		$weObj->text("没找到耶！...〒_〒")->reply();
    	}
    }
    /**
     * 
     * [replyExpress 回复快递信息]
     * @param unknown $openid
     * @param unknown $weObj
     * @param unknown $config
     * @param unknown $matchs
     * @access public
     * @author polo
     * @version 2015-3-7 下午1:33:45
     * @copyright Show More
     */
    public function replyExpress($openid,$weObj,$config,$matchs){
    	$result = json_decode(\plugins\Wechat\Api\Express\Express::getExpressInfo($matchs[1]),true);
    	if($result['message'] == 'ok'){
    		$kuaidi = '单号为' . $matchs[1] . '(最近更新时间:' . $result['updatetime'] . ')的查询结果如下:
    	
';
    		foreach($result['data'] as $v){
    			$kuaidi .= $v['time'] . ' ' . $v['context'] . '
    	
';
    		}
    		$weObj->text($kuaidi)->reply();
    	}else if($result['status'] == '201'){
    		$weObj->text($result['message'])->reply();
    	}else{
    		$weObj->text('查询失败,请重新回复查询内容')->reply();
    	}
    }
    /**
     * 
     * [replyLotteryList 回复彩票种类和查询码]
     * @param unknown $openid
     * @param unknown $weObj
     * @param unknown $config
     * @param unknown $matchs
     * @access public
     * @author polo
     * @version 2015-3-7 下午1:35:15
     * @copyright Show More
     */
    public function replyLotteryList($openid,$weObj,$config,$matchs){
    	$lotteryList = \plugins\Wechat\Api\Lottery\Lottery::getLotteryList();
    	$text = "查询相应彩票结果请回复对应的查询码,比如,要查询超级大乐透,则请回复'cp0';
";
    	foreach($lotteryList as $k => $v){
    		$text .= $v['area'].$v['descr'] . " : cp" . $k . "
";
    		if($k > 77) break;
    	}
    	$weObj->text($text)->reply();
    }
    /**
     * 
     * [replyLotteryRes 回复查询彩票开奖结果]
     * @param unknown $openid
     * @param unknown $weObj
     * @param unknown $config
     * @param unknown $matchs
     * @access public
     * @author polo
     * @version 2015-3-7 下午1:36:39
     * @copyright Show More
     */
    public function replyLotteryRes($openid,$weObj,$config,$matchs){
    	$lotteryList = \plugins\Wechat\Api\Lottery\Lottery::getLotteryList();
    	$lotteryCode = $lotteryList[$matchs[1]]['code'];
    	$lotteryName = $lotteryList[$matchs[1]]['area'] . $lotteryList[$matchs[1]]['descr'];
    	$lotteryRes = \plugins\Wechat\Api\Lottery\Lottery::getLotteryResult($lotteryCode,'json','utf8',5);
    	$lotteryRes = json_decode($lotteryRes,true);
    	if($lotteryRes['rows'] > 0){
    		$text = $lotteryName . "最近5期开奖结果如下:
    	
";
    		foreach($lotteryRes['data'] as $v){
    			$text .= "第" . $v['expect'] . "期" . "
开奖时间:" . $v['opentime'] . "
结果:" . $v['opencode'] . "
    	
";
    		}
    		$weObj->text($text)->reply();
    	}else{
    		$weObj->text('抱歉,查不到该彩票开奖信息')->reply();
    	}
    }
    /**
     * 
     * [replyFind 回复找周边]
     * @param unknown $openid
     * @param unknown $weObj
     * @param unknown $config
     * @param unknown $matchs
     * @access public
     * @author polo
     * @version 2015-3-7 下午1:38:39
     * @copyright Show More
     */
    public function replyFind($openid,$weObj,$config,$matchs){
    	$judge = M('PluginWechatUser')->where(array('openid'=>$openid))->find();
    	if($judge){
    		if($judge['latitude'] && $judge['longitude']){
    			$json_array = json_decode(file_get_contents('http://api.map.baidu.com/place/v2/search?query=' . urlencode($matchs[1]) . '&output=json&ak=' . $config['BaiduAk'] . '&page_size=10&page_num=0&scope=2&location=' . $judge['latitude'] . ',' . $judge['longitude'] . '&radius=2000'),true);
    			if($json_array['message'] == 'ok'){
    				foreach($json_array['results'] as $k => $v){
    					$img_array = json_decode(file_get_contents('http://map.baidu.com/detail?qt=img&uid=' . $v['uid']),true);
    					$articles[$k] = array (
    							'Title' => $v['name'] . "\n地址:" . $v['address'] . "\n距离:" . $v['detail_info']['distance'] . "米",
    							'Description' => '',
    							'PicUrl' => $img_array['images']['all'][0]['imgUrl'],
    							'Url' => $v['detail_info']['detail_url']
    					);
    				}
    				$weObj->news($articles)->reply();
    			}else{
    				$weObj->text('抱歉,查询不到')->reply();
    			}
    		}else{
    			$weObj->text('请先发送位置哦,不然恩波不知道该从何找起')->reply();
    			exit();
    		}
    	}else{
    		if($config['IsAuth'] == 0){
    			$user_data = array(
    					'subscribe' => 1,
    					'openid' => $openid,
    					'subscribe_time' => time()
    			);
    		}else if($config['IsAuth'] == 1){
    			$user_data = $weObj->getUserInfo($openid);
    		}
    		M('PluginWechatUser')->add($user_data);
    		$weObj->text('请先发送位置哦,不然恩波不知道该从何找起')->reply();
    		exit();
    	}
    }
}