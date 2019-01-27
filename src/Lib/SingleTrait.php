<?php 
namespace Yangze\ModulesHelper\Lib;

trait SingleTrait{
    /**
     * instance
     */
    static public $instance;

    /**
     * __construct
     *
     * @return
     */
    private function __construct()
    {
    }

    /**
     * __clone
     *
     * @return
     */
    private function __clone()
    {
    }

    /**
     * getinstance
     *
     * @return object
     */
    public static function getinstance()
    {
        if (!static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

}
