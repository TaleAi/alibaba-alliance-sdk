<?php
/**
 * Created by PhpStorm.
 * User: Jarvan
 * Date: 20/3/26
 * Time: 17:22.
 */

namespace AlibabaAllianceSdk\Util;

use AlibabaAllianceSdk\ClientPolicy;

class SignatureUtil
{
    /**
     * @param $path
     * @param array        $parameters
     * @param ClientPolicy $clientPolicy
     *
     * @return string
     */
    public static function signature($path, array $parameters, ClientPolicy $clientPolicy)
    {
        $paramsToSign = [];
        foreach ($parameters as $k => $v) {
            $paramToSign = $k . $v;
            array_push($paramsToSign, $paramToSign);
        }
        sort($paramsToSign);
        $implodeParams      = implode($paramsToSign);
        $pathAndParams      = $path . $implodeParams;
        $sign               = hash_hmac('sha1', $pathAndParams, $clientPolicy->secKey, true);
        $signHexWithLowcase = bin2hex($sign);
        $signHexUppercase   = strtoupper($signHexWithLowcase);

        return $signHexUppercase;
    }
}
