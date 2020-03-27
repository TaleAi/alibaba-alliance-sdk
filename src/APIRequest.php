<?php
/**
 * Created by PhpStorm.
 * User: Jarvan
 * Date: 20/3/26
 * Time: 16:04.
 */

namespace AlibabaAllianceSdk;

class APIRequest
{
    /**
     * @var APIId
     */
    public $apiId;

    /**
     * @var
     */
    public $addtionalParams = [];

    /**
     * @var
     */
    public $requestEntity;

    /**
     * @var
     */
    public $attachments=[];

    /**
     * @var string
     */
    public $authCodeKey;

    /**
     * @var string
     */
    public $accessToken;

    /**
     * @var AuthorizationToken
     */
    public $authToken;
}
