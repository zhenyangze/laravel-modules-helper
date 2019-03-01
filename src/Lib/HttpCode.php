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
     * __construct 
     *
     * @return 
     */
    public function __construct()
    {
        $this->config = config('modules_helper.http_code');
    }

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
