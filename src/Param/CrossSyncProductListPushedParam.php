<?php
/**
 * Created by PhpStorm.
 * User: Jarvan
 * Date: 20/3/26
 * Time: 15:30.
 */

namespace AlibabaAllianceSdk\Param;

class CrossSyncProductListPushedParam
{
    private $sdkStdResult = [];

    /**
     * @return mixed
     */
    public function getProductIdList()
    {
        $tempResult = $this->sdkStdResult['productIdList'];

        return $tempResult;
    }

    /**
     * 设置1688的商品ID列表,列表长度不能超过20个.
     *
     * @param array $productIdList
     */
    public function setProductIdList(array $productIdList)
    {
        $this->sdkStdResult['productIdList'] = $productIdList;
    }

    public function getSdkStdResult()
    {
        return $this->sdkStdResult;
    }
}
