<?php

namespace Yangze\ModulesHelper\Lib;

/**
 * HttpCode
 */
class HttpCode
{
    use SingleTrait;
    /**
     * config
     */
    private $config = [];

    /**
     * code
     *
     * @param $identify
     * @param $code
     *
     * @return null||string
     */
    public static function code($identify, $code = '')
    {
        if (empty($code)) {
            return array_get(static::getinstance()->config, $identify);
        }
        static::getinstance()->config[$identify] = $code;
    }

    /**
     * message
     *
     * @param $identify
     *
     * @return string
     */
    public static function message($identify = '')
    {
        return $identify;
    }
}
