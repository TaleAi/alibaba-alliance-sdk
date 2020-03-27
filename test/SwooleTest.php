<?php
/**
 * Created by PhpStorm.
 * User: Jarvan
 * Date: 20/3/27
 * Time: 11:02.
 */
error_reporting(E_ALL);
require dirname(__DIR__) . '/vendor/autoload.php';

use AlibabaAllianceSdk\AllianceSdk;
use AlibabaAllianceSdk\Param\CrossProductInfoParam;
use AlibabaAllianceSdk\Param\CrossSyncProductListPushedParam;

class SwooleTest
{
    public function __construct()
    {
        $http = new \swoole_http_server('0.0.0.0', 10000);
        $http->set([
            'worker_num'    => 2,
            'dispatch_mode' => 2,
            'reload_async'  => true,
            'max_wait_time' => 50,
            'daemonize'     => 0,
            'max_request'   => 20000,
        ]);
        $http->on('Start', [$this, 'onStart']);
        $http->on('request', [$this, 'onRequest']);
        $http->start();
    }

    public function onStart(\swoole_server $server)
    {
        echo 'swoole is start 0.0.0.0:10000' . PHP_EOL;
    }

    public function onRequest(\swoole_http_request $request, \swoole_http_response $response)
    {
        $config = [
            'appKey'      => 'XXX',
            'secKey'      => 'XXX',
            'serverHost'  => 'XXX',
            'accessToken' => 'XXX',
            'isCurl'      => false,
        ];
        $productId  = '547233552359';
        $alianceSdk = new AllianceSdk($config);
        $param      = new CrossSyncProductListPushedParam();
        $param->setProductIdList([$productId]);
        $response1 = $alianceSdk->syncProduct($param);

        $param = new CrossProductInfoParam();
        $param->setProductId($productId);
        $response2 = $alianceSdk->getProRequest($param);

        $response->end(json_encode([$response1, $response2]));
    }
}

new SwooleTest();
