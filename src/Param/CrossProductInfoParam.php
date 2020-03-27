<?php
/**
 * Created by PhpStorm.
 * User: Jarvan
 * Date: 20/3/26
 * Time: 17:44.
 */

namespace AlibabaAllianceSdk\Param;

class CrossProductInfoParam
{
    private $sdkStdResult = [];

    /**
     * @return mixed
     */
    public function getProductId()
    {
        $tempResult = $this->sdkStdResult['productId'];

        return $tempResult;
    }

    /**
     * 设置1688商品ID.
     *
     * @param $productId
     */
    public function setProductId($productId)
    {
        $this->sdkStdResult['productId'] = $productId;
    }

    public function getSdkStdResult()
    {
        return $this->sdkStdResult;
    }
}
