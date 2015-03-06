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
        //TODO:根据content,查询关键字表,调用相应API回复,本版本先不建
        if(preg_match('/(.+)天气/i',$content, $matchs)){
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
        }else if(preg_match('/快递(.+)/i',$content, $matchs)){
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

        }else if(preg_match('/双色球/',$content, $matchs)){
        	$url = 'http://f.opencai.net/utf8/ssq.json';
        	$ch = curl_init();
        	$timeout = 5;
        	curl_setopt($ch, CURLOPT_URL, $url);
        	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        	$file_contents = curl_exec($ch);
        	curl_close($ch);
        	dump(json_decode($file_contents,true));
        }else if(preg_match('/找(.+)/i',$content, $matchs)){
        	$judge = M('PluginWechatUser')->where(array('openid'=>$openid))->find();
        	if($judge){
        		if($judge['latitude'] && $judge['longitude']){
        			$json_array = file_get_contents('http://api.map.baidu.com/place/v2/search?query=' . urlencode($matchs[1]) . '&output=json&ak=' . $config['BaiduAk'] . '&page_size=10&page_num=0&scope=2&location=' . $judge['latitude'] . ',' . $judge['longitude'] . '&radius=2000');
        			$json_array = json_decode($json_array,true);
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
}