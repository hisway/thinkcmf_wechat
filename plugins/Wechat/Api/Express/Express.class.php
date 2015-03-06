<?php
// +----------------------------------------------------------------------
// | Express.class.php
// +----------------------------------------------------------------------
// | Author: polo <gao.bo168@gmail.com>
// +----------------------------------------------------------------------
// | Data: 2015-3-4下午4:36:31
// +----------------------------------------------------------------------
// | Version: 2015-3-4下午4:36:31
// +----------------------------------------------------------------------
// | Copyright: ShowMore
// +----------------------------------------------------------------------
namespace plugins\Wechat\Api\Express;
/* demo：include 'Express.class.php';echo '<pre>';var_dump(json_decode(Express::getExpressInfo('868124032474'),true)); */
class Express{
    /**
     *快递查询接口常量
     */
    const   KUAIDI100 = 'http://www.kuaidi100.com';

    /**返回快递信息
     * @param $order_num 快递单号
     * @return bool|mixed|string 快递信息
     */
    public static function getExpressInfo($order_num)
    {
        $query_api = self::KUAIDI100 . '/query?';
        $keywords = self::getExpressName($order_num);
        //未查看到快递信息返回false
        if (empty($keywords)) return false;
        //返回查询的信息
        return self::getContent($query_api . "type=" . $keywords . "&postid=" . $order_num);
    }

    /**获取url信息
     * @param $url 需要访问URL地址
     * @return mixed|string 返回的结果信息
     */
    private static function getContent($url)
    {
    	$ch = curl_init();
    	$timeout = 5;
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    	$file_contents = curl_exec($ch);
    	curl_close($ch);
        return $file_contents;
    }

    /**获得快递公司名字
     * @param $order_num 订单号
     * @return bool 返回订单快递公司名称
     */
    private static function getExpressName($order_num)
    {
        $api_url = self::KUAIDI100 . "/autonumber/auto?";
        $res_names = json_decode(self::getContent($api_url . "num=" . $order_num), true);
        return empty($res_names) ? false : $res_names[0]['comCode'];
    }
}