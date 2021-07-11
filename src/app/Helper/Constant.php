<?php


namespace App\Helper;

class Constant
{
    const FORMAT_DATETIME = 'Y-m-d H:i:s';
    const FORMAT_DATE_HOUR_MIN = 'Y-m-d H:i';
    const FORMAT_DATE = 'Y-m-d';
    const FORMAT_TIME = 'H:i';
    const REGEX_MAIL      = '/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix';
    const REGEX_PASSWORD  = '/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])/';
    const REGEX_NOT_JAPANESE   = '/^[^ぁ-んァ-ン一-龯ｧ-ﾝﾞﾟ０-９Ａ-ｚｦ-ﾟ]+$/';
    const REGEX_NUMBER_COMMA = '/^[0-9]+(,[0-9]+)+$/';
    const REGEX_JAPANESE_ZIP_CODE = '/^([0-9]{3}-[0-9]{4})?$|^[0-9]{7}+$/';
    const REGEX_JAPANESE_PHONE = '/^[0-9]{10,15}$/';
    const DOMAIN_NAME_REGEX = '/(?=^.{4,253}$)(^((?!-)[a-zA-Z0-9-]{0,62}[a-zA-Z0-9]\.)+[a-zA-Z]{2,63}$)/';
    const FLAG_ON = 1;
    const FLAG_OFF = 0;

    public static function isOn($flag)
    {
        return $flag == self::FLAG_ON;
    }
}
