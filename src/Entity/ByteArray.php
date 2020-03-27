<?php
/**
 * Created by PhpStorm.
 * User: Jarvan
 * Date: 20/3/26
 * Time: 17:10.
 */

namespace AlibabaAllianceSdk\Entity;

class ByteArray
{
    private $bytesValue;

    public function setBytesValue($bytesValue)
    {
        $this->bytesValue = $bytesValue;
    }

    public function getByteValue()
    {
        return $this->bytesValue;
    }
}
