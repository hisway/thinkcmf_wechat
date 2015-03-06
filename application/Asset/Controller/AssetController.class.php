<?php

/**
 * 附件上传
 */
namespace Asset\Controller;
use Common\Controller\AdminbaseController;
class AssetController extends AdminbaseController {


    function _initialize() {
    	$adminid=sp_get_current_admin_id();
    	$userid=sp_get_current_userid();
    	if(empty($adminid) && empty($userid)){
    		exit("非法上传！");
    	}
    }

    /**
     * swfupload 上传 
     */
    public function swfupload() {
        if (IS_POST) {
			
            //上传处理类
            // $config=array(
            // 		'rootPath' => './'.C("UPLOADPATH"),
            // 		'savePath' => '',
            // 		'maxSize' => 11048576,
            // 		'saveName'   =>    array('uniqid',''),
            // 		'exts'       =>    array('jpg', 'gif', 'png', 'jpeg',"txt",'zip'),
            // 		'autoSub'    =>    false,
            // );
            $config = array(
                'mimes'    => '', //允许上传的文件MiMe类型
                'maxSize'  => 10*1024*1024, //上传的文件大小限制 (0-不做限制)
                'exts'     => 'jpg,gif,png,jpeg,txt,zip', //允许上传的文件后缀
                'autoSub'  => true, //自动子目录保存文件
                'subName'  => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
                'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
                'saveExt'  => '', //文件保存后缀，空则使用原后缀
                'replace'  => false, //存在同名是否覆盖
                'hash'     => true, //是否生成hash编码
                'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
                );
            $qiniu = array(
                'accessKey' => 'y8GpaE2oehHvaZr6qBp4DiGO1E7CoHojXrmTRbRF',
                'secrectKey' => '6BgNN5atYnYnc_Vt2PAOsIUdooOCGLEYqVZrq4rj',
                'domain' => 'source.gaoboy.com',
                'bucket' => 'gaoboy',
              );
			$upload = new \Think\Upload($config,'Qiniu',$qiniu);
			$info=$upload->upload();
            //开始上传
            if ($info) {
                //上传成功
                //写入附件数据库信息
                $first=array_shift($info);
                if(!empty($first['url'])){
                	$url=$first['url'];
                }else{
                	$url=C("TMPL_PARSE_STRING.__UPLOAD__").$first['savename'];
                }
                
				echo "1," . $url.",".'1,'.$first['name'];
				exit;
            } else {
                //上传失败，返回错误
                exit("0," . $upload->getError());
            }
        } else {
            $this->display(':swfupload');
        }
    }

}
