<?php
/**
 * Created by PhpStorm.
 * User: Jarvan
 * Date: 20/3/26
 * Time: 15:12.
 */

namespace AlibabaAllianceSdk;

use AlibabaAllianceSdk\Param\CrossProductInfoParam;
use AlibabaAllianceSdk\Param\CrossSyncProductListPushedParam;
use AlibabaAllianceSdk\Serializer\Param2RequestSerializer;
use AlibabaAllianceSdk\Util\DateUtil;
use AlibabaAllianceSdk\Util\RequestUtil;
use AlibabaAllianceSdk\Util\SignatureUtil;

class AllianceSdk
{
    private $appKey;
    private $secKey;
    private $serverHost;
    private $accessToken;
    private $isCurl = true;

    /**
     * @var ClientPolicy
     */
    private $clientPolicy;

    public function __construct(array $config)
    {
        if (empty($config)) {
            throw new \Exception('config is empty');
        }
        $this->appKey      = $config['appKey'] ?? '';
        $this->secKey      = $config['secKey'] ?? '';
        $this->serverHost  = $config['serverHost'] ?? '';
        $this->accessToken = $config['accessToken'] ?? '';
        $this->isCurl      = $config['isCurl'] ?? true; //不配置时，默认启动为curl请求
        if (empty($this->appKey) || empty($this->secKey) || empty($this->serverHost) || empty($this->accessToken)) {
            throw new \Exception('config parameter is error');
        }
        $this->clientPolicy         = new ClientPolicy();
        $this->clientPolicy->appKey = $this->appKey;
        $this->clientPolicy->secKey = $this->secKey;
    }

    /**
     * @param CrossSyncProductListPushedParam $param
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function syncProduct(CrossSyncProductListPushedParam $param)
    {
        if (empty($param->getProductIdList())) {
            throw new \Exception('getProductIdList is empty');
        }
        $reqPolicy      = $this->getRequestPolicy();
        $request        = new APIRequest();
        $apiId          = new APIId('com.alibaba.product.push', 'alibaba.cross.syncProductListPushed', 1);
        $request->apiId = $apiId;

        $request->requestEntity = $param;
        $request->accessToken   = $this->accessToken;

        $response = $this->send($request, $reqPolicy);

        return $response;
    }

    /**
     * 获取商品信息.
     *
     * @param CrossProductInfoParam $param
     *
     * @return mixed
     */
    public function getProRequest(CrossProductInfoParam $param)
    {
        $reqPolicy      = $this->getRequestPolicy();
        $request        = new APIRequest();
        $apiId          = new APIId('com.alibaba.product', 'alibaba.cross.productInfo', 1);
        $request->apiId = $apiId;

        $request->requestEntity = $param;
        $request->accessToken   = $this->accessToken;
        $response               = $this->send($request, $reqPolicy);

        return $response;
    }

    /**
     * @param APIRequest    $request
     * @param RequestPolicy $requestPolicy
     *
     * @return mixed
     */
    private function send(APIRequest $request, RequestPolicy $requestPolicy)
    {
        $urlRequest = $this->generateRequestPath($request, $requestPolicy, $this->clientPolicy);

        if ($requestPolicy->useHttps) {
            if (443 == $this->clientPolicy->httpsPort) {
                $urlRequest = 'https://' . $this->clientPolicy->serverHost . $urlRequest;
            } else {
                $urlRequest = 'https://' . $this->clientPolicy->serverHost . ':' . $this->clientPolicy->httpsPort . $urlRequest;
            }
        } else {
            if (80 == $this->clientPolicy->httpPort) {
                $urlRequest = 'http://' . $this->clientPolicy->serverHost . $urlRequest;
            } else {
                $urlRequest = 'http://' . $this->clientPolicy->serverHost . ':' . $this->clientPolicy->httpPort . $urlRequest;
            }
        }

        $serializerTools = new Param2RequestSerializer();
        $requestData     = $serializerTools->serialize($request->requestEntity);
        $requestData     = array_merge($requestData, $request->addtionalParams);
        if (null != $request->accessToken) {
            $requestData['access_token'] = $request->accessToken;
        }
        /*if ($requestPolicy->requestSendTimestamp) {
            $requestData ["_aop_timestamp"] = time();
        }*/
        $requestData['_aop_datePattern'] = DateUtil::getDateFormatInServer();
        if ($requestPolicy->useSignture) {
            if (null != $this->clientPolicy->appKey && null != $this->clientPolicy->secKey) {
                $pathToSign                    = $this->generateAPIPath($request, $requestPolicy, $this->clientPolicy);
                $signaturedStr                 = SignatureUtil::signature($pathToSign, $requestData, $this->clientPolicy);
                $requestData['_aop_signature'] = $signaturedStr;
            }
        }

        /*$paramToSign = '';
        foreach ($requestData as $k => $v) {
            $paramToSign = $paramToSign . $k . '=' . urlencode($v) . '&';
        }
        $paramLength = strlen($paramToSign);
        if ($paramLength > 0) {
            $paramToSign = substr($paramToSign, 0, $paramLength - 1);
        }*/

        //发起请求
        $data = $this->isCurl ? RequestUtil::fpmCurlSend($urlRequest, $requestData) : RequestUtil::coroutineSend($urlRequest, $requestData);

        return $data;
    }

    /**
     * @param APIRequest    $request
     * @param RequestPolicy $requestPolicy
     * @param ClientPolicy  $clientPolicy
     *
     * @return string
     */
    private function generateRequestPath(APIRequest $request, RequestPolicy $requestPolicy, ClientPolicy $clientPolicy)
    {
        if ($requestPolicy->accessPrivateApi) {
            $urlResult = '/api';
        } else {
            $urlResult = '/openapi';
        }

        $defs = [
            $urlResult,
            '/',
            $requestPolicy->requestProtocol,
            '/',
            $request->apiId->version,
            '/',
            $request->apiId->namespace,
            '/',
            $request->apiId->name,
            '/',
            $clientPolicy->appKey,
        ];

        $urlResult = implode($defs);

        return $urlResult;
    }

    /**
     * @param APIRequest    $request
     * @param RequestPolicy $requestPolicy
     * @param ClientPolicy  $clientPolicy
     *
     * @return string
     */
    private function generateAPIPath(APIRequest $request, RequestPolicy $requestPolicy, ClientPolicy $clientPolicy)
    {
        $urlResult = '';
        $defs      = [
            $urlResult,
            $requestPolicy->requestProtocol,
            '/',
            $request->apiId->version,
            '/',
            $request->apiId->namespace,
            '/',
            $request->apiId->name,
            '/',
            $clientPolicy->appKey,
        ];

        $urlResult = implode($defs);

        return $urlResult;
    }

    /**
     * 获取一个请求规则类.
     *
     * @return RequestPolicy
     */
    private function getRequestPolicy()
    {
        $reqPolicy                       = new RequestPolicy();
        $reqPolicy->httpMethod           = 'POST';
        $reqPolicy->needAuthorization    = true;
        $reqPolicy->requestSendTimestamp = false;
        $reqPolicy->useHttps             = false;
        $reqPolicy->useSignture          = true;
        $reqPolicy->accessPrivateApi     = false;

        return $reqPolicy;
    }
}
