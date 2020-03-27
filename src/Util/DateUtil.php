<?php
/**
 * Created by PhpStorm.
 * User: Jarvan
 * Date: 20/3/26
 * Time: 17:07.
 */

namespace AlibabaAllianceSdk\Util;

class DateUtil
{
    public static function getDateFormatInServer()
    {
        return 'yyyyMMddHHmmssSSSZ';
    }

    public static function getDateFormat()
    {
        return 'YmdHisu';
    }

    public static function parseToString($dateTime)
    {
        if (null == $dateTime) {
            return;
        }

        return date(self::getDateFormat(), $dateTime);
    }
}
