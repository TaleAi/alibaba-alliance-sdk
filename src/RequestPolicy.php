<?php
/**
 * Created by PhpStorm.
 * User: Jarvan
 * Date: 20/3/26
 * Time: 15:49.
 */

namespace AlibabaAllianceSdk;

class RequestPolicy
{
    public $requestSendTimestamp;
    public $useHttps                 = true;
    public $requestProtocol          = 'param2';
    public $responseProtocol         = 'param2';
    public $responseCompress         = true;
    public $requestCompressThreshold = -1;
    public $timeout                  = 5000;
    public $httpMethod               = 'POST';
    public $queryStringCharset       = 'GB18030';
    public $contentCharset           = 'UTF-8';
    public $useSignture              = true;
    public $needAuthorization        = true;
    public $accessPrivateApi         = false;
    public $defaultApiVersion        = 1;
}
