<?php
/**
 * Created by PhpStorm.
 * User: Jarvan
 * Date: 20/3/26
 * Time: 16:24.
 */
error_reporting(E_ALL);
require dirname(__DIR__) . '/vendor/autoload.php';

use AlibabaAllianceSdk\AllianceSdk;
use AlibabaAllianceSdk\Param\CrossProductInfoParam;
use AlibabaAllianceSdk\Param\CrossSyncProductListPushedParam;

$config = [
    'appKey'      => 'XXX',
    'secKey'      => 'XXX',
    'serverHost'  => 'XXX',
    'accessToken' => 'XXX',
    'isCurl'      => true,
];
$productId  = '547233552359';
$alianceSdk = new AllianceSdk($config);
$param      = new CrossSyncProductListPushedParam();
$param->setProductIdList([$productId]);
$response = $alianceSdk->syncProduct($param);
var_dump($response);

$param = new CrossProductInfoParam();
$param->setProductId($productId);
$response = $alianceSdk->getProRequest($param);
var_dump($response);
