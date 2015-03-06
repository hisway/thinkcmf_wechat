<?php
// +----------------------------------------------------------------------
// | WechatPlugin.class.php
// +----------------------------------------------------------------------
// | Author: polo <gao.bo168@gmail.com>
// +----------------------------------------------------------------------
// | Data: 2015-3-3下午2:33:43
// +----------------------------------------------------------------------
// | Version: 2015-3-3下午2:33:43
// +----------------------------------------------------------------------
// | Copyright: ShowMore
// +----------------------------------------------------------------------
namespace plugins\Wechat;
use Common\Lib\Plugin;
use plugins\Wechat\Api\TpWechat\TpWechat;
class WechatPlugin extends Plugin{
    public $info = array(
        'name'=>'Wechat',
        'title'=>'微信公众号',
        'description'=>'微信公众号接入',
        'status'=>1,
        'author'=>'Polo',
        'version'=>'1.0'
    );
    
    public function install(){//安装方法必须实现
        $db_prefix = C('DB_PREFIX');
        $sql=<<<SQL
CREATE TABLE `{$db_prefix}plugin_wechat_user` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `uid` int(20) NOT NULL COMMENT '绑定本站uid',
  `subscribe` tinyint(2) NOT NULL COMMENT '用户是否订阅该公众号标识，值为0时，代表此用户没有关注该公众号，拉取不到其余信息',
  `openid` varchar(40) NOT NULL COMMENT '用户的标识，对当前公众号唯一',
  `nickname` varchar(255) NOT NULL COMMENT '用户的昵称',
  `sex` tinyint(2) NOT NULL COMMENT '用户的性别，值为1时是男性，值为2时是女性，值为0时是未知',
  `city` varchar(50) NOT NULL COMMENT '用户所在城市',
  `country` varchar(50) NOT NULL COMMENT '用户所在国家',
  `province` varchar(50) NOT NULL COMMENT '用户所在省份',
  `language` varchar(50) NOT NULL COMMENT '用户的语言，简体中文为zh_CN',
  `headimgurl` varchar(255) NOT NULL COMMENT '用户头像，最后一个数值代表正方形头像大小（有0、46、64、96、132数值可选，0代表640*640正方形头像），用户没有头像时该项为空。若用户更换头像，原有头像URL将失效。',
  `subscribe_time` int(10) NOT NULL COMMENT '用户关注时间，为时间戳。如果用户曾多次关注，则取最后关注时间',
  `unionid` varchar(255) NOT NULL COMMENT '只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段',
  `latitude` varchar(20) NOT NULL COMMENT '地理位置纬度',
  `longitude` varchar(20) NOT NULL COMMENT '地理位置经度',
  `labelname` varchar(255) NOT NULL COMMENT '微信反馈的地理位置信息',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;
        D()->execute($sql);
        return true;//安装成功返回true，失败false
    }
    
    public function uninstall(){//卸载方法必须实现
        $db_prefix = C('DB_PREFIX');
        D()->execute("DROP TABLE IF EXISTS {$db_prefix}plugin_wechat_user;");
        return true;//卸载成功返回true，失败false
    }
    
    //实现的footer钩子方法
    public function wechat($param){
        $config=$this->getConfig();
        switch ($param['type']) {
            case 'connect':
                $options = array(
                    			'token'=>$config['Token'], //填写你设定的key
                    			'encodingaeskey'=>$config['EncodingAESKey'], //填写加密用的EncodingAESKey
                    			'appid'=>$config['AppID'], //填写高级调用功能的app id
                    			'appsecret'=>$config['AppSecret'] //填写高级调用功能的密钥
                    		);
                $weObj = new TpWechat($options);
                $weObj->valid();
                //用户openid:
                $openid = $weObj->getRev()->getRevFrom();
                $type = $weObj->getRev()->getRevType();
                switch($type) {
                case TpWechat::MSGTYPE_TEXT:
                    /* 收到用户主动回复消息处理 */
                    $content = $weObj->getRev()->getRevContent(); //获取消息内容
                    /* 将消息内容与已有关键字进行匹配,对相应关键字进行相关响应 */
                    $reply = D('plugins://Wechat/PluginWechat')->reply($openid,$content,$weObj,$config);
            		exit;
            		break;
                case TpWechat::MSGTYPE_LOCATION:
                    /* 收到用户主动回复地理位置 */
                	$location = $weObj->getRev()->getRevGeo();
                	$judge = M('PluginWechatUser')->where(array('openid'=>$openid))->find();
                	if($judge){
                		M('PluginWechatUser')->where(array('id' => $judge['id']))->setField(array('latitude'=>$location['x'],'longitude'=>$location['y'],'labelname'=>$location['label']));
                	}else{
                		if($config['IsAuth'] == 0){
                			$user_data = array(
                					'subscribe' => 1,
                					'openid' => $openid,
                					'subscribe_time' => time(),
                					'latitude' => $location['x'],
                					'longitude' => $location['y'],
                					'labelname' => $location['label']
                			);
                		}else if($config['IsAuth'] == 1){
                			$user_data = $weObj->getUserInfo($openid);
                			$user_data['latitude'] = $location['x'];
                			$user_data['longitude'] = $location['y'];
                			$user_data['labelname'] = $location['label'];
                		}
                		M('PluginWechatUser')->add($user_data);
                	}
                    $weObj->text("您的最新位置已经更新,查询周边信息可回复'找xxx',比如'找ATM','找银行','找酒店','找厕所'等等,下次查询前记得先发位置再查询哟O(∩_∩)O~")->reply();
                    break;
           		case TpWechat::MSGTYPE_EVENT:
           		    $rev_event = $weObj->getRevEvent();
           		    /* 检测事件类型 */
           		    switch ($rev_event['event']){
           		        case TpWechat::EVENT_MENU_CLICK:
           		            //TODO:CLICK事件
           		            break;
           		        case TpWechat::EVENT_SUBSCRIBE:
           		            /* 如果公众号没有认证,则不能拉取用户信息 */
           		            if($config['IsAuth'] == 0){
           		                $user_data = array(
           		                    'subscribe' => 1,
           		                    'openid' => $openid,
           		                    'subscribe_time' => time()
           		                );
           		            }else if($config['IsAuth'] == 1){
           		                $user_data = $weObj->getUserInfo($openid);
           		            }
           		            $judge = M('PluginWechatUser')->where(array('openid'=>$openid))->find();
           		            if($judge){
           		                M('PluginWechatUser')->where(array('id' => $judge['id']))->save($user_data);
           		            }else{
           		                M('PluginWechatUser')->add($user_data);
           		            }
           		            /* 下推关注欢迎语 */
           		            $weObj->text($config['Welcome'])->reply();
           		            break;
       		            case TpWechat::EVENT_UNSUBSCRIBE:
       		                $judge = M('PluginWechatUser')->where(array('openid'=>$openid))->find();
       		                if($judge){
       		                    M('PluginWechatUser')->where(array('id' => $judge['id']))->setField(array('subscribe'=>0));
       		                }
       		                break;
       		            case TpWechat::EVENT_LOCATION:
       		                /* 认证号才有的功能 */
       		                $location = $weObj->getRev()->getRevEventGeo(); //获取上报地理位置
       		               break;
       		            default:
       		                break;
           		    }
           			break;
           		case TpWechat::MSGTYPE_IMAGE:
           			break;
           		default:
           			$weObj->text("help info")->reply();
           			break;
               }
                break;/* connect end */
            default:
                break;
        }
    }
}