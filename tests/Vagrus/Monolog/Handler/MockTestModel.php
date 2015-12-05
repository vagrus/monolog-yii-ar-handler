<?php

namespace Vagrus\Monolog\Handler;

class MockTestModel
{
    public static $attributes;
    public static $isSaved;
    public static $isValidating;

    public function __construct()
    {
        self::$attributes = array();
        self::$isSaved = false;
        self::$isValidating = false;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return bool
     */
    public function setAttribute($name, $value)
    {
        self::$attributes[$name] = $value;

        return true;
    }

    /**
     * @param bool $runValidation
     * @param array|null $attributes
     * @return bool
     */
    public function save($runValidation = true, $attributes = null)
    {
        self::$isValidating = $runValidation;
        self::$isSaved = true;

        return true;
    }
}
