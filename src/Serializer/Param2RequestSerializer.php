<?php
/**
 * Created by PhpStorm.
 * User: Jarvan
 * Date: 20/3/26
 * Time: 16:58.
 */

namespace AlibabaAllianceSdk\Serializer;

use AlibabaAllianceSdk\Entity\ByteArray;
use AlibabaAllianceSdk\Entity\SDKDomain;
use AlibabaAllianceSdk\Util\DateUtil;
use AlibabaAllianceSdk\Util\SDKDomainUtil;

class Param2RequestSerializer
{
    public function serialize($serializer)
    {
        $serializedResult = [];
        if (null == $serializer) {
            return $serializedResult;
        }
        $ref               = new \ReflectionObject($serializer);
        $sdkStdResultArray = null;
        foreach ($ref->getMethods() as $tempMethod) {
            $methodName = $tempMethod->name;
            if ('getSdkStdResult' == $methodName) {
                $sdkStdResultArray = $tempMethod->invoke($serializer);
            }
        }
        if (null == $sdkStdResultArray) {
            foreach ($ref->getMethods() as $tempMethod) {
                $methodName = $tempMethod->name;
                if (0 === strpos($methodName, 'get') && 'getSdkStdResult' != $methodName) {
                    $propertyName = substr($methodName, 3);
                    $propertyName = lcfirst($propertyName);
                    $resultValue  = $tempMethod->invoke($serializer);
                    if (($resultValue instanceof \DateTime)) {
                        $timeValue                       = $resultValue->getTimestamp();
                        $strTime                         = DateUtil::parseToString($timeValue);
                        $serializedResult[$propertyName] = $strTime;
                    } elseif (($resultValue instanceof ByteArray)) {
                        $tempValue                       = base64_encode($resultValue->getByteValue());
                        $serializedResult[$propertyName] = $tempValue;
                    } elseif (($resultValue instanceof SDKDomain)) {
                        $sdkDomainUtil                   = new SDKDomainUtil();
                        $tempArray                       = $sdkDomainUtil->generateSDKDomainArray($resultValue);
                        $resultJsonValue                 = json_encode($tempArray);
                        $serializedResult[$propertyName] = $resultJsonValue;
                    } elseif (is_array($resultValue)) {
                        $resultJsonValue                 = json_encode($resultValue);
                        $serializedResult[$propertyName] = $resultJsonValue;
                    } else {
                        $serializedResult[$propertyName] = $resultValue;
                    }
                }
            }
        } else {
            foreach ($sdkStdResultArray as $k => $v) {
                $resultValue = $v;
                if (($resultValue instanceof \DateTime)) {
                    $timeValue            =$resultValue->getTimestamp();
                    $strTime              = DateUtil::parseToString($timeValue);
                    $serializedResult[$k] = $strTime;
                } elseif (($resultValue instanceof ByteArray)) {
                    $tempValue            = base64_encode($resultValue->getByteValue());
                    $serializedResult[$k] = $tempValue;
                } elseif (($resultValue instanceof SDKDomain)) {
                    $sdkDomainUtil        = new SDKDomainUtil();
                    $tempArray            = $sdkDomainUtil->generateSDKDomainArray($resultValue);
                    $resultJsonValue      = json_encode($tempArray);
                    $serializedResult[$k] = $resultJsonValue;
                } elseif (is_array($resultValue)) {
                    $resultJsonValue      = json_encode($resultValue);
                    $serializedResult[$k] = $resultJsonValue;
                } else {
                    $serializedResult[$k] = $resultValue;
                }
            }
        }

        return $serializedResult;
    }
}
