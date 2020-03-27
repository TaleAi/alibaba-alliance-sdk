<?php
/**
 * Created by PhpStorm.
 * User: Jarvan
 * Date: 20/3/26
 * Time: 17:13.
 */

namespace AlibabaAllianceSdk\Util;

use AlibabaAllianceSdk\Entity\ByteArray;
use AlibabaAllianceSdk\Entity\SDKDomain;

class SDKDomainUtil
{
    public function generateSDKDomainArray($sdkDomain)
    {
        $serializedResult = [];
        $ref              = new \ReflectionObject($sdkDomain);
        foreach ($ref->getMethods() as $tempMethod) {
            $methodName = $tempMethod->name;
            if (0 === strpos($methodName, 'get')) {
                $propertyName = substr($methodName, 3);
                $propertyName = lcfirst($propertyName);
                $resultValue  = $tempMethod->invoke($sdkDomain);
                if (($resultValue instanceof \DateTime)) {
                    $timeValue =$resultValue->getTimestamp();
                    $strTime   = DateUtil::parseToString($timeValue);

                    $serializedResult[$propertyName] = $strTime;
                } elseif (($resultValue instanceof ByteArray)) {
                    $tempValue                       = base64_encode($resultValue->getByteValue());
                    $serializedResult[$propertyName] = $tempValue;
                } elseif (($resultValue instanceof SDKDomain)) {
                    $tempArray                       = $this->generateSDKDomainArray($resultValue);
                    $serializedResult[$propertyName] = $tempArray;
                } elseif (is_array($resultValue)) {
                    $sdkDomainSubArrayArray = [];
                    foreach ($resultValue as $tempValue) {
                        $result = $this->processSDKDomain($tempValue);
                        array_push($sdkDomainSubArrayArray, $result);
                    }
                    $serializedResult[$propertyName] = $sdkDomainSubArrayArray;
                } else {
                    $serializedResult[$propertyName] = $resultValue;
                }
            }
        }

        return $serializedResult;
    }

    private function processSDKDomain($resultValue)
    {
        if (($resultValue instanceof \DateTime)) {
            return $resultValue;
        } elseif (($resultValue instanceof ByteArray)) {
            $tempValue = base64_encode($resultValue->getByteValue());

            return $tempValue;
        } elseif (($resultValue instanceof SDKDomain)) {
            $sdkDomainSubArray = $this->generateSDKDomainArray($resultValue);

            return $sdkDomainSubArray;
        } elseif (is_array($resultValue)) {
            $sdkDomainSubArrayArray = [];
            foreach ($resultValue as $tempValue) {
                $result = $this->processSDKDomain($tempValue);
                array_push($sdkDomainSubArrayArray, $result);
            }

            return $sdkDomainSubArrayArray;
        }

        return $resultValue;
    }
}
