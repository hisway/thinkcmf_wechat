<?php
// +----------------------------------------------------------------------
// | Lottery.class.php
// +----------------------------------------------------------------------
// | Author: polo <gao.bo168@gmail.com>
// +----------------------------------------------------------------------
// | Data: 2015-3-6下午2:44:35
// +----------------------------------------------------------------------
// | Version: 2015-3-6下午2:44:35
// +----------------------------------------------------------------------
// | Copyright: ShowMore
// +----------------------------------------------------------------------
namespace plugins\Wechat\Api\Lottery;
class Lottery{
    /**
     * [getLotteryList 获取彩票票种类型]
     * @return mixed
     * @access public
     * @author polo<gao.bo168@gmail.com>
     * @version 2015-3-6 下午3:55:10
     * @copyright Show More
     */
    public static function getLotteryList(){
        $lottery = file_get_contents('http://api.opencai.net/static/lottery.js');
        $lottery = substr($lottery, 16);
        $n=strpos($lottery,'var');
        if ($n) $lottery=substr($lottery,0,$n);
        return json_decode($lottery,true);
    }
    /**
     * [getLotteryResult 传入彩票代号得到彩票结果,自定义格式,xml格式或json格式,ut8编码或gb2312编码]
     * @param unknown $lotterycode
     * @param string $codetype
     * @param string $rows
     * @return mixed
     * @access public
     * @author polo<gao.bo168@gmail.com>
     * @version 2015-3-6 下午3:55:26
     * @copyright Show More
     */
    public static function getLotteryResult($lotterycode,$codetype='json',$rows='5'){
        $url = 'http://120.26.124.144:8080/' . $lotterycode . '-' . $rows . '.' . $codetype;
        return file_get_contents($url);
    }
}